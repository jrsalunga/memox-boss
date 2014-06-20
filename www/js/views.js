_.extend(Backbone.View.prototype, {
	close: function () {
	    if (this.beforeClose) {
	        this.beforeClose();
	    }
	    console.log('this close');
	    console.log(this);

	    this.unbind();
	    this.remove();

	    this.$el.unbind();
	    this.$el.remove();

	   
	 
		console.log(this.$el);
		console.log(this.el);	    

	    //delete this.$el; // Delete the jQuery wrapped object variable
	   	delete this.el; // Delete the variable reference to this node
	    delete this;
	    //this = null;
	},
	showCurrentView: function(view, el) {
		//console.log('showView');
    	if (this.currentView){
    		console.log('showCurrentView this');
      		this.currentView.close();
    	}
    	
	 	this.currentView = view;
	 	
	    this.currentView.render();
	   	//console.log(this.currentView);
	    //console.log($(".r-pane .p-development"));
	 	$(el).html(this.currentView.el);
	 	return this;
 	},
 	money: function(val){
 		return accounting.formatMoney(val,"", 2,",");
 	}
});


//	apvRM
var ApvReportModel = Backbone.Model.extend({
	defaults: {
		code: '',
		supplier: '',
		percent: '',
		totline: 0,
		totamount: 0.00
	}
});
var ApvReportCollection = Backbone.Collection.extend({
	model: ApvReportModel,
	initialize: function(){
		this.on('add', function(){
			//console.log(this);
		}, this)
	}
});


var ApvDtl = Backbone.View.extend({
	tagName: 'tr',
	initialize: function(){

		this.model.on('change', this.render, this);

		this.template = _.template('<td><a href="/reports/accounts-payable-print/<%- id %>" target="_blank"><%= refno %></a></td><td><%= due %></td>'
			+'<td><% if(posted==1){ %> <span class="glyphicon glyphicon-posted pull-right" title="Posted"></span>'
			+'<% } else { %>  <span class="glyphicon glyphicon-unposted pull-right" title="Unposted"></span>  <% } %></td>'
			+'<td style="text-align: right;"><%= accounting.formatMoney(totamount,"", 2,",") %></td>'
			+'<td style="text-align: right;"><%= accounting.formatMoney(balance,"", 2,",") %></td>');

	},
	render: function(){
		//console.log(this);
		this.$el.html(this.template(this.model.toJSON()));
		this.$el.attr("data-posted", this.model.get('posted'));
		return this;
	}
});

var ApvDtls = Backbone.View.extend({
	initialize: function(){

		//this.collection.on('reset', this.render, this);
		this.$el.html('<table class="table table-striped tb-data">'
			+'<thead>'
			+'<th>Ref No.</th>'
			+'<th>Due</th>'
			+'<th>Posted</th>'
			+'<th>Amount</th>'
			+'<th>Balance</th>'
			+'</thead>'
			+'<tbody class="apv-list"></tbody></table>');
	},
	render: function(){
		
			this.cleanUp();
			this.$el.find('.tb-data tbody').empty();
			this.addAll();

		return this;
	},
	addOne: function(apvhdr){
		this.apvReport = new ApvDtl({model: apvhdr});
		//console.log(apvReport);
		this.apvReport.listenTo(this, 'clean_up', this.apvReport.close);
		this.$el.find('.tb-data tbody').append(this.apvReport.render().el);
		
	},
	addAll: function(){
		this.collection.each(this.addOne, this);
	},
	cleanUp: function(){
		//console.log('this trigger clean_up');
		this.trigger('clean_up');
	}
});


var ApvhdrDetail = Backbone.View.extend({
	className: 'panel panel-default',
	initialize: function(){
		this.model.on('change', this.render, this);
		this.apvDtls = new ApvDtls({collection: this.collection});
		this.template = _.template(' '
        	+'<div id="panel-<%-supplierid%>" class="panel-heading">'
          	+'<h4 class="panel-title">'
            +'<a data-toggle="collapse" data-parent="#apvhdr-details" href="#collapse-<%-guid%>">'
            +'<%-name%>'//' <span class="badge"><%-totline%></span>'
            +'</a>'
            +' <span class="badge a"><%-totline%></span>'
            +' <span class="badge p" style="display:none;"><%-postedlen%></span>'
            +' <span class="badge u" style="display:none;"><%-unpostedlen%></span>'
            +'<span class="pull-right tot a"><%-amount%></span>'
            +'<span class="pull-right tot u" style="display:none;"><%-unposted%></span>'
            +'<span class="pull-right tot p" style="display:none;"><%-posted%></span>'
          	+'</h4></div>'
        	+'<div id="collapse-<%-guid%>" class="panel-collapse collapse">'
          	+'<div class="panel-body">'
          	//+'Lorem Ipsum soloer'
          	+'</div></div>');
	},
	render: function(){
		//console.log(this.apvDtls);
		this.model.set({guid: this.uid()}, {silent: true});

		this.$el.html(this.template(this.model.toJSON()));
		this.$el.attr("data-posted", this.model.get('status'));
		//var apvDtls = new ApvDtls({collection: this.collection});
		//this.$el.find('.panel-body').html(apvDtls.render().el);

		this.showCurrentView(this.apvDtls, this.$el.find('.panel-body'));
		return this;
	},
	uid: function(){

		var S4 = function() {
	   		return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
		}

		var guid = function() {
	   		return (S4()+S4()+S4()+S4()+S4()+S4()+S4()+S4());
		}

		return guid();
	}
});

var ApvhdrDetails = Backbone.View.extend({
	initialize: function(){

		this.c = new Apvhdrs();
		this.alltotal = new ApvReportCollection();

		//this.posted = new ApvReportCollection();
		//this.unposted = new ApvReportCollection();
		this.collection.on('reset', this.resetVars, this);
		
	},
	resetVars: function(){
		//console.log(this.loadData());
		//console.log(this.loadData('0'));
		//console.log(this.loadData('1'));

		this.alltotal.reset(this.loadData());
		//this.posted.reset(this.loadData("1"));
		//this.unposted.reset(this.loadData("0"));
		this.addAll();
	},
	loadData: function(p){
		var that = this, f, supplierByDue;

		if(p){
			f = this.collection.where({posted: p});
			supplierByDue = _.groupBy(f, function(m){
		    	return m.get('suppliercode');
			});

		} else {
			supplierByDue = this.collection.groupBy(function(m){
		    	return m.get('suppliercode');
			});
		}

		//console.log(supplierByDue);
		/*
		var sumAmount = function(total, supplier){
			if(p){
				if(supplier.get('posted')==p) {
					return total += parseFloat(supplier.get('totamount'));
				} else {
        			return total; 
   				 }				
			} else {
				return total += parseFloat(supplier.get('totamount'));
			}    
		}
		*/
		
		
		var sumAmount = function(total, supplier){
		    return total += parseFloat(supplier.get('totamount'));
		}
		
		var sumPosted = function(total, supplier){
			var d = 0;
			if (supplier.get('posted')=="1") {
				return total += parseFloat(supplier.get('totamount'));
			} else {
				return total;
			}
		    
		}

		var sumUnposted = function(total, supplier){
			var d = 0;
		    if (supplier.get('posted')=="0") {
				return total += parseFloat(supplier.get('totamount'));
			} else {
				return total;
			}
		}

		var getpostedlen = function(total, supplier){
		    if (supplier.get('posted')=="1") {
				return total += 1;
			} else {
				return total;
			}
		}

		var getunpostedlen = function(total, supplier){
		    if (supplier.get('posted')=="0") {
				return total += 1;
			} else {
				return total;
			}
		}

		var getObj = function(total, supplier){

			var x = {
				code: supplier.get('suppliercode'),
				supplier: supplier.get('supplier'),
				supplierid: supplier.get('supplierid'),
				posted: supplier.get('posted'),
				id: supplier.get('id'),

			}

			return x;
		}

		var sums = _.map( supplierByDue, function(suppliers, supplier){
			//console.log(suppliers);
			var o = suppliers.reduce(getObj, 0);
			var amt = suppliers.reduce(sumAmount, 0);
		    var pct = (amt/that.collection.getFieldTotal('totamount'))*100;
		    var p = suppliers.reduce(sumPosted, 0);
			var u = suppliers.reduce(sumUnposted, 0);
			var pl = suppliers.reduce(getpostedlen, 0);
			var ul = suppliers.reduce(getunpostedlen, 0);
		    var x = {
		        name: o.supplier,
		        amount: accounting.formatMoney(amt,"", 2,","),
		        percent: parseFloat(accounting.toFixed(pct,2)),
		        code: o.code,
		        totline: suppliers.length,
		        supplierid: o.supplierid,
		        status: o.posted,
		        id: o.id,
		        posted: accounting.formatMoney(p,"", 2,","),
		        unposted: accounting.formatMoney(u,"", 2,","),
		        postedlen: pl,
		        unpostedlen: ul
		    }
		    return x;
		})
		//console.log(sums);

		var high = _.chain(sums)
			  .sortBy(function(sums){ return sums.name; })
			  .map(function(sums){ return sums.name + ' is ' + sums.amount; })
			  .first()
			  .value();
		//console.log(high);

		sums.sort(function (a, b) {
		    if (a.name > b.name)
		      return 1;
		    if (a.name < b.name)
		      return -1;
		    // a must be equal to b
		    return 0;
		});


		return sums;
	},
	render: function(){
		
	},
	addAll: function(){
		this.removeApvhdrDetailViews();
		this.$el.find('.report-detail-all').empty();
		//this.$el.find('.report-detail-posted').html('');
		//this.$el.find('.report-detail-unposted').html('');
		this.alltotal.each(this.loadAll, this);
		//this.posted.each(this.loadPosted, this);
		//this.unposted.each(this.loadUnposted, this);
		
	},
	where: function(p){
		var arr = [];
		var x = this.collection.where(p);
		_.each(x, function(e,i,l){
			arr.push(e.toJSON());
		});
		return arr
	},
	loadAll: function(apvRM){
		//var curapv = this.collection.where({supplierid: apvRM.get('supplierid')});
		
		this.c.reset(this.where({supplierid: apvRM.get('supplierid')}));
		
		this.apvhdrDetail = new ApvhdrDetail({model: apvRM, collection: this.c});
		//console.log('attach clean_up');
		// attach clean_up event to apvhdrDetail to listenTo for removal of this view for re render
		this.apvhdrDetail.listenTo(this, 'clean_up', this.apvhdrDetail.close);
		//console.log(this.apvhdrDetail);
		this.$el.find('.report-detail-all').append(this.apvhdrDetail.render().el);
		
		return this;
	},
	removeApvhdrDetailViews: function(){
		//console.log('this trigger clean_up');
		this.trigger('clean_up');
	},
	/*
	loadPosted: function(apvRM){
		this.c.reset(this.where({supplierid: apvRM.get('supplierid')}));
		
		var apvhdrDetail = new ApvhdrDetail({model: apvRM, collection: this.c});
		this.$el.find('.report-detail-posted').append(apvhdrDetail.render().el);
		
		return this;		
	},
	loadUnposted: function(apvRM){
		this.c.reset(this.where({supplierid: apvRM.get('supplierid')}));
		
		var apvhdrDetail = new ApvhdrDetail({model: apvRM, collection: this.c});
		this.$el.find('.report-detail-unposted').append(apvhdrDetail.render().el);
		
		return this;
	}
	*/
});

/*
var ApvReport = Backbone.View.extend({
	tagName: 'tr',
	initialize: function(){

		this.model.on('change', this.render, this);

		this.template = _.template('<td><%= refno %></td><td><%= due %></td><td><%= supplier %></td>'
			+'<td style="text-align: right;"><%= accounting.formatMoney(totamount,"", 2,",") %></td>'
			+'<td style="text-align: right;"><%= accounting.formatMoney(balance,"", 2,",") %></td>');

	},
	render: function(){
		this.$el.html(this.template(this.model.toJSON()));
		return this;
	}
});

var ApvReports = Backbone.View.extend({
	initialize: function(){

		this.collection.on('reset', this.render, this);
		this.$el.html('<table class="table table-striped tb-data">'
			+'<thead>'
			+'<th>APV Ref No</th>'
			+'<th>Due</th>'
			+'<th>Posted</th>'
			+'<th>Amount</th>'
			+'<th>Balance</th>'
			+'</thead>'
			+'<tbody></tbody></table>');
	},
	render: function(){
		this.$el.find('.tb-data tbody').empty();
		return this;
	},
	addOne: function(apvhdr){
		var apvReport = new ApvReport({model: apvhdr});
		//console.log(apvReport);
		this.$el.find('.tb-data tbody').append(apvReport.render().el);		
	},
	addAll: function(){
		this.collection.each(this.addOne, this);
	}
});
*/

var ReportApvhdr = Backbone.View.extend({
	el: '#apvhdr-report',
	initialize: function(){

		this.apvhdrs = new Apvhdrs();

		
		/*
		this.collection.on('reset', function(){
			console.log('reset ReportApvhdr');
			//console.log(this.collection.toJSON());
			this.apvhdrs.reset(this.collection.toJSON());
		}, this);
		*/
		this.active = false;

		this.listenTo(this.collection, 'reset', function(){
			this.apvhdrs.reset(this.collection.toJSON());
			this.active = 'all';
			this.loadCollections();
			this.setTotal();
		})

		
		this.pie = new vPie({el: "#c-pie",collection: this.apvhdrs, settings: {title: 'Percentage per Account'}});
		this.apvLine = new vApvLine({el: "#c-line", collection: this.collection});
		//this.column = new vColumn({el: '#c-column' , collection: this.apvhdrs});
		//this.apvReports = new ApvReports({el: '#apvhdr-report-list' , collection: this.apvhdrs});
		this.apvhdrDetails = new ApvhdrDetails({el: '#apvhdr-details' , collection: this.collection});
		/*
		console.log(this.pie);
		console.log(this.apvLine);
		console.log(this.column);
		console.log(this.apvhdrDetails);
		*/
		this.$el.find('#range-to').val(moment().format("YYYY-MM-DD"));
		
	},
	events: {
		'click .btn-date-range': 'searchDue',
		'click #filter-all': 'setAll',
		'click #filter-posted': 'setPosted',
		'click #filter-unposted': 'setUnposted'
	},
	render: function(){

		return this;
	},
	searchDue: function(){
		_.isEmpty(this.$el.find('#range-to').val()) ? '' : apvhdrsDue.navigate("apvdue/"+ this.$el.find('#range-to').val() , {trigger: true});
	},
	setAll: function(){

		if(this.active != 'all') {
			$(".report-detail-all .panel").slideDown();	
			$('.report-detail-all .panel-title span.tot.a').show().siblings('span.tot').hide();
			$('.report-detail-all .panel-title span.badge.a').show().siblings('span.badge').hide();
			$('.report-detail-all .apv-list tr').show();

			if(this.collection.length==0){
				this.searchDue();
			} else {
				this.apvhdrs.reset(this.collection.toJSON());
				this.active = 'all';
				$('.total-list-a').parent().addClass('list-group-item-info')
							.siblings().removeClass('list-group-item-info');
			}
		}
	},
	setPosted: function(){
		
		if(this.active != 'posted') {
			//$(".report-detail-all .panel").slideUp();
			//$('.report-detail-all .apv-list [data-posted="1"]').slideDown();
			$('.report-detail-all .apv-list [data-posted="0"]').hide().closest('.panel').slideUp();
			$('.report-detail-all .apv-list [data-posted="1"]').show().closest('.panel').slideDown();

			$('.report-detail-all .panel-title span.tot.p').show().siblings('span.tot').hide();
			$('.report-detail-all .panel-title span.badge.p').show().siblings('span.badge').hide();
			/*
			$('.report-detail-all .panel-title span.p').show();
			$('.report-detail-all .panel-title span.p').show();
			*/

			if(!this.c_posted){
				console.log('no collection: load first');
			} else {
				this.apvhdrs.reset(this.c_posted.toJSON());
				this.active = 'posted';
			}
			
			$('.total-list-p').parent().addClass('list-group-item-info')
							.siblings().removeClass('list-group-item-info');
		}

	},
	setUnposted: function(){

		if(this.active != 'unposted') {
			//$(".report-detail-all .panel").slideUp();
			//$('.report-detail-all .apv-list [data-posted="0"]').slideDown();
			$('.report-detail-all .apv-list [data-posted="1"]').hide().closest('.panel').slideUp();
			$('.report-detail-all .apv-list [data-posted="0"]').show().closest('.panel').slideDown();

			$('.report-detail-all .panel-title span.tot.u').show().siblings('span.tot').hide();
			$('.report-detail-all .panel-title span.badge.u').show().siblings('span.badge').hide();
			

			if(!this.c_unposted){
				console.log('no collection: load first');
			} else {
				this.apvhdrs.reset(this.c_unposted.toJSON());
				this.active = 'unposted';
				$('.total-list-u').parent().addClass('list-group-item-info')
							.siblings().removeClass('list-group-item-info');
			}
		}

	},
	loadCollections: function(){
		this.c_unposted = new Apvhdrs(this.where({posted: "0"}));
		this.c_posted = new Apvhdrs(this.where({posted: "1"}));
	},
	where: function(param){
		var arr = [];
		var x = this.collection.where(param);
		_.each(x, function(e,i,l){
			arr.push(e.toJSON());
		});
		return arr;
	},
	setTotal: function(){
		$('.total-list-a').text(this.money(this.collection.getFieldTotal('totamount')));
		$('.total-list-p').text(this.money(this.c_posted.getFieldTotal('totamount')));
		$('.total-list-u').text(this.money(this.c_unposted.getFieldTotal('totamount')));
	}
});



/*
var vAgeApvhdr = Backbone.View.extend({
	initialize: function(){
		this.model.on('change', this.render, this);
		this.template = _.template('<div><%-suppliercode%></div>');
	},
	render: function(){
		this.$el.html(this.template(this.model.toJSON()))
		return this;
	}
});


var vAgeApvhdrs = Backbone.View.extend({
	initialize: function(){
		this.collection.on('reset', this.render, this);
		this.collection.on('add', this.addOne, this);
	},
	render: function(){
		this.$el.empty();
		this.addAll();
		return this;
	},
	addOne: function(apvhdrs){
		var vageApvhdr = new vAgeApvhdr({model: apvhdrs});
		this.$el.append(vageApvhdr.render().el);
	},
	addAll: function(){
		this.collection.each(this.addOne, this);
	}
});


var ApvhdrAgeDetails = Backbone.View.extend({

	initialize: function(){

		//this.c = new Apvhdrs();

		this.c_age0 = new Apvhdrs();
		this.c_age30 = new Apvhdrs();
		this.c_age60 = new Apvhdrs();
		this.c_age90 = new Apvhdrs();
		this.c_age120 = new Apvhdrs();
		this.c_age150 = new Apvhdrs();
		
		


		this.v_age0 = new vAgeApvhdrs({el: '.report-detail-0 .panel-body', collection: this.c_age0});
		//this.$el.find('.report-detail-0').append(this.v_age0.render().el);

		this.v_age30 = new vAgeApvhdrs({el: '.report-detail-30 .panel-body', collection: this.c_age30});
		//this.$el.find('.report-detail-30').append(this.v_age30.render().el);

		this.v_age60 = new vAgeApvhdrs({el: '.report-detail-60 .panel-body', collection: this.c_age60});
		//this.$el.find('.report-detail-60').append(this.v_age60.render().el);

		this.v_age90 = new vAgeApvhdrs({el: '.report-detail-90 .panel-body', collection: this.c_age90});
		//this.$el.find('.report-detail-90').append(this.v_age90.render().el);

		this.v_age120 = new vAgeApvhdrs({el: '.report-detail-120 .panel-body', collection: this.c_age120});
		//this.$el.find('.report-detail-120').append(this.v_age120.render().el);

		this.v_age150 = new vAgeApvhdrs({el: '.report-detail-150 .panel-body', collection: this.c_age150});
		//this.$el.find('.report-detail-150').append(this.v_age150.render().el);
		

		this.collection.on('reset', this.resetVars, this);
	},
	resetVars: function(){

		this._iDate =  _.isEmpty($('#range-to').val()) ? alert('no date selected') : $('#range-to').val();

		this.c_age0.reset();
		this.c_age30.reset();
		this.c_age60.reset();
		this.c_age90.reset();
		this.c_age120.reset();
		this.c_age150.reset();

		this.collection.each(this.loadData, this);


		this.addAll();
	},
	loadData: function(apvhdrs){
	
		
		var now =	new Date(this._iDate.replace(/-/g, ','));
		var date = new Date(apvhdrs.get('due').replace(/-/g, ','));

		var timeDiff = Math.abs(now.getTime() - date.getTime());
		var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
		console.log(diffDays);

		if(diffDays == 0){
			//console.log('1 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age0.add(apvhdrs.toJSON());

		} else if(diffDays >= 1 && diffDays <= 30){
			//console.log('1 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age30.add(apvhdrs.toJSON());

		} else if(diffDays >= 31 && diffDays <= 60){
			//console.log('2 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age60.add(apvhdrs.toJSON());

		} else if(diffDays >= 61 && diffDays <= 90){
			//console.log('3 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age90.add(apvhdrs.toJSON());

		} else if(diffDays >= 91 && diffDays <= 120){
			//console.log('4 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age120.add(apvhdrs.toJSON());

		} else if(diffDays >= 120 ){
			//console.log('5 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age150.add(apvhdrs.toJSON());

		}
		
	},
	addAll: function(){


		
	}
});
*/




























var AgeApvDtl = Backbone.View.extend({
	tagName: 'tr',
	initialize: function(){

		this.model.on('change', this.render, this);

		this.template = _.template('<td><%- suppliercode %><td><a href="/reports/accounts-payable-print/<%- id %>" target="_blank"><%- refno %></a></td><td><%- due %></td>'
			+'<td><% if(posted==1){ %> <span class="glyphicon glyphicon-posted pull-right" title="Posted"></span>'
			+'<% } else { %>  <span class="glyphicon glyphicon-unposted pull-right" title="Unposted"></span>  <% } %></td>'
			+'<td style="text-align: right;"><%= accounting.formatMoney(totamount,"", 2,",") %></td>'
			+'<td style="text-align: right;"><%= accounting.formatMoney(balance,"", 2,",") %></td>');

	},
	render: function(){
		console.log(this);
		this.$el.html(this.template(this.model.toJSON()));
		this.$el.attr("data-posted", this.model.get('posted'));
		return this;
	}
});

var AgeApvDtls = Backbone.View.extend({
	initialize: function(){

		//this.collection.on('reset', this.render, this);
		this.$el.html('<table class="table table-striped tb-data">'
			+'<thead>'
			+'<th>Supplier</th>'
			+'<th>Ref No.</th>'
			+'<th>Due</th>'
			+'<th>Posted</th>'
			+'<th>Amount</th>'
			+'<th>Balance</th>'
			+'</thead>'
			+'<tbody class="apv-list"></tbody></table>');
	},
	render: function(){
			this.cleanUp();
			this.$el.find('.tb-data tbody').empty();
			this.addAll();

		return this;
	},
	addOne: function(apvhdr){
		this.apvReport = new AgeApvDtl({model: apvhdr});
		//console.log(apvReport);
		this.apvReport.listenTo(this, 'clean_up', this.apvReport.close);
		this.$el.find('.tb-data tbody').append(this.apvReport.render().el);
		
	},
	addAll: function(){
		this.collection.each(this.addOne, this);
	},
	cleanUp: function(){
		console.log('trigger clean_up AgeApvDtls');
		this.trigger('clean_up');
	}
});

var AgeApvhdrDetail = Backbone.View.extend({
	className: 'panel panel-default',
	initialize: function(){
		this.model.on('change', this.render, this);
		this.apvDtls = new AgeApvDtls({collection: this.collection});
		this.template = _.template(' '
        	+'<div id="panel" class="panel-heading">'
          	+'<h4 class="panel-title">'
            +'<a data-toggle="collapse" data-parent=".report-detail-all" href="#collapse-<%-code%>">'
            +'<%-supplier%>'//' <span class="badge"><%-totline%></span>'
            +'</a>'
            +' <span class="badge a"><%-totline%></span>'
            //+' <span class="badge p" style="display:none;"><%-postedlen%></span>'
            //+' <span class="badge u" style="display:none;"><%-unpostedlen%></span>'
            +'<span class="pull-right tot a"><%= accounting.formatMoney(totamount,"", 2,",") %></span>'
            //+'<span class="pull-right tot u" style="display:none;"><%-unposted%></span>'
            //+'<span class="pull-right tot p" style="display:none;"><%-posted%></span>'
          	+'</h4></div>'
        	+'<div id="collapse-<%-code%>" class="panel-collapse collapse">'
          	+'<div class="panel-body">'
          	//+'Lorem Ipsum soloer'
          	+'</div></div>');
	},
	render: function(){
		this.cleanUp() // clean up ung mga anak lang

		this.model.set({guid: this.uid()}, {silent: true});

		this.$el.html(this.template(this.model.toJSON()));
		this.$el.attr("data-posted", this.model.get('status'));
		//var apvDtls = new ApvDtls({collection: this.collection});
		//this.$el.find('.panel-body').html(apvDtls.render().el);

		//this.showCurrentView(this.apvDtls, this.$el.find('.panel-body'));
		this.apvDtls.listenTo(this, 'clean_up', this.apvDtls.close);
		this.$el.find('.panel-body').html(this.apvDtls.render().el);
		return this;
	},
	uid: function(){

		var S4 = function() {
	   		return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
		}

		var guid = function() {
	   		return (S4()+S4()+S4()+S4()+S4()+S4()+S4()+S4());
		}

		return guid();
	},
	cleanUp: function(){
		console.log('trigger clean_up AgeApvhdrDetail');
		this.trigger('clean_up');
	}
});


var AgeApvhdrDetails = Backbone.View.extend({
	initialize: function(){

		this.c_age0 = new Apvhdrs();
		this.c_age30 = new Apvhdrs();
		this.c_age60 = new Apvhdrs();
		this.c_age90 = new Apvhdrs();
		this.c_age120 = new Apvhdrs();
		this.c_age150 = new Apvhdrs();

		this.c_ages = new ApvReportCollection([
			{code: 'age0', supplier: 'Current', percent: '', totline: 0, totamount: 0.00 },
			{code: 'age30', supplier: 'Age 30', percent: '', totline: 0, totamount: 0.00 },
			{code: 'age60', supplier: 'Age 60', percent: '', totline: 0, totamount: 0.00 },
			{code: 'age90', supplier: 'Age 90', percent: '', totline: 0, totamount: 0.00 },
			{code: 'age120', supplier: 'Age 120', percent: '', totline: 0, totamount: 0.00 },
			{code: 'age150', supplier: 'Over 120', percent: '', totline: 0, totamount: 0.00 }
			]);

		//this.posted = new ApvReportCollection();
		//this.unposted = new ApvReportCollection();
		this.collection.on('reset', this.resetVars, this);
		
	},
	resetVars: function(){
		this._iDate =  _.isEmpty($('#range-to').val()) ? alert('no date selected') : $('#range-to').val();

		this.c_age0.reset();
		this.c_age30.reset();
		this.c_age60.reset();
		this.c_age90.reset();
		this.c_age120.reset();
		this.c_age150.reset();

		this.collection.each(this.loadData, this);

		this.addAll();
	},
	render: function(){
		
	},
	addAll: function(){
		this.cleanUp();
		this.$el.find('.report-detail-all').empty();
		//this.$el.find('.report-detail-posted').html('');
		//this.$el.find('.report-detail-unposted').html('');
		this.c_ages.each(this.loadAll, this);
		//this.posted.each(this.loadPosted, this);
		//this.unposted.each(this.loadUnposted, this);
		
	},
	loadAll: function(apvRM){
		 
		var col;

		switch(apvRM.get('code')){
			case 'age0':
				col = this.c_age0;
				apvRM.set({totline: this.c_age0.length, totamount: this.c_age0.getFieldTotal('totamount')});
			    break;
			case 'age30':
			    col = this.c_age30;
			    apvRM.set({totline: this.c_age30.length, totamount: this.c_age30.getFieldTotal('totamount')});
			    break;
			case 'age60':
			    col = this.c_age60;
			    apvRM.set({totline: this.c_age60.length, totamount: this.c_age60.getFieldTotal('totamount')});
			    break;
			case 'age90':
			    col = this.c_age90;
			    apvRM.set({totline: this.c_age90.length, totamount: this.c_age90.getFieldTotal('totamount')});
			    break;
			case 'age120':
			    col = this.c_age120;
			    apvRM.set({totline: this.c_age120.length, totamount: this.c_age120.getFieldTotal('totamount')});
			    break;
			case 'age150':
			    col = this.c_age150;
			    apvRM.set({totline: this.c_age150.length, totamount: this.c_age150.getFieldTotal('totamount')});
			    break;
		}

		this.vageApvhdrs = new AgeApvhdrDetail({model:apvRM, collection: col});
		console.log(this.vageApvhdrs);
		this.vageApvhdrs.listenTo(this, 'clean_up', this.vageApvhdrs.close);
		this.$el.find('.report-detail-all').append(this.vageApvhdrs.render().el);
		
	},
	loadData: function(apvhdrs){
	
		
		var now = new Date(this._iDate.replace(/-/g, ','));
		var date = new Date(apvhdrs.get('due').replace(/-/g, ','));

		var timeDiff = Math.abs(now.getTime() - date.getTime());
		var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
		//console.log(diffDays);

		if(diffDays == 0){
			//console.log('1 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age0.add(apvhdrs.toJSON());

		} else if(diffDays >= 1 && diffDays <= 30){
			//console.log('1 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age30.add(apvhdrs.toJSON());

		} else if(diffDays >= 31 && diffDays <= 60){
			//console.log('2 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age60.add(apvhdrs.toJSON());

		} else if(diffDays >= 61 && diffDays <= 90){
			//console.log('3 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age90.add(apvhdrs.toJSON());

		} else if(diffDays >= 91 && diffDays <= 120){
			//console.log('4 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age120.add(apvhdrs.toJSON());

		} else if(diffDays >= 120 ){
			//console.log('5 mons');
			//console.log(apvhdrs.get('due'));
			this.c_age150.add(apvhdrs.toJSON());

		}
		
	},
	cleanUp: function(){
		console.log('trigger clean_up AgeApvhdrDetails');
		this.trigger('clean_up');
	}
});



var ReportApvhdrAge = Backbone.View.extend({
	//el: '#apvhdr-report',
	initialize: function(){

		this.active = false;
		this.apvhdrs = new Apvhdrs();
		this.pie_apvhdrs = new Apvhdrs();

		this.pie = new vPie({el: "#c-pie", collection: this.pie_apvhdrs, settings: {title: 'Percentage per Age Total'}});
		this.stackedBar = new vStackedBar({el: "#c-stacked-bar", collection: this.collection,  settings: {title: 'Total Amount per Age'}});
		console.log(this.stackedBar);
		//this.apvhdrAgeDetails = new ApvhdrAgeDetails({el: '#apvhdr-age-details' , collection: this.collection});
		this.ageApvhdrDetails = new AgeApvhdrDetails({el: '#apvhdr-age-details' , collection: this.collection});
		console.log(this.ageApvhdrDetails);
		//this._iDate = moment().format("YYYY-MM-DD"); // input date
		this.$el.find('#range-to').val(moment().format("YYYY-MM-DD"));

		this.listenTo(this.collection, 'reset', function(){
			this.apvhdrs.reset(this.collection.toJSON());
			this.pie_apvhdrs.reset(this.collection.toJSON());
			this.apvhdrs.loadAgeData(this.$el.find('#range-to').val());
			//console.log(this.apvhdrs._age0);
		});

		this._iDate =  _.isEmpty(this.$el.find('#range-to').val()) ? moment().format("YYYY-MM-DD") : this.$el.find('#range-to').val() ;
	},
	events: {
		'click .btn-date-range': 'searchDue',
		'click #filter-mon-all': 'setAgeAll',
		'click #filter-mon-0': 'setAge0',
		'click #filter-mon-1': 'setAge30',
		'click #filter-mon-2': 'setAge60',
		'click #filter-mon-3': 'setAge90',
		'click #filter-mon-4': 'setAge120',
		'click #filter-mon-5': 'setAge150'
	},
	searchDue: function(){
		//this.currentDate
		this._iDate =  _.isEmpty(this.$el.find('#range-to').val()) ? moment().format("YYYY-MM-DD") : this.$el.find('#range-to').val() ;
		//console.log(c);

		apvhdrsAge.navigate("apvdue/"+ this._iDate , {trigger: true});
	
	},
	setAgeAll: function(){
		if(this.active != 'all'){
			$('.panel-collapse').removeClass('in').css('height', ' 0px');
        	this.pie_apvhdrs.reset(this.collection.toJSON());
        	this.active = 'all';
		}	
	},
	setAge0: function(){
		if(this.active != 'age0'){
        	this.pie_apvhdrs.reset(this.apvhdrs._age0);
        	this.active = 'age0';
		}
	},
	setAge30: function(){
		if(this.active != 'age30'){
        	this.pie_apvhdrs.reset(this.apvhdrs._age30);
        	this.active = 'age30';
		}
	},
	setAge60: function(){
		if(this.active != 'age60'){
        	this.pie_apvhdrs.reset(this.apvhdrs._age60);
        	this.active = 'age60';
		}
	},
	setAge90: function(){
		if(this.active != 'age90'){
        	this.pie_apvhdrs.reset(this.apvhdrs._age90);
        	this.active = 'age90';
		}
	},
	setAge120: function(){
		if(this.active != 'age120'){
        	this.pie_apvhdrs.reset(this.apvhdrs._age120);
        	this.active = 'age120';
		}
	},
	setAge150: function(){
		if(this.active != 'age150'){
        	this.pie_apvhdrs.reset(this.apvhdrs._age150);
        	this.active = 'age150';
		}
	},
	/*
	loadData: function(apvhdrs){
		this._iDate =  _.isEmpty(this.$el.find('#range-to').val()) ? moment().format("YYYY-MM-DD") : this.$el.find('#range-to').val() ;
		
		this._current = [];
		this._age30 = [];
		this._age60 = [];
		this._age90 = [];
		this._age120 = [];
		this._over120 = [];


		var now =	new Date(this._iDate.replace(/-/g, ','));
		var date = new Date(apvhdrs.get('due').replace(/-/g, ','));
		//console.log(now);
		//console.log(date);
		//console.log(new Date(now));
		//console.log(new Date(date));

		var timeDiff = Math.abs(now.getTime() - date.getTime());
		var diffDays = Math.ceil(timeDiff / (1000 * 3600 * 24)); 
		//console.log(apvhdrs.get('due'));
		//console.log(diffDays);
		//console.log(this._iDate);

		if(diffDays == 0){
			//console.log('0 mons');
			//console.log(apvhdrs.get('due'));
		} else if(diffDays >= 1 && diffDays <= 30){
			//console.log('1 mons');
			//console.log(apvhdrs.get('due'));
		} else if(diffDays >= 31 && diffDays <= 60){
			//console.log('2 mons');
			//console.log(apvhdrs.get('due'));
		} else if(diffDays >= 61 && diffDays <= 90){
			//console.log('3 mons');
			//console.log(apvhdrs.get('due'));
		} else if(diffDays >= 91 && diffDays <= 120){
			//console.log('4 mons');
			//console.log(apvhdrs.get('due'));
		} else if(diffDays >= 120 ){
			//console.log('5 mons');
			//console.log(apvhdrs.get('due'));
		}

	}
	*/
});
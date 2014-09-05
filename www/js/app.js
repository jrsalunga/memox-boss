




var AppRouter = Backbone.Router.extend({
    routes: {

    }
});





//var AppRouter = Backbone.Router.extend({
var ApvhdrsDue = AppRouter.extend({
    routes: {
        "": "home",
        "reports": "reports",
        "apvdue/:due": "apvdue"
       
    },
    initialize: function () {
    	var reportApvhdr = new ReportApvhdr({collection: apvhdrs});

    	this.apvdue($('#range-to').val());
		//console.log(reportApvhdr);
		//reportApvhdr.render(); 
    },
	home: function() {
       
    },
    reports: function(){
    	console.log('reports');
    	var html = '<div class="col-sm-2 col-md-2 l-pane">'
    				+'<ul class="nav nav-pills nav-stacked">'
						+'<li>'
							+'<a href="#report/apvhdr">Accounts Payable</a>'
						+'</li>'
						+'<li>'
							+'<a href="#report/cvhdr">Check</a>'
						+'<li>'
					+'</ul>'
    				+'</div>'
    				+'<div class="col-sm-10 col-md-10 r-pane"></div>';
    	
    	$('.stage').html(html);
    },
    apvdue: function(due){
    	//apvhdrs.url = '../www/api/r/apvdue?due='+ due;
        apvhdrs.url = '../api/r/apvdue?due='+ due;
        //apvhdrs.url = 'http://mfi-boss.no-ip.biz/api/r/apvdue?callback=hello&due='+ due;
		apvhdrs.fetch({reset: true});

        $('#range-to').val(due);
    }
});



var ApvhdrsAge = AppRouter.extend({
    routes: {
        "": "home",
        "apvdue/:due": "apvdue"
    },

    initialize: function () {
        this.reportApvhdrAge = new ReportApvhdrAge({el: '#apvhdr-report', collection: apvhdrs}); 
        this.apvdue($('#range-to').val());
        //console.log(this.reportApvhdrAge);  
    },
    home: function() {
    },
    apvdue: function(due){
        //apvhdrs.url = '../www/api/r/apvdue?due='+ due;
        apvhdrs.url = '../api/r/apvdue?due='+ due;
        //apvhdrs.url = 'http://mfi-boss.no-ip.biz/api/r/apvdue?callback=hello&due='+ due;
        apvhdrs.fetch({reset: true});

        $('#range-to').val(due);
    }
});




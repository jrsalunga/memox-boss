var root = location.protocol + '//' + location.host;
var rootpath = root ;
var apiPath = rootpath +'/api';

$(document).ready(function() {
	//ComputeItemAmount();
  	
	//$('.table-model input[type=checkbox]').css('border','1px red solid').val(1);
	$('.header-logo').attr('src','/images/mfi-logo.png');
	$('.navbar-brand').text('Modularfusion Inc');
	
	
	
	//$(".table-model .currency").maskMoney();
	$(".table-model .currency").each(function(){
		$(this).toNumberFormat();	
	});
	$(".table-model .currency").on('focus', function(){
		$(this).toDecimal();
	}).on('blur', function(){
		$(this).toNumberFormat();
	});


	$(".table-detail .currency").each(function(){
		$(this).toNumberFormat();	
	});
	
	
	
	$(".table-model input[type=checkbox].toggle").on('click', function(){
		
		console.log($(this).data('input'));
		var i = $(this).data('input');
		
		if($(this).attr('checked')){
    		$('#'+ i).val(1);
    	} else {
			$('#'+ i).val(0);
		}
	});
	
	

	
	
	$.ajaxSetup({
    beforeSend:function(){
		/*
		$.blockUI({ 
	    	message: ' <h2>Loading <img src="../images/ajax-loader.gif"  /> </h2>',
	    	css: {
			    border: 'none', 
			    padding: '15px', 
			    backgroundColor: '#fff', 
			    '-webkit-border-radius': '5px', 
			    '-moz-border-radius': '5px',  
				'border-radius': '5px',           
			     color: '#bbb' 
			}  
		});
		*/
		
		//$(".ajax-loading-overlay").show();
		//$(".ajax-loading").show();
    },
    complete:function(){
		
		/*$.unblockUI({ 
	    	message: ' <h2>Done</h2>',
	        onUnblock: function(){ 
				$('.blockMsg h2').html('Done');
			} 
		});*/
       
	  	$(".ajax-loading-overlay").hide();
		$(".ajax-loading").hide();
    	}
	});
	
	
	
	
	
	
	
	

	
	
	
	// prevent "#" showing on address bar
	$('a[href="#"]').click(function(e){
		//$(this).css('border','1px red solid');
		e.preventDefault();
	})
	
	
	$('.tb-detail').on('click','a[href="#"]',function(e){
		e.preventDefault();
	});
	
	
	
	$('.tb-data').on('click', 'a[class="delete"]', function(){  
	    var id = $(this).data('id');
		//var table = $(this).data('table');
		//deleteData(table,id);
		deleteData(id);
	});
	
	
	
	$('.tb-data').on('click', 'a[class="edit"]', function(e){  
	    var id = $(this).data('id');
		e.preventDefault();

		getData(id);
	});
	
	
	
	//$('.table-model').on('click', '#frm-btn-save', function(){
		//saveData();
		//$('#frm-alert').text($(".table-model").formToJSON());
	//});
	
	
	$('#frm-btn-cancel').on('click',function(){
		$('.table-model').clearForm();
		$("#frm-btn-delete").attr('disabled','disabled');
		var data = '';
		renderImage(data);	
	});
	



	/*
	$('.tb-data > tbody > tr').each(function(){ 
	    var id = $(this).data('id');
		
		$(this).click(function(){
			
			//$(this).addClass('row_selected');
			console.log(id);	
		});
	});
	*/
	
	
	


	
	

	
	
});



function initToolbar() {
	
	//$("a.toolbar-minibutton.new").hide();
	//$("a.print-preview").hide();
	

	/*
	$("a.toolbar-minibutton.new").on("click", function(){
		$(this).hide();
		$(".table-model").clearForm();
	});
	*/
	
	var id = $(".table-model #id").val();
	if(id == undefined || id == null || id == '') {
		//console.log("id is undefine");
		$("a.toolbar-minibutton.new").hide();
		$("a.print-preview").hide();
	} else {
		$("a.toolbar-minibutton.new").show();
		$("a.print-preview").show();
	}
	
	$(".table-model #id").bind('DOMSubtreeModified', function() {
   		//console.log("DOMSubtreeModified");
		var id = $(".table-model #id").val();
		if(id == undefined || id == null || id == '') {
			$("a.toolbar-minibutton.new").hide();
			$("a.print-preview").hide();
		} else {
			$("a.toolbar-minibutton.new").show();
			$("a.print-preview").show();
		}
 	});
}





function postingStatus(){
	//console.log($(".table-model #posted").val());
	
	if($(".table-model #posted").val()==='1' || $(".table-model #posted").val()===1){
		var vpostingStatus = 'true';
		//console.log(postingStatus);
		return true;
	} else {
		var vpostingStatus = 'false';
		//console.log(postingStatus);
		return false;	
	}
}






/*
* 	Common App Functions
*/

function deleteData(id,oTable) {
//function deleteData(btn) {
	//console.log('deleteGradebkhdr');
	var	table = isset(table) ? table : $(".table-model").data('table');
	
	if(confirm('Are you sure you want to delete this item? '+ id)) {
		// build modal
		$.blockUI({ message: '<h2>Deleting <img src="../images/ajax-loader.gif"  /> </h2>',css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#fff', 
            '-webkit-border-radius': '5px', 
            '-moz-border-radius': '5px',  
			'border-radius': '5px',           
            color: '#bbb' 
        }  }); 
		
		//ajax to confirm delete 
		$.ajax({
        type: 'DELETE',
        contentType: 'application/json',
        url: apiPath +'/'+ table +'/' + id,
        dataType: "json",
       // data: id,
        success: function(data, textStatus, jqXHR){
            //alert(textStatus + 'Gradebook header deleted successfully');
			//removeTableRow(data);
          	removeDataTableRow(data,oTable);
				
        	},
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on deleting '+ table +' data');
        	}
    	});
		
		$.unblockUI();
		
	} else {
		return false;
	} 			
}


function removeTableRow(data){
	$(".tb-data").find("#"+ data.id +"")
				.fadeOut("slow", function() {
					 $(this).remove(); 
					 //TableNo();
				});
}

function removeDataTableRow(data,oTable){
	oTable.fnDeleteRow( $('tr#'+ data.id) );
	$('.table-model').clearForm();
	$("#frm-btn-delete").attr('disabled','disabled');	
}

function TableNo(){
	$(".tb-data tbody tr td:first-of-type").each(function(){
		var no = $(this).parent().index() + 1;
		$(this).text(no);
	});
}



function addData(oTable) {
    //console.log('addGradebkhdr');
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = isset(table) ? table : form.data('table');

	console.log(formData);
	//console.log(table);
    $.ajax({
        type: 'POST',
        contentType: 'application/json',
		url: apiPath +'/'+ table ,
        dataType: "json",
        data: formData,
        success: function(data, textStatus, jqXHR){
            //alert(textStatus + 'Gradebook header created successfully');
			addTableData(data,oTable);
          	//addDataTableData(data,oTable);
			//console.log(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on creating '+ table +' data');
        }
    });
	
	//form.find('tr:first-of-type td input:first-of-type').css('border','red 1px solid');
	var first_input = form.find('tr:first-of-type td input:first-of-type'); 
	var v = first_input.val();
	form.clearForm();
	first_input.val(v)
	  		   .focus();
	
	
}


function addDataTableData(data,oTable) {
	
	var key, len = 0, arr = new Array();
	for(key in data) {
		if(len == 0) {
			
		} else {	
			arr[len-1] = data[key];
		}
	  //console.log(data[key]);
	  //console.log(key);
	  len++;
	}
	 
	console.log({ "mData": arr });
	//oTable.fnAddData({ "mData": [{"code":"hannah"},{"descriptor":"fdsfsafas"},{"type":"prodd"}]});
	//console.log(arr);
}


function addTableData(data,oTable) {
	
	//oTable.fnDraw();
	
    
	clear_alert();
	
	if(!data.error) {
		
		var fr = $(".tb-data tbody tr:first-of-type");
		var lr = $(".tb-data tbody tr:last-of-type");
	 	var no = parseInt($(".tb-data tbody tr:last-of-type td:first-of-type").text());
	    //var ctr = no + 1;
		var ctr = lr.index();
	  
	  	
	  
		var key, len = 0, row = '';
		for(key in data) {
			if(len == 0 ) {
				//row += '<tr id="'+ data[key] +'" data-id="'+ data[key] +'"><td>'+ ctr +'</td>';
				row += '<tr id="'+ data[key] +'" data-id="'+ data[key] +'">';
			} else if(key.substr(key.length - 2) === 'id' || key==='type'){
				var opt = $(".table-model").find('#'+key+' option[value='+data[key]+']').text();
				row += '<td>'+ opt +'</td>';
			} else {	
				row += '<td>'+ data[key] +'</td>';
			}
		  //console.log(data[key]);
		  //console.log(key);
		  len++;
		}
		
		
		//row += '<td>';
		//row += '<a class="edit" data-id="'+  data.id +'" data-table="category" href="#">Edit</a> ';
		//row += '<a class="delete" data-id="'+ data.id +'" data-table="category" href="#">Delete</a>';
		//row += '</td></tr>';
		row += '</tr>';
		//alert(len);
		
		/*    
		row += '<tr id="'+ data.id +'" ><td>'+ ctr +'</td>';
	    row += '<td>'+ data.code +'</td>';
	    row += '<td>'+ data.descriptor +'</td>';
		row += '<td>'+ data.type_name +'</td>';
	    row += '<td>';
		row += '<a class="edit" data-id="'+  data.id +'" data-table="category" href="#">Edit</a> ';
		row += '<a class="delete" data-id="'+ data.id +'" data-table="category" href="#">Delete</a>';
		row += '</td></tr>';			
		*/		
				
	   	fr.before(row);
		
		if(lr.index() == 10 || 
		   lr.index() == 25 ||
		   lr.index() == 50 ||
		   lr.index() == 100) {
		   lr.remove();
		}
		
		//lr.next().css('border','red 1px solid');
		fr.prev().find('td').each(function() {
			// color animation, optional. Do whatever you like here.
			//alert('fsafas');
			
			//fade 1
			/*
			$(this).animate({
			fontSize: "2em", backgroundColor: "#D5F9D5"
			}, 500 ).animate({
			fontSize: "1em"
			}, 500 );
			*/
			//fade 2
			$(this).effect("highlight", {}, 3000);
		
		});
		
		update_showing_entries();	   
	 	set_alert('success','Well done!', 'You successfully saved');
			
	} else {
		//console.log(data.error);	
		
		set_alert('error','Oh snap!',data.error);	
	}
	
	//TableNo();
		
}

function addTableData2(data) {
	   
	clear_alert();
	
	if(!data.error) {
		
		var fr = $(".tb-data tbody tr:first-of-type");
		var sr = $(".tb-data tbody tr:nth-of-type(2)");
		var lr = $(".tb-data tbody tr:last-of-type");
	 	var no = parseInt($(".tb-data tbody tr:last-of-type td:first-of-type").text());
	    //var ctr = no + 1;
		var ctr = lr.index();

		var a = '';
			a += '<div class="tb-data-action">';
			//a += '<a href="#" class="row-post">&nbsp;</a>';
			a += '<a href="#" class="row-delete" >&nbsp</a>';
			//a += '<a href="#" data-id="fasdfas" data-toggle="modal" data-target="#mdl-frm-detail">edit</a>'
			a += '<a href="#" class="row-edit" >&nbsp</a>';
			//a += '<a href="#" class="row-view" ></a>';
			//	a += '<a href="#" data-id="'+ detailid +'" class="row-view" >view</a>'
			a += '</div>';
	  
	  	
	  
		var key, len = 0, row = '';
		for(key in data) {
			if(len == 0 ) {
				//row += '<tr id="'+ data[key] +'" data-id="'+ data[key] +'"><td>'+ ctr +'</td>';
				row += '<tr class="even" id="'+ data[key] +'" data-id="'+ data[key] +'">';
			} else if(len == 1){
				var i = len - 1;
				var cls = $("td:eq("+ i +")" ,sr).attr('class');
				console.log(sr);
				row += '<td>'+ data[key] + a +'</td>';
			} else if(key.substr(key.length - 2) === 'id' ){
				//var opt = $(".table-model").find('#'+key+' option[value='+data[key]+']').text();
				//row += '<td>'+ opt +'</td>';
				len--;
			} else {
				var i = len - 1;	
				var cls = $("td:eq("+ i +")" ,sr).attr('class');
				var x;
				//console.log(cls);


				if(cls==='currency'){
					x = toCurrency(data[key]);
				} else if(cls==='number'){
					x = data[key];
				} else if(cls==='posted'){
					x = '✖';
				} else {
					x = data[key];
				}


				row += '<td class="'+ cls +'">'+ x +'</td>';
			}
		  //console.log(data[key]);
		  //console.log(key);
		  len++;
		}
		
		
		//row += '<td>';
		//row += '<a class="edit" data-id="'+  data.id +'" data-table="category" href="#">Edit</a> ';
		//row += '<a class="delete" data-id="'+ data.id +'" data-table="category" href="#">Delete</a>';
		//row += '</td></tr>';
		row += '</tr>';
		//alert(len);
		
		/*    
		row += '<tr id="'+ data.id +'" ><td>'+ ctr +'</td>';
	    row += '<td>'+ data.code +'</td>';
	    row += '<td>'+ data.descriptor +'</td>';
		row += '<td>'+ data.type_name +'</td>';
	    row += '<td>';
		row += '<a class="edit" data-id="'+  data.id +'" data-table="category" href="#">Edit</a> ';
		row += '<a class="delete" data-id="'+ data.id +'" data-table="category" href="#">Delete</a>';
		row += '</td></tr>';			
		*/		
				
	   	fr.before(row);
		
		if(lr.index() == 10 || 
		   lr.index() == 25 ||
		   lr.index() == 50 ||
		   lr.index() == 100) {
		   lr.remove();
		}
		
		//lr.next().css('border','red 1px solid');
		fr.prev().find('td').each(function() {
			// color animation, optional. Do whatever you like here.
			//alert('fsafas');
			
			//fade 1
			/*
			$(this).animate({
			fontSize: "2em", backgroundColor: "#D5F9D5"
			}, 500 ).animate({
			fontSize: "1em"
			}, 500 );
			*/
			//fade 2
			$(this).effect("highlight", {}, 3000);
		
		});
		
		update_showing_entries();	   
	 	set_alert('success','Well done!', 'You successfully saved');
			
	} else {
		//console.log(data.error);	
		
		set_alert('error','Oh snap!',data.error);	
	}
	
	


}

function addTableData3(data) {
	   
	clear_alert();
	
	if(!data.error) {
		
		var fr = $(".tb-data tbody tr:first-of-type");
		var sr = $(".tb-data tbody tr:nth-of-type(2)");
		var lr = $(".tb-data tbody tr:last-of-type");
	 	var no = parseInt($(".tb-data tbody tr:last-of-type td:first-of-type").text());
	    //var ctr = no + 1;
		var ctr = lr.index();

		var a = '';
			a += '<div class="tb-data-action">';
			a += '<a href="#" class="row-post">&nbsp;</a>';
			a += '<a href="#" class="row-delete" >&nbsp</a>';
			//a += '<a href="#" data-id="fasdfas" data-toggle="modal" data-target="#mdl-frm-detail">edit</a>'
			a += '<a href="#" class="row-edit" >&nbsp</a>';
			a += '<a href="#" class="row-view" ></a>';
			//	a += '<a href="#" data-id="'+ detailid +'" class="row-view" >view</a>'
			a += '</div>';
	  
	  	
	  
		var key, len = 0, row = '';
		for(key in data) {
			if(len == 0 ) {
				//row += '<tr id="'+ data[key] +'" data-id="'+ data[key] +'"><td>'+ ctr +'</td>';
				row += '<tr class="even" id="'+ data[key] +'" data-id="'+ data[key] +'">';
			} else if(len == 1){
				var i = len - 1;
				var cls = $("td:eq("+ i +")" ,sr).attr('class');
				//console.log(sr);
				row += '<td>'+ data[key] + a +'</td>';
			} else if(key.substr(key.length - 2) === 'id' ){
				//var opt = $(".table-model").find('#'+key+' option[value='+data[key]+']').text();
				//row += '<td>'+ opt +'</td>';
				len--;
			} else {
				var i = len - 1;	
				var cls = $("td:eq("+ i +")" ,sr).attr('class');
				var x;
				//console.log(cls);


				if(cls==='currency'){
					x = toCurrency(data[key]);
				} else if(cls==='number'){
					x = data[key];
				} else if(cls==='posted'){
					x = '✖';
				} else {
					x = data[key];
				}


				row += '<td class="'+ cls +'">'+ x +'</td>';
			}
		  //console.log(data[key]);
		  //console.log(key);
		  len++;
		}
		
		
		//row += '<td>';
		//row += '<a class="edit" data-id="'+  data.id +'" data-table="category" href="#">Edit</a> ';
		//row += '<a class="delete" data-id="'+ data.id +'" data-table="category" href="#">Delete</a>';
		//row += '</td></tr>';
		row += '</tr>';
		//alert(len);
		
		/*    
		row += '<tr id="'+ data.id +'" ><td>'+ ctr +'</td>';
	    row += '<td>'+ data.code +'</td>';
	    row += '<td>'+ data.descriptor +'</td>';
		row += '<td>'+ data.type_name +'</td>';
	    row += '<td>';
		row += '<a class="edit" data-id="'+  data.id +'" data-table="category" href="#">Edit</a> ';
		row += '<a class="delete" data-id="'+ data.id +'" data-table="category" href="#">Delete</a>';
		row += '</td></tr>';			
		*/		
				
	   	fr.before(row);
		
		if(lr.index() == 10 || 
		   lr.index() == 25 ||
		   lr.index() == 50 ||
		   lr.index() == 100) {
		   lr.remove();
		}
		
		//lr.next().css('border','red 1px solid');
		fr.prev().find('td').each(function() {
			// color animation, optional. Do whatever you like here.
			//alert('fsafas');
			
			//fade 1
			/*
			$(this).animate({
			fontSize: "2em", backgroundColor: "#D5F9D5"
			}, 500 ).animate({
			fontSize: "1em"
			}, 500 );
			*/
			//fade 2
			$(this).effect("highlight", {}, 3000);
		
		});
		
		update_showing_entries();	   
	 	set_alert('success','Well done!', 'You successfully saved');
			
	} else {
		//console.log(data.error);	
		
		set_alert('error','Oh snap!',data.error);	
	}
	
	


}




	



function getData(id) {
	
	var form = $(".table-model");	
    var	table = form.data('table');
	
    $.ajax({
        type: 'GET',
        contentType: 'application/json',
        url: apiPath +'/'+ table +'/'+ id,
        dataType: "json",
        //data: id,
        success: function(data, textStatus, jqXHR){
            //alert(textStatus + 'Gradebook header fech successfully');  
			renderDetails(data)		
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on getting '+ table +' header');
        }
    });
	
	
}

function renderDetails(data) {
	//console.log(data);
	var key;
	for(key in data) {
		//console.log(data[key]);
		//console.log(key);

		if(data[key]==null || data[key]==undefined) {

		} else {

			var elementType = $('#'+key).prop('tagName');
			//console.log(elementType);	
			if(elementType == 'SELECT') {
				
				$('#'+ key +' > option').each(function() {
					//console.log($(this).val());
					if($(this).val() == data[key]){
						//console.log(data[key]);
						$(this).attr('selected','selected');
					}
				});
			
			} else if($('#'+key).hasClass('currency')) {
				//console.log(data[key]);
				
				$('#'+key).val(data[key]);
				$('#'+key).toNumberFormat();

			} else {
				
				//$('#'+key).val(stripData);
				$('#'+key).val(data[key]);
				
				if(key==='picfile') {
					renderImage(data[key]);	
				}
			}	
		}
	}
	/* 
	*  function for editing the form
	*  render the fetched data to the fileds on the form 
	*/
}

function renderImage(file){
	
	var dropbox = $('#dropbox'),
		message = $('.message', dropbox);
	
		message.remove();	
	
	
	if(file == undefined || file == null || file == ''){
		
		var template = '<span class="message">'+
						'Drop images here to upload.<br>'+
						'<i>(they will only be visible to you)</i>'+
						'</span>';
		
	} else {
	
		var template = '<div class="preview">'+
							'<span class="imageHolder">'+
								'<img src="../images/products/'+ file +'" />'+
								'<span class="uploaded"></span>'+
							'</span>'+
							
						'</div>';
	}
					
	//console.log(template);
					
	var preview = $(template);
		
		
	preview.appendTo(dropbox).prev(preview).remove();	
}


function updateData(id) {
  	
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = form.data('table');
	
    $.ajax({
        type: 'PUT',
        contentType: 'application/json',
        url: apiPath +'/s/'+ table +'/' + id,
        dataType: "json",
        data: formData,
        success: function(data, textStatus, jqXHR){
          //alert(textStatus + 'Gradebook header successfully edited');
          updateTableData(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on creating Gradebook header');
        }
    });
}

function updateTableData(data) {
	var existing_tr =  $(".tb-data tbody tr#"+ data.id);
		//existing_tr.css('border','red 1px solid');
		
		var key, len = 0;
		for(key in data) {
			if(len == 0) {

			} else if(key === 'type'){

					var opt = $(".table-model").find('#'+key+' option[value='+data[key]+']').text();
					var i = len - 1;
				$('td:eq('+ i +')', existing_tr).text(opt);
			} else {	
				//console.log($(existing_tr +' td').index());
				//console.log(data[key]+' '+len);
				var i = len - 1;
				$('td:eq('+ i +')', existing_tr).text(data[key]);
				/*
				existing_tr.find('td')
                    .filter(function(index){
						console.log(len);
						return index == len
				    })
					.text(data[key]);
				*/			
			}
			len++;		
		}

		existing_tr.find('td')
				   .effect("highlight", {}, 3000);
		
		/*
		existing_tr.find('td:not(:last-of-type)').each(function(){
						var key, len = 0;
						for(key in data) {
							if(len == 0) {
								
							} else {	
								$(this).text(data[key]);
							}
							 len++;
						}	
					})				  
				   .parent()
				   .find('td')
				   .effect("highlight", {}, 3000);
				   */
}









/* 
*	Alerts!
*	type = success, error or info
*	header = header for the alert
* 	text = the content
*/
function set_alert(type, header, text) {
	
	type = isset(type) ? type : 'info';
	header = isset(header) ? header : 'Heads up!';
	text = isset(text) ? text : 'This alert needs your attention, but it\'s not super important.';
	
	var alert_row = '<div class="alert alert-'+ type +'">';
		alert_row += '<button class="close" data-dismiss="alert" type="button">×</button>';
		alert_row += '<strong>'+ header +' </strong>' + text;
		alert_row += '</div>';
	

	if($('#frm-alert').length == '0'){
		$('.form-alert').html(alert_row);
	} else {
		$('#frm-alert').html(alert_row);
	}
}

function clear_alert() {
	$('#frm-alert').html('');
	$('.form-alert').html('');
}

function update_showing_entries() {
	var info = $('.dataTables_info').text();
	var x = explode(' ', info);
	var new_x = parseInt(x[5]) + 1;
	var new_text = str_replace(x[5], new_x, info);
	$('.dataTables_info').text(new_text);	
}








/**********************************************************************************************************************/
/********************* jQuery Helper Functions ********************************************************************/
/**********************************************************************************************************************/



$.fn.serializeObject = function()
{
    var o = {};

    //$('input[type=number]', this).each(function(){ // select all the element in form
    $('input.currency', this).each(function(){ // select all the element in form
    	$(this).toDecimal(); // convert the value to e.g.  10,012.05 to 10012.05
    });                      // before sending to server


    var a = this.serializeArray();


    $.each(a, function() {
    	
    	//console.log(this.value);
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');

        } else {
            o[this.name] = this.value || '';
      
        }
    });
    return o;
};

// if value is blank, it does not include
$.fn.serializeObject2 = function() {
    var o = {};

    //$('input[type=number]', this).each(function(){ // select all the element in form
    $('input.currency', this).each(function(){ // select all the element in form
    	$(this).toDecimal(); // convert the value to e.g.  10,012.05 to 10012.05
    });                      // before sending to server


    var a = this.serializeArray();


    $.each(a, function() {

        if (o[this.name] !== undefined) {
        	console.log('pasok sa una');
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else if(this.value==""){
        	// of no value
        	// do nothing
       // } else if(this.name === 'date'){
       // 	console.log('may date');
        	
        } else {

        	if(this.name == "qty"){

        		o[this.name] = parseInt(this.value) || 0;
        	} else if(
        		this.name == "unitcost" || 
        		this.name == "amount" ||
        		this.name == "totamount"
        		){
        		
        		o[this.name] = accounting.toFixed(this.value,2) || 0;
        		
        	} else {
        		o[this.name] = stripHTML(this.value) || '';	
        	}        
        }
    });
    //console.log(o);
    return o;
};


$.fn.formToJSON = function(){
	return JSON.stringify(this.serializeObject());
}


$.fn.clearForm = function() {
  // iterate each matching form
  return this.each(function() {
    // iterate the elements within the form
    $(':input', this).each(function() {
      var type = this.type, tag = this.tagName.toLowerCase();
      if (type == 'text' || type == 'password' || tag == 'textarea')
        this.value = '';
      else if (type == 'checkbox' || type == 'radio')
        this.checked = false;
      else if (tag == 'select')
        this.selectedIndex = 0;
	  else
       this.value = "";
    });
  });
};


$.fn.serializeHTMLTable = function(){
	
	var arr = $('.tb-detail tbody tr').map(function () {
		
		var table = $(this).parent().parent();
		var o = {};	
		
		// iterate on <table> and find all the data
		for(key in table.data()){
			o[key] =  table.data(key);
		};
		
		// iterate on <tr> and find all the data
		for(key in $(this).data()){
			o[key] =  $(this).data(key);
		};
		
		// iterate on <tr> and find all the data
		$('td', this).each(function () {
	        for (key in this.dataset) {
	            o[key] = this.dataset[key];
	        }
	    })
	    
		return o
		
	}).get();
	
	return arr;
}



$.fn.HTMLTableToJSON = function(){
	
	return JSON.stringify(this.serializeHTMLTable());

}


/**********************************************************************************************************************/
/********************* JavaScript Helper Functions ********************************************************************/
/**********************************************************************************************************************/

function isset() {

  var a = arguments,
    l = a.length,
    i = 0,
    undef;

  if (l === 0) {
    throw new Error('Empty isset');
  }

  while (i !== l) {
    if (a[i] === undef || a[i] === null) {
      return false;
    }
    i++;
  }
  return true;
}


function explode (delimiter, string, limit) {

  if ( arguments.length < 2 || typeof delimiter == 'undefined' || typeof string == 'undefined' ) return null;
  if ( delimiter === '' || delimiter === false || delimiter === null) return false;
  if ( typeof delimiter == 'function' || typeof delimiter == 'object' || typeof string == 'function' || typeof string == 'object'){
    return { 0: '' };
  }
  if ( delimiter === true ) delimiter = '1';
  
  // Here we go...
  delimiter += '';
  string += '';
  
  var s = string.split( delimiter );
  

  if ( typeof limit === 'undefined' ) return s;
  
  // Support for limit
  if ( limit === 0 ) limit = 1;
  
  // Positive limit
  if ( limit > 0 ){
    if ( limit >= s.length ) return s;
    return s.slice( 0, limit - 1 ).concat( [ s.slice( limit - 1 ).join( delimiter ) ] );
  }

  // Negative limit
  if ( -limit >= s.length ) return [];
  
  s.splice( s.length + limit );
  return s;
}




function str_replace (search, replace, subject, count) {
 
  var i = 0,
    j = 0,
    temp = '',
    repl = '',
    sl = 0,
    fl = 0,
    f = [].concat(search),
    r = [].concat(replace),
    s = subject,
    ra = Object.prototype.toString.call(r) === '[object Array]',
    sa = Object.prototype.toString.call(s) === '[object Array]';
  s = [].concat(s);
  if (count) {
    this.window[count] = 0;
  }

  for (i = 0, sl = s.length; i < sl; i++) {
    if (s[i] === '') {
      continue;
    }
    for (j = 0, fl = f.length; j < fl; j++) {
      temp = s[i] + '';
      repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
      s[i] = (temp).split(f[j]).join(repl);
      if (count && s[i] !== temp) {
        this.window[count] += (temp.length - s[i].length) / f[j].length;
      }
    }
  }
  return sa ? s : s[0];
}

function is_int(mixed_var){
	return mixed_var === +mixed_var && isFinite(mixed_var) && !(mixed_var % 1);
}



function S4() {
   return (((1+Math.random())*0x10000)|0).toString(16).substring(1);
}

// Generate a pseudo-GUID by concatenating random hexadecimal.
function guid() {
   return (S4()+S4()+S4()+S4()+S4()+S4()+S4()+S4());
}

/*

function guid(){
    var S4 = function ()
    {
        return Math.floor(
                Math.random() * 0x10000 
            ).toString(16);
    };

    return (
            S4() + S4() +
            S4() +
            S4() +
            S4() +
            S4() + S4() + S4()
        );
		
	return (
            S4() + S4() + "-" +
            S4() + "-" +
            S4() + "-" +
            S4() + "-" +
            S4() + S4() + S4()
        );	
		
}

*/


function toCurrency(data) {
	var c = data;
	var newValue = parseFloat(c);  // convert the value to integer
	var converted = new NumberFormat(newValue).toFormatted();

	return converted;
}



Date.prototype.currentIsoDate = function() {
      var currentDate = new Date()
      var day = currentDate.getDate()
      var month = currentDate.getMonth() + 1
      var year = currentDate.getFullYear()
	  return year+'-'+month+'-'+day;
}



/**********************************************************************************************************************/
/************************* flat table v2 stuff ****************************************************************************/
/**********************************************************************************************************************/




function addData2(oTable) {
    console.log('addGradebkhdr');
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = isset(table) ? table : form.data('table');
	
	//console.log(table);
	console.log(formData);


	
    $.ajax({
        type: 'POST',
        contentType: 'application/json',
		url: apiPath +'/t/'+ table ,
        dataType: "json",
        data: formData,
        success: function(data, textStatus, jqXHR){
            //alert(textStatus + ' '+ table +' created successfully');
			addTableData(data,oTable);
          	//addDataTableData(data,oTable);
			//console.log(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on creating '+ table +' data');
        }
    });
	
	
	/*
	//form.find('tr:first-of-type td input:first-of-type').css('border','red 1px solid');
	var first_input = form.find('tr:first-of-type td input:first-of-type'); 
	var v = first_input.val();
	form.clearForm();
	first_input.val(v)
	  		   .focus();*/
	
	
}


function getData2(id) {
	
	var form = $(".table-model");	
    var	table = form.data('table');
	
    $.ajax({
        type: 'GET',
        contentType: 'application/json',
        url: apiPath +'/t/'+ table +'/'+ id,
        dataType: "json",
        //data: id,
        success: function(data, textStatus, jqXHR){ 
			renderDetails(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + ' Failed on getting '+ table +' header');
        }
    });
}



function updateData2(id) {
  	
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = form.data('table');
	
    $.ajax({
        type: 'PUT',
        contentType: 'application/json',
        url: apiPath +'/t/'+ table +'/' + id,
        dataType: "json",
        data: formData,
        success: function(data, textStatus, jqXHR){
          //alert(textStatus + 'Gradebook header successfully edited');
          updateTableData2(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on updating');
        }
    });
}

function updateTableData2(data) {
	var existing_tr =  $(".tb-data tbody tr#"+ data.id);
		//existing_tr.css('border','red 1px solid');
		
		var key, len = 0;
		for(key in data) {
			if(len == 0) {
				
			} else {	
				var i = len - 1;

				$('td:eq('+ i +')', existing_tr).text(data[key]);
				if($('td:eq('+ i +')', existing_tr).hasClass('currency')){
					$('td:eq('+ i +')', existing_tr).toCurrency();
				}	
			}
			len++;		
		}

		existing_tr.find('td')
				   .effect("highlight", {}, 3000);
}

function updateTableData3(data) {
	var existing_tr =  $(".tb-data tbody tr#"+ data.id);
		//existing_tr.css('border','red 1px solid');
		
		var key, len = 0;
		var a = '';
			a += '<div class="tb-data-action">';
			//a += '<a href="#" class="row-post">&nbsp;</a>';
			a += '<a href="#" class="row-delete" >&nbsp;</a>';
			//a += '<a href="#" data-id="fasdfas" data-toggle="modal" data-target="#mdl-frm-detail">edit</a>'
			a += '<a href="#" class="row-edit" >&nbsp;</a>';
			//a += '<a href="#" class="row-view" ></a>';
			//	a += '<a href="#" data-id="'+ detailid +'" class="row-view" >view</a>'
			a += '</div>';

		for(key in data) {
			//console.log(key +'-'+ data[key]);
			if(len == 0) {
				
			} else if(len == 1) {
				//console.log(len +' - '+ data[key]);
				//console.log($('td:eq(1)', existing_tr));
				$('td:first-of-type', existing_tr).html(data[key] + a);
			} else if(key.substr(key.length - 2) === 'id' ){
				len--;
			} else {	
				var i = len - 1;
				
				$('td:eq('+ i +')', existing_tr).text(data[key]);
				if($('td:eq('+ i +')', existing_tr).hasClass('currency')){
					//console.log($('td:eq('+ len +')', existing_tr));
					$('td:eq('+ i +')', existing_tr).toCurrency();
				} 


			}
			len++;		
		}

		existing_tr.find('td')
				   .effect("highlight", {}, 3000);
}

function updateTableData4(data) {
	var existing_tr =  $(".tb-data tbody tr#"+ data.id);
		//existing_tr.css('border','red 1px solid');
		
		var key, len = 0;
		var a = '';
			a += '<div class="tb-data-action">';
			a += '<a href="#" class="row-post">&nbsp;</a>';
			a += '<a href="#" class="row-delete" >&nbsp;</a>';
			//a += '<a href="#" data-id="fasdfas" data-toggle="modal" data-target="#mdl-frm-detail">edit</a>'
			a += '<a href="#" class="row-edit" >&nbsp;</a>';
			a += '<a href="#" class="row-view" ></a>';
			//	a += '<a href="#" data-id="'+ detailid +'" class="row-view" >view</a>'
			a += '</div>';

		for(key in data) {
			//console.log(key +'-'+ data[key]);
			if(len == 0) {
				
			} else if(len == 1) {
				//console.log(len +' - '+ data[key]);
				//console.log($('td:eq(1)', existing_tr));
				$('td:first-of-type', existing_tr).html(data[key] + a);
			} else if(key.substr(key.length - 2) === 'id' ){
				len--;
			} else {	
				var i = len - 1;
				
				$('td:eq('+ i +')', existing_tr).text(data[key]);
				if($('td:eq('+ i +')', existing_tr).hasClass('currency')){
					//console.log($('td:eq('+ len +')', existing_tr));
					$('td:eq('+ i +')', existing_tr).toCurrency();
				} else if($('td:eq('+ i +')', existing_tr).hasClass('posted')){
					$('td:eq('+ i +')', existing_tr).text('✖');
				}


			}
			len++;		
		}

		existing_tr.find('td')
				   .effect("highlight", {}, 3000);
}



function deleteData2(id,oTable) {
//function deleteData(btn) {
	//console.log('deleteGradebkhdr');
	var	table = isset(table) ? table : $(".table-model").data('table');
	
	if(confirm('Are you sure you want to delete this item? '+ id)) {
		// build modal
		$.blockUI({ message: '<h2>Deleting <img src="../images/ajax-loader.gif"  /> </h2>',css: { 
            border: 'none', 
            padding: '15px', 
            backgroundColor: '#fff', 
            '-webkit-border-radius': '5px', 
            '-moz-border-radius': '5px',  
			'border-radius': '5px',           
            color: '#bbb' 
        }  }); 
		
		//ajax to confirm delete 
		$.ajax({
        type: 'DELETE',
        contentType: 'application/json',
        url: apiPath +'/t/'+ table +'/' + id,
        dataType: "json",
       // data: id,
        success: function(data, textStatus, jqXHR){
            //alert(textStatus + 'Gradebook header deleted successfully');
			//removeTableRow(data);
          	removeDataTableRow(data,oTable);
				
        	},
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on deleting '+ table +' data');
        	}
    	});
		
		$.unblockUI();
		
	} else {
		return false;
	} 			
}




/**********************************************************************************************************************/
/********************* table details stuff ****************************************************************************/
/**********************************************************************************************************************/



function addToTableDetail(){
	
	var form = $(".table-detail");
	var formData = form.formToJSON();
	var table = form.data("table");

	console.log(formData);

	
	$.ajax({
        type: 'POST',
        contentType: 'application/json',
		url: apiPath +'/detail/'+ table ,
        dataType: "json",
        data: formData,
        success: function(data, textStatus, jqXHR) {
			//console.log(data);
			addDetailData(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + ': No Item found!');
            
        }
    });
}



function addDetailData(data) {
	   
	//clear_alert();
	var detailid = null;

	//console.log(data);
	
	if(!data.error) {
		
		var tb = $(".tb-detail tbody");
		var row = "";
	  
		var key, len = 0, row = '';
		for(key in data) {
			if(len == 0) {
				//console.log('len 0');
				//row += '<tr data-id="'+ data[key] +'">';
				var uuid = guid();
				
				row += '<tr id="'+ uuid +'" data-id="'+ uuid +'">';
			} else if(len == 1) {
				//console.log('len 1');
				row += '<td data-'+ key +'="'+ data[key] +'" >';
				detailid = data[key];
			} else if(len == 2) {
				//console.log('len 2');
				row +=  data[key]  +'</td>';
			} else {	
				row += '<td data-'+ key +'="'+ data[key] +'" class="currency" >'+ data[key] +'</td>';
			}
		  //console.log(data[key]);
		  //console.log(key);
		  len++;
		}
		
		row += '</tr>';	
				
	   	//lastRow.after(row);
	   	//console.log(row);
		$(row).appendTo(tb).find('td').each(function() {
			if($(this).index()===0){ // find the first <td>
				//console.log('first td');
				var a = '';
					a += '<div class="tb-data-action">';
					a += '<a href="#" data-id="'+ uuid +'" class="row-delete" >delete</a>'
					//a += '<a href="#" data-id="fasdfas" data-toggle="modal" data-target="#mdl-frm-detail">edit</a>'
					a += '<a href="#" data-id="'+ uuid +'" class="row-edit" >edit</a>'
				//	a += '<a href="#" data-id="'+ detailid +'" class="row-view" >view</a>'
					a += '</div>';
				$(this).append(a);
			}
			$(this).effect("highlight", {}, 3000);



		});
		
		
			
			
			/*
			var lastInsertAmount = $(".tb-detail tbody tr:last-of-type td:last-of-type");
			console.log(lastInsertAmount);
            var value = lastInsertAmount.text();
			var newValue = parseFloat(value);
			
			var amount = new NumberFormat(newValue).toFormatted();
			//console.log(a);
			lastInsertAmount.text(amount);
			*/

			$(".tb-detail tbody tr:last-of-type td:last-of-type").toCurrency();
			$(".tb-detail tbody tr:last-of-type td:nth-of-type(3)").toCurrency();
			
			//$(".tb-detail tbody tr:last-of-type td:last-of-type").toCurrency().addClass('float');
			//$(".tb-detail tbody tr:last-of-type td:nth-of-type(3)").toCurrency().addClass('float');

			$(".tb-detail tbody tr:last-of-type td:last-of-type").addClass('float');
			//$(".tb-detail tbody tr:last-of-type td:nth-of-type(3)").addClass('float');

		
			//$("tr td[data-qty]", tb).css('border','1px solid red');

			checkTotal();
			   
	 	//set_alert('success','Well done!', 'You successfully saved');
			
	} else {
		//set_alert('error','Oh snap!',data.error);	
	}





}


function checkTotalLine() {
	var totline = $('.tb-detail tbody tr').length;

	$('.table-model #totline').val(totline);
}






function checkTotal() {
	
	checkTotalLine();

	/*
	* note: to auto compute amount make sure to change the id of span.total 
	*/
	$('span.total').each(function() {

		var tb = $(".tb-detail tbody");
    	var field = explode("-", $(this).attr('id'));
    	var x = 0, sum = 0;
    	//console.log(field[2]);

    	//$("tr td[data-"+field[2]+"]", tb).css('border','1px solid red');

       			$("tr td[data-"+field[2]+"]", tb).each(function(){
       				
       				//$(this).css('border','1px solid red');

       				//console.log($(this));

       				if($(this).hasClass('float')) {
       				//if($(this).attr('class','float')) {
						//x = parseFloat($(this).text());
						//x = parseFloat($(this).data(field[2]));
						x = parseFloat($(this).attr('data-'+field[2]));
						//console.log($(this).data(field[2]));
						x = isNaN(x) ? 0:x;
						//console.log(field[2]+ " float: "+ x);
					} else {
						//x = parseInt($(this).text());
						//x = parseInt($(this).data(field[2]));
						x = parseInt($(this).attr('data-'+field[2]));
						//console.log($(this).data(field[2]));	
						//x = isNaN(x) ? 0:x;
						//console.log(field[2] +" int: "+ x);
					}
					
					sum = sum + x;
					//console.log("sum: "+ is_int(sum));
       			});

       			
				if($(this).hasClass('float')) {
					var converted = new NumberFormat(sum).toFormatted();	// format the value to .00 from the class
					//$("#tot"+field[2]).val(sum.toFixed(2));
					$("#tot"+field[2]).val(converted);
					//$("#"+field[2]).val(converted);
					$(this).text(converted);
					
				} else {
					$("#tot"+field[2]).val(sum);
					//$("#"+field[2]).val(sum);
					$(this).text(sum);

				}

				
       			
				$(this).effect("highlight", {}, 3000);
    		});
}



/*
* covert the element text to floatto
*/
$.fn.toCurrency = function(){
	
	var type = this.type, val, newValue, converted;
	if(type===undefined) { // check the type of element if type==input
		val = this.text();   // if it is not input, get the text value
		newValue = parseFloat(val);  // convert the value to integer
		var converted = new NumberFormat(newValue).toFormatted();	// format the value to .00 from the class	
		return this.text(converted); // return the formatted value
	} else {
		val = this.val();   // if it is not input, get the text value
		newValue = parseFloat(val);  // convert the value to integer
		var converted = new NumberFormat(newValue).toFormatted();	// format the value to .00 from the class	
		return this.val(converted); // return the formatted value
	}
	
	//console.log("to currecny log "+ converted);	
}



$.fn.toDecimal = function(){

		val = this.val();   
		var num = new NumberFormat();
		num.setInputDecimal('.');
		num.setNumber(val); // obj.value is '454,555.015'
		num.setPlaces('2', false);
		num.setCurrencyValue('');
		num.setCurrency(true);
		num.setCurrencyPosition(num.LEFT_OUTSIDE);
		num.setNegativeFormat(num.LEFT_DASH);
		num.setNegativeRed(false);
		num.setSeparators(false, ',', ',');
		val = num.toFormatted();
		return this.val(val); // return the formatted value
}

$.fn.toNumberFormat = function(){

		var val = this.val();   
		var num = new NumberFormat();
		num.setInputDecimal('.');
		num.setNumber(val); // obj.value is '454,555.015'
		num.setPlaces('2', false);
		num.setCurrencyValue('');
		num.setCurrency(true);
		num.setCurrencyPosition(num.LEFT_OUTSIDE);
		num.setNegativeFormat(num.LEFT_DASH);
		num.setNegativeRed(false);
		num.setSeparators(true, ',', ',');
		val = num.toFormatted();


		
		return this.val(val); // return the formatted value
		//return this.css('border','1px solid red');
}


$.fn.getTableDetail = function(){

	var form = $(".table-detail");
	form.clearForm();
	var id = $("a.row-edit", this).data('id'); // get the apvdtlid or row id

	console.log(id);
			
}

$.fn.serializeHTMLTableRow = function(){

		var o = {};	

		for(key in $(this).data()){
			o[key] =  $(this).data(key);
		};

		$('td', this).each(function () {
	        for (key in this.dataset) {
	            o[key] = this.dataset[key];
	        }
	    })
	    
		return o;
		
	
}


$.fn.HTMLTableRowToJSON = function(){
	
	return JSON.stringify(this.serializeHTMLTableRow());

}


/*
* function to update the selected row
*/
$.fn.renderDetailsToForm = function(data){

	var key;
	for(key in data) {
		$('#'+key, this).val(data[key]);	
	}
}


function stripHTML(x) {

	if(x!=null) {
		var regex = /(<([^>]+)>)/ig; 
		return newVal = x.replace(regex, "");
	} else {
		return false;
	}
	
}





/*
* function to update the selected row detail 
*/
function updateToTableDetail(){

	var form = $(".table-detail");
	var data = form.serializeObject();
	var existing_tr =  $(".tb-detail tbody tr#"+ data.id);
	var key, len = 0;

		for(key in data) {

			var stripData = stripHTML(data[key]);

			if(len == 0) {

			} else if(len == 1) {
				
			} else {		
				var i = len - 1;


				$('td:eq('+ i +')', existing_tr).text(stripData);
				$('td:eq('+ i +')', existing_tr).attr('data-'+key, stripData);
			}
			len++;		
		}

	existing_tr.find('td')
			   .effect("highlight", {}, 3000);

	checkTotal();

	$("td:last-of-type", existing_tr).toCurrency();
	$("td:nth-of-type(3)", existing_tr).toCurrency();
}











//*********************  ****************************/

function ComputeItemAmount() {
	
	
	var qty = $(".form-detail #qty");
	var unitcost = $(".form-detail #unitcost");
	var damount = $(".form-detail #damount");
	var amount = $(".form-detail #amount");
	
	unitcost.on("blur", function(e){
		var total = parseInt(qty.val()) * parseFloat(unitcost.val());
		
		total = isNaN(total) ? 0:total;
		
		var a = new NumberFormat(total).toFormatted();
			
			damount.val(a);
			amount.val(total.toFixed(2));
	});
	
	qty.on("blur",  function(e){
		var total = parseInt(qty.val()) * parseFloat(unitcost.val());
			total = isNaN(total) ? 0:total;
			
		
		var a = new NumberFormat(total).toFormatted();
			
			damount.val(a);
			amount.val(total.toFixed(2));
	});
	

	
}



function computeAmount() {
	var x1, x2, y;

	$(".form-detail").find(".variable").each(function(){
		//console.log($(this).attr("class"));
		//var sum += Number();
		

		if($(this).hasClass('float')) {
			x1 = parseFloat($(this).val());
			x1 = isNaN(x1) ? 0:x1;
		} else {
			x2 = parseInt($(this).val());
			x2 = isNaN(x2) ? 0:x2;
		}
	});

	y = x1 * x2;

	var a = new NumberFormat(y).toFormatted();
	
	$(".variable-result").val(y.toFixed(2));
	$(".variable-result-view").val(a); // .toFixed eg: 1.0499999 = 1.05
}


$.fn.getTotal = function() {

	var field = explode("-", $(this).attr('id'));
	console.log(field[2]);

	var tr = $(".tb-detail tbody tr");
	var x, sum;


	//console.log($("td", tr));
	tr.find("td").each(function(){

		$("td", tr).css('border','1px solid red');
//
	//	console.log("selected: "+ $(this));

	//$("tr", tb).each(function(){

		//$("")

	//}):

		//console.log($(this).text());
		/*
		if($(this).hasClass('float')) {
			x = parseFloat($(this).text());
			x = isNaN(x) ? 0:x;
		} else {
			x = parseInt($(this).text());
			x = isNaN(x) ? 0:x;
		}

		sum =+ x;
		*/
	});


	//$(this).text(sum);
}







// function addChild
function postDetail(){
	
	var form = $(".table-detail");
	var formData = form.HTMLTableToJSON();
	var table = form.data("table");
	var aData;

	
	$.ajax({
        type: 'POST',
        contentType: 'application/json',
		url: apiPath +'/post/detail/'+ table ,
        dataType: "json",
        async: false,
        data: formData,
        success: function(data, textStatus, jqXHR) {
			aData = data;
        },
        error: function(jqXHR, textStatus, errorThrown){
        	// comment out not so show when there is no child/detail but header created
            //alert(textStatus + ' on postDetail(): Failed on creating '+ table +' data');
            aData = {};
        }
    });

    return aData;
	
}


function addParent() {
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = form.data('table');	
	var aData; 

	$.ajax({
        type: 'POST',
        contentType: 'application/json',
		url: apiPath +'/t/'+ table ,
        dataType: "json",
        async: false,
        data: formData,
        success: function(data, textStatus, jqXHR){
            aData = data;
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + ' on addParent(): Failed on creating '+ tableParent +' data');
            aData = {};
        }
    });	

	return aData;

}



function addParentChild() {
    console.log('addParentChild');
    clear_alert();

    var form = $(".table-model");	
    var	table = form.data('table');	
    var parent_respone = addParent();

    if(!parent_respone.error) {

    	renderDetails(parent_respone);

    	$("#frm-btn-delete").removeAttr('disabled');
    	$("#frm-btn-post").removeAttr('disabled');
    	
    	var link = $('.print-preview').attr('href')
    	$('.print-preview').attr('href', link + parent_respone.id);

    	$(".tb-detail").attr('data-'+table+'id',parent_respone.id);

    	var childRespone = postDetail();

    	
    	if(!childRespone.error) {
    		set_alert('success','Well done!', 'You successfully saved');
    	} else {
    		set_alert('error','Oh snap!',childRespone.error);
    	}
    	
    } else {

    	set_alert('error','Oh snap!',parent_respone.error);
    }
}



/*  
*   
*/
//function update parent
function putDetail(){
	
	var form = $(".table-detail");
	var formModel = $(".table-model");
	var formData = form.HTMLTableToJSON();
	var table = form.data("table");
	var table2 = $(".table-model").data("table");
	var id = $(".tb-detail").data(table2+'id');
	
	var aData;

	
	$.ajax({
        type: 'PUT',
        contentType: 'application/json',
		url: apiPath +'/post/detail/'+ table +'/'+ table2 +'/' + id ,
        dataType: "json",
        async: false,
        data: formData,
        success: function(data, textStatus, jqXHR) {
			aData = data;
			//console.log(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on creating '+ table +' data');
        }
    });

    return aData;
	
}




function updateParent(id) {
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = form.data('table');	
	var aData; 

	$.ajax({
        type: 'PUT',
        contentType: 'application/json',
		url: apiPath +'/t/'+ table +'/'+ id ,
        dataType: "json",
        async: false,
        data: formData,
        success: function(data, textStatus, jqXHR){
            aData = data;
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on creating '+ tableParent +' data');
        }
    });	

	return aData;

}


function updateParentChild(id) {
    console.log('updateParentChild');
    clear_alert();

    var form = $(".table-model");	
    var	table = form.data('table');	
    var parent_respone = updateParent(id);

    console.log(parent_respone);

    if(!parent_respone.error) {

    	renderDetails(parent_respone);

    	set_alert('success','Well done!', 'You successfully saved header');

    	$(".tb-detail").attr('data-'+table+'id',parent_respone.id);

    	var childRespone = putDetail();



    	if(!childRespone.error) {
    		set_alert('success','Well done!', 'You successfully saved');
    	} else {
    		set_alert('error','Oh snap!',childRespone.error);
    	}	
    } else {
    	console.log(parent_respone);
    	set_alert('error','Oh snap!',parent_respone.error);
    }


  	$(".table-model .currency").each(function(){
		$(this).toNumberFormat();	
	});



}


function deleteParent(id) {
	var form = $(".table-model");
    var	table = form.data('table');	
	var aData; 

	console.log('delete '+ table);

	
	$.ajax({
        type: 'DELETE',
        contentType: 'application/json',
		url: apiPath +'/t/'+ table +'/'+ id ,
        dataType: "json",
        async: false,
        //data: formData,
        success: function(data, textStatus, jqXHR){
            aData = data;
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on creating '+ tableParent +' data');
        }
    });	


	return aData;

}


function deleteParentChild(id) {
	console.log('deleteParentChild');
    clear_alert();

    var form = $(".table-model");	
    var	table = form.data('table');	

    $.blockUI({ 
    	message: ' <h2>Deleting <img src="../../../images/ajax-loader.gif"  /> </h2>',
    	css: {
		    border: 'none', 
		    padding: '15px', 
		    backgroundColor: '#fff', 
		    '-webkit-border-radius': '5px', 
		    '-moz-border-radius': '5px',  
			'border-radius': '5px',           
		     color: '#bbb' 
		}  
	});

    var parent_respone = deleteParent(id);

    $.unblockUI({ 
    	message: ' <h2>Done</h2>',
        onUnblock: function(){ 
			$('.blockMsg h2').html('Done');
		} 
	}); 

    //console.log(parent_respone);

    if(!parent_respone.error) {
    	set_alert('success','Well done!', 'You successfully deleted APV header');
    } else {
    	set_alert('error','Oh snap!', 'Unable to delete APV header');
    }

    

}


/*
*    new submitdetail handler for saving form data (.table-model)
*/
function saveTableModel(){
	var id = $(".table-model #id").val();
	var role = $(".table-model").data("role");
	var respone;

	
	$.blockUI({ 
    	message: ' <h2>Saving <img src="../../../images/ajax-loader.gif"  /> </h2>',
    	css: {
		    border: 'none', 
		    padding: '15px', 
		    backgroundColor: '#fff', 
		    '-webkit-border-radius': '5px', 
		    '-moz-border-radius': '5px',  
			'border-radius': '5px',           
		     color: '#bbb' 
		}  
	});


	if(role==='parent') {
		//console.log('parent role');
		
		if(id === null || id === undefined || id === ''){
			addParentChild();
		} else {
			updateParentChild(id);
		}	
	} else {
		//console.log('single role');

		if(id === null || id === undefined || id === ''){
			console.log('addData3');
			respone = addData3();
		} else {
			console.log('updateData3');
			respone = updateData3(id);
		}
		console.log(respone);
		renderDetails(respone);	
	}


	 $.unblockUI({ 
    	message: ' <h2>Done</h2>',
        onUnblock: function(){ 
			$('.blockMsg h2').html('Done');
		} 
	});

	/*
	if(!respone.error) {
    	set_alert('success','Well done!', 'You successfully saved');

    	$("#frm-btn-delete").removeAttr('disabled');
    	$("#frm-btn-post").removeAttr('disabled');
    } else {
    	set_alert('error','Oh snap!', respone.error);
    }
	*/


}


/*
*  function for saveTableModel()
*/
function addData3(){
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = isset(table) ? table : form.data('table');
    var aData; 

    $.ajax({
        type: 'POST',
        contentType: 'application/json',
		url: apiPath +'/s/'+ table ,
        dataType: "json",
        async: false,
        data: formData,
        success: function(data, textStatus, jqXHR){
			aData = data; 			
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on creating '+ table +' data');
        }
    });
	
	return aData;
}

/*
*  function for saveTableModel()
*/
function updateData3(id) {
	var form = $(".table-model");	
    var formData = form.formToJSON();	
    var	table = isset(table) ? table : form.data('table');
    var aData;
	
    $.ajax({
        type: 'PUT',
        contentType: 'application/json',
        url: apiPath +'/s/'+ table +'/'+ id,
        dataType: "json",
        async: false,
        data: formData,
        success: function(data, textStatus, jqXHR){
            aData = data; 	
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + 'Failed on getting '+ table +' header');
        }
    });

    return aData;
}


function postCancelledTable(id){
	var form = $(".table-model");
	var formData = form.formToJSON();	
    var	table = form.data('table');	
	var aData; 

	$.ajax({
        type: 'POST',
        contentType: 'application/json',
		url: apiPath +'/txn/post/'+ table +'/'+ id +'/cancelled',
        dataType: "json",
        async: false,
        //data: formData,
        success: function(data, textStatus, jqXHR){
            aData = data;
        },
        error: function(jqXHR, textStatus, errorThrown){
            alert(textStatus + ' Failed on posting data');
        }
    });	
	
	$(".table-model .currency").each(function(){
		$(this).toNumberFormat();	
	});


	return aData;		 
}








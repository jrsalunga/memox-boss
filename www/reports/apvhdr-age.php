<?php
require_once('../../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("../login"): "";
?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Project Activity Management</title>


<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/styles-ui2.css">
<!--
<link rel="stylesheet" href="css/main-ui.css">
<link rel="stylesheet" href="css/styles-ui.css">
-->

<script src="../js/vendors/jquery-1.10.1.min.js"></script>
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<!--
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<script src="../js/vendors/jquery-1.9.1.js"></script>
<script src="js/vendors/underscore-min.js"></script>
<script src="js/vendors/backbone-min.js"></script>
-->
<script src="../js/vendors/underscore-min.js"></script>
<script src="../js/vendors/backbone-min.js"></script>
<script src="../js/vendors/bootstrap.min.js"></script>
<script src="../js/vendors/backbone-validation-min.js"></script>
<script src="../js/vendors/jquery.cookie-1.4.js"></script>
<script src="../js/vendors/moment.2.1.0-min.js"></script>
<script src="../js/vendors/accounting.js"></script>
<script src="../js/vendors/jquery.filedrop.js"></script>
<script src="../js/vendors/highcharts-3.0.9.js"></script>
<script src="../js/vendors/highcharts.exporting.js"></script>
<script src="../js/common.js"></script>


<script src="../js/models.js"></script>
<script src="../js/collections.js"></script>
<script src="../js/views.js"></script>
<script src="../js/highcharts.js"></script>
<script src="../js/report.apvhdr.js"></script>
<script src="../js/app.js"></script>


<script>




$(document).ready(function(e) {
	
	
	apvhdrsAge = new ApvhdrsAge();
	Backbone.history.start();
	
	$("#range-to").datepicker({"dateFormat": "yy-mm-dd",
		select: function(event, ui){
		
		}
   	});
	
	/*
   $(".apv-status .btn").click(function(){
		var thisFilter = $(this).data("posted");
		if(thisFilter==2){
			$(".report-detail-all .panel").slideDown();
		} else {
			$(".report-detail-all .panel").slideUp();
			$('.report-detail-all [data-posted="'+ thisFilter +'"]').slideDown();
		}
		return false;
   });
   */

	
});
</script>
</head>
<body id="app-body" class="state-nav">


	   <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top" role="navigation">
      <div>
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="../">MFI BOSS</a>
        </div>
        
        <div class="navbar-collapse collapse">
        	<ul class="nav navbar-nav">
              <li><a href="index">Reports</a></li>
            </ul>
       		<ul class="nav navbar-nav navbar-right">
            <!--
                <li><a href="#home">Home</a></li>
                <li><a href="#location/jeff">About</a></li>
            -->    
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <span class="glyphicon glyphicon-cog"></span>
                    <b class="caret"></b>
                    </a>
                        <ul class="dropdown-menu">
                        	<li><a href="#settings">Settings</a></li>
                            <li><a href="../logout">Sign Out</a></li>

     
                      </ul>
                </li>
            </ul>  
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div>
    <div class="stage">
		<div class="col-sm-2 col-md-2 l-pane">
    	<ul class="nav nav-pills nav-stacked">
            <li>
                <a href="apvhdr">Accounts Payable</a>
            </li>
            <li class="active">
				<a href="apvhdr-age">Accounts Payable (Aged)</a>
			</li>
            <li>
            	<a href="cvhdr">CV Schedule </a>
            <li>
            <li>
            	<a href="cv-sched">CV Schedule (Bank)</a>
            <li>
		</ul>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section id="apvhdr-report">
        	<div class="row">
            	
            	
                
            	<div class="col-md-5">
                	<div>
                        <form role="form">
                            <div class="form-group apv-param">
                                <div class="input-group">
                                  <input id="range-to" type="text" class="form-control" placeholder="YYYY-MM-DD">
                                  <div class="input-group-btn">
                                    <button type="button" class="btn btn-success btn-date-range" tabindex="-1">Go</button>
                                  </div>
                                </div>
                            </div>
                            <div class="form-group apv-status">
                                <div class="btn-group">
                                	<div class="btn-group">
                                    <button id="filter-mon-all" type="button" class="btn btn-info" data-month="6" title="All account payables">All</button>
                                  	</div>
                                    <div class="btn-group">
                                    <button id="filter-mon-0" type="button" class="btn btn-info" data-month="0" data-toggle="collapse" data-target="#collapse-age0" data-parent=".report-detail-all" title="Account payables in current day">Now</button>
                                  	</div>
                                    <div class="btn-group">
                                  	<button id="filter-mon-1" type="button" class="btn btn-info" data-month="1" data-toggle="collapse" data-target="#collapse-age30" data-parent=".report-detail-all" title="Account payables age 30 days">30</button>
                                  	</div>
                                    <div class="btn-group">
                                    <button id="filter-mon-2" type="button" class="btn btn-info" data-month="2" data-toggle="collapse" data-target="#collapse-age60" data-parent=".report-detail-all" title="Account payables age 60 days">60</button>
                                  	</div>
                                    <div class="btn-group">
                                  	<button id="filter-mon-3" type="button" class="btn btn-info" data-month="3" data-toggle="collapse" data-target="#collapse-age90" data-parent=".report-detail-all" title="Account payables age 90 days">90</button>
                                  	</div>
                                    <div class="btn-group">
                                    <button id="filter-mon-4" type="button" class="btn btn-info" data-month="4" data-toggle="collapse" data-target="#collapse-age120" data-parent=".report-detail-all" title="Account payables age 120 days">120</button>
                                  	</div>
                                    <div class="btn-group">
                                  	<button id="filter-mon-5" type="button" class="btn btn-info" data-month="5" data-toggle="collapse" data-target="#collapse-age150" data-parent=".report-detail-all" title="Account payables age over 120 days">Over 120</button>
                                	</div>
                                </div>
                            </div>
                        </form>
               		</div>
                    <div id="c-pie">
                    	<div class="c-pie-img ">
                   		</div>
               		</div>
                    <div id="c-stacked-bar">
                    	<div class="c-stacked-bar-img ">
                   		</div>
               		</div>
                </div>
                <!--
                <div class="col-md-9" style="height: 98px;">
                </div>
				-->
                <div class="col-md-7">
                	 <div id="apvhdr-age-details">
                     	
                        <div class="report-detail-all panel-group" >
                        </div>
   
                    </div>
                </div>
                
                
                <div id="c-column" class="col-md-12">
                    <div class="c-column-img">
                    </div>
                </div>
                
                <div id="c-line" class="col-md-12">
                    <div class="c-line-img">
                    </div>
                </div>
                
                <div id="apvhdr-report-list" class="col-md-12">
         
                </div>
                
                
                
            </div>
            </section>
        </div>
  

    </div>
</div> <!-- /container -->
    
    
    


</body>
</html>
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

<title>MemoXpress - Account Payables</title>
<link rel="shortcut icon" type="image/x-icon" href="../images/memoxpress-favicon.jpg" />

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
	
	apvhdrsDue = new ApvhdrsDue();
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
   	<?php
        include_once('../inc/navbar.php');
    ?>
    <div>
    <div class="stage">
		<div class="col-sm-2 col-md-2 l-pane">
    	<?php
            $activ = 'apvhdr';
            include_once('../inc/menu-nav.php');
        ?>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section id="apvhdr-report">
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Account Payables</h1>
                	</div>
                </div>
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
                                <div class="btn-group btn-group-justified">
                                	<div class="btn-group">
                                  	<button id="filter-all" type="button" class="btn btn-info" data-posted="2">All</button>
                                  	</div>
                                    <div class="btn-group">
                                  	<button id="filter-posted" type="button" class="btn btn-info" data-posted="1">Posted</button>
                                  	</div>
                                    <div class="btn-group">
                                  	<button id="filter-unposted" type="button" class="btn btn-info" data-posted="0">Unposted</button>
                                	</div>
                                </div>
                            </div>
                        </form>
               		</div>
                    <div>
                    	<ul id="total-list" class="list-group">
                        	<li class="list-group-item">All: <span class="pull-right total-list-a"></span></li>
                          	<li class="list-group-item">Posted: <span class="pull-right total-list-p"></span></li>
                          	<li class="list-group-item">Unposted: <span class="pull-right total-list-u"></span></li>
                        </ul>
                    </div>
                    <div id="c-pie">
                    	<div class="c-pie-img chart">
                    </div>
                    <!--
                    <div id="c-column">
                    <div class="c-column-img">
                    </div>
                    </div>
                    -->
                	
                </div>
                </div>
                <!--
                <div class="col-md-9" style="height: 98px;">
                </div>
				-->
                <div class="col-md-7">
                	 <div id="apvhdr-details" class="panel-group">
                     	
                        <div class="report-detail-all">
                        </div>
                        
                        <div class="report-detail-posted">
                        </div>
                        
                        <div class="report-detail-unposted">
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
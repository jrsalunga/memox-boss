<?php
require_once('../../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("../login"): "";
$dr = new DateRange($_GET['fr'],$_GET['to'], false);
?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MemoXpress - Check Voucher Scheduling</title>
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

<script src="../js/vendors/highcharts-4.0.1.min.js"></script>
<script src="../js/vendors/highcharts.data.js"></script>
<script src="../js/vendors/highcharts.exporting-4.0.1.js"></script>

<script src="../js/common.js"></script>

<script src="../js/app.js"></script>


<script>

function daterange(){

  $( "#fr" ).datepicker({
      defaultDate: "+1w",
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      numberOfMonths: 2,
      onClose: function( selectedDate ) {
        $( "#to" ).datepicker( "option", "minDate", selectedDate );
      }
    });
    $( "#to" ).datepicker({
      defaultDate: "+1w",
      dateFormat: 'yy-mm-dd',
      changeMonth: true,
      numberOfMonths: 2,
      onClose: function( selectedDate ) {
        $( "#fr" ).datepicker( "option", "maxDate", selectedDate );
      }
    });
}



$(document).ready(function(e) {
	
	
	daterange();
	
	$.get('../api/report/chk-day?fr=<?=$dr->fr?>&to=<?=$dr->to?>', function (csv) {
		//console.log(csv);
		var total = 0;
		$('#graph').highcharts({
            data: {
                csv: csv,
                // Parse the American date format used by Google
                parseDate: function (s) {  
					//console.log(s)   
					//var match = false;
                    //var match = s.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/);
					var match = s.match(/^([0-9]{1,4})\-([0-9]{1,2})\-([0-9]{1,2})$/);
                    if (match) {
                        console.log(Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]));
                        return Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]);
                    } else {
						//console.log(s);
					}
                }
            },
			chart: {
               	type:'pie',
			   	marginLeft: 100,
			   	style: {
					fontFamily: "Helvetica"
				},
				events: {
                    load: function(event) {
                        $('.CAcg h4').text(accounting.formatMoney(total,"", 2,","));
                	}
               	}
            },	
            title: {
                text: null
            },
			plotOptions: {
                pie: {
                    allowPointSelect: true,
					animation: true,
                    cursor: 'pointer',
                    showInLegend: true,
                    dataLabels: {
                        enabled: false,                        
                        formatter: function() {
                            return this.percentage.toFixed(2) + '%';
                        }
                    } 									
                }
            },
            legend: {
                enabled: true,
                layout: 'vertical',
                align: 'right',
                width: 400,
                verticalAlign: 'top',
				borderWidth: 0,
                useHTML: true,
				labelFormatter: function() {
					total += this.y;
					return '<div style="width:400px"><span style="float: left; width: 100px;">' + this.name + '</span><span style="float: left; width: 170px; text-align: right;">' + accounting.formatMoney(this.y,"", 2,",") + '</span><span style="float: left; width: 100px; text-align: right;">' + this.percentage.toFixed(2) + '%</span></div>';
				},
				title: {
					text: 'Total Amount per Bank',
					
				},
				itemStyle: {
					fontWeight: 'normal',
					fontSize: '14px',
					lineHeight: '22px'
				}
            },
			series: [
				{
					type: 'pie',
	
				}
			],
			exporting: {
				enabled: false
			}
        });
		
	});

	
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
          <a href="/">
          		<img src="../images/memoxpress.png" class="img-responsive header-logo" style="height:44px; width:100px; margin: 3px;">
        	</a>
           <a class="navbar-brand" href="/">MemoXpress</a>
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
            <li>
				<a href="apvhdr-age">Accounts Payable (Aged)</a>
			</li>
            <li>
            	<a href="cvhdr">CV Schedule </a>
            <li>
            <li class="active">
            	<a href="cv-sched">CV Schedule (Bank)</a>
            <li>
		</ul>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section>
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Check Breakdown - Day</h1>
                	</div>
                </div>
                <div class="row">
                	<div class="col-md-6">
                    	
                        
                          	<a class="btn btn-default" href="cv-sched-raw"><span class="glyphicon glyphicon-arrow-left"></span></a>
                          	
                        
                       
                    </div>
                	<div class="col-md-6 datepick pull-right">
                		<form role="form" class="form-inline pull-right">
                            <div class="form-group">
                                <label class="sr-only" for="fr">From:</label>
                                <input type="text" class="form-control" id="fr" name="fr" placeholder="YYYY-MM-DD" value="<?=$dr->fr?>">
                            </div>	
                            <div class="form-group">
                                <label class="sr-only" for="to">To:</label>
                                <input type="text" class="form-control" id="to" name="to" placeholder="YYYY-MM-DD"  value="<?=$dr->to?>">
                            </div>
          
                            <button type="submit" class="btn btn-success">Go</button>
                        </form>
                	</div>
                </div>
                <div class="row">
                	
                    <div class="col-md-8">
                        <div id="graph" class="graph-chk-day-pie">
                        
                        </div>
                    </div>
                    <div class="col-md-4">  
                    	<div class="CAcg">
                   			<p>Total:</p>
                        	<h4></h4>
                        </div>
                    </div>

                	
                    <div class="col-md-3 col-sm-6 col-md-offset-9">
                    	<div class="btn-group pull-right">
                          	<a class="btn btn-default" href="?fr=<?=$dr->fr_prev_day()?>&to=<?=$dr->to_prev_day()?>"><span class="glyphicon glyphicon-backward"></span></a>
                          	<a class="btn btn-default" href="?fr=<?=$dr->fr_next_day()?>&to=<?=$dr->to_next_day()?>"><span class="glyphicon glyphicon-forward"></span></a>
                        </div>
                        <p></p>
                    	<p>&nbsp;</p>
                    </div>
                    
                    <div class="col-md-12">
                    	<table class="table table-bordered">
                        	<thead>
                            	<tr>
                            	<?php
    								echo '<th>Day(s)</th><th>Bank</th><th>Check No</th><th>Payee</th><th>Check Amount</th>';
    							?>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php
    								foreach($dr->getDaysInterval() as $date){
    									$currdate = $date->format("Y-m-d");
    									echo '<tr>';
    									
										$sql = "SELECT * FROM vcvchkdtl ";
    									$sql .= "WHERE checkdate = '".$currdate."' ";
										$sql .= "ORDER BY bankcode ASC";
    									$cvchkdtls = vCvchkdtl::find_by_sql($sql); 
										global $database;
										$len = count($cvchkdtls);
										
										if($len > 0){
											echo '<td rowspan="'.$len.'">';
											echo $date->format("M d").'</td>';
											foreach($cvchkdtls as $cvchkdtl){
												//$code = Bank::row($cvchkdtl->bankacctid,0);
												echo '<td class="bnk-'.$code.'" title="'.$cvchkdtl->bank.'">'.$cvchkdtl->bankcode.'</td>';
												echo '<td class="bnk-'.$code.'" >'.$cvchkdtl->checkno.'</td>';
												echo '<td class="bnk-'.$code.'" >'.$cvchkdtl->payee.'</td>';
												echo '<td class="bnk-'.$code.'"  style="text-align:right;">'.number_format($cvchkdtl->amount,2).'</td></tr>';
											}
										} else {
											echo '<td>'.$date->format("M d").'</td><td>-</td><td>-</td><td>-</td></tr>';
										}
										
										
										
										//echo $database->last_query;
    									
    									
    									echo '</tr>';
    								}
    							?>
                            </tbody>
                        </table>
                    </div>
                </div>
                            
            </section>
        </div>
  

    </div>
</div> <!-- /container -->
    
    
    


</body>
</html>

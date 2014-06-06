<?php
require_once('../../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("../login"): "";
if(isset($_GET['fr']) && isset($_GET['to'])){
    sanitize($_GET);
    $dr = new DateRange($_GET['fr'],$_GET['to']);
} else {
    $dr = new DateRange(NULL,NULL,false);   
}
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
	
	$.get('../api/report/cv?fr=<?=$dr->fr?>&to=<?=$dr->to?>', function (csv) {
		//console.log(csv);
		$('#graph').highcharts({
            data: {
                csv: csv,
                // Parse the American date format used by Google
                parseDate: function (s) {  
					
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
                zoomType: 'x',
                height: 250,
                spacingRight: 0,
				marginTop: 35
            },
			colors:[
                '#51ABD2', '#F29885', '#ACFFD2'],
				
            title: {
                text: ''
            },
           	subtitle: {
                text: document.ontouchstart === undefined ?
                    '' :
                    'Pinch the chart to zoom in'
            },
            xAxis: {
                type: 'datetime',
                tickInterval: 7 * 24 * 3600 * 1000, // one week
                tickWidth: 0,
                gridLineWidth: 1,
                labels: {
                    align: 'left',
                    x: 3,
                    y: 15
                }
            },
            yAxis: [{ // left y axis
				min: 0,
                title: {
                    text: null
                },
                labels: {
                    align: 'left',
                    x: 3,
                    y: 16,
                    format: '{value:.,0f}'
                },
                showFirstLabel: false
            },
			/* 
			{ // right y axis
                linkedTo: 0,
                gridLineWidth: 0,
                opposite: true,
                title: {
                    text: null
                },
                labels: {
                    align: 'right',
                    x: -3,
                    y: 16,
                    format: '{value:.,0f}'
                },
                showFirstLabel: false
            }
			*/
			],
            legend: {
                align: 'left',
                verticalAlign: 'top',
                y: -10,
                floating: true,
                borderWidth: 0
            },
            tooltip: {
                shared: true,
                crosshairs: true
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function (e) {
								console.log(this.series);
								
								console.log(Highcharts.dateFormat('%Y-%m-%d', this.x));
								/*
                                hs.htmlExpand(null, {
                                    pageOrigin: {
                                        x: e.pageX,
                                        y: e.pageY
                                    },
                                    headingText: this.series.name,
                                    maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) +':<br/> '+
                                        this.y +' visits',
                                    width: 200
                                });
								*/
                            }
                        }
                    },
                    marker: {
                        lineWidth: 1,
						symbol: 'circle'
                    }
                }
            },
            series: [
				{
					name: 'Unposted',
					lineWidth: 3,
					marker: {
						radius: 4
					}
				},
				{
					name: 'Posted',
					lineWidth: 3,
					marker: {
						radius: 4
					}
				},
				{
					type: 'area',
					name: 'Total',
					lineWidth: 0,
					marker: {
						radius: 0
					},
					index: -1,
					fillOpacity: 0.4
				}
			]
        });
		
	});

	$("table.table").fixMe({
        container: '.navbar'
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
            <li class="active">
            	<a href="cvhdr">CV Schedule </a>
            <li>
            <li>
            	<a href="cv-sched">CV Schedule (Bank)</a>
            <li>
		</ul>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section>
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Check Voucher Schedule</h1>
                	</div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        
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
                	<div class="col-md-12 title">
                		<div class="col-md-12">
                        	<div id="graph" class="graph-full">
                            </div>
                        </div>
                	</div>
                    <div class="col-md-3 col-sm-6 col-md-offset-9">
                        <div class="pull-right">
                            <a class="btn btn-default" href="print-cvhdr">
                            <span class="glyphicon glyphicon-print"></span>
                            Printer Friendly
                            </a>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <br>
                    	<table class="table table-bordered table-hover">
                        	<thead>
                            	<tr>
                            	<th>Days</th><th>Unposted</th><th>Posted</th><th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php
    								foreach($dr->getDaysInterval() as $date){
    									$currdate = $date->format("Y-m-d");
    									echo $currdate==date('Y-m-d', strtotime('now'))?'<tr class="success">':'<tr>';
    									echo '<td><a href="chk-day?fr='.$currdate.'&to='.$currdate.'&ref=cvhdr">'.$date->format("M j, Y").'</a></td>';
    									$tot = 0;
										$tot_check = 0;
    									for($x = 0; $x <= 1; $x++){
    										$sql = "SELECT SUM(b.amount) as amount, COUNT(b.amount) as checkno FROM cvhdr a, cvchkdtl b ";
    										$sql .= "WHERE a.id = b.cvhdrid AND b.checkdate = '".$currdate."' ";
    										$sql .= "AND a.posted = '".$x."'";
    										$cvchkdtl = Cvchkdtl::find_by_sql($sql); 
    										$cvchkdtl = array_shift($cvchkdtl);
    										$amt = empty($cvchkdtl->amount) ? '-': number_format($cvchkdtl->amount, 2);
											$tot = $tot + $cvchkdtl->amount;
    										echo '<td style="text-align: right;">';
											if($cvchkdtl->checkno > 0){
												echo '<span class="pull-left" title="'.$cvchkdtl->checkno.' check(s)">';
												echo '<a href="chk-day?fr='.$currdate.'&to='.$currdate.'&posted='.$x.'&ref=cvhdr" style="color: #5cb85c; text-decoration: none;">';
												echo $cvchkdtl->checkno .' <span class="glyphicon glyphicon-money"></span></a></span>';
												
											}	
											echo $amt.'</td>';
											$tot = ($tot == 0) ? '-':$tot;
											$tot_check = $tot_check + $cvchkdtl->checkno;
											if($x==1){
												echo '<td style="text-align: right;">';
												if($tot_check > 0){
													echo '<span class="pull-left" title="'.$tot_check.' check(s)">';
													echo '<a href="chk-day?fr='.$currdate.'&to='.$currdate.'&ref=cvhdr" style="color: #5cb85c; text-decoration: none;">';
													echo $tot_check .' <span class="glyphicon glyphicon-money"></span></a></span>';
												}	
												echo $tot!='-' ? number_format($tot,2).'</td>': '-</td>';
											} else {
													
											}
    										
    									}	
    									
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

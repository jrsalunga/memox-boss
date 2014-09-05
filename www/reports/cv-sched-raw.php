<?php
require_once('../../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("../login"): "";
if(isset($_GET['fr']) && isset($_GET['to'])){
    sanitize($_GET);
    $dr = new DateRange($_GET['fr'],$_GET['to']);
} else {
    $dr = new DateRange(NULL,NULL,false);   
}
$uri = explode('?',$_SERVER['REQUEST_URI']);
$qs = !empty($uri[1]) ? '?'.$uri[1] : '?fr='.$dr->fr.'&to='.$dr->to;
?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MemoXpress - Check Scheduling by Bank (Detailed)</title>
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

<!-- Additional files for the Highslide popup effect -->
<!--
<script type="text/javascript" src="http://www.highcharts.com/media/com_demo/highslide-full.min.js"></script>
<script type="text/javascript" src="http://www.highcharts.com/media/com_demo/highslide.config.js" charset="utf-8"></script>
<link rel="stylesheet" type="text/css" href="http://www.highcharts.com/media/com_demo/highslide.css" />
-->
<script src="../js/common.js"></script>
<script src="../js/highcharts.js"></script>

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
		},
		beforeShow: function() {
			setTimeout(function(){
				$('#ui-datepicker-div').css('z-index', 1002);
			}, 0);
		}
	});
    $( "#to" ).datepicker({
  		defaultDate: "+1w",
      	dateFormat: 'yy-mm-dd',
      	changeMonth: true,
      	numberOfMonths: 2,
      	onClose: function( selectedDate ) {
        	$( "#fr" ).datepicker( "option", "maxDate", selectedDate );
      	},
		beforeShow: function() {
			setTimeout(function(){
				$('#ui-datepicker-div').css('z-index', 1002);
			}, 0);
		}
    });
}





$(document).ready(function(e) {
	
	daterange();
	
	
	
	$.get('../api/report/bank/total?fr=<?=$dr->fr?>&to=<?=$dr->to?>', function (csv) {
		//console.log(csv);
		//var totalOption = {
		$('#sg-total').highcharts({
			data: {
				csv: csv,
				parseDate: function (s) {		
					var match = s.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/);
					if (match) {
						return Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]);
					}
				}
			},
			chart: {
				height: 50,
				type: 'area',

			},
            title: {
                text: null
            },
            tooltip: {
                enabled: false,
            },
			xAxis: {
                type: 'datetime',
                tickInterval: 7 * 24 * 3600 * 1000, // one week
                tickWidth: 0,
                gridLineWidth: 0,
                labels: {
                     enabled: false
                    
                }
            },
			 yAxis: { // left y axis
				min: 0,
                gridLineWidth: 0,
                labels: {
                    enabled: false
                },
                showFirstLabel: false,
                title: null
            },
            plotOptions: {
                series: {
                    marker: {
                        states: {
                            hover: {
                                enabled: false
                            },
                            select: {
                                enabled: false
                            }
                        }
                    }
                }
            },
			series: [{
                name: null,
                lineWidth: 2,
                marker: {
                    radius: 1
                },
                showInLegend: false,
                fillOpacity: 0.3   
			}],
            exporting: { enabled: false }
		
	   });
	});
	
	
	
	$.get('../api/report/bank/status/posted?fr=<?=$dr->fr?>&to=<?=$dr->to?>', function (csv) {
		//console.log(csv);
		//var totalOption = {
		$('#sg-posted').highcharts({
			data: {
				csv: csv,
				parseDate: function (s) {		
					var match = s.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/);
					if (match) {
						return Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]);
					}
				}
			},
			chart: {
				height: 50,
				type: 'area',

			},
            title: {
                text: null
            },
            tooltip: {
                enabled: false,
            },
			xAxis: {
                type: 'datetime',
                tickInterval: 7 * 24 * 3600 * 1000, // one week
                tickWidth: 0,
                gridLineWidth: 0,
                labels: {
                     enabled: false
                    
                }
            },
			 yAxis: { // left y axis
				min: 0,
                gridLineWidth: 0,
                labels: {
                    enabled: false
                },
                showFirstLabel: false,
                title: null
            },
            plotOptions: {
                series: {
                    marker: {
                        states: {
                            hover: {
                                enabled: false
                            },
                            select: {
                                enabled: false
                            }
                        }
                    }
                }
            },
			series: [{
                name: null,
                lineWidth: 2,
                marker: {
                    radius: 1
                },
                showInLegend: false,
                fillOpacity: 0.3   
			}],
            exporting: { enabled: false }
		
	   });
	});
	
	
	
	$.get('../api/report/bank/status/unposted?fr=<?=$dr->fr?>&to=<?=$dr->to?>', function (csv) {
		//console.log(csv);
		//var totalOption = {
		$('#sg-unposted').highcharts({
			data: {
				csv: csv,
				parseDate: function (s) {		
					var match = s.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/);
					if (match) {
						return Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]);
					}
				}
			},
			chart: {
				height: 50,
				type: 'area',

			},
            title: {
                text: null
            },
            tooltip: {
                enabled: false,
            },
			xAxis: {
                type: 'datetime',
                tickInterval: 7 * 24 * 3600 * 1000, // one week
                tickWidth: 0,
                gridLineWidth: 0,
                labels: {
                     enabled: false
                    
                }
            },
			 yAxis: { // left y axis
				min: 0,
                gridLineWidth: 0,
                labels: {
                    enabled: false
                },
                showFirstLabel: false,
                title: null
            },
            plotOptions: {
                series: {
                    marker: {
                        states: {
                            hover: {
                                enabled: false
                            },
                            select: {
                                enabled: false
                            }
                        }
                    }
                }
            },
			series: [{
                name: null,
                lineWidth: 2,
                marker: {
                    radius: 1
                },
                showInLegend: false,
                fillOpacity: 0.3   
			}],
            exporting: { enabled: false }
		
	   });
	});
	
	
	
	
	//$.getJSON('http://www.highcharts.com/samples/data/jsonp.php?filename=analytics.csv&callback=?', function (csv) {
	$.get('../api/cv-sched?fr=<?=$dr->fr?>&to=<?=$dr->to?>', function (csv) {
        //console.log(csv);
        $('#graph').highcharts({

            data: {
                csv: csv,
                // Parse the American date format used by Google
                parseDate: function (s) {
                    
                    var match = s.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/);
                    if (match) {
                        console.log(Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]))
                        return Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]);
                    }
                }
            },
			chart: {
                zoomType: 'x',
                height: 250,
                spacingRight: 0
            },
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
                    align: 'center',
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
                y: 20,
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
                        lineWidth: 1
                    }
                }
            },

            series: [
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BPI-MTI',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				},
				{
					name: 'BDO-QP2',
					lineWidth: 2,
					marker: {
						radius: 2
					}
				}
			]
        });
    });

	$("table.table").fixMe({
        container: '.navbar'
    });

	
});
</script>
<style>
table.table tbody td {
	font-size: 13px;
}
</style>
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
            $activ = 'cv-sched';
            include_once('../inc/menu-nav.php');
        ?>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section>
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Check Schedule - Bank Detailed</h1>
                	</div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <a class="btn btn-default" href="cv-sched">
                          	<span class="glyphicon glyphicon-unshare"></span>
                           	Back to Summary
                       	</a>
                    </div>
                	<div class="col-md-6 datepick">
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
                	<div class="col-md-3 col-sm-6 GAcf">
                    	<div>
                            <p>Total</p>
                            <div class="GAJv">
                            	<?php
									$drtot = vCvchkdtl::total_by_date_range($dr->fr, $dr->to); 									
								?>
                                <h4><?=number_format($drtot->amount,2)?></h4>
                                <div id="sg-total" class="thumb-graph">
                                	
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!--
                	<div class="col-md-3 GAcf">
                    	<div>
                            <p></p>
                            <div class="GAJv">
                            	
                                <h4></h4>
                                <div id="sg-total" class="thumb-graph">
                                	
                                </div>
                            </div>
                        </div>
                    </div>
                    -->  
                    <div class="col-md-3 GAcf">
                    	<div>
                            <p>Unposted</p>
                            <div class="GAJv">
                            	<?php
									$drtotu = vCvchkdtl::total_status_by_date_range($dr->fr, $dr->to, 0); 									
								?>
                                <h4><?=number_format($drtotu->amount,2)?></h4>
                                <div id="sg-unposted" class="thumb-graph">
                                	
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 GAcf">
                    	<div>
                            <p>Posted</p>
                            <div class="GAJv">
                            	<?php
									$drtotp = vCvchkdtl::total_status_by_date_range($dr->fr, $dr->to, 1); 									
								?>
                                <h4><?=number_format($drtotp->amount,2)?></h4>
                                <div id="sg-posted" class="thumb-graph">
                                	
                                </div>
                            </div>
                        </div>
                    </div>
                    
                      
                	<div class="col-md-10">
                        <a class="btn btn-default <?=!isset($_GET['posted'])?'active':''?>" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>"><span class="glyphicon glyphicon-floppy"></span> All</a>
                        <a class="btn btn-default <?=(isset($_GET['posted']) && $_GET['posted']==0)?'active':''?>" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=0"><span class="glyphicon glyphicon-floppy-remove"></span> Unposted</a>
                        <a class="btn btn-default <?=(isset($_GET['posted']) && $_GET['posted']==1)?'active':''?>" <?=!isset($_GET['posted'])?'active':''?> href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=1"><span class="glyphicon glyphicon-floppy-saved"></span> Posted</a>

                        <?php
                            $banks = Bank::find_all();
                        ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-default" title="Filtered By">
                            	<span class="glyphicon glyphicon glyphicon-filter"></span> 
                                All Bank
                         	</button>
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                <span class="caret"></span>
                                <span class="sr-only">Toggle Dropdown</span>
                            </button>
                            <ul class="dropdown-menu" role="menu">
                                <li class="active"><a href="#">All Bank</a></li>
                                <li class="divider"></li>
                                <?php
                                foreach($banks as $bank){
                                    echo '<li>';    
                                    echo '<a href="/reports/cv-bank/'.$bank->id.'">'.$bank->code.'</a>';
                                    echo '</li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-2">
                    	<div class="pull-right">
                    		<a class="btn btn-default" href="print-cv-sched-raw<?=$qs?>"><span class="glyphicon glyphicon-print"></span> Printer Friendly</a>                     
                    	</div>     
                    </div>	
             		<div class="col-md-12">
                    	
                        <br>
                    	<table class="table table-bordered table-hover cv-sched-raw">
                        	<thead>
                            	<tr>
                            	<?php
    								echo '<th>DAYS</th>';
    								foreach($banks as $bank){
    									echo '<th title="'.$bank->descriptor.'">'. $bank->code .'</th>';	
    								}
									echo '<th>TOTAL</th>';
    							?>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php
    								foreach($dr->getDaysInterval() as $date){
    									$currdate = $date->format("Y-m-d");
    									echo $currdate == date('Y-m-d', strtotime('now')) ? '<tr class="success">':'<tr>';
    									echo '<td><a href="chk-day?fr='.$currdate.'&to='.$currdate.'">'.$date->format("M d").'</a></td>';
    									$tot = 0;
    									foreach($banks as $bank){
											/*
    										$sql = "SELECT SUM(amount) as amount FROM vcvchkdtl ";
    										$sql .= "WHERE checkdate = '".$currdate."' ";
                                            if(isset($_GET['posted']) && ($_GET['posted']==1 || $_GET['posted']==0)){
                                                $sql .= "AND posted = '".$_GET['posted']."' ";
                                            } 
    										$sql .= "AND bankid = '".$bank->id."'";
    										$cvchkdtl = vCvchkdtl::find_by_sql($sql); 
    										$cvchkdtl = array_shift($cvchkdtl);
											*/
											//global $database;
                                            //echo $database->last_query.'<br>';
											
											$cvchkdtl = vCvchkdtl::summary_by_date_with_bankid($currdate, $bank->id, $_GET['posted']); 
											
    										$amt = empty($cvchkdtl->amount) ? '-': number_format($cvchkdtl->amount, 2);
    										$tot = $tot + $cvchkdtl->amount;
    										echo '<td style="text-align: right;">'.$amt.'</td>';
											$tot = ($tot == 0) ? '-':$tot;
											if(end($banks)==$bank){
												if($tot=='-'){
													echo '<td style="text-align: right;">-</td>';
												} else {
													echo '<td style="text-align: right;">'.number_format($tot,2).'</td>';
												}	
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
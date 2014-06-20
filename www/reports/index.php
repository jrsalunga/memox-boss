<?php
require_once('../../lib/initialize.php');
ini_set('display_errors','Off');
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

<title>MemoXpress - Reports Dashboard</title>
<link rel="shortcut icon" type="image/x-icon" href="../images/memoxpress-favicon.jpg" />

<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/styles-ui2.css">
<!--
<link rel="stylesheet" href="css/main-ui.css">
<link rel="stylesheet" href="css/styles-ui.css">
-->


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
              <li><a href="">Reports</a></li>
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
				<a href="cvhdr">CV Schedule</a>
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
                		<h1>Reports Dashboard</h1>
                	</div>
                </div>
                <div class="row">
                	<div class="col-md-6">
                        
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
                	<div class="col-md-12">
                		<div class="col-md-12">
                        	<div id="graph" class="graph-full">
                            </div>
                        </div>
                	</div>
                    <div class="col-md-6">
                    	<div class="panel panel-default">
                         	<div class="panel-heading">
                            	<h3 class="panel-title">Account Payables due today</h3>
                          	</div>
                          	<div class="panel-body">
                            	<div class="list-group">
								<?php
                                    $sql = "SELECT * FROM vapvhdr WHERE due = '".date('Y-m-d', strtotime('now'))."' ORDER BY supplier;";
                                    $curr_apvhdrs = vApvhdr::find_by_sql($sql);
                                    foreach($curr_apvhdrs as $curr_apvhdr){
                                        //echo .'<br>';
                                        echo '<a href="/reports/accounts-payable-print/'.$curr_apvhdr->id.'" target="_blank" class="list-group-item">';
                                        echo '<p class="list-group-item-text">'.$curr_apvhdr->refno.'</p>';
                                        echo '<h4 class="list-group-item-heading">'.$curr_apvhdr->supplier.'</h4>';
                                        echo '<p class="list-group-item-text"><span class="pull-right"><strong>'.number_format($curr_apvhdr->totamount,2).'</strong></span></p>';
										echo '<div style="clear:both;"></div>';
                                        echo '</a>';
                                    }
                                ?>
                            	</div>
                          	</div>
                        </div>
                		
                        <br>
                        
                	</div>
                    <div class="col-md-6">
                    	<div class="panel panel-default">
                         	<div class="panel-heading">
                            	<h3 class="panel-title">Checks dated today</h3>
                          	</div>
                          	<div class="panel-body">
                            	<div class="list-group">
                				<?php
									$sql = "SELECT * FROM vcvchkdtl WHERE checkdate = '".date('Y-m-d', strtotime('now'))."' ORDER BY supplier;";
									$curr_vcvchkdtls = vCvchkdtl::find_by_sql($sql);
									foreach($curr_vcvchkdtls as $curr_vcvchkdtl){
										//echo $curr_vcvchkdtl->checkno.'<br>';
										echo '<a href="/reports/check-print/'.$curr_vcvchkdtl->cvhdrid.'" target="_blank" class="list-group-item">';
                                        echo '<p class="list-group-item-text">'.$curr_vcvchkdtl->checkno;
										echo '<span class="pull-right">'.date('F j, Y', strtotime($curr_vcvchkdtl->cvhdrdate)).'</span></p>';
                                        echo '<h4 class="list-group-item-heading">'.$curr_vcvchkdtl->supplier.'</h4>';
                                        echo '<p class="list-group-item-text">'. $curr_vcvchkdtl->bankcode;
										echo '<span class="pull-right"><strong>'.number_format($curr_vcvchkdtl->amount,2).'</strong></span></p>';
                                        echo '<div style="clear:both;"></div>';
										echo '</a>';
									}
								?>
                                </div>
                            </div>
                        </div>
                        <br>
                        
                	</div>
                </div>
      		</section>
        </div>
  

    </div>
</div> <!-- /container -->
    
    
    
    
    
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
<script src="../js/highcharts.js"></script>
<script src="../js/app.js"></script>


<script>




$(document).ready(function(e) {
	
	daterange();
	
	$.get('../api/report/ap-vs-cv?fr=<?=$dr->fr?>&to=<?=$dr->to?>', function (csv) {
		console.log(csv);
		$('#graph').highcharts({

            data: {
                csv: csv,
                // Parse the American date format used by Google
                parseDate: function (s) {
                    
                    var match = s.match(/^([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2})$/);
                    if (match) {
                        //console.log(Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]))
                        return Date.UTC(+('20' + match[3]), match[1] - 1, +match[2]);
                    }
                }
            },
			chart: {
                zoomType: 'x',
                height: 250,
                spacingRight: 0,
				marginTop: 35
            },
			colors: ['#7cb5ec', '#8085e9', '#88e0e3', '#FC9D9A', '#f15c80', '#F9CDAD', '#8085e8', '#8d4653', '#91e8e1'],
			/*
			colors:[
                '#48A0C4', '#ACFFD2', '#F29885', '#D53C25', '#FD668B', '#FCB319','#86A033', '#614931', '#00526F', '#594266', '#cb6828', '#aaaaab', '#a89375'
                ],
				*/
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
            	}
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
					name: 'AP Unposted',
					lineWidth: 3,
					marker: {
						radius: 4
					}
				},
				{
					name: 'AP Posted',
					lineWidth: 3,
					marker: {
						radius: 4
					}
				},
				{
					type: 'area',
					name: 'AP Total',
					lineWidth: 0,
					marker: {
						radius: 0
					},
					index: -1,
					fillOpacity: 0.4
				},
				{
					name: 'Check Unposted',
					lineWidth: 3,
					marker: {
						radius: 4
					}
				},
				{
					name: 'Check Posted',
					lineWidth: 3,
					marker: {
						radius: 4
					}
				},
				{
					type: 'area',
					name: 'Check Total',
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
	
});
</script>


</body>
</html>
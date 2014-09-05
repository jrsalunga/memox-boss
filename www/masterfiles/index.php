<?php
require_once('../../lib/initialize.php');
ini_set('display_errors','On');
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

<title>MemoXpress - Masterfiles Dashboard</title>
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
    <?php
        include_once('../inc/navbar.php');
    ?>
    <div>
    <div class="stage">
		<div class="col-sm-2 col-md-2 l-pane">
    	<?php
            $activ = '';
            include_once('../inc/masterfiles-menu-nav.php');
        ?>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section>
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Masterfiles</h1>
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
                },
				plotLines: [{ // mark the weekend
				color: 'green',
				width: 1,
				value: window.datenow,
				zIndex: 3
				}]
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
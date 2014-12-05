<?php
require_once('../../lib/initialize.php');
ini_set('display_errors','On');
!$session->is_logged_in() ? redirect_to("../login"): "";

if(isset($_GET['fr']) && isset($_GET['to'])){
    sanitize($_GET);
    $dr = new DateRange($_GET['fr'],$_GET['to'], false);
} else {
    $dr = new DateRange(NULL,NULL,false);   
}
	

$uri = explode('?',$_SERVER['REQUEST_URI']);
$qs = !empty($uri[1]) ? '?'.$uri[1] : '?fr='.$dr->fr.'&to='.$dr->to;


$posted = (isset($_GET['posted']) && ($_GET['posted']==1 || $_GET['posted']==0)) ? $_GET['posted']:NULL;
$bankid = (isset($_GET['bankid']) && is_uuid($_GET['bankid'])) ? $_GET['bankid']:NULL;
$supplierid = (isset($_GET['supplierid']) && is_uuid($_GET['supplierid'])) ? $_GET['supplierid']:NULL;


?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MemoXpress - Check Breakdown</title>
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
	
	$(".pop").popover({
    	html: true,
    	content: function () {
        	return $(".pop-content").html();
    	}
  	});
	
	/*
	$('td.checkno').each(function( idx, el ){
		var idx1 = idx;
		var el1 = el;
		$('td.checkno').each(function( idx, el ){
			if(idx1 == idx){
				//console.log('same index');
			} else {
				if(($(el1).text()!='0' || $(el).text()!='0') && (parseInt($(el1).text()) == parseInt($(el).text()))){
					
					var html = $(el1).text();
					html += ' <span class="glyphicon glyphicon-warning" title="warning: Duplicate check no!" style="cursor: pointer;"></span>';
					$(el1).html(html);
					
					$(el1).css('color','red');
					$(el1).attr('title','duplicate check no');
				}
			}
		});
	});
	*/
	
	
	$("td").hover(function() {
	
	  $el = $(this);
	  
	  $el.parent().addClass("hover");
	
	  if ($el.parent().has('td[rowspan]').length == 0)
		
		$el
		  .parent()
		  .prevAll('tr:has(td[rowspan]):first')
		  .find('td[rowspan]')
		  .addClass("hover");
	
	}, function() { 
		  
	  $el
		.parent()
		.removeClass("hover")
		.prevAll('tr:has(td[rowspan]):first')
		.find('td[rowspan]')
		.removeClass("hover");
	
	});
	
	
	daterange();
	
	$.get('../api/report/chk-day<?=$qs?>', function (csv) {
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

    $("table.table").fixMe({
        container: '.navbar'
    });

	
});
</script>
<style>
td.hover {
	background-color:#f5f5f5;
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
                		<h1>Check Breakdown
                        <?php
                        if(isset($_GET['posted']) && $_GET['posted']=='0'){
                            echo ' (Unposted)';
                        } else if(isset($_GET['posted']) && $_GET['posted']=='1'){
                            echo ' (Posted)';
                        } else {

                        }
                        ?>
                        </h1>
                	</div>
                </div>
                <div class="row">
                	<div class="col-md-6">
                        <a type="button" class="btn btn-default" href="<?php
                        	if(isset($_GET['ref'])){
								if($_GET['ref']=='cv-bank' && is_uuid($_GET['bankid'])){
									echo $_GET['ref'].'/'.$_GET['bankid'];
								} else {
									echo $_GET['ref'];
								}		
							} else {
								echo 'cv-sched-raw'; 
							}
						?>">
                            <span class="glyphicon glyphicon-unshare"></span>
                        </a>
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

                	
                   <div class="col-md-8">  
                   		<?php
							// functions href moved to functions.php
						?>
                        <a class="btn btn-default <?=!isset($_GET['posted'])?'active':''?>" href="<?=hrefer($dr)?>"><span class="glyphicon glyphicon-floppy"></span> All</a>
                        <a class="btn btn-default <?=(isset($_GET['posted']) && $_GET['posted']==0)?'active':''?>" href="<?=hrefer($dr)?>&posted=0"><span class="glyphicon glyphicon-floppy-remove"></span> Unposted</a>
                        <a class="btn btn-default <?=(isset($_GET['posted']) && $_GET['posted']==1)?'active':''?>" href="<?=hrefer($dr)?>&posted=1"><span class="glyphicon glyphicon-floppy-saved"></span> Posted</a>
                    
                    	<?php
							if(isset($_GET['bankid']) && is_uuid($_GET['bankid'])){
						?>	
							<span title="filtered by: <?=Bank::row($_GET['bankid'], 1)?>"><span class="glyphicon glyphicon glyphicon-filter"></span>  <?=Bank::row($_GET['bankid'], 0);?></span>
                        <?php
							}
						?>
                    	
                    </div>
                    <div class="col-md-4">
                        <div class="btn-group pull-right">
                            <a class="btn btn-default" href="<?=hrefer_prev($dr)?>"><span class="glyphicon glyphicon-backward"></span></a>
                            <a class="btn btn-default" href="<?=hrefer_next($dr)?>"><span class="glyphicon glyphicon-forward"></span></a>
                        </div>
                        <a class="btn btn-default" href="/api/export/chk-day<?=$qs?>"><span class="glyphicon glyphicon-save"></span> Export</a>
                        <a class="btn btn-default" href="print-chk-day<?=$qs?>&ref=print"><span class="glyphicon glyphicon-print"></span> Printer Friendly</a>
                    </div>
                    
                    <div class="col-md-12">
                        <br/>
                    	<table class="table table-bordered table-hover">
                        	<thead>
                            	<tr>
                                <th>Day(s)</th><th>CV Ref No</th><th>Bank</th><th>Check No</th><th>Payee</th><th>Check Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                            	<?php
    								foreach($dr->getDaysInterval() as $date){
    									$currdate = $date->format("Y-m-d");
    									echo '<tr>';
    									/*
										$sql = "SELECT * FROM vcvchkdtl ";
    									$sql .= "WHERE checkdate = '".$currdate."' ";
										if(isset($_GET['posted']) && ($_GET['posted']==1 || $_GET['posted']==0)){
											$sql .= "AND posted = '".$_GET['posted']."' ";
										} 
										if(isset($_GET['bankid']) && is_uuid($_GET['bankid'])){
											$sql .= "AND bankid = '".$_GET['bankid']."' ";
										} 
										$sql .= "ORDER BY bankcode ASC, payee";
    									$cvchkdtls = vCvchkdtl::find_by_sql($sql);
										*/
										
											
										$summary = array();
										$cvchkdtls = vCvchkdtl::find_by_date_with_bankid($currdate,$bankid,$posted,$supplierid);
										$prev_bank_tot = 0;
										$prev_bank = '';
										$len = count($cvchkdtls) + 1;
										if($cvchkdtls){
											echo '<td rowspan="'.$len.'">';
											echo $date->format("M j, Y").'<div><em>'.$date->format("l").'</em></div></td>';
											
											
											foreach($cvchkdtls as $cvchkdtl){
												echo '<td class="bnk-'.$cvchkdtl->bankcode.'">';
												echo '<a href="/reports/check-print/'.$cvchkdtl->cvhdrid.'" target="_blank">'.$cvchkdtl->refno.'</a>';
												if($cvchkdtl->posted==1){
													echo '<span class="glyphicon glyphicon-ok-circle pull-right" style="color:#5cb85c;" title="posted"></span>';
												} else {
													echo '<span class="glyphicon glyphicon-remove-circle pull-right" style="color:#f0ad4e;line-height: 1.3;" title="unposted"></span>';
												}
												echo '</td>';
												echo '<td class="bnk-'.$cvchkdtl->bankcode.'" title="'.$cvchkdtl->bank.'">'.$cvchkdtl->bankcode.'</td>';	
												echo '<td ';
												if($cvchkdtl->chkctr > 1 && $cvchkdtl->checkno!=0){
													echo 'style="color:#f0ad4e;"';	
												}
												echo ' class="bnk-'.$cvchkdtl->bankcode.' checkno" >';
												if($cvchkdtl->chkctr > 1 && $cvchkdtl->checkno!=0){
													echo '<a href="/masterfiles/check?q='.$cvchkdtl->checkno.'" style="color:#f0ad4e;" target="_blank">'.$cvchkdtl->checkno.'</a>';
												} else {
													echo $cvchkdtl->checkno;
												}
												/*
												if($prev_bank == $cvchkdtl->bankcode){
													$prev_bank_tot = $prev_bank_tot + $cvchkdtl->amount;
												} else {
													array_push($summary, array('bankcode'=>$prev_bank, 'totamount'=>$prev_bank_tot));
													$prev_bank = $cvchkdtl->bankcode;
													$prev_bank_tot = 0;
												}
												*/
												/*
												$summary[$cvchkdtl->bankcode]['bankcode'] = $cvchkdtl->bankcode;

												if(!isset($summary[$cvchkdtl->bankcode]['totamount'])){
													$summary[$cvchkdtl->bankcode]['totamount'] = 0;
												}
												$summary[$cvchkdtl->bankcode]['totamount'] = $summary[$cvchkdtl->bankcode]['totamount'] + $cvchkdtl->amount;
												$summary[$cvchkdtl->bankcode]['posted'] = $cvchkdtl->posted;
												

												
												if(array_key_exists($cvchkdtl->bankcode, $summary)) {
									                $summary[$cvchkdtl->bankcode] +=  $cvchkdtl->amount;
									            } else {
									                $summary[$cvchkdtl->bankcode] =  $cvchkdtl->amount;
									            }
												
												*/


												if($cvchkdtl->chkctr > 1 && $cvchkdtl->checkno!=0){
													
													$childs = vCvchkdtl::find_all_by_field('checkno', $cvchkdtl->checkno);
													//global $database;
													
													switch((count($childs))){
														case 2:
															$title = 'Duplicate';
															break;
														case 3:
															$title = 'Triplicate';
															break;
														case 4:
															$title = 'Quadruplicate';
															break;
														case 5:
															$title = 'Pentuiplicate';
															break;
														case 6:
															$title = 'Sextuplicate';
															break;
														default:
															$title = 'Many Copy';
													}
													
													echo ' <span class="glyphicon glyphicon-warning pop" rel="popover" style="cursor:pointer;"';
													echo 'data-container="body" data-toggle="popover" data-title="'.$title.'"';
   													echo 'data-content=" ';
													
													$found = false;
													foreach($childs as $child){
														if($cvchkdtl->refno == $child->refno && $found==false){
															$found=true;
														} else {
															echo '<a href=\'/reports/check-print/'.$child->cvhdrid.'\' target=\'_blank\'>';
															echo $child->refno;
															echo '</a><br>';
														}
													}
													echo ' "></span>';
												}
												echo '</td>';
												echo '<td class="bnk-'.$cvchkdtl->bankcode.'" >'.$cvchkdtl->payee.'</td>';
												echo '<td class="bnk-'.$cvchkdtl->bankcode.'"  style="text-align:right;">'.number_format($cvchkdtl->amount,2).'</td></tr>';
											}

											echo '</tr>';

	    									echo '<tr>';
	                                        echo '<td colspan="5" class="day-summary">';

	                                        		//echo json_encode($summary).'<br>';
	                                        		$rs = summaryReportPerDay($cvchkdtls, 'bankcode');
	                                        		echo '<ul>';
	                                        		$tot=0;
	                                        		foreach($rs as $k => $v){
														//echo $k.' - '.$v['totamt'].'<br>';
														echo '<li>'.$k.' <span>'.number_format($v['totamt'],2).'</span></li>';
														$tot += $v['totamt'];
													}
	                                        		/*
	                                        		foreach ($summary as $key => $value) {
	                                        			//echo $value['bankcode'].'-'.$value['totamount'].'<br>';
	                                        			echo '<li>'.$key.' <span>'.number_format($value,2).'</span></li>';
	                                        		}
	                                        		
	                                     			$s = array_map(function($e){
	                                     				//echo json_encode($e).'<br>';
	                                     				return $e['totamount'];
	                                     			}, $summary);
													*/
													echo '<li>Total Amount: <span>'. number_format($tot,2).'</span></li>';	
	                                     			//echo array_sum($s);

													echo '<li>Total Check: <span>';
	                                        		echo $len-1;
	                                        		echo '</span></li>';
	                                        		echo '</ul>';
	                                        echo '</td>';
	                                        echo '</tr>';
										} else {
											echo '<td>'.$date->format("M j, Y").'<div><em>'.$date->format("l").'</em></div></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>';
											echo '</tr>';
										}
    									
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

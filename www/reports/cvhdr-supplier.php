<?php
//setcookie("to", "", time() - 3600); // 86400 = 1 day
//setcookie("fr", "", time() - 3600); // 86400 = 1 day
require_once('../../lib/initialize.php');
ini_set('display_errors','Off');
!$session->is_logged_in() ? redirect_to("/login"): "";

$cleanUrl->setParts('status');

if(isset($_GET['fr']) && isset($_GET['to'])){
    sanitize($_GET);
    $dr = new DateRange($_GET['fr'],$_GET['to']);
} else {
    $dr = new DateRange(NULL,NULL,false);   
}



if($status=='posted'){
	$posted = 1;
} else if($status=='unposted'){
	$posted = 0;
} else {
	$posted = NULL;
}

global $database;
if(isset($_GET['q']) && $_GET['q']!=''){
	$sql = "SELECT * FROM supplier WHERE descriptor LIKE ";
	$sql .= "'%".$database->escape_value($_GET['q'])."%' ORDER BY descriptor";
	$suppliers = Supplier::find_by_sql($sql);
} else {
	$suppliers = Supplier::find_all();
}


//echo $database->last_query;

?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MemoXpress - Check Voucher by Supplier</title>
<link rel="shortcut icon" type="image/x-icon" href="/images/memoxpress-favicon.jpg" />

<link rel="stylesheet" href="/css/bootstrap.css">
<link rel="stylesheet" href="/css/styles-ui2.css">



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
            $activ = 'cvhdr-supplier';
            include_once('../inc/menu-nav.php');
        ?>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section>
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Check Voucher - Supplier</h1>
                	</div>
                </div>
                <div class="row">
                	<div class="col-md-5">
                        <div class="datepick" align="right" style="margin-bottom:15px;">
                            <form role="form" class="form-inline">
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
                        <div class="form-group apv-status">
                            <div class="btn-group btn-group-justified">
                                <div class="btn-group">
                                	<a id="filter-all" class="btn btn-info <?=empty($status)? 'active':''?>" href="/reports/cvhdr-supplier">All</a>
                                </div>
                                <div class="btn-group">
                                	<a id="filter-singles" class="btn btn-info <?=($status=='posted')?'active':''?>" href="/reports/cvhdr-supplier/posted/">Posted</a>
                                </div>
                                <div class="btn-group">
                                <a id="filter-hirise" class="btn btn-info <?=($status=='unposted')?'active':''?>" href="/reports/cvhdr-supplier/unposted/">Unposted</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <ul id="total-list" class="list-group">
                                <li class="list-group-item <?=empty($status)? 'list-group-item-info':''?>">
                                	<?php
										$acv = vCvhdr::sum_group_by_supplier($dr->fr,$dr->to);
									?>
                                    All: <span class="pull-right" title="<?=number_format($acv->percentage,2)?>%"><?=number_format($acv->totchkamt,2)?></span>
                                    <span class="pull-right total-list-a"></span>
                                </li>
                                <li class="list-group-item  <?=($status=='posted')?'list-group-item-info':''?>">
                                	<?php
										$pcv = vCvhdr::sum_group_by_supplier($dr->fr,$dr->to,"1");
										//global $database;
										//echo $database->last_query;
									?>
                                    Posted: <span class="pull-right" title="<?=number_format($pcv->percentage,2)?>%"><?=number_format($pcv->totchkamt,2)?></span>
                                    <span class="pull-right total-list-p"></span>
                                </li>
                                <li class="list-group-item <?=($status=='unposted')?'list-group-item-info':''?>">
                                	<?php
										$ucv = vCvhdr::sum_group_by_supplier($dr->fr,$dr->to,"0");
									?>
                                    Unposted: <span class="pull-right" title="<?=number_format($ucv->percentage,2)?>%"><?=number_format($ucv->totchkamt,2)?></span>
                                    <span class="pull-right total-list-u"></span>
                                </li>
                            </ul>
                        </div>
                        <div id="graph">
                        </div>
                	</div>
                    <div class="col-md-7">
                    	<div id="cvhdr-list" class="panel-group">
                        	<?php
								if(empty($suppliers)){
									echo 'No records found!';
								} else {
									foreach($suppliers as $supplier){
										$cvhdr = vCvhdr::summary_by_supplier($supplier->id, $dr->fr, $dr->to, $posted);
										//echo $database->last_query;
										
										echo '<div class="panel panel-default">';
										echo '<div class="panel-heading">';
										echo '<h4 class="panel-title">';
										echo '<a data-toggle="collapse" data-parent="#cvhdr-list" href="#collapse-'.$supplier->id.'" class="collapsed">';
										echo $supplier->descriptor;
										if($cvhdr->printctr > 0){
                                        	echo ' <span class="badge">'.$cvhdr->printctr.'</span>';
											echo '<span class="pull-right tot"title="'.number_format($cvhdr->percentage,2).'% of the Total Amount">&#8369 '.number_format($cvhdr->totchkamt,2).'</span>';
										}
										echo '</a></h4></div>';
										if($cvhdr->printctr > 0){
											echo '<div id="collapse-'.$supplier->id.'" class="panel-collapse collapse">';
											echo '<div class="panel-body">';
											
											echo '<div><table class="table table-striped apv-list">';
											//echo '<thead><tr><th>CV Ref No</th><th>Bank</th><th>Check No</th><th>Check Date</th><th>CV Status</th><th>Check Amount</th></tr></thead>';
											echo '<tbody>';
											
											$chld_cvhdrs = vCvchkdtl::status_with_supplier($supplier->id, $dr->fr, $dr->to, $posted);
											foreach($chld_cvhdrs as $chld_cvhdr){
												echo '<tr ';
												echo $chld_cvhdr->cancelled ==1 ? 'style="text-decoration:line-through':'';
												echo '">';
												echo '<td title="CV Ref No"><a href="/reports/check-print/'.$chld_cvhdr->cvhdrid.'" target="_blank">'.$chld_cvhdr->refno .'</a></td>';
												echo '<td title="Bank: '.$chld_cvhdr->bank.'">'.$chld_cvhdr->bankcode.'</td>';
												echo '<td title="Check No">';
												echo $chld_cvhdr->checkno == 0 ? '-':'<span class="glyphicon glyphicon-money" style="color:#5cb85c;"></span> ';
												 
													$childs = vCvchkdtl::find_all_by_field('checkno', $chld_cvhdr->checkno);
													if(count($childs) > 1 && $chld_cvhdr->checkno != 0){
														echo ' <a href="/masterfiles/check?q='.$chld_cvhdr->checkno.'" target="_blank"> '. $chld_cvhdr->checkno .'</a> ';
														echo ' <span class="glyphicon glyphicon-warning" title="Duplicate" style="color:#f0ad4e;"></span>';
													} else {
														echo $chld_cvhdr->checkno;	
													}
													
												echo '</td>';
												echo '<td title="Check Date">'. date('M j, Y', strtotime($chld_cvhdr->checkdate)) .'</td>';
												echo '<td title="CV ';
												echo $chld_cvhdr->posted ==1 ? 'Posted':'Unposted';
												echo '"><span class="glyphicon glyphicon-';
												echo $chld_cvhdr->posted ==1 ? 'posted':'unposted';
												echo '"></span></td>';
												echo '<td style="text-align:right;">'. number_format($chld_cvhdr->amount,2) .'</td>';	
												echo '</tr>';
											}	
											
											echo '<tbody></table></div>';
											echo '</div></div>';
										}
										echo '</div>';
										
									}
								}
							?>
                        </div>
                    </div>
                </div>
                <div class="row">
                	
                </div>
      		</section>
        </div>
  

    </div>
</div> <!-- /container -->
<!--
<link rel="stylesheet" href="css/main-ui.css">
<link rel="stylesheet" href="css/styles-ui.css">
-->

<script src="/js/vendors/jquery-1.10.1.min.js"></script>
<script src="/js/vendors/jquery-ui-1.10.3.js"></script>
<!--
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<script src="../js/vendors/jquery-1.9.1.js"></script>
<script src="js/vendors/underscore-min.js"></script>
<script src="js/vendors/backbone-min.js"></script>
-->
<script src="/js/vendors/underscore-min.js"></script>
<script src="/js/vendors/backbone-min.js"></script>
<script src="/js/vendors/bootstrap.min.js"></script>
<script src="/js/vendors/backbone-validation-min.js"></script>
<script src="/js/vendors/jquery.cookie-1.4.js"></script>
<script src="/js/vendors/moment.2.1.0-min.js"></script>
<script src="/js/vendors/accounting.js"></script>
<script src="/js/vendors/jquery.filedrop.js"></script>


<script src="/js/vendors/highcharts-4.0.1.min.js"></script>
<script src="/js/vendors/highcharts.data.js"></script>
<script src="/js/vendors/highcharts.exporting-4.0.1.js"></script>
<script src="/js/vendors/drilldown.js"></script>

<script src="/js/common.js"></script>
<script src="/js/highcharts.js"></script>
<script src="/js/app.js"></script>


<script>


getOtherPercent = function(list, pct){
	var othersPct = _.reduce(list, function(memo, el){
		if(parseInt(el.percentage) < pct){
			return memo + parseFloat(el.percentage);
		} else {
			return memo;	
		}
	}, 0);
	
	return othersPct;
}

getOthersAmt = function(list, pct){
	var othersAmt = _.reduce(list, function(memo, el){ 
		if(parseInt(el.percentage) < pct){
			return memo + parseFloat(el.totchkamt);
		} else {
			return memo;	
		}
	}, 0);
	return othersAmt;
}

getOther2Percent = function(list, pct, index, len){
	var othersPct = _.reduce(list, function(memo, el, i){
		//console.log('index:'+ i); 
		if(parseInt(el.percentage) < pct){
			return memo + parseFloat(el.percentage);
		} else {
			return memo;	
		}
	}, 0);
	
	return othersPct;
}

getOthers2Amt = function(list, pct, index, len){
	var othersAmt = _.reduce(list, function(memo, el){ 
		if(parseInt(el.percentage) < pct){
			return memo + parseFloat(el.totchkamt);
		} else {
			return memo;	
		}
	}, 0);
	return othersAmt;
}
				

$(document).ready(function(e) {
	
	daterange();
	
	$.getJSON('/api/report/cvhdr-supplier?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=<?=$posted?>&data=json', function (cvhdrs){	
		 
		 var minlen = 20,
		 	maxlen = 100,
		 	minpct = 1,
			maxpct = 2.5,
		 	suppliersData = [],
			drilldownSeries = [];
			
			
		 
		
		console.log(cvhdrs.length); 
		if(cvhdrs.length >= minlen){
		//if(cvhdrs.length > maxlen){
		//	alert('Cannot render graph. Too large date range parameter.');
			
		//} else if((cvhdrs.length <= maxlen) && (cvhdrs.length >= minlen)) {
			//console.log('mid len');	
			//console.log(cvhdrs.length);
			var level = parseInt(cvhdrs.length/minlen);
			//console.log(level);
			

			//console.log('othersPct: '+othersPct);
			if(level > 0){
				//console.log('yes: othersPct > 0');
				
				for(x=0; x<=level; x++){
					//console.log('level '+x);
					
					/*
					var othersPct = getOther2Percent(cvhdrs, maxpct, x, minlen);
					var othersAmt = getOthers2Amt(cvhdrs, maxpct);				
					
					suppliersData.push({
						name: 'Others',
						amount: accounting.formatMoney(othersAmt,"", 2,","),
						y: parseFloat(accounting.toFixed(othersPct,2)),
						supplier: 'other',
						id: 'othersuid',
						drilldown: 'others'
					});
					
					drilldownSeries.push({
						id: 'others',
						name: 'Others',
						data: []	
					});
					*/
					
				}
				
			}
			
			var tot = 0;
			var totpct = 0;
			var l = 1;
			var cv1 = 0;	
			var arr = [];
			
			_.each(cvhdrs, function(cvhdr, idx){
				if(parseInt(cvhdr.percentage) > maxpct){
					var x = {
						name: cvhdr.suppliercode,
						amount: accounting.formatMoney(cvhdr.totchkamt,"", 2,","),
						y: parseFloat(accounting.toFixed(cvhdr.percentage,2)),
						supplier: cvhdr.supplier,
						id: cvhdr.supplierid
					}	
					suppliersData.push(x);
					cv1++;
					
				} else {	
					
					var z = cv1 + (parseInt(minlen) * l);
					var y = cv1 + parseInt(minlen);
					
					//console.log(idx+' y:'+y);
					if(idx <= y){
						//console.log('1st level');
					}
					
					
					
					//console.log(z);
					
					if(idx >= z){
						//console.log('push:'+tot);
						//console.log('level:'+l);
						
						suppliersData.push({
						name: 'Others '+ l,
						amount: accounting.formatMoney(tot,"", 2,","),
						y: parseFloat(accounting.toFixed(totpct,2)),
						supplier: 'other'+l,
						id: 'othersuid'+l,
						drilldown: 'others'+l
						});
						
						drilldownSeries.push({
							id: 'others'+l,
							name: 'Others '+l,
							data: arr,
							drilldown: 'others'+l	
						});
						
						//console.log(drilldownSeries);
						
						l++;
						tot = 0;
						totpct = 0;
						arr = [];
					
                    } 

                    
                    if(idx==(cvhdrs.length-1)){
                        

                        suppliersData.push({
                        name: 'Others '+ l,
                        amount: accounting.formatMoney(tot,"", 2,","),
                        y: parseFloat(accounting.toFixed(totpct,6)),
                        supplier: 'other'+l,
                        id: 'othersuid'+l,
                        drilldown: 'others'+l
                        });
                        
                        drilldownSeries.push({
                            id: 'others'+l,
                            name: 'Others '+l,
                            data: arr,
                            drilldown: 'others'+l   
                        });

                    }


                    
					
					

                    tot = tot + parseFloat(cvhdr.totchkamt);
					totpct = totpct + parseFloat(cvhdr.percentage);
					
					
					arr.push({
						name: cvhdr.suppliercode,
						amount: accounting.formatMoney(cvhdr.totchkamt,"", 2,","),
						y: parseFloat(accounting.toFixed(cvhdr.percentage,6)),
						supplier: cvhdr.supplier,
						id: cvhdr.supplierid
					})	
					
					
					
					
					
					/*
					
					if(idx == z){
						console.log('last push:'+tot);
						console.log('level:'+l);
						console.log('push:'+tot);
						console.log('level:'+l);
						
						arr.push({
						name: 'Others '+ l,
						amount: accounting.formatMoney(tot,"", 2,","),
						y: parseFloat(accounting.toFixed(totpct,2)),
						supplier: 'other'+l,
						id: 'othersuid'+l,
						drilldown: 'others'
						});
						
						drilldownSeries.push({
							id: 'others'+l,
							name: 'Others '+l,
							data: arr,
							drilldown: 'others'+l	
						});
						
					} 
                    */
					
					
					
					
					
					
					
					
					
					
					
					
				}
			});	
			
			
			
			
		} else {
			console.log('below minlen');
			// check if there is CVHDR that % less than minpct
		
			var othersPct = getOtherPercent(cvhdrs, minpct);
			
			// check if 
			if(othersPct > 0){
				//console.log('yes! '+ othersPct +' > 0');
				var othersAmt = getOthersAmt(cvhdrs, minpct);
				/*
				var othersAmt = _.reduce(cvhdrs, function(memo, cvhdr){ 
					if(parseInt(cvhdr.percentage) < minpct){
						return memo + parseFloat(cvhdr.totchkamt);
					} else {
						return memo;	
					}
				}, 0);
				*/
				//console.log('others totchkamt: '+ othersAmt);
				
				suppliersData.push({
					name: 'Others',
					amount: accounting.formatMoney(othersAmt,"", 2,","),
					y: parseFloat(accounting.toFixed(othersPct,2)),
					supplier: 'other',
					id: 'othersuid',
					drilldown: 'others'
				});
				//console.log(drilldownSeries);
				
				drilldownSeries.push({
					id: 'others',
					name: 'Others',
					data: []	
				});
				/*
				_.each(cvhdrs, function(cvhdr, memo){
					if(parseInt(cvhdr.percentage) > minpct){
						// 
					} else {
						
					}
				});
				*/
			} // end: othersPct > 0
			
			
			_.each(cvhdrs, function(cvhdr, memo){
				if(parseInt(cvhdr.percentage) > minpct){
					var x = {
						name: cvhdr.suppliercode,
						amount: accounting.formatMoney(cvhdr.totchkamt,"", 2,","),
						y: parseFloat(accounting.toFixed(cvhdr.percentage,2)),
						supplier: cvhdr.supplier,
						id: cvhdr.supplierid
					}	
					suppliersData.push(x);
				} else {
					if(othersPct > 0){
						var x = {
							name: cvhdr.suppliercode,
							amount: accounting.formatMoney(cvhdr.totchkamt,"", 2,","),
							y: parseFloat(accounting.toFixed(cvhdr.percentage,2)),
							supplier: cvhdr.supplier,
							id: cvhdr.supplierid
						}	
						drilldownSeries[0].data.push(x);
					}
				}
			});	
		}
		
		
		
		$('#graph').highcharts({
                chart: {
					type: 'pie',
                    plotBackgroundColor: null,
                    plotBorderWidth: null,
                    plotShadow: false,
                    height: 300,
                },
                title: {
					text: null,
                    //text: 'Cvhdr by Suppler',
                    //text: this.settings.title,
                    margin: 2,
                    style: {
                        fontSize: '14px'
                    }
                },
                tooltip: {
                    pointFormat: 'Total Amount: <b>  {point.amount} </b> ({point.percentage:.2f}%)'
                },
                plotOptions: {
                    series: {
                        dataLabels: {
                            enabled: true,
                            color: '#000000',
                            connectorColor: '#ccc',
                            //format: '{point.code}: {point.percentage:.2f} %'
                        },
                    }
                },
                series: [{
                    name: 'Main',
                    colorByPoint: true,
                    data: suppliersData
                }],
                drilldown: {
                    series: drilldownSeries
                }
            });	
	});
	
	

});
</script>


</body>
</html>
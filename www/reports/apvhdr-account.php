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
//$apvhdrs = vApvhdr::status_with_group_account($dr->fr, $dr->to, $posted);
$accounts = Account::find_all();
//global $database;
//echo $database->last_query;

?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>MemoXpress - Account Payable Voucher by Accounts</title>
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
            $activ = 'apvhdr-account';
            include_once('../inc/menu-nav.php');
        ?>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section>
            	<div class="row">
                	<div class="col-md-12 title">
                		<h1>Account Payable Voucher - Accounts</h1>
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
                                	<a id="filter-all" class="btn btn-info <?=empty($status)? 'active':''?>" href="/reports/apvhdr-account">All</a>
                                </div>
                                <div class="btn-group">
                                	<a id="filter-singles" class="btn btn-info <?=($status=='posted')?'active':''?>" href="/reports/apvhdr-account/posted/">Posted</a>
                                </div>
                                <div class="btn-group">
                                <a id="filter-hirise" class="btn btn-info <?=($status=='unposted')?'active':''?>" href="/reports/apvhdr-account/unposted/">Unposted</a>
                                </div>
                            </div>
                        </div>
                        <div>
                            <ul id="total-list" class="list-group">
                                <li class="list-group-item <?=empty($status)? 'list-group-item-info':''?>">
                                	<?php
										$acv = vApvhdr::sum_group_by_account($dr->fr,$dr->to);
									?>
                                    All: <span class="pull-right" title="<?=number_format($acv->percentage,2)?>%">&#8369; <?=number_format($acv->totamount,2)?></span>
                                    <span class="pull-right total-list-a"></span>
                                </li>
                                <li class="list-group-item  <?=($status=='posted')?'list-group-item-info':''?>">
                                	<?php
										$pcv = vApvhdr::sum_group_by_account($dr->fr,$dr->to,"1");
									?>
                                    Posted: <span class="pull-right" title="<?=number_format($pcv->percentage,2)?>%">&#8369; <?=number_format($pcv->totamount,2)?></span>
                                    <span class="pull-right total-list-p"></span>
                                </li>
                                <li class="list-group-item <?=($status=='unposted')?'list-group-item-info':''?>">
                                	<?php
										$ucv = vApvhdr::sum_group_by_account($dr->fr,$dr->to,"0");
									?>
                                    Unposted: <span class="pull-right" title="<?=number_format($ucv->percentage,2)?>%">&#8369; <?=number_format($ucv->totamount,2)?></span>
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
								foreach($accounts as $account){
									$apvhdr = vApvhdr::summary_by_account($account->id, $dr->fr, $dr->to, $posted);
									//global $database;
									//echo $database->last_query;
									echo '<div class="panel panel-default">';
									echo '<div class="panel-heading">';
									echo '<h4 class="panel-title">';
									echo '<a data-toggle="collapse" data-parent="#cvhdr-list" href="#collapse-'.$account->id.'" class="collapsed">';
									echo $account->descriptor;
									if($apvhdr->printctr>0){
										echo ' <span class="badge">'.$apvhdr->printctr.'</span>';
										echo '<span class="pull-right tot" title="'.number_format($apvhdr->percentage,2).'% of the Total Amount">&#8369; '.number_format($apvhdr->totamount,2).'</span>';
									}
									echo '</a></h4></div>';
									if($apvhdr->printctr>0){
										echo '<div id="collapse-'.$account->id.'" class="panel-collapse collapse">';
										echo '<div class="panel-body">';
										echo '<div><table class="table table-striped apv-list">';
										//echo '<thead><tr><th>APV Ref No</th><th>Date</th><th>Amount</th></tr></thead>';
										echo '<tbody>';
										
										$chld_cvhdrs = vApvhdr::status_with_account($account->id, $dr->fr, $dr->to, $posted);
										//global $database;
										//echo $database->last_query;
										foreach($chld_cvhdrs as $chld_cvhdr){
											//echo $chld_cvhdr->refno.' - '.$chld_cvhdr->totchkamt.'<br>';
											echo '<tr ';
											echo $chld_cvhdr->cancelled ==1 ? 'style="text-decoration:line-through':'';
											echo '">';
											echo '<td title="'.$chld_cvhdr->supplier.'">'.$chld_cvhdr->suppliercode .'</td>';
											echo '<td title="APV Reference No"><a href="/reports/accounts-payable-print/'.$chld_cvhdr->id.'" target="_blank">'.$chld_cvhdr->refno .'</a></td>';
											echo '<td title="Due Date">'. date('F j, Y', strtotime($chld_cvhdr->due)) .'</td>';
											echo '<td title="APV ';
											echo $chld_cvhdr->posted ==1 ? 'Posted':'Unposted';
											echo '"><span class="glyphicon glyphicon-';
											echo $chld_cvhdr->posted ==1 ? 'posted':'unposted';
											echo '"></span></td>';
											echo '<td title="'.number_format($chld_cvhdr->percentage,2).' of '.number_format($chld_cvhdr->totamount,2).' Total Amount" style="text-align:right;">'. number_format($chld_cvhdr->percentage,2) .'</td>';	
											echo '</tr>';
										}	
										echo '<tbody></table></div>';
										echo '</div></div>';
									}
									echo '</div>';		
									
									
								}
								
								
								/*
								if(empty($apvhdrs)){
									echo 'No records found!';
								} else {
									foreach($apvhdrs as $apvhdr){
										//echo $cvhdr->supplier.' - '.$cvhdr->totchkamt.'<br>';
										echo '<div class="panel panel-default">';
										echo '<div class="panel-heading">';
										echo '<h4 class="panel-title">';
										echo '<a data-toggle="collapse" data-parent="#cvhdr-list" href="#collapse-'.$apvhdr->accountid.'" class="collapsed">';
										echo $apvhdr->account;
										if($apvhdr->printctr>0){
                                        	echo ' <span class="badge">'.$apvhdr->printctr.'</span>';
											echo '<span class="pull-right tot" title="'.number_format($apvhdr->percentage,2).'% of the Total Amount">&#8369; '.number_format($apvhdr->totamount,2).'</span>';
										}
										
										echo '</a></h4></div>';
										if($apvhdr->printctr>0){
											echo '<div id="collapse-'.$apvhdr->accountid.'" class="panel-collapse collapse">';
											echo '<div class="panel-body">';
											
											
											echo '<div><table class="table table-striped apv-list">';
											//echo '<thead><tr><th>APV Ref No</th><th>Date</th><th>Amount</th></tr></thead>';
											echo '<tbody>';
											
											$chld_cvhdrs = vApvhdr::status_with_account($apvhdr->accountid, $dr->fr, $dr->to, $posted);
											global $database;
											echo $database->last_query;
											foreach($chld_cvhdrs as $chld_cvhdr){
												//echo $chld_cvhdr->refno.' - '.$chld_cvhdr->totchkamt.'<br>';
												echo '<tr ';
												echo $chld_cvhdr->cancelled ==1 ? 'style="text-decoration:line-through':'';
												echo '">';
												echo '<td title="'.$chld_cvhdr->supplier.'">'.$chld_cvhdr->suppliercode .'</td>';
												echo '<td title="APV Reference No"><a href="/reports/accounts-payable-print/'.$chld_cvhdr->id.'" target="_blank">'.$chld_cvhdr->refno .'</a></td>';
												echo '<td title="Due Date">'. date('F j, Y', strtotime($chld_cvhdr->date)) .'</td>';
												echo '<td title="';
												echo $chld_cvhdr->posted ==1 ? 'Posted':'Unposted';
												echo '"><span class="glyphicon glyphicon-';
												echo $chld_cvhdr->posted ==1 ? 'posted':'unposted';
												echo '"></span></td>';
												echo '<td title="APV Total Amount" style="text-align:right;">'. number_format($chld_cvhdr->totamount,2) .'</td>';	
												echo '</tr>';
											}	
											echo '<tbody></table></div>';
											echo '</div></div>';
										}
										echo '</div>';		
									}
								}
								*/
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
			return memo + parseFloat(el.totamount);
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
	
	$.getJSON('/api/report/apvhdr-account?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=<?=$posted?>&data=json', function (cvhdrs){	
		 
		 var minlen = 10,
		 	maxlen = 100,
		 	minpct = 1,
			maxpct = 2,
		 	suppliersData = [],
			drilldownSeries = [];
			
			
		 
		
		//console.log(cvhdrs.length); 
		if(cvhdrs.length > maxlen){
			alert('Cannot render graph. Too large date range parameter.');
			
		} else if((cvhdrs.length <= maxlen) && (cvhdrs.length >= minlen)) {
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
						name: cvhdr.accountcode,
						amount: accounting.formatMoney(cvhdr.totamount,"", 2,","),
						y: parseFloat(accounting.toFixed(cvhdr.percentage,2)),
						supplier: cvhdr.account,
						id: cvhdr.accountid
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

                    
                    

                    
					
					
					console.log(cvhdr.totamount);
                    tot = tot + parseFloat(cvhdr.totamount);
					console.log(tot);
					totpct = totpct + parseFloat(cvhdr.percentage);
					
					
					arr.push({
						name: cvhdr.accountcode,
						amount: accounting.formatMoney(cvhdr.totamount,"", 2,","),
						y: parseFloat(accounting.toFixed(cvhdr.percentage,6)),
						supplier: cvhdr.account,
						id: cvhdr.accountid
					})	
					
					
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
						name: cvhdr.accountcode,
						amount: accounting.formatMoney(cvhdr.totamount,"", 2,","),
						y: parseFloat(accounting.toFixed(cvhdr.percentage,2)),
						supplier: cvhdr.account,
						id: cvhdr.accountid
					}	
					suppliersData.push(x);
				} else {
					if(othersPct > 0){
						var x = {
							name: cvhdr.accountcode,
							amount: accounting.formatMoney(cvhdr.totamount,"", 2,","),
							y: parseFloat(accounting.toFixed(cvhdr.percentage,2)),
							supplier: cvhdr.account,
							id: cvhdr.accountid
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
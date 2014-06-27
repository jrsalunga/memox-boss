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
$cvhdrs = vCvhdr::status_with_group_supplier($dr->fr, $dr->to, $posted);
//global $database;
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
				<a href="/reports/apvhdr">Accounts Payable</a>
			</li>
			<li>
				<a href="/reports/apvhdr-age">Accounts Payable (Aged)</a>
			</li>
            <li class="active">
				<a href="/reports/cvhdr-supplier">Check Voucher</a>
			</li>
			<li>
				<a href="/reports/cvhdr">CV Schedule</a>
			<li>
            <li>
				<a href="/reports/cv-sched">CV Schedule (Bank)</a>
			<li>
		</ul>
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
                        <div id="graph">
                        </div>
                	</div>
                    <div class="col-md-7">
                    	<div id="cvhdr-list" class="panel-group">
                        	<?php
								if(empty($cvhdrs)){
									echo 'No records found!';
								} else {
									foreach($cvhdrs as $cvhdr){
										//echo $cvhdr->supplier.' - '.$cvhdr->totchkamt.'<br>';
										echo '<div class="panel panel-default">';
										echo '<div class="panel-heading">';
										echo '<h4 class="panel-title">';
										echo '<a data-toggle="collapse" data-parent="#cvhdr-list" href="#collapse-'.$cvhdr->supplierid.'" class="collapsed">';
										echo $cvhdr->supplier;
                                        echo ' <span class="badge">'.$cvhdr->printctr.'</span>';
										echo '<span class="pull-right tot">'.number_format($cvhdr->totchkamt,2).'</span>';
										echo '</a></h4></div>';
										echo '<div id="collapse-'.$cvhdr->supplierid.'" class="panel-collapse collapse">';
										echo '<div class="panel-body">';
										
										
										echo '<div><table class="table table-striped apv-list">';
										//echo '<thead><tr><th>APV Ref No</th><th>Date</th><th>Amount</th></tr></thead>';
										echo '<tbody>';
										
										$chld_cvhdrs = vCvhdr::status_with_supplier($cvhdr->supplierid, $dr->fr, $dr->to, $posted);
										foreach($chld_cvhdrs as $chld_cvhdr){
											//echo $chld_cvhdr->refno.' - '.$chld_cvhdr->totchkamt.'<br>';
											echo '<tr>';
											//echo '<td>'.$apvdtl1->account .'</td>';
											echo '<td><a href="/reports/check-print/'.$chld_cvhdr->id.'" target="_blank">'.$chld_cvhdr->refno .'</a></td>';
											echo '<td>'. date('F j, Y', strtotime($chld_cvhdr->date)) .'</td>';
											echo '<td><span class="glyphicon glyphicon-';
											echo $chld_cvhdr->posted ==1 ? 'posted':'unposted';
											echo '"></span></td>';
											echo '<td style="text-align:right;">'. number_format($chld_cvhdr->totchkamt,2) .'</td>';	
											echo '</tr>';
										}	
										echo '<tbody></table></div>';
										echo '</div></div></div>';
										
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
				

$(document).ready(function(e) {
	
	daterange();
	
	$.getJSON('/api/report/cvhdr-supplier?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=<?=$posted?>&data=json', function (cvhdrs){	
		 
		 var minlen = 15,
		 	maxlen = 100,
		 	minpct = 1,
			maxpct = 2,
		 	suppliersData = [],
			drilldownSeries = [];
		 
		
		//console.log(cvhdrs.length); 
		if(cvhdrs.length > maxlen){
			alert('over maxlen!');
			
		} else if((cvhdrs.length <= maxlen) && (cvhdrs.length >= minlen)) {
			console.log('mid len');
			
			console.log(cvhdrs.length);
			console.log(cvhdrs.length/minlen);
			
			
			
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
                    text: 'Cvhdr by Suppler',
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
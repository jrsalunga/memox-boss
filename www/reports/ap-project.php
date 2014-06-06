<?php
require_once('../../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("../login"): "";

$cleanUrl->setParts('type');
sanitize($_GET);
$q = $_GET['q'];

if(empty($q)){
	switch($type){
		case $type == ' ':
			$sql = 'SELECT b.descriptor, sum(a.amount) as amount, b.type, b.id FROM apvdtl a, project b WHERE a.type = 1 AND a.projectid = b.id GROUP BY a.projectid ORDER BY b.descriptor';
			
			$sql_pie = "SELECT c.code AS code, c.descriptor , SUM(a.amount) AS amount, a.accountid as id ";
			$sql_pie .= "FROM apvdtl a INNER JOIN project b ON a.projectid = b.id ";
			$sql_pie .= "INNER JOIN account c ON a.accountid = c.id ";
			$sql_pie .= "GROUP BY c.id ";
			break;
		case $type == 'singles':
			$sql = 'SELECT b.descriptor, sum(a.amount) as amount, b.type, b.id FROM apvdtl a, project b WHERE a.type = 1 AND a.projectid = b.id AND b.type = 1 GROUP BY a.projectid ORDER BY b.type, 1';
			
			$sql_pie = "SELECT c.code AS code, c.descriptor , SUM(a.amount) AS amount, a.accountid as id ";
			$sql_pie .= "FROM apvdtl a INNER JOIN project b ON a.projectid = b.id ";
			$sql_pie .= "INNER JOIN account c ON a.accountid = c.id ";
			$sql_pie .= "WHERE b.type = 1 GROUP BY c.id ";
			break;
		case $type == 'hirise':
			$sql = 'SELECT b.descriptor, sum(a.amount) as amount, b.type, b.id FROM apvdtl a, project b WHERE a.type = 1 AND a.projectid = b.id AND b.type = 2 GROUP BY a.projectid ORDER BY b.type, 1';
			
			$sql_pie = "SELECT c.code AS code, c.descriptor , SUM(a.amount) AS amount, a.accountid as id ";
			$sql_pie .= "FROM apvdtl a INNER JOIN project b ON a.projectid = b.id ";
			$sql_pie .= "INNER JOIN account c ON a.accountid = c.id ";
			$sql_pie .= "WHERE b.type = 2 GROUP BY c.id ";
			break;	
	}
} else {
	$sql  = 'SELECT b.descriptor, sum(a.amount) as amount, b.type, b.id ';
	$sql .= 'FROM apvdtl a, project b ';
	$sql .= "WHERE a.type = 1 AND a.projectid = b.id AND b.type = 2 AND b.descriptor LIKE '%". $q ."%' ";
	$sql .= 'GROUP BY a.projectid ORDER BY b.type, 1';
}

$projects = Project::find_by_sql($sql);


	
$pie = HCPie::find_by_sql($sql_pie);
//echo json_encode($pie);
	

$tot_singles = Apvdtl::find_by_sql('SELECT sum(a.amount) as amount FROM apvdtl a, project b WHERE b.type = 1 AND a.projectid = b.id');
$tot_singles = array_shift($tot_singles);
$tot_hirise = Apvdtl::find_by_sql('SELECT sum(a.amount) as amount FROM apvdtl a, project b WHERE b.type = 2 AND a.projectid = b.id');
$tot_hirise = array_shift($tot_hirise);


?>

<!DOCTYPE HTML>
<html lang="en-ph"><head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Project Activity Management</title>



<link rel="stylesheet" href="<?=$relativeslash?>../css/bootstrap.css">
<link rel="stylesheet" href="<?=$relativeslash?>../css/styles-ui2.css">


<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/styles-ui2.css">
<!--
<link rel="stylesheet" href="css/main-ui.css">
<link rel="stylesheet" href="css/styles-ui.css">
-->

<script src="<?=$relativeslash?>../js/vendors/jquery-1.10.1.min.js"></script>
<script src="<?=$relativeslash?>../js/vendors/jquery-ui-1.10.3.js"></script>
<!--
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<script src="../js/vendors/jquery-1.9.1.js"></script>
<script src="js/vendors/underscore-min.js"></script>
<script src="js/vendors/backbone-min.js"></script>
-->
<script src="<?=$relativeslash?>../js/vendors/underscore-min.js"></script>
<script src="<?=$relativeslash?>../js/vendors/backbone-min.js"></script>
<script src="<?=$relativeslash?>../js/vendors/bootstrap.min.js"></script>
<script src="<?=$relativeslash?>../js/vendors/backbone-validation-min.js"></script>
<script src="<?=$relativeslash?>../js/vendors/jquery.cookie-1.4.js"></script>
<script src="<?=$relativeslash?>../js/vendors/moment.2.1.0-min.js"></script>
<script src="<?=$relativeslash?>../js/vendors/accounting.js"></script>
<script src="<?=$relativeslash?>../js/vendors/jquery.filedrop.js"></script>
<script src="<?=$relativeslash?>../js/vendors/highcharts-3.0.9.js"></script>
<script src="<?=$relativeslash?>../js/vendors/highcharts.exporting.js"></script>
<script src="<?=$relativeslash?>../js/common.js"></script>


<script src="<?=$relativeslash?>../js/models.js"></script>
<script src="<?=$relativeslash?>../js/collections.js"></script>
<script src="<?=$relativeslash?>../js/views.js"></script>
<script src="<?=$relativeslash?>../js/highcharts.js"></script>
<script src="<?=$relativeslash?>../js/report.apvhdr.js"></script>
<script src="<?=$relativeslash?>../js/app.js"></script>


<script>




$(document).ready(function(e) {
	
	//apvhdrsDue = new ApvhdrsDue();
	
	
	var hccpie = new hccPie(<?=json_encode($pie)?>);
	
	var hcvpie = new hcvPie({el: "#c-pie2", collection: hccpie, settings: {title: 'Accounts Percentage'}});
	console.log(hcvpie);
	hcvpie.render();
	
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
				<a href="<?=$relativeslash?>apvhdr">Accounts Payable</a>
			</li>
            <li>
				<a href="<?=$relativeslash?>apvhdr-age">Accounts Payable (Aged)</a>
			</li>
            <li class="active">
				<a href="<?=$relativeslash?>ap-project">AP (Project)</a>
			</li>
			<li>
				<a href="<?=$relativeslash?>cvhdr">Check</a>
			<li>
            
		</ul>
    	</div>
    	<div class="col-sm-10 col-md-10 r-pane pull-right">
        	<section id="apvhdr-report">
            <div class="row">
            	<div class="col-md-12 title">
                <h1>Account Payables by Project</h1>
                </div>
            </div>
        	<div class="row">
            	
            	
                
            	<div class="col-md-5">
                	<div class="form-group apv-status">
                        <div class="btn-group btn-group-justified">
                            <div class="btn-group">
                            	<a id="filter-all" href="<?=$relativeslash?>ap-project" class="btn btn-info <?=empty($type)? 'active':''?>">All</a>
                            </div>
                            <div class="btn-group">
                            	<a id="filter-singles" href="<?=$relativeslash?>ap-project/singles" class="btn btn-info <?=($type=='singles')?'active':''?>">Singles</a>
                            </div>
                            <div class="btn-group">
                            	<a id="filter-hirise" href="<?=$relativeslash?>ap-project/hirise" class="btn btn-info <?=($type=='hirise')?'active':''?>">Hi Rise</a>
                            </div>
                        </div>
                    </div>
                    <div>
                    	<ul id="total-list" class="list-group">
                        	<li class="list-group-item <?=empty($type)? 'list-group-item-info':''?>">
                            	All:
                                <span class="pull-right pct">
                                &nbsp;
                                </span> 
                            	<span class="pull-right total-list-a"><?=number_format($tot_singles->amount + $tot_hirise->amount, 2)?></span>
                            </li>
                          	<li class="list-group-item <?=($type=='singles')?'list-group-item-info':''?>">
                            	Singles:
                                <span class="pull-right pct">
                                	<em><?=number_format(($tot_singles->amount*100)/($tot_singles->amount + $tot_hirise->amount),2)?>%</em> 
                                </span>
                            	<span class="pull-right total-list-p"> 
									<?=number_format($tot_singles->amount, 2)?>
                              	</span>
                          	</li>
                          	<li class="list-group-item <?=($type=='hirise')?'list-group-item-info':''?>">
                            	Hi Rise:
                                <span class="pull-right pct">
                                	<em><?=number_format(($tot_hirise->amount*100)/($tot_singles->amount + $tot_hirise->amount),2)?>%</em>
                               	</span> 
                            	<span class="pull-right total-list-u">
									<?=number_format($tot_hirise->amount, 2)?>
                                </span>
                                
                            </li>
                        </ul>
                    </div>
					<div id="c-pie2">
						<div class="c-pie-img chart" data-highcharts-chart="1">
                        </div>
                   	</div>
                
                	
  
                    
                </div>
                <div class="col-md-7">
                	 <div id="project-list" class="panel-group">
                       <?php
					   		
							
							foreach($projects as $project){
								//$apvdtls = vApvdtl::find_all_by_field_id('project', $project->id);
								
								
								$sql = "SELECT account, SUM(amount) as amount, accountid FROM vapvdtl ";
								$sql .= "WHERE projectid='".$project->id."' GROUP BY accountid ORDER BY account";

								$apvdtls = vApvdtl::find_by_sql($sql);
								global $database;
								//echo $database->last_query;
								//echo $project->descriptor .'  <em>('. number_format($project->amount,2) .')</em><br>';								
								echo '<div class="panel panel-default">';
                       			echo '<div class="panel-heading">';
                            	echo '<h4 class="panel-title"><span class="glyphicon ';
								echo $project->type == 2 ? 'glyphicon-tower':'glyphicon-home';
								echo '"></span> ';
								echo '<a href="#collapse-'.$project->id.'" class="collapsed" data-parent="#project-list" data-toggle="collapse">'.$project->descriptor.'</a> ';
								//echo '<span class="badge">'.$database->affected_rows().'</span>';
								echo '<span class="pull-right tot">'.number_format($project->amount,2).'</span>';
                        		echo '</h4></div>';
								echo '<div id="collapse-'.$project->id.'" class="panel-collapse collapse" style="height: 0px;">';
								echo '<div class="panel-body project-list-parent">';
									
									echo '<div id="account-list-'.$project->id.'" class="panel-group panel-group-child">';
									foreach($apvdtls as $apvdtl){
										$sql = "SELECT * FROM vapvdtl WHERE projectid='".$project->id."' "; 
										$sql .= "AND accountid = '".$apvdtl->accountid."' ORDER BY date";
										$apvdtls1 = vApvdtl::find_by_sql($sql);
										
										
										echo '<div class="panel panel-default">';
										echo '<div class="panel-heading panel-'.$apvdtl->accountid.'">';
										echo '<h4 class="panel-title">';
										echo '<a href="#collapse-'.$project->id.'-'.$apvdtl->accountid.'" class="collapsed" data-parent="#account-list-'.$project->id.'" data-toggle="collapse">'.$apvdtl->account.'</a> ';
										echo '<span class="badge">'.$database->affected_rows().'</span>';
										echo '<span class="pull-right tot">'.number_format($apvdtl->amount,2).'</span>';
										echo '</h4></div>';
										echo '<div id="collapse-'.$project->id.'-'.$apvdtl->accountid.'" class="panel-collapse collapse" style="height: 0px;">';
										echo '<div class="panel-body">';
										
										
										echo '<div><table class="table table-striped apv-list">';
										//echo '<thead><tr><th>APV Ref No</th><th>Date</th><th>Amount</th></tr></thead>';
										echo '<tbody>';
										foreach($apvdtls1 as $apvdtl1){
											echo '<tr>';
											//echo '<td>'.$apvdtl1->account .'</td>';
											echo '<td><a href="http://mfi.com/transactions/accounts-payable-print/'.$apvdtl1->apvhdrid.'" target="_blank">'.$apvdtl1->refno .'</a></td>';
											echo '<td>'. date('F j, Y', strtotime($apvdtl1->date)) .'</td>';
											echo '<td style="text-align:right;">'. number_format($apvdtl1->amount,2) .'</td>';	
											echo '</tr>';
										}	
										echo '<tbody></table></div>';
										
										
										echo '</div></div></div>';
										
									}	
									echo '</div>';
									
								
								/*
								echo '<div><table class="table table-striped tb-data">';
								echo '<thead><tr><th>Account</th><th>APV Ref No</th><th>Date</th><th>Amount</th></tr></thead><tbody>';
								foreach($apvdtls as $apvdtl){
									echo '<tr>';
									echo '<td>'.$apvdtl->account .'</td>';
									echo '<td><a href="http://mfi.com/transactions/accounts-payable-print/'.$apvdtl->apvhdrid.'" target="_blank">'.$apvdtl->refno .'</a></td>';
									echo '<td>'.$apvdtl->date .'</td>';
									echo '<td style="text-align:right;">'. number_format($apvdtl->amount,2) .'</td>';	
									echo '</tr>';
								}	
								echo '<tbody></table></div>';
								*/
								
								echo '</div></div></div>';
							}
					   
					   ?>   	
               		</div> <!-- end .panel-group -->
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
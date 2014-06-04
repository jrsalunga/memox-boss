<?php
require_once('../../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("../login"): "";
if(isset($_GET['fr']) && isset($_GET['to'])){
	$dr = new DateRange($_GET['fr'],$_GET['to'], false);
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
<link rel="shortcut icon" type="image/x-icon" href="../images/memoxpress-favicon.jpg" />
<title>Check Voucher Schedule - Bank</title>

<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/styles-ui2.css">

<script src="../js/vendors/jquery-1.10.1.min.js"></script>
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<script src="../js/common.js"></script>
<script type="application/javascript">
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

    $("table.table").fixMe({
        container: '.gutter'
    });

});
</script>    
<style>

* {
	padding: 0;
	margin: 0
}



.prn-body,
.prn-header {
	margin: 0 20px;
}
.prn-header h1 {
	font-size:20px;
}
table.floatThead-table {
    background-color: #FFF;
}

.table td {
	/*white-space:nowrap;*/
}
</style>
<style media="screen">
body {
 padding-top: 70px;	
}
.gutter {
	border-bottom: 1px solid #E5E5E5;
	height: 52px;
	background-color: #F2F3F2;
	position: fixed;
	width: 100%;
	top: 0;
	z-index: 100;
}
.btn-grp {
	display: inline-block;
	line-height: 3.4;
	margin: 0 20px;	
}
.hide-me {
	display: none;
}
</style>
<style media="print">
.gutter {
	display:none;
}
table {
	font-size: 11px;
}

.table > tbody > tr > td {
	padding: 3px;
	line-height: 1.42857143;
	vertical-align: top;
	border-top: 1px solid #ddd;
}
</style>
</head>
<body>
<div class="gutter">
	<div class="row">
        <div class="col-md-7">
            <div class="btn-grp">
            	<a type="button" class="btn btn-default" href="cv-sched-raw">
                    <span class="glyphicon glyphicon-unshare"></span>
                    Back
                </a>
                <!--
                <button type="button" class="btn btn-default" onClick="window.history.back()">
                    <span class="glyphicon glyphicon-unshare"></span>
                    Back
                </button>
                -->
                <button type="button" class="btn btn-default" onClick="window.print()">
                    <span class="glyphicon glyphicon-print"></span>
                    Print
                </button>
                <!--
                <div class="btn-group">
                	<a class="btn btn-default" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>"><span class="glyphicon glyphicon-floppy"></span> All</a>
                    <a class="btn btn-default" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=0"><span class="glyphicon glyphicon-floppy-remove"></span> Unposted</a>
                    <a class="btn btn-default" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=1"><span class="glyphicon glyphicon-floppy-saved"></span> Posted</a>
            	</div>
                <div class="btn-group">
                    <a class="btn btn-default" title="previous date range" href="?fr=<?=$dr->fr_prev_day()?>&to=<?=$dr->to_prev_day()?>">
                    	<span class="glyphicon glyphicon-backward"></span> Prev
                   	</a>
                    <a class="btn btn-default" title="next date range" href="?fr=<?=$dr->fr_next_day()?>&to=<?=$dr->to_next_day()?>">
                    	 Next <span class="glyphicon glyphicon-forward"></span>
                 	</a>
                </div>
                -->
            </div>
        </div>
        <!--
        <div class="col-md-4 datepick">
        	<div class="btn-grp">
            	<div class="btn-group">
                	<a class="btn btn-default" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>"><span class="glyphicon glyphicon-floppy"></span> All</a>
                    <a class="btn btn-default" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=0"><span class="glyphicon glyphicon-floppy-remove"></span> Unposted</a>
                    <a class="btn btn-default" href="?fr=<?=$dr->fr?>&to=<?=$dr->to?>&posted=1"><span class="glyphicon glyphicon-floppy-saved"></span> Posted</a>
                
            	</div>
            </div>
        </div>
        -->
        <div class="col-md-5 datepick">
        	<div class="btn-grp pull-right">
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
        </div>
    </div>
	
</div>
<div class="prn-header">
	<h1>Check Voucher Schedule - Bank</h1>
</div>
<div class="prn-body">
	<?php
    						$banks = Bank::find_all();
    					?>
                    	<table class="table table-bordered table-hover">
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
    									echo '<td>'.$date->format("M j, Y").'</td>';
    									$tot = 0;
    									foreach($banks as $bank){
    										$sql = "SELECT SUM(amount) as amount FROM cvchkdtl ";
    										$sql .= "WHERE checkdate = '".$currdate."' ";
    										$sql .= "AND bankacctid = '".$bank->id."'";
    										$cvchkdtl = Cvchkdtl::find_by_sql($sql); 
    										$cvchkdtl = array_shift($cvchkdtl);
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
</body>
</html>
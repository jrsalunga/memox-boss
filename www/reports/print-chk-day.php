<?php
require_once('../../lib/initialize.php');
!$session->is_logged_in() ? redirect_to("../login"): "";
if(isset($_GET['fr']) && isset($_GET['to'])){
	$dr = new DateRange($_GET['fr'],$_GET['to'], false);
} else {
	$dr = new DateRange(NULL,NULL,false);	
}
if(isset($_GET['ref']) && $_GET['ref']=='print'){
	$uri = parse_url($_SERVER['REQUEST_URI']);
	$uri = parse_str($uri['query']);
	$back_uri = 'chk-day?fr='.$fr.'&to='.$to;
} else {
	$back_uri = 'chk-day';
}
?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Check Breakdown</title>

<link rel="stylesheet" href="../css/bootstrap.css">
<link rel="stylesheet" href="../css/styles-ui2.css">

<script src="../js/vendors/jquery-1.10.1.min.js"></script>
<script src="../js/vendors/jquery-ui-1.10.3.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/floatthead/1.2.7/jquery.floatThead.min.js"></script>
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
	
    function pageTop(){
  		return $(".gutter").height();
	}
	/*
	$('table.table').floatThead({
    	scrollingTop: pageTop,
     	useAbsolutePositioning: false
  	});
	*/
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
	font-size: 12px;
}
</style>
</head>
<body>
<div class="gutter">
	<div class="row">
        <div class="col-md-7">
            <div class="btn-grp">
            	<a type="button" class="btn btn-default" href="<?=$back_uri?>">
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
	<h1>Check Breakdown</h1>
</div>
<div class="prn-body">
	<table class="table table-bordered">
        <thead>
            <tr>
            <?php
                echo '<th>Day(s)</th><th>CV Ref No</th><th>Bank</th><th>Check No</th><th>Payee</th><th>Check Amount</th>';
            ?>
            </tr>
        </thead>
        <tbody>
            <?php
                foreach($dr->getDaysInterval() as $date){
                    $currdate = $date->format("Y-m-d");
                    echo '<tr>';
                    
                    $sql = "SELECT * FROM vcvchkdtl ";
                    $sql .= "WHERE checkdate = '".$currdate."' ";
					if(isset($_GET['posted']) && ($_GET['posted']==1 || $_GET['posted']==0)){
						$sql .= "AND posted = '".$_GET['posted']."' ";
					} 
                    $sql .= "ORDER BY bankcode ASC, payee";
                    $cvchkdtls = vCvchkdtl::find_by_sql($sql); 
                    global $database;
                    $len = count($cvchkdtls);
                    
                    if($len > 0){
                        echo '<td rowspan="'.$len.'">';
                        echo $date->format("M j, Y").'<div>'.$date->format("l").'<div></td>';
                        foreach($cvchkdtls as $cvchkdtl){
							echo '<td class="bnk-'.$cvchkdtl->bankcode.'">'.$cvchkdtl->refno.'</td>';
							echo '<td class="bnk-'.$cvchkdtl->bankcode.'" title="'.$cvchkdtl->bank.'">'.$cvchkdtl->bankcode.'</td>';
                            echo '<td class="bnk-'.$cvchkdtl->bankcode.'" >'.$cvchkdtl->checkno;
							if($cvchkdtl->posted==1){
								echo '<span class="glyphicon glyphicon-ok-circle pull-right" style="color:#5cb85c;" title="posted"></span>';
							} else {
								echo '<span class="glyphicon glyphicon-remove-circle pull-right" style="color:#f0ad4e;" title="unposted"></span>';
							}
							echo '</td>';
                            echo '<td class="bnk-'.$cvchkdtl->bankcode.'" >'.$cvchkdtl->payee.'</td>';
                            echo '<td class="bnk-'.$cvchkdtl->bankcode.'"  style="text-align:right;">'.number_format($cvchkdtl->amount,2).'</td></tr>';
                        }
                    } else {
                        echo '<td>'.$date->format("M j, Y").'<div><em>'.$date->format("l").'</em></div></td><td>-</td><td>-</td><td>-</td><td>-</td><td>-</td></tr>';
                    }
                    
                    
                    
                    //echo $database->last_query;
                    
                    
                    echo '</tr>';
                }
            ?>
        </tbody>
    </table>
</div>
</body>
</html>
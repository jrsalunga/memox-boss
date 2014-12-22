<?php
include_once('../../lib/initialize.php');
include_once('../../classes/class.cleanurl.php');
#error_reporting(E_ALL);
//ini_set('display_errors','On');
$cleanUrl->setParts('apvhdrid');

//echo $apvhdrid;
if(is_uuid($apvhdrid)){
	$apvhdr = vApvhdr::find_by_id($apvhdrid);
	if(!$apvhdr){
		$apvhdr = vApvhdr::first('refno');
	}
} else {
	$apvhdr = vApvhdr::first('refno');
}
//$apvhdr = vApvhdr::find_by_id($apvhdrid);
//global $database;
//echo $database->last_query;
//echo var_dump($cvhdr);


?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<title>Accounts Payable : <?=$apvhdr->refno?></title>
<link rel="shortcut icon" type="image/x-icon" href="../images/memoxpress-favicon.jpg" />

<link rel="stylesheet" href="/css/print.css">

<script src="/js/vendors/jquery-1.10.1.min.js"></script>
<script src="/js/vendors/jquery-ui-1.10.3.js"></script>

<script language="javascript">
function floatMe(){
	set = $(document).scrollTop()+25;
	$('#settings-dialog').animate({top:set+'px'},{duration:0,queue:false});
}
function fixPrintSettings(){
	if(parseInt($(document).width()) <= 1100){
		$('.print-preview').html('<span class="glyphicon glyphicon-print"></span>');
		$('.previous a').html('<span class="glyphicon glyphicon-chevron-left"></span>').css('margin-bottom','10px');
		$('.next a').html('<span class="glyphicon glyphicon-chevron-right"></span>');
		$('.pager li').css('display', 'block');
		//console.log('fix');
	} else {
		$('.print-preview').html('<span class="glyphicon glyphicon-print"></span> Print Preview');
		$('.previous a').html('Prev');
		$('.next a').html('Next');
		$('.pager li').css('display', 'inline');
	}	
}
$(document).ready(function(){
	floatMe();
	$(window).scroll(floatMe);
	fixPrintSettings();
	$(window).resize(fixPrintSettings);
});
</script>


<style media="screen">
#page-wrap {
    background-color: #FFFFFF;
    margin-left: auto;
    margin-right: auto;
    width: 814px;
    position:relative;
    
    border: 1px solid #888888;
    margin-top: 20px;
    margin-bottom: 30px;
    
    min-height: 1046px;
    
    
    -webkit-box-shadow:rgba(0, 0, 0, 0.496094) 0 0 10px;
	-moz-box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
  
}
</style>
<style media="print">
#page-wrap {
    background-color: #FFFFFF;
    margin-left: auto;
    margin-right: auto;
    width: 814px;
    position:relative;
    
    margin-top: 0;
    margin-bottom: 0;
    /*
    border: none;
    height: 1046px;
	*/
/*	border: 1px solid #F00; */
    min-height: 1054px;
}

#settings-dialog {
	display:none;
}
#footer.bottom {
    position: relative;
	color: #000;
}
</style>


</head>
<body>


<div id="page-wrap">
	<div class="isposted" style="visibility: <?=$apvhdr->posted==1?"visible":"hidden"?>">
    	<h1>Posted</h1>
    </div>
    <div class="iscancelled" style="visibility: <?=$apvhdr->cancelled==1?"visible":"hidden"?>">
    	<h1>Cancelled</h1>
    </div>
    <div id="header">
    	<div id="main-logo">
            <img src="<?=$relativeslash?>../images/memoxpress.png" />
        </div>
    	<div id="header-wrap">
        	
        	<h2>MemoXpress</h2>
            <p>Pacific Center Bldg, Quintin Paredes St., Manila</p>
            <h1 class="reportLabel">Accounts Payable Voucher</h1>
        </div>		
    </div>
    <div id="body">
   		<div id="m-container">
   			<div id="hdr">
            	<div id="supplier-title">
                <?php
					#$location = Location::find_by_id($apvhdr->locationid);
				?>
                <div></div>
                </div>           	
                <table id="meta">
                	<tbody>
                    	<tr>
                        	<td>Reference #</td><td><?=$apvhdr->refno?></td>
                        </tr>
                        <tr>
                        	<td>Date</td><td><?=short_date($apvhdr->date)?></td>
                        </tr>
                        <tr>
                        	<td>Due</td><td><?=short_date($apvhdr->due)?></td>
                        </tr>
                        <tr>
                        	<td>Supplier</td><td><?=Supplier::row($apvhdr->supplierid,1)?></td>
                        </tr>
                        <tr>
                        	<td>Supplier Ref #</td><td><?=$apvhdr->supprefno?></td>
                        </tr>
                        <tr>
                        	<td>PO Ref #</td><td><?=$apvhdr->porefno?></td>
                        </tr>
                    </tbody>
                </table>
                <div style="clear:both"></div>
            </div>
            <table id="items">
            	<thead>
                	<tr>
                    	<th>Code </th>
                        <th colspan="2">Description</th>             
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
					$items = Apvdtl::find_all_by_field_id('apvhdr',$apvhdr->id);
					foreach($items as $item){
						$item_code = Account::row($item->accountid,0);
						$item_descriptor = Account::row($item->accountid,1);
						
						echo "<tr>";
						echo "<td>". $item_code ."</td><td colspan='2'>". uc_first($item_descriptor)."</em></td><td>&#8369; ". number_format($item->amount,2) ."</td>";
						echo "</tr>";
					}				
					?>
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total Amount</td>
                        <td class="total-value">&#8369; <?=number_format($apvhdr->totamount,2)?></td>
                    </tr>
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total Debit</td>
                        <td class="total-value">&#8369; <?=number_format($apvhdr->totdebit,2)?></td>
                    </tr>
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total Credit</td>
                        <td class="total-value">&#8369; <?=number_format($apvhdr->totcredit,2)?></td>
                    </tr>
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Balance</td>
                        <td class="total-value">&#8369; <?=number_format($apvhdr->balance,2)?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
        <div style="margin: 0 20px 50px;"><strong>Notes:</strong> <em><?=$apvhdr->notes?></em></div>
    </div>
    <div id="footer" class="bottom">
    	<div>
        <?php
			$cvapvdtl = vCvapvdtl::find_by_field_id('apvhdr', $apvhdr->id);
			if(!$cvapvdtl){
				echo '<p>no check voucher</p>';
			} else {
				$cvhdr = Cvhdr::find_by_id($cvapvdtl->cvhdrid);
				echo '<p>with check voucher ref no <a href="/reports/check-print/'.$cvhdr->id.'" target="_blank">'.$cvhdr->refno.'</a> ';
				echo $cvhdr->posted==1 ? '<span title="Posted" class="glyphicon glyphicon-posted-bw"></span></p>':'<span title="Unposted" class="glyphicon glyphicon-unposted-bw"></span></p>';	
			}
		?>
        </div>
    </div>
</div>
<div id="settings-dialog" class="show">
	<div>
    	<a href="javascript:window.print()" class="btn btn-default print-preview">
        <span class="glyphicon glyphicon-print"></span> 
        Print Preview</a>
  	</div>
    <?php
		$n = Apvhdr::next('refno', $apvhdr->refno);
		$p = Apvhdr::previous('refno', $apvhdr->refno);
	?>
    
    <div class="pager-c">
    	<ul class="pager">
          <li class="previous">
          	<?=$p?'<a href="/reports/accounts-payable-print/'.$p->id.'">Prev</a>':'<span class="disabled">Prev</span>'?>
          </li>
          <li class="next">
          	<?=$n?'<a href="/reports/accounts-payable-print/'.$n->id.'">Next</a>':'<span class="disabled">Next</span>'?>
          </li>
        </ul>
    </div>
    
</div>
</body>
</html>
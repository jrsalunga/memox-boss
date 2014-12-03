<?php
include_once('../../lib/initialize.php');
include_once('../../classes/class.cleanurl.php');
#error_reporting(E_ALL);
ini_set('display_errors','On');
$cleanUrl->setParts('cvhdrid');

//echo is_uuid($cvhdrid) ? 'uid ':'not uid ';
if(is_uuid($cvhdrid)){
	$cvhdr = vCvhdr::find_by_id($cvhdrid);
	if(!$cvhdr){
		$cvhdr = vCvhdr::first('refno');
	}
} else {
	$cvhdr = vCvhdr::first('refno');
}
//global $database;
//echo $database->last_query;
//echo var_dump($cvhdr);


?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<title>Check Voucher : <?=$cvhdr->refno?></title>
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
</style>


</head>
<body>


<div id="page-wrap">
	<div class="isposted" style="visibility: <?=$cvhdr->posted==1?"visible":"hidden"?>">
    	<h1>Posted</h1>
    </div>
    <div class="iscancelled" style="visibility: <?=$cvhdr->cancelled==1?"visible":"hidden"?>">
    	<h1>Cancelled</h1>
    </div>
    <div id="header">
    	<div id="main-logo">
            <img src="<?=$relativeslash?>../images/memoxpress.png" />
        </div>
    	<div id="header-wrap">
        	
        	<h2>MemoXpress</h2>
            <p>Pacific Center Bldg, Quintin Paredes St., Manila</p>
            <h1 class="reportLabel">Check Voucher</h1>
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
                        	<td>Reference #</td><td><?=$cvhdr->refno?></td>
                        </tr>
                        <tr>
                        	<td>Date</td><td><?=short_date($cvhdr->date)?></td>
                        </tr>
                        <tr>
                        	<td>Supplier</td><td><?=Supplier::row($cvhdr->supplierid,1)?></td>
                        </tr>
                    	<tr>
                        	<td>Payee</td><td><?=$cvhdr->payee?></td>
                        </tr>
                    </tbody>
                </table>
                <div style="clear:both"></div>
            </div>
            <table id="cvapvdtl" class="items">
            	<thead>
                	<tr>
                    	<th>APV Refno </th>
                        <th>Due </th>
                        <th>Supplier Ref # </th>
                 		<th>PO Ref # </th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
					
					$cvapvdtls = vCvapvdtl::find_all_by_field_id('cvhdr',$cvhdr->id);
					
					//echo json_encode($database->last_query);
					$totapvamt = 0;
					/*
					foreach($cvapvdtls as $cvapvdtl){
						//$code = Apvhdr::row($cvapvdtl->apvhdrid,0);
						echo "<tr>";
						echo "<td colspan='2'><a style=\"text-decoration: none; color: #000;\" target=\"_blank\" href=\"/reports/accounts-payable-print/". $cvapvdtl->apvhdrid ."\">";
						echo  $cvapvdtl->refno ."</a></td><td colspan='2' style='width: 200px; text-align: right;'>&#8369; ". number_format($cvapvdtl->amount,2) ."</td>";
						echo "</tr>";
						$totapvamt = $totapvamt + $cvapvdtl->amount;
					}
					*/
					//echo json_encode($items);
					
					foreach($cvapvdtls as $cvapvdtl){
						//$code = Apvhdr::row($cvapvdtl->apvhdrid,0);
						echo "<tr>";
						echo "<td><a style=\"text-decoration: none; color: #000;\" target=\"_blank\" href=\"/reports/accounts-payable-print/". $cvapvdtl->apvhdrid ."\">";
						
                        /*
                        $apvdtl = Apvdtl::find_by_field_id('apvhdr', $cvapvdtl->apvhdrid);

                        global $database;
                        //echo $database->last_query;

                        if(isset($apvdtl)){
                            $account = Account::row($apvdtl->accountid, 0, TRUE);
                            $acct_desc = $account->descriptor;
                            $acct_code = $account->code;
                        } else {
                            $acct_desc = ' ';
                            $acct_code = ' ';
                        }
                        */
                        
                        echo  $cvapvdtl->refno ;
                       // echo '<span title="'.$acct_desc.'">'.$acct_code."</span></a></td>";
						echo '<td>'.date('m/d/Y', strtotime($cvapvdtl->due)).'</td>';
						echo '<td>'.$cvapvdtl->supprefno.'</td>';
						echo '<td>'.$cvapvdtl->porefno.'</td>';
						echo "<td>". number_format($cvapvdtl->amount,2) ."</td>"; //&#8369;
						echo "</tr>";
						$totapvamt = $totapvamt + $cvapvdtl->amount;
					}
					?>
                    <tr>
                    	<!--
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        -->
                        <td class="total-line" colspan="4">Total APV Amount</td>
                        <td class="total-value">
						<span
                        <?=(round($cvhdr->totapvamt,2)!=round($totapvamt,2))?'style="color:red;" title="not balance on the total of  APVs individual amount: '. number_format($totapvamt,2).'"':'title="AP total individual amount: '. number_format($totapvamt,2).'"'?>
                        >&#8369;</span>
						<?=number_format($cvhdr->totapvamt,2)?></td>
                    </tr>
       				
                </tbody>
            </table>
            
            
            
            <table id="cvchkdtl" class="items">
            	<thead>
                	<tr>
                    	<th>Bank / Acct No </th>
                        <th>Check No </th>
                 		<th>Check Date </th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
					
					$cvchkdtls = Cvchkdtl::find_all_by_field_id('cvhdr',$cvhdr->id);
					
					
					$totchkamt = 0;
					foreach($cvchkdtls as $cvchkdtl){
						$bank = Bank::find_by_id($cvchkdtl->bankacctid);
						//echo json_encode($bank);
						echo "<tr>";
						echo "<td>". $bank->code .' / '. $bank->acctno ."</td>";
						echo "<td>". $cvchkdtl->checkno ."</td>";
						echo "<td>". date('m/d/Y', strtotime($cvchkdtl->checkdate)) ."</td>";
						echo "<td>". number_format($cvchkdtl->amount,2) ."</td>"; //&#8369; 
						echo "</tr>";
						$totchkamt = $totchkamt + $cvchkdtl->amount;
					}
					
					//echo json_encode($items);
					
	
					
					?>
      
                    <tr>
                    	<!--
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        -->
                        <td class="total-line" colspan="3">Total Check Amount</td>
                        <td class="total-value">
                        <span
                        <?=(round($cvhdr->totchkamt,2)!=round($totchkamt,2))?'style="color:red;" title="not balance on the total of CHKs individual amount: '. number_format($totchkamt,2).'"':'title="CV total individual amount: '. number_format($totchkamt,2).'"'?>
                        >&#8369;</span>
                         <?=number_format($cvhdr->totchkamt,2)?></td>
                    </tr>
         
                </tbody>
            </table>
    	</div>
        <div style="margin: 0 30px;"><strong>Notes:</strong> <em><?=$cvhdr->notes?></em></div>
    </div>
    <div id="footer">
    	<div>&nbsp;</div>
    </div>
    
    
    <!--
    <div style="position:absolute; bottom: 0px;">
    	test
    </div>
    -->
</div>
<div id="settings-dialog" class="show">
	<div>
    	<a href="javascript:window.print()" class="btn btn-default print-preview" title="Print Preview">
        <span class="glyphicon glyphicon-print"></span> 
        Print Preview</a>
  	</div>
    
   
    <?php
	$n = Cvhdr::next('refno', $cvhdr->refno);
	$p = Cvhdr::previous('refno', $cvhdr->refno);
	?>
    
    <div class="pager-c">
    	<ul class="pager">
          <li class="previous">
          	<?=$p?'<a href="/reports/check-print/'.$p->id.'" title="Previous Record">Prev</a>':'<span class="disabled">Prev</span>'?>
		  </li>
          <li class="next">
          	<?=$n?'<a href="/reports/check-print/'.$n->id.'" title="Next Record">Next</a>':'<span class="disabled">Next</span>'?>         
          </li>
        </ul>
    </div>
    
    
    
</div>
</body>
</html>
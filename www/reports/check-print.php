<?php
include_once('../../lib/initialize.php');
include_once('../../classes/class.cleanurl.php');
#error_reporting(E_ALL);
ini_set('display_errors','Off');
$cleanUrl->setParts('cvhdrid');

#echo $apvhdrid;
$cvhdr = vCvhdr::find_by_id($cvhdrid);



?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<title>Check Voucher : <?=$cvhdr->refno?></title>

<link rel="stylesheet" href="../css/print.css">
<link rel="stylesheet" href="<?=$relativeslash?>../css/print.css">


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
    
    height: 1046px;
    
    
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
    height: 1054px;
}
</style>


</head>
<body>


<div id="page-wrap">
	<div class="isposted" style="visibility: <?=$apvhdr->posted==1?"visible":"hidden"?>">
    	<h1>Posted</h1>
    </div>
    <div id="header">
    	<div id="main-logo">
            <img src="<?=$relativeslash?>../images/mfi-logo.jpg" />
        </div>
    	<div id="header-wrap">
        	
        	<h2>ModularFusion Inc</h2>
            <p>1763 Paz M. Guanzon Street, Paco, 1007 Manila</p>
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
                <div><?=$location->code?></div>
                
                
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
            <table id="items" class="cvapvdtl">
            	<thead>
                	<tr>
                    	<th colspan="2">APV Refno </th>
                        
                 
                        <th colspan="2">Amount</th>
                    </tr>
                </thead>
                <tbody>
                	<?php
					
					$cvapvdtls = Cvapvdtl::find_all_by_field_id('cvhdr',$cvhdrid);
					
					//echo json_encode($database->last_query);
					
					foreach($cvapvdtls as $cvapvdtl){
						$code = Apvhdr::row($cvapvdtl->apvhdrid,0);
						
						
						echo "<tr>";
						echo "<td colspan='2'><a style=\"text-decoration: none; color: #000;\" target=\"_blank\" href=\"/reports/accounts-payable-print/". $cvapvdtl->apvhdrid ."\">";
						echo  $code ."</a></td><td colspan='2' style='width: 200px; text-align: right;'>". number_format($cvapvdtl->amount,2) ."</td>";
						echo "</tr>";
					}
					
					//echo json_encode($items);
					
	
					
					?>
      
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total APV Amount</td>
                        <td class="total-value"><?=number_format($cvhdr->totapvamt,2)?></td>
                    </tr>
       
                </tbody>
            </table>
            
            
            
            <table id="items" class="cvchkdtl">
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
					
					$cvchkdtls = Cvchkdtl::find_all_by_field_id('cvhdr',$cvhdrid);
					
					
					
					foreach($cvchkdtls as $cvchkdtl){
						
						$bank = Bank::find_by_id($cvchkdtl->bankacctid);
						//echo json_encode($bank);
						
						echo "<tr>";
						echo "<td>". $bank->code .' / '. $bank->acctno ."</td><td>". $cvchkdtl->checkno ."</td><td>". $cvchkdtl->checkdate ."</td><td>". number_format($cvchkdtl->amount,2) ."</td>";
						echo "</tr>";
					}
					
					//echo json_encode($items);
					
	
					
					?>
      
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total Check Amount</td>
                        <td class="total-value"><?=number_format($cvhdr->totchkamt,2)?></td>
                    </tr>
         
                </tbody>
            </table>
    	</div>
        <div style="margin: 0 30px;"><strong>Notes:</strong> <em><?=$cvhdr->notes?></em></div>
    </div>
    <div id="footer">
    	<div>&nbsp;</div>
    </div>
</div>

</body>
</html>
<?php
include_once('../../lib/initialize.php');
include_once('../../classes/class.cleanurl.php');
#error_reporting(E_ALL);
ini_set('display_errors','Off');
$cleanUrl->setParts('apvhdrid');

#echo $apvhdrid;
$apvhdr = Apvhdr::find_by_id($apvhdrid);
#echo var_dump($apvhdr);


?>
<!DOCTYPE HTML>
<html lang="en-ph">
<head>
<meta charset="utf-8">
<title>Accounts Payable : <?=$apvhdr->refno?></title>

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
                <div><?=$location->code?></div>
                
                
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
					
					$items = Apvdtl::find_all_by_field_id('apvhdr',$apvhdrid);
					
					//global $database;
					//echo $database->last_query.'<br>';
					
					foreach($items as $item){
						$item_code = Account::row($item->accountid,0);
						$item_descriptor = Account::row($item->accountid,1);
						$prj = Project::row($item->projectid,0);
						$p = $item->type == 1 ? '('.$prj.')' : '';
						
						echo "<tr>";
						echo "<td>". $item_code ."</td><td colspan='2'>". uc_first($item_descriptor) .' <em>'. $p ."</em></td><td>". number_format($item->amount,2) ."</td>";
						echo "</tr>";
					}
					
					//echo json_encode($item);
					
	
					
					?>
      
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total Amount</td>
                        <td class="total-value"><?=number_format($apvhdr->totamount,2)?></td>
                    </tr>
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total Debit</td>
                        <td class="total-value"><?=number_format($apvhdr->totdebit,2)?></td>
                    </tr>
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Total Credit</td>
                        <td class="total-value"><?=number_format($apvhdr->totcredit,2)?></td>
                    </tr>
                    <tr>
                    	<td class="blank" colspan="0"></td>
                        <td class="blank" colspan="0"></td>
                        <td class="total-line" colspan="0">Balance</td>
                        <td class="total-value"><?=number_format($apvhdr->balance,2)?></td>
                    </tr>
                </tbody>
            </table>
    	</div>
        <div style="margin: 0 30px;"><strong>Notes:</strong> <em><?=$apvhdr->notes?></em></div>
    </div>
    <div id="footer">
    	<div>&nbsp;</div>
    </div>
</div>

</body>
</html>
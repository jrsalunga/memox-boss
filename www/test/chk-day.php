

<?php
include_once('../../lib/initialize.php');
ini_set('display_errors','Off');



$cvchkdtls = vCvchkdtl::find_by_date_with_bankid($currdate,$bankid,$posted,$supplierid);

if(isset($_GET['fr']) && isset($_GET['to'])){
    sanitize($_GET);
    $dr = new DateRange($_GET['fr'],$_GET['to'], false);
} else {
    $dr = new DateRange(NULL,NULL,false);   
}
	

$uri = explode('?',$_SERVER['REQUEST_URI']);
$qs = !empty($uri[1]) ? '?'.$uri[1] : '?fr='.$dr->fr.'&to='.$dr->to;

function summaryReportPerDays($datas,$uf='id'){
	$arr = array();
	$chkctr=0;
	foreach($datas as $data){
		//$arr[] = (array) $data;
		
		if(array_key_exists($data->bankcode, $arr)) {
			$arr[$data->{$uf}]['totamt'] +=  $data->amount;
		} else {
			$arr[$data->{$uf}]['totamt'] =  $data->amount;
			$arr[$data->{$uf}]['checkdate'] = $data->checkdate;

		}
	}
	
	return $arr;
	
	
	
}


$posted = (isset($_GET['posted']) && ($_GET['posted']==1 || $_GET['posted']==0)) ? $_GET['posted']:NULL;
$bankid = (isset($_GET['bankid']) && is_uuid($_GET['bankid'])) ? $_GET['bankid']:NULL;
$supplierid = (isset($_GET['supplierid']) && is_uuid($_GET['supplierid'])) ? $_GET['supplierid']:NULL;
	//echo '[';

	foreach($dr->getDaysInterval() as $date){
		$currdate = $date->format("Y-m-d");
	
		$cvchkdtls = vCvchkdtl::find_by_date_with_bankid($currdate,$bankid,$posted,$supplierid);
		//global $database;
		
		//echo $database->last_query.'<br>';
		if($cvchkdtls){
			
			$rs = summaryReportPerDay($cvchkdtls, 'bankcode');
			$tot=0;
			foreach($rs as $k => $v){
				echo $k.' - '.$v['totamt'].'<br>';
				$tot += $v['totamt'];
				
			}
			echo $tot.'<br>';
		} 
		
		
	
	 
	
	} 
	//echo ']';	


?>

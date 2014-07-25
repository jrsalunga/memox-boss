

<?php
include_once('../../lib/initialize.php');
ini_set('display_errors','Off');

echo '<h3>cv</h3>';

$sql = "SELECT * FROM vcvhdr ORDER BY refno ASC";

$cvhdrs = vCvhdr::find_by_sql($sql);

foreach($cvhdrs  as $cvhdr){
	//echo $cvhdr->refno.' - ';
	//echo $cvhdr->id.' <br> ';
	
	$cvchkdtls = Cvchkdtl::find_all_by_field_id('cvhdr',$cvhdr->id);
	$totchkamt = 0;
	foreach($cvchkdtls as $cvchkdtl){
		$totchkamt = $totchkamt + $cvchkdtl->amount;
	}
	
	if($cvhdr->totchkamt == (string)$totchkamt){
		
	} else {
		echo $cvhdr->totchkamt.'-'.$totchkamt.' ';
		
		//echo gettype((float)$cvhdr->totchkamt).'-';
		//echo gettype($totchkamt);
		echo $cvhdr->refno.' - ';
		echo '<a href="/reports/check-print/'.$cvhdr->id.'" target="_blank">'.$cvhdr->id.'</a>';
		
		echo '<br> ';
	}
	
	
	
}



echo '<h3>apv</h3>';

foreach($cvhdrs  as $cvhdr){
	//echo $cvhdr->refno.' - ';
	//echo $cvhdr->id.' <br> ';
	
	$cvapvdtls = vCvapvdtl::find_all_by_field_id('cvhdr',$cvhdr->id);
	$totapvamt = 0;
	foreach($cvapvdtls as $cvapvdtl){
		//echo $cvapvdtl->amount.'<br>';
		$totapvamt = $totapvamt + $cvapvdtl->amount;
	}
	
	if($cvhdr->totapvamt==(string)$totapvamt){
		
	} else {
		echo $cvhdr->totapvamt.'-'.$totapvamt.' ';
		
		//echo gettype((float)$cvhdr->totchkamt).'-';
		//echo gettype($totchkamt);
		echo $cvhdr->refno.' - ';
		echo '<a href="/reports/check-print/'.$cvhdr->id.'" target="_blank">'.$cvhdr->id.'</a>';
		
		echo '<br> ';
	}
	
	
	
}








?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>test</title>
	<link rel="stylesheet" href="">

	
	
</head>
<body>
	
</body>
</html>
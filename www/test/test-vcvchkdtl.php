

<?php
include_once('../../lib/initialize.php');

$vcvchkdtl = vCvchkdtl::summary_by_date('2014-07-31');
global $database;
echo $database->last_query .'<br>';

echo $vcvchkdtl->amount; 


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
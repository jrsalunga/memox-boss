

<?php
include_once('../../lib/initialize.php');

#$apvhdr = Apvhdr::getLastNumber();


#echo var_export($apvhdr);

#$refno = 1;
#echo str_pad($refno, 10, "0", STR_PAD_LEFT);


$usr = new  User;

$usr->code = "<script>alert('code')</script>sad";
$usr->descriptor = "anything' OR 'x'='x";
//$usr->descriptor = "touch /home/guestbook/uploads/$tempfile";

echo $usr->save() ? 'saved!':mysql_error();

$x = "<script>alert('me')</script>";


?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<title>test</title>
	<link rel="stylesheet" href="">

	
	<?php 
	echo $x;
	?>
</head>
<body>
	
</body>
</html>
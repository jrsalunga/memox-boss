<?php
include_once('../../lib/initialize.php');




$sql = "DESCRIBE vxcvhdr";
$rows = $database->query($sql);

while($row = $database->fetch_row($rows)) {

	echo "'".$row[0]."' ,";
}

echo"<br><br>";


$rows = $database->query($sql);

while($row = $database->fetch_row($rows)) {
	echo $row[0]."<br>";

}

echo"<br><br><h2>Backbone default model</h2>";


$rows = $database->query($sql);

while($row = $database->fetch_row($rows)) {
	echo $row[0].": '',<br>";

}

?>
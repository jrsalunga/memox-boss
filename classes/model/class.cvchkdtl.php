<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');
require_once(ROOT.DS.'classes'.DS.'functions.php');

class Cvchkdtl extends DatabaseObject{
	
	protected static $table_name="cvchkdtl";
	protected static $db_fields = array('id', 'cvhdrid' ,'bankacctid' ,'checkno' ,'checkdate' ,'amount' );
	
	/*
	* Database related fields
	*/
	public $id;
	public $cvhdrid;
	public $bankacctid;
	public $checkno;
	public $checkdate;
	public $amount;
	
	
	public static function total_by_date_range($fr, $to){
		$range = new DateRange($fr, $to);
		
		$sql = "SELECT SUM(amount) as amount FROM cvchkdtl WHERE checkdate BETWEEN '".$fr."' AND '".$to."'";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
		
	}
	
	
	public static function total_status_by_date_range($fr, $to, $s){
		$range = new DateRange($fr, $to);
		
		$sql = "SELECT SUM(b.amount) as amount FROM cvhdr a, cvchkdtl b ";
		$sql .= "WHERE a.id = b.cvhdrid AND a.posted = '".$s."' AND b.checkdate BETWEEN '".$fr."' AND '".$to."'";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
		
	}
	


}




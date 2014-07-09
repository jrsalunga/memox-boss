<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vCvapvdtl extends DatabaseObject{
	
	protected static $table_name="vcvapvdtl";
	protected static $db_fields = array('amount' ,'id' ,'apvhdrid' ,'cvhdrid' ,'aprefno' ,'apdate' ,'apdue' ,'supplier' ,'supplierid' ,'supprefno' ,'porefno' ,'terms' ,'totamount' ,'apvnotes' ,'apposted' ,'apcancelled' ,'cvrefno' ,'cvdate' ,'totapvamt' ,'totchkamt' ,'cvnotes' ,'cvposted' ,'cvcancelled' );
	
	/*
	* Database related fields
	*/
	public $id;
	public $amount;
	public $apvhdrid;
	public $cvhdrid;
	public $aprefno;
	public $apdate;
	public $apdue;
	public $supplier;
	public $supplierid;
	public $supprefno;
	public $porefno;
	public $terms;
	public $totamount;
	public $apvnotes;
	public $apposted;
	public $apcancelled;
	public $cvrefno;
	public $cvdate;
	public $totapvamt;
	public $totchkamt;
	public $cvnotes;
	public $cvposted;
	public $cvcancelled;

	
	
	public static function find_all($order=NULL) {
		if(empty($order) || $order==NULL) {
			return parent::find_by_sql("SELECT * FROM ".static::$table_name. " ORDER BY checkdate DESC");
		} else {
			return parent::find_by_sql("SELECT * FROM ".static::$table_name." ".$order);
		}
  	}
	
	public static function find_all_by_field_id($field=0,$id=0) {
		if(!is_uuid($id) && $id==NULL) {
			return false;
		} else {
   			$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE {$field}id='{$id}' ORDER BY aprefno DESC");
			return !empty($result_array) ? $result_array : false;
		}
  	}
	
	
}


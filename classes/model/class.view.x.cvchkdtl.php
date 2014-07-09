<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vxCvchkdtl extends DatabaseObject{
	
	protected static $table_name="vxcvchkdtl";
	protected static $db_fields = array('checkno' ,'checkdate' ,'amount' ,'id' ,'cvrefno' ,'cvdate' ,'cvpayee' ,'cvnotes' ,'cvposted' ,'cvcancelled' ,'cvhdrid' ,'cvapvdtlamt' ,'cvapvdtlid' ,'aprefno' ,'apdate' ,'apdue' ,'apporefno' ,'apterms' ,'aptotamount' ,'apbalance' ,'apnotes' ,'apposted' ,'apcancelled' ,'apvhdrid' ,'apvdtlamt' ,'apvdtlid' ,'accountcode' ,'account' ,'accountid' ,'acctcatcode' ,'acctcat' ,'acctcatid' ,'bankcode' ,'bank' ,'bankacctno' ,'bankid' ,'suppliercode' ,'supplier' ,'supplierterms' ,'supplierbalance' ,'supplierid');
	
	/*
	* Database related fields
	*/
	public $checkno;
	public $checkdate;
	public $amount;
	public $id;
	public $cvrefno;
	public $cvdate;
	public $cvpayee;
	public $cvnotes;
	public $cvposted;
	public $cvcancelled;
	public $cvhdrid;
	public $cvapvdtlamt;
	public $cvapvdtlid;
	public $aprefno;
	public $apdate;
	public $apdue;
	public $apporefno;
	public $apterms;
	public $aptotamount;
	public $apbalance;
	public $apnotes;
	public $apposted;
	public $apcancelled;
	public $apvhdrid;
	public $apvdtlamt;
	public $apvdtlid;
	public $accountcode;
	public $account;
	public $accountid;
	public $acctcatcode;
	public $acctcat;
	public $acctcatid;
	public $bankcode;
	public $bank;
	public $bankacctno;
	public $bankid;
	public $suppliercode;
	public $supplier;
	public $supplierterms;
	public $supplierbalance;
	public $supplierid;

	
	
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
   			$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE {$field}id='{$id}' ORDER BY checkdate DESC");
			return !empty($result_array) ? $result_array : false;
		}
  	}
	
	
}


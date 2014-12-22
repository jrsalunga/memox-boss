<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vApvdtl extends DatabaseObject {
	
	protected static $table_name="vapvdtl";
	protected static $db_fields = array('id', 'apvhdrid' ,'accountid' ,'type' ,'projectid' ,'amount' ,'refno' ,'account' ,'account_code' ,'project' ,'date');
	
	/*
	* Database related fields
	*/
	public $id;
	public $apvhdrid;
	public $accountid;
	public $type;
	public $projectid;
	public $amount;
	public $refno;
	public $account;
	public $account_code;
	public $project;
	public $date;
	
	
	function __contruct(){
		$this->amount = number_format($this->amount	,2);
	}
	
	
	
	
	
	
}


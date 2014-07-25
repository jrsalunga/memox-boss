<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vxCvhdr extends DatabaseObject{
	
	protected static $table_name="vxcvhdr";
	protected static $db_fields = array('cvrefno' ,'cvdate' ,'cvpayee' ,'cvtotapvamt' ,'cvtotchkamt' ,'cvnotes' ,'cvposted' ,'cvcancelled' ,'cvhdrid' ,'cvapvdtlamt' ,'cvapvdtlid' ,'aprefno' ,'apdate' ,'apdue' ,'supplier' ,'suppliercode' ,'supplierid' ,'apporefno' ,'apterms' ,'aptotamount' ,'apbalance' ,'apnotes' ,'apposted' ,'apcancelled' ,'apvhdrid' ,'apvdtlamt' ,'apvdtlid' ,'accountcode' ,'account' ,'accountid' ,'acctcatcode' ,'acctcat' ,'acctcatid');
	
	/*
	* Database related fields
	*/
	public $cvrefno;
	public $cvdate;
	public $cvpayee;
	public $cvnotes;
	public $cvtotapvamt;
	public $cvtotchkamt;
	public $cvposted;
	public $cvcancelled;
	public $cvhdrid;
	public $cvapvdtlamt;
	public $cvapvdtlid;
	public $aprefno;
	public $apdate;
	public $apdue;
	public $supplier;
	public $suppliercode;
	public $supplierid;
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

	
	
	
	
	
	
	

	
}




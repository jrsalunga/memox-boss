<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vCvhdr extends DatabaseObject{
	
	protected static $table_name="vcvhdr";
	protected static $db_fields = array('id', 'refno' ,'date' ,'supplier', 'supplierid' ,'payee' ,'totapvamt' ,'totchkamt' ,'notes' ,'posted' ,'cancelled' ,'printctr' ,'totapvline' ,'totchkline');
	
	/*
	* Database related fields
	*/
	public $id;
	public $refno;
	public $date;
	public $supplier;
	public $supplierid;
	public $payee;
	public $totapvamt;
	public $totchkamt;

	public $posted;
	public $cancelled;
	public $notes;
	public $printctr;
	public $totapvline;
	public $totchkline;


	
	
	
	
	
	
	
	

	
}




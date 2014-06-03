<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class Cvapvdtl extends DatabaseObject{
	
	protected static $table_name="cvapvdtl";
	protected static $db_fields = array('id', 'cvhdrid' ,'apvhdrid' ,'amount' );
	
	/*
	* Database related fields
	*/
	public $id;
	public $cvhdrid;
	public $apvhdrid;
	public $amount;
	


}




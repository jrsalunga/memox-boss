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


	
	
	
	public static function status_with_supplier($posted=NULL){
		if($posted===TRUE || $posted==1){
			$sql = "SELECT b.descriptor, SUM(a.totchkamt) as totchkamt FROM cvhdr a ";
			$slq .= "INNER JOIN supplier b ON a.supplierid = b.id GROUP BY b.descriptor";
			
		} else if($posted===FALSE || $posted==0){
			$sql = "SELECT b.descriptor, SUM(a.totchkamt) as totchkamt FROM cvhdr a ";
			$slq .= "INNER JOIN supplier b ON a.supplierid = b.id GROUP BY b.descriptor";
		} else {
			$sql = "SELECT b.descriptor, SUM(a.totchkamt) as totchkamt FROM cvhdr a ";
			$slq .= "INNER JOIN supplier b ON a.supplierid = b.id GROUP BY b.descriptor";
		}
	}
		
	
	
	

	
}




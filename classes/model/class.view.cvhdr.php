<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vCvhdr extends DatabaseObject{
	
	protected static $table_name="vcvhdr";
	protected static $db_fields = array('id', 'refno' ,'date' ,'supplier', 'supplierid' ,'payee' ,'totapvamt' ,'totchkamt' ,'notes' ,'posted' ,'cancelled' ,'printctr' ,'totapvline' ,'totchkline', 'percentage', 'suppliercode');
	
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
	
	public $suppliercode;
	public $percentage;

	
	
	
	public static function status_with_group_supplier($fr, $to, $posted=NULL){
		if(isset($posted) && !is_null($posted)){
			$sql = "SELECT supplier, supplierid, SUM(totchkamt) as totchkamt, COUNT(refno) as printctr FROM vcvhdr WHERE posted = ".$posted;
			$sql .= " AND date BETWEEN '".$fr."' AND '".$to."' GROUP BY supplier";
		} else {
			$sql = "SELECT supplier, supplierid, SUM(totchkamt) as totchkamt, COUNT(refno) as printctr FROM vcvhdr ";
			$sql .= "WHERE date BETWEEN '".$fr."' AND '".$to."' GROUP BY supplier";
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}
	
	public static function status_with_supplier($supplierid, $fr, $to, $posted=NULL){
		if(isset($supplierid) && is_uuid($supplierid) && isset($posted) && !is_null($posted)){
			$sql = "SELECT * FROM vcvhdr WHERE posted = ".$posted;
			$sql .= " AND date BETWEEN '".$fr."' AND '".$to."' AND supplierid = '".$supplierid."' ORDER BY date DESC, refno DESC";
		} else if(isset($supplierid) && is_uuid($supplierid) && (!isset($posted) || is_null($posted))){
			$sql = "SELECT * FROM vcvhdr ";
			$sql .= "WHERE date BETWEEN '".$fr."' AND '".$to."' AND supplierid = '".$supplierid."' ORDER BY date DESC, refno DESC";
		} else {
			return false;
			exit;	
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}
	
	public static function group_by_supplier($fr, $to, $posted){
		if((!isset($fr) || !empty($fr)) && (!isset($to) || !empty($to))){
			$sql = "SELECT b.code as suppliercode, b.descriptor as supplier, a.supplierid, SUM(a.totchkamt) as totchkamt, ";
			$sql .= "TRUNCATE((SUM(a.totchkamt) / (SELECT SUM(totchkamt) FROM vcvhdr ";
			$sql .= "WHERE date BETWEEN '".$fr."' AND '".$to."')) * 100,10) as percentage ";
			$sql .= "FROM cvhdr a ";
			$sql .= "INNER JOIN supplier b ON a.supplierid = b.id ";
			$sql .= "WHERE date BETWEEN '".$fr."' AND '".$to."' ";
			if($posted!="" && ($posted=="1" || $posted=="0")){
            	$sql .= "AND posted = '".$posted."' ";
        	}
			$sql .= "GROUP BY b.descriptor ";
			$sql .= "ORDER BY totchkamt DESC ";
			
			//echo $sql;	
			
			$result_array = static::find_by_sql($sql);
			return !empty($result_array) ? $result_array : false;
			
		} else {
			return false;	
		}
	}
		
	
	
	

	
}




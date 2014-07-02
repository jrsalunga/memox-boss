<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vApvhdr extends DatabaseObject{
	
	protected static $table_name="vapvhdr";
	protected static $db_fields = array('id', 'refno' ,'date' ,'supplier', 'supplierid' ,'suppliercode' ,'supprefno' ,'porefno' ,'terms' ,'totamount' ,'balance' ,'notes' ,'posted' ,'cancelled' ,'printctr' ,'totline', 'due', 'account', 'accountid' );
	
	/*
	* Database related fields
	*/
	public $id;
	public $refno;
	public $date;
	public $supplier;
	public $supplierid;
	public $supprefno;
	public $porefno;
	public $terms;
	public $totamount;
	public $balance;
	public $posted;
	public $totcredit;
	public $totdebit;
	public $notes;
	public $cancelled;
	public $printctr;
	public $totline;
	public $due;
	public $suppliercode;
	
	public $account;
	public $accountid;
	
	
	
	public static function queryReport($to, $from){
		$sql = "SELECT * FROM vapvhdr WHERE due BETWEEN '". $to ."' AND '". $from ."' ORDER BY due ASC";
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
		
	}
	
	
	public static function getDue($duedate, $posted){
		if(is_int($posted) && $posted==0 || $posted==1){
			$sql = "SELECT * FROM vapvhdr WHERE due <= '". $duedate ."' AND posted = ". $posted ." ORDER BY due ASC";
		} else {
			$sql = "SELECT * FROM vapvhdr WHERE due <= '". $duedate ."' ORDER BY due ASC";
		}
		
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
		
	}
	
	public static function status_with_group_account($fr, $to, $posted=NULL){
		
		$sql = "SELECT a.descriptor AS account, SUM(c.totamount) AS totamount, (SUM(c.totamount)/";
		$sql .= "(SELECT SUM(totamount) FROM vapvhdr WHERE due BETWEEN '".$fr."' AND '".$to."' ";
		if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
			$sql .= "AND posted = '".$posted."' ";
		}
		$sql .= " )) * 100 AS percentage, ";
		$sql .= "COUNT(c.refno) AS printctr, a.id AS accountid ";
		$sql .= "FROM account a LEFT JOIN apvdtl b ON a.id = b.accountid ";
		$sql .= "LEFT JOIN vapvhdr c ON c.id = b.apvhdrid AND c.due BETWEEN '".$fr."' AND '".$to."' ";
		if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
			$sql .= "AND posted = '".$posted."' ";
		}
		$sql .= "GROUP BY a.id ORDER BY account ASC";
		
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}
	
	
	public static function status_with_account($accountid, $fr, $to, $posted=NULL){
		if(isset($accountid) && is_uuid($accountid) && isset($posted) && !is_null($posted)){
			$sql = "SELECT a.* FROM vapvhdr a ";
			$sql .= "INNER JOIN apvdtl b ON a.id = b.apvhdrid AND a.posted = '".$posted."' ";
			$sql .= "AND a.due BETWEEN '".$fr."' AND '".$to."' ";
			$sql .= "INNER JOIN account c ON c.id = b.accountid AND b.accountid = '".$accountid."' ";
			$sql .= "ORDER BY a.due DESC, a.refno DESC ";
		} else if(isset($accountid) && is_uuid($accountid) && (!isset($posted) || is_null($posted))){
			$sql = "SELECT a.* FROM vapvhdr a ";
			$sql .= "INNER JOIN apvdtl b ON a.id = b.apvhdrid ";
			$sql .= "AND a.due BETWEEN '".$fr."' AND '".$to."' ";
			$sql .= "INNER JOIN account c ON c.id = b.accountid AND b.accountid = '".$accountid."' ";
			$sql .= "ORDER BY a.due DESC, a.refno DESC ";
		} else {
			return false;
			exit;	
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}
	
	
	

	
}




<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vCvchkdtl extends DatabaseObject{
	
	protected static $table_name="vcvchkdtl";
	protected static $db_fields = array('checkno' ,'checkdate' ,'amount' ,'id' ,'refno' ,'payee' ,'posted' ,'cancelled' ,'cvhdrdate' ,'cvhdrid' ,'supplier' ,'suppliercode' ,'supplierid' ,'bank' ,'bankcode' ,'acctno' ,'bankid', 'chkctr');
	
	/*
	* Database related fields
	*/
	public $id;
	public $checkno;
	public $checkdate;
	public $amount;
	public $refno;
	public $payee;
	public $posted;
	public $cancelled;
	public $cvhdrdate;
	public $cvhdrid;
	public $supplier;
	public $suppliercode;
	public $supplierid;
	public $bank;
	public $bankcode;
	public $acctno;
	public $bankid;
	public $chkctr;
	
	
	public static $dt_columns = array(
        array('db'=>'refno', 'dt'=>'refno'),
        array('db'=>'checkno', 'dt'=>'checkno'),
        array('db'=>'bankcode', 'dt'=>'bankcode'),
        array('db'=>'checkdate', 'dt'=>'checkdate'),
        array('db'=>'payee', 'dt'=>'payee'),
        array('db'=>'amount', 'dt'=>'amount'),
        array('db'=>'id', 'dt'=>'id'),
        array('db'=>'cvhdrid', 'dt'=>'cvhdrid')
    );

	
	
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
	
	
	public static function checks($valid=TRUE){
		$sql = "SELECT * FROM ".static::$table_name;
		if($valid){
			$sql .= " WHERE checkno <> 0 ";
		}
		$sql .= "ORDER BY checkno";
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
	
	public static function sum_group_by_account($fr, $to, $posted){
		if((!isset($fr) || !empty($fr)) && (!isset($to) || !empty($to))){
		
			$sql = "SELECT SUM(a.totamount) AS totamount, ";
			$sql .= "TRUNCATE(SUM(a.totamount)";
			$sql .= "/((SELECT SUM(totamount) ";
			$sql .= "FROM vapvhdr a, apvdtl b, account c ";
			$sql .= "WHERE a.id = b.apvhdrid AND b.accountid = c.id ";
			$sql .= "AND a.due BETWEEN '".$fr."' AND '".$to."') ";
			$sql .= "* .01),5) AS percentage ";
			$sql .= "FROM vapvhdr a, apvdtl b, account c ";
			$sql .= "WHERE a.id = b.apvhdrid AND b.accountid = c.id ";
			$sql .= "AND a.due BETWEEN '".$fr."' AND '".$to."' ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND a.posted = '".$posted."' ";
			}
			
			
			$result_array = static::find_by_sql($sql);
			return !empty($result_array) ? array_shift($result_array) : false;
		} else {
			return false;
		}
		
		
	}
	
	/*	
	*	@param: checkdate, posted
	*	fetch all summary of check details with @param but not cancelled
	*	url: /report/cv-sched
	*/
	public static function summary_by_date($checkdate, $posted=NULL){
		$sql = "SELECT SUM(amount) as amount, COUNT(amount) as checkno  ";
		$sql .= "FROM ". static::$table_name; 
		$sql .= " WHERE checkdate = '".$checkdate."' AND cancelled = 0 ";
		if(!is_null($posted) && ($posted===1 || $posted===0)){
			$sql .= "AND posted = '".$posted."'"; 
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	/*
	* same as above but w/ bankid
	*/
	public static function summary_by_date_with_bankid($checkdate, $bankid, $posted=NULL){
		$sql = "SELECT SUM(amount) as amount, COUNT(amount) as checkno  ";
		$sql .= "FROM ". static::$table_name; 
		$sql .= " WHERE checkdate = '".$checkdate."' AND bankid = '".$bankid."' AND cancelled = 0 ";
		if((!is_null($posted) || $posted!="") && ($posted==1 || $posted==0)){
			$sql .= "AND posted = '".$posted."'"; 
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	/*	
	*	@param: checkdate, bankid, posted   @return: array of obj
	*	fetch all check details with @param but not cancelled fiter with bankid or posted/status
	*	url: /report/chk-day ~ Check Brakdown
	*/
	public static function find_by_date_with_bankid($checkdate, $bankid=NULL, $posted=NULL, $supplierid=NULL){
		$sql = "SELECT a.*, (SELECT COUNT(checkno) FROM vcvchkdtl WHERE checkno = a.checkno AND cancelled = 0) as chkctr FROM ". static::$table_name. " a";
		$sql .= " WHERE checkdate = '".$checkdate."' AND cancelled = 0 ";
		if((!is_null($posted) || $posted!="") && ($posted==1 || $posted==0)){
			$sql .= "AND posted = '".$posted."' "; 
		}
		if((!is_null($bankid) || $bankid!="") && is_uuid($bankid)){
			$sql .= "AND bankid = '".$bankid."' "; 
		}
		if((!is_null($supplierid) || $supplierid!="") && is_uuid($supplierid)){
			$sql .= "AND supplierid = '".$supplierid."' "; 
		}
		$sql .= "ORDER BY bankcode ASC, payee";
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}
	
	/*	
	*	@param: date range
	*	total all the amount w/in date range
	*	url: /report/cv-sched
	*/
	public static function total_by_date_range($fr, $to){
		//$range = new DateRange($fr, $to);
		
		$sql = "SELECT SUM(amount) as amount FROM ".static::$table_name;
		$sql .= " WHERE checkdate BETWEEN '".$fr."' AND '".$to."' AND cancelled = 0";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	/*
	* same as above but w/ posted/status
	*/
	public static function total_status_by_date_range($fr, $to, $posted){
		//$range = new DateRange($fr, $to);
		
		$sql = "SELECT SUM(amount) as amount FROM ".static::$table_name;
		$sql .= " WHERE checkdate BETWEEN '".$fr."' AND '".$to."' AND cancelled = 0 AND posted = '".$posted."'";
		$result_array = static::find_by_sql($sql);
		
		return !empty($result_array) ? array_shift($result_array) : false;	
	}
	
	/*	
	*	@param: date range, bank id
	*	total all the amount w/in date range by bank
	*	url: reports/cv-bank/<bankid> (graph)
	*/
	public static function bank_total_by_date_range($bankid, $fr, $to){
		//$range = new DateRange($fr, $to);
		
		$sql = "SELECT SUM(amount) as amount FROM ".static::$table_name;
		$sql .= " WHERE bankid = '".$bankid."' AND checkdate BETWEEN '".$fr."' AND '".$to."' ";
		$sql .= "AND cancelled = 0";
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	/*	
	*	@param: date range, bank id
	*	total all the amount per status w/in date range by bank 
	*	url: reports/cv-bank/<bankid> (graph)
	*/
	public static function bank_total_status_by_date_range($bankid, $fr, $to, $posted){
		//$range = new DateRange($fr, $to);
		
		$sql = "SELECT SUM(amount) as amount FROM ".static::$table_name;
		$sql .= " WHERE bankid = '".$bankid."' AND checkdate BETWEEN '".$fr."' AND '".$to."' ";
		$sql .= "AND cancelled = 0 ";
		if((!is_null($posted) || $posted!="") && ($posted==1 || $posted==0)){
			$sql .= "AND posted = '".$posted."' "; 
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? array_shift($result_array) : false;
		
	}
	
	
	public static function find_all_by_field($field=NULL,$value=NULL) {
		if(is_null($field) && is_null($value)) {
			return false;
		} else {
   			$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE {$field}='{$value}' AND cancelled = 0");
			return !empty($result_array) ? $result_array : false;
		}
  	}
	
	
	/** for api **/
	public static function group_by_account($fr, $to, $posted){
		if((!isset($fr) || !empty($fr)) && (!isset($to) || !empty($to))){
		
			$sql = "SELECT a.code AS accountcode, a.descriptor AS account, a.id AS accountid, ";
			$sql .= "SUM(c.totamount) AS totamount, (SUM(c.totamount)/ ";
			$sql .= "(SELECT SUM(totamount) FROM vapvhdr ";
			$sql .= "WHERE due BETWEEN '".$fr."' AND '".$to."' ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND posted = '".$posted."' ";
			}
			$sql .= ")) * 100 AS percentage ";
			$sql .= "FROM account a ";
			$sql .= "INNER JOIN apvdtl b ON a.id = b.accountid ";
			$sql .= "INNER JOIN vapvhdr c ON c.id = b.apvhdrid ";
			$sql .= "AND c.due BETWEEN '".$fr."' AND '".$to."' ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND c.posted = '".$posted."' ";
			}
			$sql .= "GROUP BY a.id ORDER BY totamount DESC ";
			
			$result_array = static::find_by_sql($sql);
			return !empty($result_array) ? $result_array : false;
		} else {
			return false;	
		}
	}
	
	
	
	/*	
	*	@param: supplierid, date range, posted
	*	@return: array of this class object or FALSE if no record found
	*	fetch all CV(not cancelled) to summarize cvchkdtl total amount w/ percentage per account(using supplierid)
	*	sub query for vCvhdr::summary_by_supplier (summary listing for each supplier)
	*	url: /reports/cvhdr-supplier
	*   
	* 	
	*/
	public static function status_with_supplier($supplierid, $fr, $to, $posted=NULL){
		if(!is_null($supplierid) && is_uuid($supplierid) && $supplierid!=''){	
			$sql = "SELECT a.* ";
			$sql .= "FROM vcvchkdtl a ";
			$sql .= "WHERE a.checkdate BETWEEN '".$fr."' AND '".$to."' ";
			$sql .= "AND a.supplierid = '".$supplierid."' AND a.cancelled = 0 ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND a.posted = '".$posted."' ";
			}
			$sql .= "ORDER BY a.checkdate DESC";
		} else {
			return false;
			exit;
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}



	function daySummary(){

		
	}
	
	
}


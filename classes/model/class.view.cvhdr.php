<?php
// If it's going to need the database, then it's 
// probably smart to require it before we start.
require_once(ROOT.DS.'classes'.DS.'database.php');

class vCvhdr extends DatabaseObject{
	
	protected static $table_name="vcvhdr";
	protected static $db_fields = array('id', 'refno' ,'date' ,'supplier', 'supplierid' ,'payee' ,'totapvamt' ,'totchkamt' ,'notes' ,'posted' ,'cancelled' ,'printctr' ,'totapvline' ,'totchkline', 'percentage', 'suppliercode', 'account', 'accountcode', 'accountid');
	
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
	public $account;
	public $accountcode;
	public $accountid;
	
	
	

	/*	
	*	@param: date range, posted
	*	@return: array of this class object or FALSE if no record found
	*	fetch all CV(not cancelled) to summarize total amount w/ percentage GROUP BY account(list all account)
	*	url: /reports/cvhdr-account
	* 	comment: the table used from this query is from vxCvhdr
	*/
	public static function status_with_group_account($fr, $to, $posted=NULL){
		
		
		$sql = "SELECT a.descriptor AS account, SUM(b.cvtotchkamt) AS totchkamt, ";
		$sql .= "SUM(b.cvtotchkamt)/((SELECT SUM(y.cvtotchkamt) FROM account z ";
		$sql .= "LEFT JOIN vxcvhdr y ON z.id = y.accountid AND y.cvdate BETWEEN '".$fr."' AND '".$to."' AND cvcancelled = 0 ";
		if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
			$sql .= "AND y.cvposted = '".$posted."' ";
		}
		$sql .= ") * .01) AS percentage, ";
		$sql .= "COUNT(b.cvrefno) AS printctr, a.id AS accountid FROM account a ";
		$sql .= "LEFT JOIN vxcvhdr b ON a.id = b.accountid AND b.cvdate BETWEEN '".$fr."' AND '".$to."' AND cvcancelled = 0 ";
		if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND b.cvposted = '".$posted."' ";
			}
		$sql .= "GROUP BY a.id ORDER BY a.descriptor";
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
		
	}
	
	/*	
	*	@param: acccountid, date range, posted
	*	@return: array of this class object or FALSE if no record found
	*	fetch all APV(not cancelled) FILTERED BY accountid (sub query for self::status_with_group_account())
	*	url: /reports/apvhdr-account	
	*/
	public static function status_with_account($accountid, $fr, $to, $posted=NULL){
		//if(isset($accountid) && is_uuid($accountid) && isset($posted) && !is_null($posted)){
		if(!is_null($accountid) && is_uuid($accountid) && $accountid!=''){
			$sql = "SELECT cvrefno AS refno, cvdate as date, cvposted as posted, cvtotchkamt AS totchkamt, supplier, suppliercode, cvhdrid AS id ";
			$sql .= "FROM vxcvhdr WHERE accountid = '".$accountid."' AND cvcancelled = 0 ";
			$sql .= "AND cvdate BETWEEN '".$fr."' AND '".$to."' ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND a.posted = '".$posted."' ";
			}
			$sql .= "ORDER BY cvdate DESC, cvrefno DESC ";
	
		} else {
			return false;
			exit;	
		}
		
		$result_array = static::find_by_sql($sql);
		return !empty($result_array) ? $result_array : false;
	}
	
	
	public static function sum_group_by_account($fr, $to, $posted){
		if((!isset($fr) || !empty($fr)) && (!isset($to) || !empty($to))){
		
			$sql = "SELECT SUM(b.cvtotchkamt) AS totchkamt, ";
			$sql .= "SUM(b.cvtotchkamt)/((SELECT SUM(y.cvtotchkamt) FROM account z ";
			$sql .= "LEFT JOIN vxcvhdr y ON z.id = y.accountid AND y.cvdate BETWEEN '".$fr."' AND '".$to."' ";
			$sql .= ") * .01) AS percentage, ";
			$sql .= "COUNT(b.cvrefno) AS printctr FROM account a ";
			$sql .= "LEFT JOIN vxcvhdr b ON a.id = b.accountid AND b.cvdate BETWEEN '".$fr."' AND '".$to."' ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND b.cvposted = '".$posted."' ";
			}

			
			
			$result_array = static::find_by_sql($sql);
			return !empty($result_array) ? array_shift($result_array) : false;
		} else {
			return false;
		}
		
		
	}
	
	
	/** for api **/
	public static function group_by_account($fr, $to, $posted){
		if((!isset($fr) || !empty($fr)) && (!isset($to) || !empty($to))){
		
			$sql = "SELECT a.code AS accountcode, a.descriptor AS account, a.id AS accountid, SUM(b.cvtotchkamt) AS totchkamt,";
			//$sql = "SELECT a.descriptor AS account, SUM(b.cvtotchkamt) AS totchkamt, ";
			$sql .= "SUM(b.cvtotchkamt)/((SELECT SUM(y.cvtotchkamt) FROM account z ";
			$sql .= "INNER JOIN vxcvhdr y ON z.id = y.accountid AND y.cvdate BETWEEN '".$fr."' AND '".$to."' ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
				$sql .= "AND y.cvposted = '".$posted."' ";
			}
			$sql .= ") * .01) AS percentage, ";
			$sql .= "COUNT(b.cvrefno) AS printctr, a.id AS accountid FROM account a ";
			$sql .= "INNER JOIN vxcvhdr b ON a.id = b.accountid AND b.cvdate BETWEEN '".$fr."' AND '".$to."' ";
			if(isset($posted) && (!is_null($posted) || $posted!="") && ($posted=="1" || $posted=="0")){
					$sql .= "AND b.cvposted = '".$posted."' ";
				}
			$sql .= "GROUP BY a.id ORDER BY a.descriptor";
			
			$result_array = static::find_by_sql($sql);
			return !empty($result_array) ? $result_array : false;
		} else {
			return false;	
		}
	}
		
		
	


	
	
	
	
	
	
	

	
}




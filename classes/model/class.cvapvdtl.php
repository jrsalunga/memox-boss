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
	
	
	
	public static function find_all_by_field_id($field=0,$id=0) {
		if(!is_uuid($id) && $id==NULL) {
			return false;
		} else {
			$sql = "SELECT * FROM ".static::$table_name." WHERE {$field}id='{$id}' ORDER BY amount";
   			$result_array = static::find_by_sql($sql);
			return !empty($result_array) ? $result_array : false;
		}
  	}
	


}




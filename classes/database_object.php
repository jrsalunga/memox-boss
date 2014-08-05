<?php
/*
COMMON DATABASE OBJECT CLASS
*/

require_once(ROOT.DS.'classes'.DS.'database.php');

class DatabaseObject {
	
	//public $isParent;
	
	protected static $table_name="category";
	protected static $db_fields = array('id', 'code', 'descriptor', 'type');
	protected static $parent_table = array('apvhdr');

	
	public static function find_all($order=NULL) {
		if(empty($order) || $order==NULL) {
			return static::find_by_sql("SELECT * FROM ".static::$table_name);
		} else {
			return static::find_by_sql("SELECT * FROM ".static::$table_name." ".$order);
		}
  	}
  
  	public static function find_by_id($id=0) {
		if(!preg_match('/^[A-Fa-f0-9]{32}+$/',$id) && $id==NULL) {
			return false;
		} else {
   			$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE id='{$id}' LIMIT 1");
			return !empty($result_array) ? array_shift($result_array) : false;
		}
  	}
	
	public static function find_by_field_id($field=0,$id=0) {
		if(!is_uuid($id) && $id==NULL) {
			return false;
		} else {
   			$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE {$field}id='{$id}' LIMIT 1");
			return !empty($result_array) ? array_shift($result_array) : false;
		}
  	}
	
	public static function find_all_by_field_id($field=0,$id=0) {
		if(!is_uuid($id) && $id==NULL) {
			return false;
		} else {
   			$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE {$field}id='{$id}'");
			return !empty($result_array) ? $result_array : false;
		}
  	}
	
	public static function find_all_by_field($field=NULL,$value=NULL) {
		if(is_null($field) && is_null($value)) {
			return false;
		} else {
   			$result_array = static::find_by_sql("SELECT * FROM ".static::$table_name." WHERE {$field}='{$value}'");
			return !empty($result_array) ? $result_array : false;
		}
  	}
	
	public static function delete_all_by_field_id($field=0,$id=0) {
		global $database;
		if(!is_uuid($id) && $id==NULL) {
			return false;
		} else {
   			$database->query("DELETE FROM ".static::$table_name." WHERE {$field}id='{$id}'");
			return ($database->affected_rows() >= 1) ? true : false;
		}
  	}
  
  	public static function find_by_sql($sql="") {
            global $database;
            $result_set = $database->query($sql);
    

            $object_array = array();
            while ($row = $database->fetch_array($result_set)) {
                $object_array[] = static::instantiate($row);
            }
            return $object_array;
        }

	public static function count_all() {
            global $database;
            $sql = "SELECT COUNT(*) FROM ".static::$table_name;
            $result_set = $database->query($sql);
            $row = $database->fetch_array($result_set);
            return array_shift($row);
	}
	
	public static function row($id,$col) {
		global $database;
		$sql = "SELECT * FROM ".static::$table_name." WHERE id = '".$id."'";
		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
	
		return !empty($row) ? $row["$col"] : false;	
	}
	
	public static function getLastNumber(){
		global $database;
		
		#$table = static::$table_name;
		
		$database->startTransaction();
		$sql = "SELECT * FROM autoinc WHERE tableid = '". static::$table_name ."' FOR UPDATE";
		$result_set = $database->query($sql);
		$rows = $database->fetch_array($result_set);
		
		if($database->affected_rows()==1){
			$nLastNumber = $rows[1] + 1;
			$sql = "UPDATE autoinc SET lastnumber = ". $nLastNumber ." WHERE tableid = '". static::$table_name ."'";
			$result_set = $database->query($sql);
			$database->commit();
			return $nLastNumber;
			exit();
		} else {
			$database->rollback();
			return false;
			exit();
		}
	}

	private static function instantiate($record) {
		// Could check that $record exists and is an array
	$class_object = get_called_class();	
        $object = new $class_object;
		// Simple, long-form approach:
		// $object->id 		= $record['id'];
		// $object->username 	= $record['username'];
		// $object->password 	= $record['password'];
		// $object->first_name  = $record['first_name'];
		// $object->last_name 	= $record['last_name'];
		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	private function has_attribute($attribute) {
	  // We don't care about the value, we just want to know if the key exists
	  // Will return true or false
	  return array_key_exists($attribute, $this->attributes());
	}

	protected function attributes() { 
		// return an array of attribute names and their values
	  $attributes = array();
	  foreach(static::$db_fields as $field) {
	    if(property_exists($this, $field)) {
	      $attributes[$field] = $this->$field;
	    }
	  }
	  return $attributes;
	}
	
	protected function sanitized_attributes() {
	  global $database;
	  $clean_attributes = array();
	  // sanitize the values before submitting
	  // Note: does not alter the actual value of each attribute
	  foreach($this->attributes() as $key => $value){
		  if(isset($value) && $value!=NULL) {
	    	$clean_attributes[$key] = $database->escape_value($value);
	    	//echo $value .' - ';
	    	//$clean_attributes[$key] = $this->sanitize($value);
	    	//echo $clean_attributes[$key] .'<br>';
		  }
	  }
	  return $clean_attributes;
	}

	public function cleanInput($input) {
 
	  $search = array(
	    '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
	    '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
	    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
	    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
	  );
	 
	    //$output = preg_replace($search, '', $input);
	 	foreach ($search as $src) {
	 		if(preg_match($src, $input, $matches)){
	  			//echo 'Match: '.$src.' = '.$input.'<br>';
	  		}
	  		//echo $matches[1].'<br>';
	 	}
	  	

	  	$output = preg_replace_callback($search, function($matches){
	  		sanitize_log(getenv('COMPUTERNAME'), 'input = '. $matches[0]);
	  	}, $input);
	    return $output;
	}


	


	public function sanitize($input) {

		    if (is_array($input)) {
	        foreach($input as $var=>$val) {
	            $output[$var] = $this->sanitize($val);
	        }
	    }
	    else {
	        if (get_magic_quotes_gpc()) {
	            $input = stripslashes($input);
	        }
	        $input  = $this->cleanInput($input);
	        $output = mysql_real_escape_string($input);
	    }
	    return $output;
	}
	
	
	
	
	///////////////// CRUD PART ///////////////////////////////////////////
	
	public function save() {
	  // A new record won't have an id yet.
	  return isset($this->id) ? $this->update() : $this->create();
	}
	
	public function create() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - INSERT INTO table (key, key) VALUES ('value', 'value')
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		if(!isset($this->id) || $this->id==NULL) {
			$this->id = $database->get_uid();
		}
		$attributes = $this->sanitized_attributes();
	  $sql = "INSERT INTO ".static::$table_name." (";
		$sql .= join(", ", array_keys($attributes));  // - join the array eg:  key1 = 'value1', key2 = 'value2', ...', 
	  $sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
	  
	  if($database->query($sql)) {
	    return true;
	  } else {
	    return false;
	  }
	}

	public function update() {
	  global $database;
	 
		// Don't forget your SQL syntax and good habits:
		// - UPDATE table SET key='value', key='value' WHERE condition
		// - single-quotes around all values
		// - escape all values to prevent SQL injection
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
			#echo $key." - ".$value."<br>";
			
			#echo empty($value) ? "empty  <br>":"not empty <br>";
		  	// - check if the $key is 'id'
			if($key != 'id'){
				// - check if the $value has a value to be updated
				if(isset($value) && $value!=NULL) {
					// - saved in array only the field with value to be updated
			  		$attribute_pairs[] = "{$key}='{$value}'";
				}
			}
		}
		$sql = "UPDATE ".static::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);   // - join the array eg:  key1 = 'value1', key2 = 'value2', ...', 
		$sql .= " WHERE id = '". $database->escape_value($this->id)."'";
	  	$database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		// Don't forget your SQL syntax and good habits:
		// - DELETE FROM table WHERE condition LIMIT 1
		// - escape all values to prevent SQL injection
		// - use LIMIT 1
	  $sql = "DELETE FROM ".static::$table_name;
	  $sql .= " WHERE id = '". $database->escape_value($this->id) ."'";
	  $sql .= " LIMIT 1";
	  $database->query($sql);
	  return ($database->affected_rows() == 1) ? true : false;
	
		// NB: After deleting, the instance of User still 
		// exists, even though the database entry does not.
		// This can be useful, as in:
		//   echo $user->first_name . " was deleted";
		// but, for example, we can't call $user->update() 
		// after calling $user->delete().
	}
	
	public function delete_by_id($field,$id) {
		global $database;

		// - DELETE FROM table WHERE condition LIMIT 1

	  $sql = "DELETE FROM ".static::$table_name;
	  $sql .= " WHERE ".$field."id = '". $database->escape_value($id) ."'";
	  //$database->query($sql);
	  return ($database->query($sql)) ? true : false;
	
	}
	
	// record locking for transaction
	public function lock_record() {
		global $database;

		$sql = "SELECT * FROM ". static::$table_name ." WHERE id = '". $database->escape_value($this->id) ."' FOR UPDATE";
	  	$database->query($sql);
	  	return ($database->query($sql)) ? true : false;
		//return true;
	
	}
	
	public static function find_all_posted($field=0,$id=0,$posted=true) {
		
		if(false !== array_search('posted', static::$db_fields)) {
		
			if($posted) {
				$sql = "SELECT * FROM ". static::$table_name ." WHERE {$field}id='{$id}' AND posted = 1 AND cancelled = 0 AND balance > 0";
				
				$result_array = static::find_by_sql($sql);
				return !empty($result_array) ? $result_array : false;
			} else {
				$result_array = static::find_by_sql("SELECT * FROM ". static::$table_name ." WHERE {$field}id='{$id}' AND posted = 0 AND cancelled = 0 AND balance > 0");
				return !empty($result_array) ? $result_array : false;
			}
		} else {
			return false;	
		}
  	}
	
	public function result_respone($no=NULL, $msg=NULL){
		global $database;
		
		
		switch($no){
			case 0:
				$m = (isset($msg) || $msg != NULL) ? $msg:'no error';
				
				$respone = array(
		         	'status' => 'success', 
		            'code' => '200',
		            'message' => $m
		       	);
				break;
				
			case 1:
				if($database->errno()==0){
					 $m = $msg == NULL ? 'nothing to update here':$msg;
					
					$respone = array(
			         	'status' => 'warning', 
			            'code' => '300',
			            'message' => $m
		       		);
				} else {
					$respone = array(
			         	'status' => 'error', 
			            'code' => '400',
			            'message' => strtolower(strtoupper($database->error())) .' on '. static::$table_name
		       		);
				}
				
				break;
			case 2:
				$respone = array(
		         	'status' => 'error', 
		            'code' => '401',
		            'message' => 'unable to lock record on table '. static::$table_name
		       	);
				break;
			default:
				$respone = array(
		         	'status' => NULL, 
		            'code' => NULL,
		            'message' => NULL
		       	);
				break;
				
			
				
		}
		
		

       return $respone;
	}
	
	
	


	
	
	
}

?>
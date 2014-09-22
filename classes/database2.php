<?php
require_once(ROOT.DS."conf".DS."config.php");

class MySQLDatabase2 {
	
	// Singleton object. Leave $me alone.
    private static $me;
	
	private $connection;
	public $last_query;
	public $last_uid;

	
  	function __construct() {
		$this->open_connection();
  	}
  
  
	
	 // Get Singleton object
    
    public static function getInstance() {
    	
    	if(is_null(self::$me)) {
        	self::$me = new MySQLDatabase2();
		}
        return self::$me;
        
        /*
        if(isset(self::$me) {
        	return self::$me;
        }
        */
    }

	/*
	public function open_connection() {
		$this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS);
		mysqli_set_charset($this->connection, 'utf8');
		if (!$this->connection) {
			die("Database connection failed: " . mysqli_error());
		} else {
			$db_select = mysqli_select_db($this->connection, DB_NAME);
			if (!$db_select) {
				die("Database selection failed: " . mysqli_error());
			}
		}
	}
	*/
	
	public function open_connection(){
		try {
			$this->connection = @new PDO(
				"mysql:host=".DB_SERVER.";dbname=".DB_NAME,DB_USER, DB_PASS,
				array( PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION )
			);
		}
		catch (PDOException $e) {
			self::fatal(
				"An error occurred while connecting to the database. ".
				"The error reported by the server was: ".$e->getMessage()
			);
		}
	}
	
	static function fatal($msg){
		echo json_encode( array( 
			"error" => $msg
		) );
		exit(0);
	}
	
	/*
	public function query($sql) {
		$this->last_query = $sql;
		$result = mysqli_query($sql, $this->connection);
		
		return $result;
	}
	*/
	
	public function query($bindings, $sql=null){
		// Argument shifting
		if($sql === null){
			$sql = $bindings;
		}

		//var_dump($this->connection);
		$stmt = $this->connection->prepare($sql);
		$this->last_query = $sql;
		
		// Bind parameters
		if ( is_array( $bindings ) ) {
			for ( $i=0, $ien=count($bindings) ; $i<$ien ; $i++ ) {
				$binding = $bindings[$i];
				$stmt->bindValue( $binding['key'], $binding['val'], $binding['type'] );
			}
		}

		// Execute
		try {
			$stmt->execute();
		}
		catch (PDOException $e) {
			self::fatal( "An SQL error occurred: ".$e->getMessage() );
		}

		// Return all
		return $stmt->fetchAll();
	}
	
	
	
	
	
	
	
	
	

	public function close_connection() {
		if(isset($this->connection)) {
			mysqli_close($this->connection);
			unset($this->connection);
		}
	}

	
	
	public function escape_value( $value ) {
		$value = trim($value);
		if( $this->real_escape_string_exists ) { // PHP v4.3.0 or higher
			// undo any magic quote effects so mysql_real_escape_string can do the work
			if( $this->magic_quotes_active ) { $value = stripslashes( $value ); }
			$value = mysqli_real_escape_string( $value );
		} else { // before PHP v4.3.0
			// if magic quotes aren't already on then add slashes manually
			if( !$this->magic_quotes_active ) { $value = addslashes( $value ); }
			// if magic quotes are active, then the slashes already exist
		}
		return $value;
	}
	
	// "database-neutral" methods
  public function fetch_array($result_set) {
    return mysqli_fetch_array($result_set);
  }
  
  public function fetch_assoc($result_set) {
    return mysqli_fetch_assoc($result_set);
  }
  
   public function fetch_row($result_set) {
    return mysqli_fetch_row($result_set);
  }
  
  public function num_rows($result_set) {
   return mysqli_num_rows($result_set);
  }
  
  public function insert_id() {
    // get the last id inserted over the current db connection
    return mysqli_insert_id($this->connection);
  }
  
	public function error(){
		return mysqli_error();
	}  
	
	public function errno(){
		return mysqli_errno();
	} 
  
	public function affected_rows() {
    	return mysqli_affected_rows($this->connection);
  	}

	private function confirm_query($result) {
		if (!$result) {
	    $output = "Database query failed: " . mysqli_errno() ."->". mysqli_error() ;
	    //$output .= "Last SQL query: " . $this->last_query;
	    die( $output );
		}
	}
	
	public function get_uid() {
		$sql = "SELECT REPLACE (UUID(), \"-\", \"\")";
		$result = $this->query($sql);
		$row = $this->fetch_array($result);
		$this->last_uid = $row[0];
		return $row[0];
	}
	
	public function startTransaction() {
		$sql = "START TRANSACTION";
		$result = $this->query($sql);
		$this->confirm_query($result);
		return $result;
	}
	
	public function commit() {
		$sql = "COMMIT";
		$result = $this->query($sql);
		$this->confirm_query($result);
		return $result;
	}
	
	public function rollback() {
		$sql = "ROLLBACK";
		$result = $this->query($sql);
		$this->confirm_query($result);
		return $result;
	}
	
	public function export2excel($sql, $filename) {
		if(!empty($sql) || $sql!=NULL) {
			$result = $this->query($sql);
			
			while($row = $this->fetch_assoc($result)){
			 	$aData[] = $row;
			}
			
			//feed the final array to our formatting function...
			$contents = $this->getExcelData($aData);
		
			$filename = isset($filename) ? $filename.".xls":"Download.xls";
		
			//prepare to give the user a Save/Open dialog...
			header ("Content-type: application/octet-stream");
			header ("Content-Disposition: attachment; filename=".$filename);
		
			//setting the cache expiration to 30 seconds ahead of current time. an IE 8 issue when opening the data directly in the browser without first saving it to a file
			$expiredate = time() + 30;
			$expireheader = "Expires: ".gmdate("D, d M Y G:i:s",$expiredate)." GMT";
			header ($expireheader);
		
			//output the contents
			return $contents;
			exit;
			
		} else {
			return false;
		}
	
	}
	
	private function getExcelData($data){
    $retval = "";
    if (is_array($data)  && !empty($data))
    {
     $row = 0;
     foreach(array_values($data) as $_data){
      if (is_array($_data) && !empty($_data))
      {
          if ($row == 0)
          {
              // write the column headers
              $retval = implode("\t",array_keys($_data));
              $retval .= "\n";
          }
           //create a line of values for this row...
              $retval .= implode("\t",array_values($_data));
              $retval .= "\n";
              //increment the row so we don't create headers all over again
              $row++;
       }
     }
    }
  return $retval;
 }
	
}
global $database2;
$database2 = new MySQLDatabase2();
$db2 =& $database2;

?>
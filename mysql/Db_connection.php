<?php

class Db_connection_yp_mysql_caching 
{
    private static $_instance;
 	private $link;
 	private $db_host;
 	private $db_name;
 	private $db_user;
 	private $db_password;
 	
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
	
    // Do not allow an explicit call of the constructor: $v = new Singleton();
    final private function __construct() {
    	
    	$this->db_host = $GLOBALS['db_host'];
    	$this->db_name = $GLOBALS['db_name'];
    	$this->db_user = $GLOBALS['db_user'];
    	$this->db_password = $GLOBALS['db_password'];
		
    	$this->openConnection();
    }
	
    public function __destruct() {
    	$this->closeConnection();
    }
    
    // Do not allow the clone operation: $x = clone $v;
    final private function __clone() { }
	
	function openConnection() {
		$this->link = mysqli_connect($this->db_host,$this->db_user,$this->db_password);
	}
	
	function closeConnection() {
		mysqli_close($this->link);
	}
	
	public function getDbConnection() {
		$db['link'] = $this->link;
		$db['db_name'] = $this->db_name;
		return $db;
	}
    
}

?>
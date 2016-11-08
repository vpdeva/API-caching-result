<?php
//error_reporting(E_ALL);

class MySqlTable_yp_mysql_caching {
	
	//Private properties
	var $tableName;	//current MySql Table name
	var $link;
	var $db_name;
	
/*******************************************************************************
*                                                                              *
*                               Public methods                                 *
*                                                                              *
*******************************************************************************/

	function MySqlTable_yp_mysql_caching($tableName='') {
	  	$this->tableName = $tableName;
	  	
		$c1 = Db_connection_yp_mysql_caching::getInstance();
		$db_connection = $c1->getDbConnection();
		$this->link = $db_connection['link'];
		$this->db_name = $db_connection['db_name'];
	}
	
	function loadByFields($fields, $values, $options="") {
		$fields = explode(',',$fields);
		$values = explode(',',$values);
		$loops = count($fields);
		
		$selectedFields = "*";
		if($options!="") {
			foreach($options as $i => $v) {
				if($i=='fields') $selectedFields = $v;
			}
		}
		
		$query = "SELECT ".$selectedFields." FROM ". $this->tableName ." WHERE";
		for($i=0; $i<$loops; $i++) {
			if($i==0) $query .= " " .mysqli_real_escape_string($this->link,$fields[$i]). " = '" .mysqli_real_escape_string($this->link,$values[$i]). "'";
			else $query .= " AND " .mysqli_real_escape_string($this->link,$fields[$i]). " = '" .mysqli_real_escape_string($this->link,$values[$i]). "'";
		}
		
		if($options!="") {
			foreach($options as $i => $v) {
				if($i=='group') $groupByCond .= " GROUP BY ".mysqli_real_escape_string($this->link,$v)." ";
				if($i=='order') $orderByCond = " ORDER BY ".mysqli_real_escape_string($this->link,$v)." ";
			}
			$query .= $groupByCond.$orderByCond;
		}
		//echo $query.'<br>';
		return $this->loadArrayFromQuery($query);
	}
	
	function escape($value) {
		$value = mysqli_real_escape_string($this->link,$value);
		return $value;
	}
	
	function selectAll($fields='', $options='') {
		
		$fieldsArray = explode(',',$fields);
		
		if($fields=='') {
			$selectedFields = "*";
		}
		else {
			$selectedFields = '';
			$i=0;
			foreach($fieldsArray as $value) {
				if($i==0) $selectedFields = $value;
				else $selectedFields = $selectedFields.', '.$value;
				$i++;
			}
		}
		
		$array = $this->loadIntoArray();
		$query = "Select ".$selectedFields." FROM ". $this->tableName." WHERE 1 ";
		foreach($array as $key=>$value) {
			if($value!="")
				$query.= " AND ".$key." = '".mysqli_real_escape_string($this->link,$value)."'";
		}
		
		if($options!="") {
			foreach($options as $i => $v) {
				if($i=='group') $groupByCond .= " GROUP BY ".mysqli_real_escape_string($this->link,$v)." ";
				if($i=='order') $orderByCond = " ORDER BY ".mysqli_real_escape_string($this->link,$v)." ";
			}
			$query .= $groupByCond.$orderByCond;
		}
	     
		//echo $query.'<br>';
		return $this->loadArrayFromQuery($query);
	}
	
	/*
	Next functions
	*/
	
	function customQuery($query) {
		
		$result = $this->executeQuery($query);
		
		$return = array();
		while ($rows = mysqli_fetch_array($result)){	
			$return[] = $rows;
		}
		return $return;
	}
	
	function insert() {
		
		$array = $this->loadIntoArray();
		
		$query = "INSERT INTO ". $this->tableName." (";
		$s = "";
		foreach($array as $key=>$value)
		{
			if($value || is_numeric($value))
			{
				$query.= $s.$key;
				$s = ", ";
			}
		}
		$query .= ") VALUES (";
		$s = "";
		foreach($array as $value)
		{
			if($value || is_numeric($value))
			{
				$query.= $s."'".mysqli_real_escape_string($this->link,$value)."'";
				$s = ", ";
			}
		}
		$query .= ")";
		//echo $query;
		
		$this->executeQuery($query);
		$result = mysqli_insert_id($this->link);
		
		return $result;
	}


	function delete($id) {
		$query = "DELETE FROM ". $this->tableName." WHERE id = '".mysqli_real_escape_string($this->link,$id)."'";
		$this->executeQuery($query);
	}
	
	function updateByFields($fields,$id) {
		// Get the id of the table...
		$array = $this->loadIntoArray();
		$i=0;
		foreach($array as $key=>$value) {
			if($i==0) {
				$table_id=$key;
				break;
			}
			$i++;
		}
		// END Get the id of the table...
		
		$query = "UPDATE ". $this->tableName." SET ";
		$i=0;
		foreach($fields as $key=>$value) {
			if($i==0) $query.= $key." = '".mysqli_real_escape_string($this->link,$value)."'";
			else $query.= ", ".$key." = '".mysqli_real_escape_string($this->link,$value)."'";
			$i++;
		}
		
		if(is_array($id)) {
			$query .= " WHERE 1 ";
			foreach($id as $key=>$value) {
				$query .= " AND ".$key." = '".mysqli_real_escape_string($this->link,$value)."'";;
			}
		}
		else {
			$query .=" WHERE ".$table_id." = '".mysqli_real_escape_string($this->link,$id)."'";
		}
		
		//echo $query.'<br>';
		return $this->executeQuery($query);
	}
	
	function update($id) {
		
		$array = $this->loadIntoArray();
		$query = "UPDATE ". $this->tableName." SET ";
		$s = "";
		foreach($array as $key=>$value)
		{
			if($value)
			{
				$query.= $s.$key." = '".mysqli_real_escape_string($this->link,$value)."'";
				$s = ",";
			}
			elseif(is_numeric($value))
			{
				$query.= $s.$key." = '".mysqli_real_escape_string($this->link,$value)."'";
				$s = ",";
			}
			
		}
		$query .=" WHERE id = '".$id."'";
		//echo $query;
		
		$this->executeQuery($query);
	}
	
	function counter() {
		$sql = 'UPDATE count set count=count+1 WHERE id=1';
		mysqli_query($this->link, $sql);
	}
	
	function executeQuery($query) {
		//echo $query.'<br>';
		//echo 'db: '.$this->db_name.'<br>';
		mysqli_select_db($this->link, $this->db_name);
		$this->counter();
		$result = mysqli_query($this->link, $query);
		return $result;
	}
	
	//execute the query and return an array of corresponding MySqlTable inherited object
	function loadArrayFromQuery($query) {
		$result = $this->executeQuery($query);
		$return = array();
		while ($rows = mysqli_fetch_array($result))
		{	
			$return[] = $this->loadFromArray($rows);
		}
		return $return;
	}
}

?>

<?php

/*
Database structure:
CREATE TABLE  `hooks` (
`id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
`data` VARCHAR( 200 ) NOT NULL ,
`status` LONGTEXT NOT NULL ,
`moe` VARCHAR( 12 ) NOT NULL ,
created TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = MYISAM ;
*/

include_once("mysql/db_class.php");


class Yp_cache {

	function Yp_cache() {
	}

	//returns cached data, or false if nothing is found
	function cache_get($criteria=array()) {
		$key = $criteria['key'];
		$type = $criteria['type'];

		if($key!='') {

			$c1 = new MySqlTable_yp_mysql_caching('cache');
			$sql = "SELECT * FROM ".$GLOBALS['db_table_name']." WHERE 1 AND data_key='".$c1->escape($key)."' AND data_type='".$c1->escape($type)."'";

			$result = $c1->customQuery($sql);

			if(count($result)>0) {
				$id = $result[0]['id'];
				$data = $result[0]['data'];
				$expire = $result[0]['data_expire'];

				$now = time();

				if($expire>$now||$expire=='') {
					$data = json_decode($data, true);
					return $data;
				}
				else {
					$this->cache_delete(array("key"=>$key, "type"=>$type));
					return false;
				}
			}
			else {
				return false;
			}
		}

		else {
			return false;
		}
	}

	function cache_set($criteria) {
		$key = $criteria['key'];
		$data = $criteria['data'];
		$type = $criteria['type'];
		$expire = $criteria['expire'];

		$data = json_encode($data);

		if($key!=''&&$data!='') {

			$this->cache_delete(array("key"=>$key, "type"=>$type));

			if($expire!='') $expire = $expire+time();

			//save data
			$c1 = new MySqlTable_yp_mysql_caching('cache');
			$sql = "INSERT INTO ".$GLOBALS['db_table_name']." (data_key,data,data_type,data_expire) VALUES ('".$c1->escape($key)."','".$c1->escape($data)."','".$c1->escape($type)."','".$c1->escape($expire)."')";
			$c1->executeQuery($sql);
		}
	}

	function cache_delete($criteria=array()) {
		$key = $criteria['key'];
		$type = $criteria['type'];

		if($key!='') {
			$c1 = new MySqlTable_yp_mysql_caching('cache');
			$sql = "DELETE FROM ".$GLOBALS['db_table_name']." WHERE 1";
			$sql .= " AND data_key='".$c1->escape($key)."' AND data_type='".$c1->escape($type)."'";
			$c1->executeQuery($sql);
		}
		else {
			return false;
		}
	}

	function cache_flush() {
		$c1 = new MySqlTable_yp_mysql_caching('cache');
		$sql = "TRUNCATE TABLE ".$GLOBALS['db_table_name']."";
		$c1->executeQuery($sql);
	}

}

?>

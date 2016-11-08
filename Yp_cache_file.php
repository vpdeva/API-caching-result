<?php

class Yp_cache {
	
	function Yp_cache() {
	}
	
	function get_file_full_path($key, $type='') {
		$file_name = $key;
		if($type!='') $file_name .= '-'.$type;
		$file_name = dirname(__FILE__).'/file/'.$file_name.'.txt';
		return $file_name;
	}
	
	//returns cached data, or false if nothing is found
	function cache_get($criteria=array()) {
		$key = $criteria['key'];
		$type = $criteria['type'];
		
		if($key!='') {
			
			if(file_exists($this->get_file_full_path($key,$type))) {
				
				$now = time();
				
				$fhandle = fopen($this->get_file_full_path($key,$type), 'r'); 
				$file_data=''; 
				while(!feof($fhandle)) $file_data .= fread($fhandle, filesize($this->get_file_full_path($key,$type))); 
				fclose($fhandle);
				
				$file_data = json_decode($file_data,true);
				$data = $file_data['data'];
				$expire = $file_data['expire'];
				
				if($expire>$now||$expire=='') {
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
		
		if($key!=''&&$data!='') {
			
			$this->cache_delete(array("key"=>$key, "type"=>$type));
			
			if($expire!='') $expire = $expire+time();
			else $expire='';
			
			$file_data['data'] = $data;
			$file_data['expire'] = $expire;
			$file_data = json_encode($file_data);
			
			//save data
			$this->createFile($this->get_file_full_path($key,$type),$file_data);
		}
	}
	
	function cache_delete($criteria=array()) {
		$key = $criteria['key'];
		$type = $criteria['type'];
		
		if($key!='') {
			if(file_exists($this->get_file_full_path($key,$type))) unlink($this->get_file_full_path($key,$type));
		}
		else {
			return false;
		}
	}
	
	function cache_flush() {
		//delete all files
		$mask = dirname(__FILE__).'/file/*.txt';
		array_map( "unlink", glob( $mask ) );
	}
	
	function createFile($file,$content) {
		$fh = fopen($file, 'w') or die("Can't create the file");
		fwrite($fh, $content);
		fclose($fh);
	}

}

?>
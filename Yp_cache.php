<?php
include_once("config.php");

if($GLOBALS['yp_caching_storage']=='mysql') include_once("Yp_cache_mysql.php");
elseif($GLOBALS['yp_caching_storage']=='file') include_once("Yp_cache_file.php");
?>

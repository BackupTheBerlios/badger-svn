<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://badger.berlios.org 
*
**/
define("BADGER_ROOT", "../../"); 
include BADGER_ROOT.'core/log/badgerLog.php';
function handleBadgerException($e){
	echo "Error Message: " . $e->getMessage();
	echo "<br /><br />";
	echo "Error Code:" . $e->getCode();
	$logged_error = "ERROR:" . $e->getMessage() . " - ERROR CODE: " . $e->getCode() . " ON LINE " . $e->getLine() . " IN FILE " . $e->getFile();
	$logger->log($logged_error);
}

?>
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
require_once(BADGER_ROOT . "/includes/includes.php");
  
try{
	function badgerExceptionTest(){
	
		 throw new badgerException('2');
		
	}
	$something = badgerExceptionTest();
 
}catch (Exception $e) {
   handleBadgerException($e);
}

?>
<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
**/
define("BADGER_ROOT", "../.."); 
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
 
	function badgerExceptionTest(){
	
		 throw new BadgerException('fuck', 'me', "gfh");
		
	}
	$something = badgerExceptionTest();
 


require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
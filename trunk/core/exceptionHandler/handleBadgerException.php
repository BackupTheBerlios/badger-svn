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
/**
 * @author baxxter, sperber 
 * @version $LastChangedRevision$
 */
/**
 * defines root path relative to current location
 */
define("BADGER_ROOT", "../../"); 

/**
 * function called upon by global exception handler
 * 
 * @param exception object
 * @return void
 */
function handleBadgerException($e){
	
	/**
	 * Object containing global logging information
	 * 
	 * @var object
	 */	
	global $logger; 
	
	echo "<b>ERROR!</b><br />";
	echo "Error Code:" . $e->getCode(); //retrieve error code from exception object
	
	#$errorMessage = getFromDatabase("error", $e->getCode());
	#echo $errorMessage; 
	/**
	 * Compiled error message
	 * 
	 * @var string 
	 */	
	$loggedError = "ERROR: - ERROR CODE: " . $e->getCode() . " ON LINE " . $e->getLine() . " IN FILE " . $e->getFile(); // compile error message to be logged
	$logger->log($loggedError); //write to log file
}

?>
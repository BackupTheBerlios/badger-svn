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
 * defines root path relative to current location
 */
define("BADGER_ROOT", "../../"); 

/**
 * class used to extend the default exception class 
 * 
 * @author baxxter, sperber 
 * @version $LastChangedRevision$
 */
class badgerException extends Exception
{
	/**
	 * AdditionalInfo regarding the exception
	 * 
	 * @var string 
	 */
	private $additionalInfo;
  
   // Redefine the exception so code isn't optional
   	public function __construct($code, $additionalInfo = 'test') {
		$this->additionalInfo = $additionalInfo;
       	// call default exception constructor
		parent::__construct($message = NULL, $code);
   	}
   	/**
 * function to receive the additionalInfo
 * 
 * @return String
 */
	public function getAdditionalInfo (){
	return $this->additionalInfo;		
	}
}
?>
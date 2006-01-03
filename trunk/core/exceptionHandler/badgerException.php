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
include(BADGER_ROOT . "/includes/includes.php"); 


class badgerException extends Exception
{
   // Redefine the exception so message isn't optional
   public function __construct($message, $code = 0) {
       // some code
  
       // make sure everything is assigned properly
       parent::__construct($message, $code);
   }

}
?>
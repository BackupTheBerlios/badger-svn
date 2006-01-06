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
 
 //document root path relative to current location
 define("BADGER_ROOT", "../"); 
 
 //Includes
 
/**
 * makes use of the dbAdapter database abstraction layer
 */
 require_once BADGER_ROOT.'core/dbAdapter/DB.php';

/**
 * global config values
 */
 require_once BADGER_ROOT.'includes/config.inc.php';
 
 /**
 * makes global logging functionality available on all pages
 */
 require_once BADGER_ROOT.'core/log/badgerLog.php';
 
 /**
 * makes global badgerException class available
 */
 require_once BADGER_ROOT.'core/exceptionHandler/BadgerException.class.php';
 
 /**
 * Exception handling function available on all pages including this file
 */
 require_once BADGER_ROOT.'core/exceptionHandler/handleBadgerException.php';
 
/**
 * makes translation libs available
 */
require_once BADGER_ROOT.'core/Translation2/Translation2.php';
 
?>

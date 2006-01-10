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



 // Database Values
 	/**
	 * Type of database
	 * 
	 * @var global constant
	 */	
 define("DB_TYPE", "mysql");
 
 	/**
	 * Database username
	 * 
	 * @var global constant
	 */	
 define("DB_USERNAME", "root");
 
  	/**
	 * Database password
	 * 
	 * @var global constant
	 */	
 define("DB_PASSWORD", "");
 
  	/**
	 * Database host address
	 * 
	 * @var global constant
	 */	
 define("DB_HOST", "localhost");
 
  	/**
	 * Name of the database to be used
	 * 
	 * @var global constant
	 */	
 define("DB_DATABASE_NAME", "badger");
 
 // Errorhandling & Logging
 
  	/**
	 * Path and name of logging file
	 * 
	 * @var global constant
	 */	
 define("LOG_FILE_NAME", BADGER_ROOT."/test_exceptions.log");
 
  	/**
	 * Formatting for date used in log entries according to http://www.php.net/date
	 * 
	 * @var global constant
	 */	
 define("LOG_DATE_FORMAT", "Ymd-His");
 
 // Internationalisation: Language ID
 define("LANGUAGE_ID", "en");
 
// Report ALL Errors
error_reporting(E_ALL);
 
?>
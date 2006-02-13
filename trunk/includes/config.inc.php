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
 define("DB_USERNAME", "projectssql2");
 
  	/**
	 * Database password
	 * 
	 * @var global constant
	 */	
 define("DB_PASSWORD", "b4dg3rdbdb");
 
  	/**
	 * Database host address
	 * 
	 * @var global constant
	 */	
 define("DB_HOST", "edop.de");
 
  	/**
	 * Name of the database to be used
	 * 
	 * @var global constant
	 */	
 define("DB_DATABASE_NAME", "projectssql2");
 
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
 
// Report ALL Errors
error_reporting(E_ALL);
 
?>
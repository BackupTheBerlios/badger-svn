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
 //Test for log in a file
 //document root path relative to current location 
 define("BADGER_ROOT", "../../"); 
 require_once BADGER_ROOT.'/core/log/Log.php';
 
 
 $filename = LOG_FILE_NAME;
 $eventDate = date(LOG_DATE_FORMAT); 

 /* Write some entries to the log file. */
 $conf = array('lineFormat' => '%2$s [%3$s] %4$s', 'timeFormat' => '%H:%M:%S');
 $logger = &Log::singleton('file', $filename, $eventDate, $conf);
?>

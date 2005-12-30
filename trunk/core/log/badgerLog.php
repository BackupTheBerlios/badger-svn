<?php
/*
 * Created on 30.12.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 //Test for log in a file
 //document root path relative to current location 
 define("BADGER_ROOT", "../../"); 
 
 include BADGER_ROOT.'/core/log/Log.php';
 
 
 $filename = LOG_FILE_NAME;
 $eventDate = date(LOG_DATE_FORMAT); 

 /* Write some entries to the log file. */
 $conf = array('lineFormat' => '%2$s [%3$s] %4$s', 'timeFormat' => '%H:%M:%S');
 $logger = &Log::singleton('file', $filename, $eventDate, $conf);
?>

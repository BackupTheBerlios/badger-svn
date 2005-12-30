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

/* this is a test*/

define("BADGER_ROOT", "../../"); 
include(BADGER_ROOT . "/includes/includes.php"); 


function errorHandler($code, $message, $file, $line)
{
    global $logger;

    /* Map the PHP error to a Log priority. */
    switch ($code) {
    case E_WARNING:
    	echo "Warning";
    case E_USER_WARNING:
        $priority = PEAR_LOG_WARNING;
        echo "User Warning";
        break;
    case E_NOTICE:
    case E_USER_NOTICE:
        $priority = PEAR_LOG_NOTICE;
        echo "Notice";
        break;
    case E_ERROR:
    case E_USER_ERROR:
        $priority = PEAR_LOG_ERR;
        echo "Error";
        break;
    default:
        $priotity = PEAR_LOG_INFO;
        echo "Info";
    }

    #$logger->log($message . ' in ' . $file . ' at line ' . $line,
	#                 $priority);

}

#$logger = &Log::singleton('console', '', 'ident');


set_error_handler('errorHandler');
trigger_error('This is an information log message.', E_USER_NOTICE);
#$test = test();
?>

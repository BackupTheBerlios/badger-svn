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
 require_once BADGER_ROOT.'core/dbAdapter/DB.php';
 require_once BADGER_ROOT.'includes/config.inc.php';
 require_once BADGER_ROOT.'core/log/badgerLog.php';
 require_once BADGER_ROOT.'core/exceptionHandler/badgerException.php';
 require_once BADGER_ROOT.'core/exceptionHandler/handleBadgerException.php';
 require_once BADGER_ROOT.'core/Translation2/Translation2.php';
 
?>

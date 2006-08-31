<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
**/
define ('BADGER_ROOT', '../..');

require_once (BADGER_ROOT . '/includes/fileHeaderFrontEnd.inc.php');

require_once (BADGER_ROOT . '/core/Date.php');

$date = new Date('2006-01-29');
$date2 = new Date('2006-02-29');
$date3 = new Date($date2);
$date3->addSeconds(30 * 24 * 60 * 60);

echo "date: " . $date->getDate() . " date2: " . $date2->getDate() . " date3: " . $date3->getDate();

?>
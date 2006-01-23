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
 
define("BADGER_ROOT", "../.."); 
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once BADGER_ROOT.'/core/UserSettings.class.php';

$us = new UserSettings($badgerDb);

try {
	echo $us->getProperty('password') . "<br />\n";
} catch (BadgerException $ex) {
	echo "Exception!" . "<br />\n";
} 

// set test 2
$us->setProperty('password','Hans');
echo $us->getProperty('password') . "<br />\n";
$us->setProperty('password','Paul');
echo $us->getProperty('password') . "<br />\n";

// set
$us->setProperty('123','Haus');
echo $us->getProperty('123') . "<br />\n";

// del
$us->delProperty('123');
try {
	echo $us->getProperty('123') . "<br />\n";
} catch (BadgerException $ex) {
	handleBadgerException($ex);
} 

?>
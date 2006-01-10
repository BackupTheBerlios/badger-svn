<?php
/*
 * Created on Jan 10, 2006
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
define("BADGER_ROOT", "../.."); 
require_once BADGER_ROOT.'/includes/includes.php';
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
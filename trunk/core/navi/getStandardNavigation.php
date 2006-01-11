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

/**
 * This file is called by StandardNavigation. It externalizes the CSS and JS code of
 * StandardNavigation, assuming it does not change for each side. This saves some
 * bandwith, as this file should be cached.
 * 
 * @author Eni Kao
 */
define ('BADGER_ROOT', '../..');

require_once(BADGER_ROOT . '/includes/includes.php');
require_once(BADGER_ROOT . '/core/navi/StandardNavigation.class.php');
require_once(BADGER_ROOT . '/core/navi/NavigationFromDB.class.php');

if (isset($_GET['part'])) {
	
	$navi = NavigationFromDB::getNavigation();
	$naviObj = new StandardNavigation();
	$naviObj->setStructure($navi);
	
	//We do our best to get this cached
	header('Cache-Control: public');
	header('Expires: ' . date('r', time() + 24 * 60 * 60));
	
	
	switch ($_GET['part']) {
		case 'css':
			header('Content-Type: text/css');
			
			echo $naviObj->getCSS();
			break;
		
		case 'js':
			header('Content-Type: text/javascript');
			
			echo $naviObj->getJS();
			break;
	}
}
?>
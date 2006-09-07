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
define('BADGER_ROOT', '../..'); 
require_once BADGER_ROOT . '/includes/fileHeaderBackEnd.inc.php';

if (isset($_GET['action'])) {
	switch (getGPC($_GET, 'action')) {
		case 'select':
			addSelection();
			break;
		
		case 'unselect':
			delSelection();
			break;

		default:
			echo 'illegal call';
	}
}

function addSelection() {
	global $us;
	
	if (!isset($_GET['id'])) {
		return;
	}
	
	try {
		$preselectedAccounts = $us->getProperty('statisticsPreselectedAccounts');
	} catch (BadgerException $ex) {}
		
	$ids = getGPC($_GET, 'id', 'integerList');
	
	foreach ($ids as $id) {
		if (!array_search((int) $id, $preselectedAccounts)) {
			$preselectedAccounts[] = (int) $id;
		}
	}
	
	$us->setProperty('statisticsPreselectedAccounts', $preselectedAccounts);
	
	echo 'select successful';
}

function delSelection() {
	global $us;
	
	if (!isset($_GET['id'])) {
		return;
	}
	
	if (getGPC($_GET, 'id') != 'all') {
		try {
			$preselectedAccounts = $us->getProperty('statisticsPreselectedAccounts');
		} catch (BadgerException $ex) {}
			
		$ids = getGPC($_GET, 'id', 'integerList');
		
		foreach ($ids as $id) {
			if ($key = array_search((int) $id, $preselectedAccounts)) {
				$preselectedAccounts[$key] = null;
			}
		}
	} else {
		$preselectedAccounts = array();
	}
	
	$us->setProperty('statisticsPreselectedAccounts', $preselectedAccounts);

	echo 'unselect successful';
}
?>
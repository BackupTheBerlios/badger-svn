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
require_once BADGER_ROOT . '/core/urlTools.php';
require_once BADGER_ROOT . '/core/update/common.php';

if (getBadgerDbVersion() !== BADGER_VERSION) {

	$urlParts = getCurrentURL();

	if (substr($urlParts['path'], -23) !== '/core/update/update.php') {

		$urlParts['path'] = BADGER_ROOT . '/core/update/update.php';
		
		unset($urlParts['query']);
		unset($urlParts['fragment']);
		
		$url = buildURL($urlParts);
	
		$logger->log('Update: Redirect to Update URL: ' . $url);
	
		header('Location: ' . $url);
		
		exit;
	}
}
?>
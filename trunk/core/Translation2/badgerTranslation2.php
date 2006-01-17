<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Finance Management
* Visit http://badger.berlios.org 
*
**/
require_once BADGER_ROOT.'/core/UserSettings.class.php';

$us = new UserSettings($badgerDb);

$driver = 'DB';

$tr =& Translation2::factory($driver, $badgerDbConnectionInfo);

function getBadgerTranslation2 ($page, $id){
	global $tr,$us;
	return $tr->get($id, $page, $us->getProperty('badgerLanguage'));
}

?>
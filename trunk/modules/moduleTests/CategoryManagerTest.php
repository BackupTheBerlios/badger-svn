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
define ('BADGER_ROOT', '../..');

require_once BADGER_ROOT . '/includes/fileHeaderFrontEnd.inc.php';
require_once BADGER_ROOT . '/modules/account/CategoryManager.class.php';

header('Content-Type: text/plain');
define ('endl', "\n");

$cm = new CategoryManager($badgerDb);

while ($cat = $cm->getNextCategory()) {
	echo 'Category Title: ' . $cat->getTitle() . endl;
}

$cat1 = $cm->getCategoryById(1);
echo 'Category Id: ' . $cat1->getId() . endl;

$cat2 = $cm->addCategory('Neue Kategorie ' . rand(0, 100), 'Bähschraipunk');
echo 'New Category Title: ' . $cat2->getTitle() . endl;

$cat3 = $cm->addCategory('Temporäre Kategorie');
$tmpId = $cat3->getId();
echo 'Temporary Category Id: ' . $tmpId . endl;

$cm->deleteCategory($tmpId);

$cat4 = $cm->getCategoryById($tmpId);
echo 'Temporary Category Title (never shown): ' . $cat4->getTitle() . endl;
?>
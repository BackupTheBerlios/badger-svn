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
require_once BADGER_ROOT . '/modules/account/CurrencyManager.class.php';

header('Content-Type: text/plain');
define ('endl', "\n");

$cm = new CurrencyManager($badgerDb);

while ($curr = $cm->getNextCurrency()) {
	echo 'Currency Symbol: ' . $curr->getSymbol() . endl;
}

$curr1 = $cm->getCurrencyById(1);
echo 'Currency Id: ' . $curr1->getId() . endl;

$curr2 = $cm->addCurrency('SY' . rand(0, 9), 'Langer Titel');
echo 'New Currency Symbol: ' . $curr2->getSymbol() . endl;

$curr3 = $cm->addCurrency('tmp', 'temporär');
$tmpId = $curr3->getId();
echo 'Temporary Currency Id: ' . $tmpId . endl;

$cm->deleteCurrency($tmpId);

$curr4 = $cm->getCurrencyById($tmpId);
echo 'Temporary Currency Symbol (never shown): ' . $curr4->getSymbol() . endl;
?>
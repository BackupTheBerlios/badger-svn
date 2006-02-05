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
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';

header('Content-Type: text/plain');
define ('endl', "\n");

$am = new AccountManager($badgerDb);

while ($acc = $am->getNextAccount()) {
	echo $acc->getTitle() . endl;
}

$acc1 = $am->getAccountById(1);
echo $acc1->getId() . endl;

$curr = new Currency(1, 'TUR', 'Teuro');
$lowerLimit = new Amount(rand(-100, 100));
$upperLimit = new Amount(rand(1000, 3000));

$acc2 = $am->addAccount('Neues Konto ' . rand(0, 100), $curr, 'Bähschraipunk', $lowerLimit, $upperLimit);
echo $acc2->getTitle() . endl;

$acc3 = $am->addAccount('Temporäres Konto', $curr);
$tmpId = $acc3->getId();
echo $tmpId . endl;

$am->deleteAccount($tmpId);

$acc4 = $am->getAccountById($tmpId);
echo $acc4->getTitle() . endl;
?>
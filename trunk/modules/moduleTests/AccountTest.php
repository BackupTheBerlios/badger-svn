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
require_once BADGER_ROOT . '/modules/account/CategoryManager.class.php';

$am = new AccountManager($badgerDb);

//ugly, but required
$CategoryManager = new CategoryManager($badgerDb);

header('Content-Type: text/plain');
define ('endl', "\n");


$acc = $am->getAccountById(1);
echo $acc->getId() . endl;
echo $acc->getTitle() . endl;

$trans1 = $acc->getFinishedTransactionById(1);
echo $trans1->getId() . endl;

$valutaDate = new Date('2006-10-10');
$amount = new Amount(rand(-10000, 10000));

$trans2 = $acc->addFinishedTransaction('Neue Transaktion ' . rand(0, 100), $amount, 'Bähschraipunk', $valutaDate, null, null, true);
echo $trans2->getTitle() . endl;

$trans3 = $acc->addFinishedTransaction('Temporäre Transaktion', $amount);
$tmpId = $trans3->getId();
echo $tmpId . endl;

$acc->deleteFinishedTransaction($tmpId);

$trans4 = $acc->getFinishedTransactionById($tmpId);
echo $trans4->getTitle() . endl;


?>
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

header('Content-Type: text/plain');
define ('endl', "\n");


$acc = $am->getAccountById(1);
echo 'Account Id: ' . $acc->getId() . endl;
echo 'Account Title: ' . $acc->getTitle() . endl;

try {
	echo 'Property notExistent: ' . $acc->getProperty('notExistent') . endl;
} catch (BadgerException $ex) {
	echo "Exception!" . endl;
} 

// set test 2
$acc->setProperty('password','Hans');
echo 'Property password: ' . $acc->getProperty('password') . endl;
$acc->setProperty('password','Paul');
echo 'Property password (2): ' . $acc->getProperty('password') . endl;

// set
$acc->setProperty('123','Haus');
echo 'Property 123: ' . $acc->getProperty('123') . endl;

// del
$acc->delProperty('123');
try {
	echo 'Property 123 (2): ' . $acc->getProperty('123') . endl;
} catch (BadgerException $ex) {
	handleBadgerException($ex);
	echo endl;
} 

while ($trans = $acc->getNextPlannedTransaction()) {
	echo 'Planned Transaction Title: ' . $trans->getTitle() . endl;
}

while ($trans = $acc->getNextFinishedTransaction()) {
	echo 'Finished Transaction Title: ' . $trans->getTitle() . endl;
}

$acc->resetFinishedTransactions();
while ($trans = $acc->getNextTransaction()) {
	echo 'Transaction Title: ' . $trans->getTitle() . ' Source Planned title: ' . (is_null($tmp = $trans->getSourcePlannedTransaction()) ? '' : $tmp->getTitle()) . endl;
}

$trans1 = $acc->getFinishedTransactionById(1);
echo 'Finished Transaction Id: ' . $trans1->getId() . endl;

$valutaDate = new Date('2006-10-10');
$amount = new Amount(rand(-10000, 10000));

$trans2 = $acc->addFinishedTransaction($amount, 'Neue Transaktion ' . rand(0, 100), 'Bähschraipunk', $valutaDate, null, null, true);
echo 'New Finished Transaction Title: ' . $trans2->getTitle() . endl;

$trans3 = $acc->addFinishedTransaction($amount, 'Temporäre Transaktion');
$tmpId = $trans3->getId();
echo 'Temporary Finished Transaction Id: ' . $tmpId . endl;

$acc->deleteFinishedTransaction($tmpId);

$ptrans1 = $acc->getPlannedTransactionById(1);
echo 'Planned Transaction Id: ' . $ptrans1->getId() . endl;

$beginDate = new Date('2000-10-' . rand(1, 31));
$endDate = new Date('2010-10-10');

$ptrans2 = $acc->addPlannedTransaction('Neue geplante Transaktion ' . rand(0, 100), $amount, 'year', 2, $beginDate, $endDate, 'Bähschraipunk 2');
echo 'New Planned Transaction Title: ' . $ptrans2->getTitle() . endl;

$ptrans3 = $acc->addPlannedTransaction('Temporäre geplante Transaktion', $amount, 'week', 10, $beginDate);
$tmpPId = $ptrans3->getId();
echo 'Temporary Planned Transaction Id: ' . $tmpPId . endl;

$acc->deletePlannedTransaction($tmpPId);

$trans4 = $acc->getFinishedTransactionById($tmpId);
echo 'Temporary Finished Transaction Title (never shown)' . $trans4->getTitle() . endl;

$ptrans4 =$acc->getPlannedTransactionById($tmpPId);
echo 'Temporary Planned Transaction Title (never shown)' . $ptrans4->getTitle() . endl;
?>
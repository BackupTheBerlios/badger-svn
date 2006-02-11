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

require_once BADGER_ROOT . '/modules/account/Account.class.php';
require_once BADGER_ROOT . '/core/Date/Span.php';

/**
 * Returns the Account balance for $account at the end of each day between $startDate and $endDate.
 * 
 * Considers the planned transactions of $account.
 * 
 * @param object $account The Account object for which the balance should be calculated. 
 * It should be 'fresh', i. e. no transactions of any type should have been fetched from it.
 * @param object $startDate The first date the balance should be calculated for as Date object.
 * @param object $endDate The last date the balance should be calculated for as Date object.
 * @return array Array of Amount objects corresponding to the balance of $account at each day between
 * $startDate and $endDate. The array keys are the dates as ISO-String (yyyy-mm-dd). 
 */
function getDailyAmount($account, $startDate, $endDate) {

	$account->setTargetFutureCalcDate($endDate);
	$account->setOrder(array (array ('key' => 'valutaDate', 'dir' => 'asc')));
	
	$result = array();
	
	$startDate->setHour(0);
	$startDate->setMinute(0);
	$startDate->setSecond(0);
	
	$endDate->setHour(0);
	$endDate->setMinute(0);
	$endDate->setSecond(0);

	$currentDate = new Date($startDate);
	$currentAmount = new Amount();
	
	//foreach transaction
	while ($currentTransaction = $account->getNextTransaction()) {
		if ($currentDate->after($endDate)) {
			//we reached $endDAte
			break;
		}

		//fill all dates between last and this transaction with the old amount
		while (is_null($tmp = $currentTransaction->getValutaDate()) ? false : $currentDate->before($tmp)) {
			$result[$currentDate->getDate()] = new Amount($currentAmount);
			
			$currentDate->addSeconds(24 * 60 * 60);
			
			if ($currentDate->after($endDate)) {
				//we reached $endDAte
				break;
			}
		}

		$currentAmount->add($currentTransaction->getAmount());
	}
	
	//fill all dates after the last transaction with the newest amount
	while (Date::compare($currentDate, $endDate) <= 0) {
		$result[$currentDate->getDate()] = new Amount($currentAmount);
		
		$currentDate->addSeconds(24 * 60 * 60);
	}

	return $result;
}
?>
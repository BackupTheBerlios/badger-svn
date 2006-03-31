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

function getSpendingMoney($accountId, $startDate) {
	global $badgerDb;

	$accountManager = new AccountManager($badgerDb);
	
	$account = $accountManager->getAccountById($accountId);
	
	$account->setType('finished');

	$account->setOrder(array (array ('key' => 'valutaDate', 'dir' => 'asc')));
	
	$account->setFilter(array (
		array (
			'key' => 'valutaDate',
			'op' => 'ge',
			'val' => $startDate
		),
		array (
			'key' => 'periodical',
			'op' => 'eq',
			'val' => false
		),
		array (
			'key' => 'exceptional',
			'op' => 'eq',
			'val' => false
		)
	));
	
	$sum = new Amount();
	$realStartDate = false;
	
	while ($currentTransaction = $account->getNextFinishedTransaction()) {
		if (!$realStartDate) {
			$realStartDate = $currentTransaction->getValutaDate();
		}

		$sum->add($currentTransaction->getAmount());
	}
	
	$span = new Date_Span($realStartDate, new Date());
	$count = $span->toDays();

	if ($count > 0) {
		$sum->div($count);
	}
	
	return $sum;
}

function getCategorySelectArray() {
	global $badgerDb;
	$cm = new CategoryManager($badgerDb);
	$order = array ( 
	array(
       'key' => 'title',
       'dir' => 'asc'
       )
 	);
	
	$cm->setOrder($order);
	
	$parentCats = array();
 	$parentCats['NULL'] = "";
	
	while ($cat = $cm->getNextCategory()) {
		$cat->getParent();
	}
	
	$cm->resetCategories();
	
	while ($cat = $cm->getNextCategory()) {
		if(is_null($cat->getParent())){
			$parentCats[$cat->getId()] = $cat->getTitle();
			$children = $cat->getChildren();
			//echo "<pre>"; print_r($children); echo "</pre>";
			if($children){
				foreach( $children as $key=>$value ){
					$parentCats[$value->getId()] = " - " . $value->getTitle();
				};
			};
		};
	};
 
	return $parentCats;
}

function handleOldFinishedTransactions($accountManager) {
	$accountManager->resetAccounts();
	
	while ($account = $accountManager->getNextAccount()) {
		transferFormerFinishedTransactions($account);
	}	
}

function transferFormerFinishedTransactions($account) {
	global $us;
	
	if ($us->getProperty('autoExpandPlannedTransactions') == false) {
		return;
	}

	$now = new Date();
	$now->setHour(0);
	$now->setMinute(0);
	$now->setSecond(0);

	$account->setType('planned');	

	$account->setFilter(array (
		array (
			'key' => 'beginDate',
			'op' => 'le',
			'val' => $now 
		)
	));

	try {
		$lastInsertDate = $us->getProperty('Account_' . $account->getId() . '_LastTransferFormerFinishedTransactions');
	} catch (BadgerException $ex) {
		$lastInsertDate = new Date('1000-01-01');
	}

	$us->setProperty('Account_' . $account->getId() . '_LastTransferFormerFinishedTransactions', $now);
	
	if (!$lastInsertDate->before($now)) {
		return;
	}

	while ($currentTransaction = $account->getNextPlannedTransaction()) { 
		$date = new Date($currentTransaction->getBeginDate());
		$dayOfMonth = $date->getDay();
		
		//While we are before now and the end date of this transaction
		while(
			!$date->after($now)
			&& !$date->after(is_null($tmp = $currentTransaction->getEndDate()) ? new Date('9999-12-31') : $tmp)
		){

			if($date->after($lastInsertDate)) {
				$account->addFinishedTransaction(
					$currentTransaction->getAmount(),
					$currentTransaction->getTitle(),
					$currentTransaction->getDescription(),
					new Date($date),
					$currentTransaction->getTransactionPartner(),
					$currentTransaction->getCategory(),
					$currentTransaction->getOutsideCapital(),
					false,
					true
				);
			}

			//do the date calculation
			switch ($currentTransaction->getRepeatUnit()){
				case 'day': 
					$date->addSeconds($currentTransaction->getRepeatFrequency() * 24 * 60 * 60);
					break;
					
				case 'week':
					$date->addSeconds($currentTransaction->getRepeatFrequency() * 7 * 24 * 60 * 60);
					break;
					
				case 'month':
					//Set the month
					$date = new Date(Date_Calc::endOfMonthBySpan($currentTransaction->getRepeatFrequency(), $date->getMonth(), $date->getYear(), '%Y-%m-%d'));
					//And count back as far as the last valid day of this month
					while($date->getDay() > $dayOfMonth){
						$date->subtractSeconds(24 * 60 * 60);
					}
					break; 
				
				case 'year':
					$newYear = $date->getYear() + $currentTransaction->getRepeatFrequency();
					if (
						$dayOfMonth == 29
						&& $date->getMonth() == 2
						&& !Date_Calc::isLeapYear($newYear)
					) {
						$date->setDay(28);
					} else {
						$date->setDay($dayOfMonth);
					}
					
					$date->setYear($newYear);
					break;
				
				default:
					throw new BadgerException('Account', 'IllegalRepeatUnit', $currentTransaction->getRepeatUnit());
					exit;
			}
		}
	} 

}
?>
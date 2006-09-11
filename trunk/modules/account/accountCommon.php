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
	while ($currentTransaction = $account->getNextFinishedTransaction()) {
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
		array (
			'key' => 'parentTitle',
			'dir' => 'asc'
		),
		array (
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
		transferFinishedTransactions($account, $currentTransaction, $lastInsertDate);
	} 
}

function transferFinishedTransactions($account, $plannedTransaction, $startDate = null) {
	$now = new Date();
	$now->setHour(0);
	$now->setMinute(0);
	$now->setSecond(0);

	$date = new Date($plannedTransaction->getBeginDate());
	$dayOfMonth = $date->getDay();
	
	//While we are before now and the end date of this transaction
	while(
		!$date->after($now)
		&& !$date->after(is_null($tmp = $plannedTransaction->getEndDate()) ? new Date('9999-12-31') : $tmp)
	){

		if($startDate === null || $date->after($startDate)) {
			$account->addFinishedTransaction(
				$plannedTransaction->getAmount(),
				$plannedTransaction->getTitle(),
				$plannedTransaction->getDescription(),
				new Date($date),
				$plannedTransaction->getTransactionPartner(),
				$plannedTransaction->getCategory(),
				$plannedTransaction->getOutsideCapital(),
				false,
				true,
				$plannedTransaction
			);
		}

		//do the date calculation
		switch ($plannedTransaction->getRepeatUnit()){
			case 'day': 
				$date->addSeconds($plannedTransaction->getRepeatFrequency() * 24 * 60 * 60);
				break;
				
			case 'week':
				$date->addSeconds($plannedTransaction->getRepeatFrequency() * 7 * 24 * 60 * 60);
				break;
				
			case 'month':
				//Set the month
				$date = new Date(Date_Calc::endOfMonthBySpan($plannedTransaction->getRepeatFrequency(), $date->getMonth(), $date->getYear(), '%Y-%m-%d'));
				//And count back as far as the last valid day of this month
				while($date->getDay() > $dayOfMonth){
					$date->subtractSeconds(24 * 60 * 60);
				}
				break; 
			
			case 'year':
				$newYear = $date->getYear() + $plannedTransaction->getRepeatFrequency();
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
				throw new BadgerException('Account', 'IllegalRepeatUnit', $plannedTransaction->getRepeatUnit());
				exit;
		}
	}
}

class CompareTransaction {
	private $order;

	public function __construct($order) {
		$this->order = $order;
	}
	
	/**
	 * Compares two transactions according to $this->order.
	 * 
	 * For use with usort type of sort functions.
	 * 
	 * @param $aa object The first Transaction object.
	 * @param $bb object The second Transaction object.
	 * 
	 * @return integer -1 if $aa is smaller than $bb, 0 if they are equal, 1 if $aa is bigger than $bb.
	 */
	public function compare($aa, $bb) {
		$tmp = 0;
	
		$default = 0;
		
		$repeatUnits = array (
			'day' => 1,
			'week' => 2,
			'month' => 3,
			'year' => 4
		);
	
		for ($run = 0; isset($this->order[$run]); $run++) {
			if ($this->order[$run]['dir'] == 'asc') {
				$a = $aa;
				$b = $bb;
				$default = -1;
			} else {
				$a = $bb;
				$b = $aa;
				$default = 1;
			}
			//echo "a: " . $a->getId() . "<br />";
			
			switch ($this->order[$run]['key']) {
				case 'transactionId':
				case 'plannedTransactionId':
				case 'finishedTransactionId':
					$tmp = $a->getId() - $b->getId();
					break;
				
				case 'type':
					$tmp = strncasecmp($a->getType(), $b->getType(), 9999);
					break;

				case 'accountTitle':
					$tmp = strncasecmp($a->getAccount()->getTitle(), $b->getAccount()->getTitle(), 9999);
					break;
	
				case 'title':
					$tmp = strncasecmp($a->getTitle(), $b->getTitle(), 9999);
					//echo $tmp;
					break;
				
				case 'description':
					$tmp = strncasecmp($a->getDescription(), $b->getDescription(), 9999);
					break;
					
				case 'valutaDate':
					if ($a->getValutaDate() && $b->getValutaDate()) {
						$tmp = Date::compare($a->getValutaDate(), $b->getValutaDate());
					} else if ($a->getValutaDate() && !$b->getValutaDate()) {
						$tmp = 1;
					} else if (!$a->getValutaDate() && $b->getValutaDate()) {
						$tmp = -1;
					}
					break;
				
				case 'beginDate':
					$tmp = Date::compare($a->getBeginDate(), $b->getBeginDate());
					break;
				
				case 'endDate':
					if ($a->getEndDate() && $b->getEndDate()) {
						$tmp = Date::compare($a->getEndDate(), $b->getEndDate());
					}
					break;
				
				case 'amount':
					$tmp = $a->getAmount()->compare($b->getAmount());
					break;
		
				case 'outsideCapital':
					$tmp = $a->getOutsideCapital()->sub($b->getOutsideCapital());
					break;
		
				case 'transactionPartner':
					$tmp = strncasecmp($a->getTransactionPartner(), $b->getTransactionPartner(), 9999);
					break;
				
				case 'categoryId':
					if ($a->getCategory() && $b->getCategory()) {
						$tmp = $a->getCategory()->getId() - $b->getCategory()->getId();
					} else if ($a->getCategory() && !$b->getCategory()) {
						$tmp = -1;
					} else if (!$a->getCategory() && $b->getCategory()) {
						$tmp = 1;
					}
					break;
				
				case 'categoryTitle':
					if ($a->getCategory() && $b->getCategory()) {
						$tmp = strncasecmp($a->getCategory()->getTitle(), $b->getCategory()->getTitle(), 9999);
					} else if ($a->getCategory()) {
						$tmp = -1;
					} else if ($b->getCategory()) {
						$tmp = 1;
					}
					//echo "tmp: $tmp</pre>";
					break;
				
				case 'parentCategoryId':
					if ($a->getCategory() && $a->getCategory()->getParent() && $b->getCategory() && $b->getCategory()->getParent()) {
						$tmp = $a->getCategory()->getParent()->getId() - $b->getCategory()->getParent()->getId();
					} else if ($a->getCategory() && $a->getCategory()->getParent()) {
						$tmp = -1;
					} else if ((!$a->getCategory() || !$a->getCategory()->getParent())) {
						$tmp = 1;
					}
					break;
				
				case 'parentCategoryTitle':
					if ($a->getCategory() && $a->getCategory()->getParent() && $b->getCategory() && $b->getCategory()->getParent()) {
						$tmp = strncasecmp($a->getCategory()->getParent()->getTitle(), $b->getCategory()->getParent()->getTitle(), 9999);
					} else if ($a->getCategory() && $a->getCategory()->getParent()) {
						$tmp = -1;
					} else if ($b->getCategory() && $b->getCategory()->getParent()) {
						$tmp = 1;
					}
					//echo "tmp: $tmp</pre>";
					break;
				
				case 'concatCategoryTitle':
					$aTitle = '';
					$bTitle = '';
					if ($a->getCategory() && $a->getCategory()->getParent()) {
						$aTitle = $a->getCategory()->getParent()->getTitle() . ' - ';
					}
					if ($a->getCategory()) {
						$aTitle .= $a->getCategory()->getTitle();
					}
					if ($b->getCategory() && $b->getCategory()->getParent()) {
						$bTitle = $b->getCategory()->getParent()->getTitle() . ' - ';
					}
					if ($b->getCategory()) {
						$bTitle .= $b->getCategory()->getTitle();
					}
					if ($aTitle != '' && $bTitle != '') {
						$tmp = strncasecmp($aTitle, $bTitle, 9999);
					} else if ($aTitle != '') {
						$tmp = -1;
					} else if ($bTitle != '') {
						$tmp = 1;
					}
					//echo "tmp: $tmp</pre>";
					break;
	
				case 'repeatUnit':
					$tmp = $repeatUnits[$a->getRepeatUnit()] - $repeatUnits[$b->getRepeatUnit()];
					break;
				
				case 'repeatFrequency':
					$tmp = $a->getRepeatFrequency() - $b->getRepeatFrequency();
					break;
				
				case 'sum':
					$tmp = 0;
					break;
			}
			
			if ($tmp != 0) {
				return $tmp;
			}
		}
	
		return $default;
	}
}

function getAllFinishedTransactions(&$finishedTransactions, $selectedFields) {
	$result = array();
	$currResultIndex = 0;

	foreach($finishedTransactions as $currentTransaction){
		$classAmount = ($currentTransaction->getAmount()->compare(0) >= 0) ? 'dgPositiveAmount' : 'dgNegativeAmount'; 

		$category = $currentTransaction->getCategory();
		if (!is_null($category)) {
			$parentCategory = $category->getParent();
		} else {
			$parentCategory = null;
		}

		if ($parentCategory) {
			$concatCategoryTitle = $parentCategory->getTitle() . ' - ';
		} else {
			$concatCategoryTitle = '';
		}
		if ($category) {
			$concatCategoryTitle .= $category->getTitle();
		}

		$result[$currResultIndex] = array();
		$result[$currResultIndex]['finishedTransactionId'] = $currentTransaction->getId(); 

		foreach ($selectedFields as $selectedField) {
			switch ($selectedField) {
				case 'accountTitle':
					$result[$currResultIndex]['accountTitle'] = $currentTransaction->getAccount()->getTitle();
					break;
				
				case 'title':
					$result[$currResultIndex]['title'] = $currentTransaction->getTitle();
					break;
				
				case 'description':
					$result[$currResultIndex]['description'] = $currentTransaction->getDescription();
					break;
			
				case 'valutaDate':
					$result[$currResultIndex]['valutaDate'] = ($tmp = $currentTransaction->getValutaDate()) ? $tmp->getFormatted() : '';
					break;
					
				case 'amount':
					$result[$currResultIndex]['amount'] = array (
						'class' => $classAmount,
						'content' => $currentTransaction->getAmount()->getFormatted()
					);
					break;
				
				case 'outsideCapital':
					$result[$currResultIndex]['outsideCapital'] = is_null($tmp = $currentTransaction->getOutsideCapital()) ? '' : $tmp;
					break;
				
				case 'transactionPartner':
					$result[$currResultIndex]['transactionPartner'] = $currentTransaction->getTransactionPartner();
					break;
					
				case 'categoryId':
					$result[$currResultIndex]['categoryId'] = ($category) ? $category->getId() : '';
					break;
				
				case 'categoryTitle':
					$result[$currResultIndex]['categoryTitle'] = ($category) ? $category->getTitle() : '';
					break;
				
				case 'parentCategoryId':
					$result[$currResultIndex]['parentCategoryId'] = ($parentCategory) ? $parentCategory->getId() : '';
					break;
				
				case 'parentCategoryTitle':
					$result[$currResultIndex]['parentCategoryTitle'] = ($parentCategory) ? $parentCategory->getTitle() : '';
					break;
				
				case 'concatCategoryTitle':
					$result[$currResultIndex]['concatCategoryTitle'] = $concatCategoryTitle;
					break;
				
				case 'exceptional':
					$result[$currResultIndex]['exceptional'] = is_null($tmp = $currentTransaction->getExceptional()) ? '' : $tmp;
					break;
				
				case 'periodical':
					$result[$currResultIndex]['periodical'] = is_null($tmp = $currentTransaction->getPeriodical()) ? '' : $tmp;
					break;
			} //switch
		} //foreach selectedFields
		
		$currResultIndex++;
	} //foreach finishedTransactions
	
	return $result;

}
?>
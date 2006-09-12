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

require_once BADGER_ROOT . '/core/Date.php';
require_once BADGER_ROOT . '/core/Amount.class.php';
require_once BADGER_ROOT . '/modules/account/Category.class.php';
require_once BADGER_ROOT . '/modules/account/CategoryManager.class.php';

/**
 * A finished transaction.
 * 
 * @author Eni Kao, Mampfred
 * @version $LastChangedRevision$
*/
class PlannedTransaction {
	/**
	 * The DB object.
	 * 
	 * @var object DB
	 */
	private $badgerDb;

	/**
	 * The Account this category belongs to.
	 * 
	 * @var object Account
	 */
	private $account;
	
	/**
	 * The id of this transaction.
	 * 
	 * @var integer
	 */
	private $id;

	/**
	 * The title of this transaction.
	 * 
	 * @var string
	 */
	private $title;
	
	private $originalTitle;

	/**
	 * The description of this transaction.
	 * 
	 * @var string
	 */
	private $description;

	/**
	 * The amount of this transaction.
	 * 
	 * @var object Amount
	 */
	private $amount;

	/**
	 * The origin of this transaction.
	 * 
	 * @var boolean
	 */
	private $outsideCapital;

	/**
	 * The transaction partner of this transaction.
	 * 
	 * @var string
	 */
	private $transactionPartner;

	/**
	 * The category of this transaction.
	 * 
	 * @var object Category
	 */
	private $category;

	/**
	 * The begin date of this transaction.
	 * 
	 * @var object Date
	 */
	private $beginDate;
	
	private $beginDateLocked;

	/**
	 * The end date of this transaction.
	 * 
	 * @var object Date
	 */
	private $endDate;
	
	private $endDateLocked;

	/**
	 * The repeat unit of this transaction.
	 * 
	 * day, week, month or year
	 * 
	 * @var string
	 */
	private $repeatUnit;

	/**
	 * The repeat frequency of this transaction.
	 * 
	 * @var integer
	 */
	private $repeatFrequency;
	
	/**
	 * The type of this transaction.
	 * 
	 * 'FinishedTransaction' or 'PlannedTransaction' (a expanded one)
	 * 
	 * @var string
	 */
	private $type;
	
	private $updateMode;
	
	const UPDATE_MODE_ALL = 1;
	const UPDATE_MODE_PREVIOUS = 2;
	const UPDATE_MODE_FOLLOWING = 3;
	
	private $otherPlannedTransaction;
	
	private $updateSplitDate;
	
	/**
	 * Creates a Planned Transaction.
	 * 
	 * @param $badgerDb object The DB object.
	 * @param $account object The Account object who created this Transaction.
	 * @param $data mixed An associative array with the values out of the DB OR the id of the Transaction.
	 * @param $repeatUnit string The repeat unit of the Transaction.
	 * @param $repeatFrequency string The repeat frequency of the Transaction.
	 * @param $beginDate object The Date object with the begin date of the Transaction.
	 * @param $endDate object The Date object with the end date of the Transaction.
	 * @param $title string The title of the Transaction.
	 * @param $amount object The Amount object with the amount of this Transaction.
	 * @param $description string The description of the Transaction.
	 * @param $transactionPartner string The transaction partner of the Transaction
	 * @param $outsideCapital boolean The origin of the Transaction.
	 */
    function __construct(
    	&$badgerDb,
    	&$account,
    	$data,
    	$repeatUnit = null,
    	$repeatFrequency = null,
    	$beginDate = null,
    	$endDate = null,
    	$title = null,
    	$amount = null,
    	$description = null,
    	$transactionPartner = null,
    	$category = null,
    	$outsideCapital = null,
    	$type = 'PlannedTransaction'
    ) {
    	$CategoryManager = new CategoryManager($badgerDb);
    	
    	$this->badgerDb = $badgerDb;
    	$this->account = $account;
    	
    	if (is_array($data)) {
			$this->id = $data['planned_transaction_id'];
    		$this->title = $data['title'];
    		$this->description = $data['description'];
    		$this->amount = new Amount($data['amount']);
    		$this->outsideCapital = $data['outside_capital'];
    		$this->transactionPartner =  $data['transaction_partner'];
    		if ($data['category_id']) {
    			$this->category = $CategoryManager->getCategoryById($data['category_id']);
    		}
    		$this->beginDate = new Date($data['begin_date']);
    		if ($data['end_date']) {
    			$this->endDate = new Date($data['end_date']);
    		}
    		$this->repeatUnit = $data['repeat_unit'];
    		$this->repeatFrequency = $data['repeat_frequency'];
    		$this->type = 'PlannedTransaction';
    	} else {
    		$this->id = $data;
    		$this->title = $title;
    		$this->description = $description;
    		$this->amount = $amount;
    		$this->outsideCapital = $outsideCapital;
    		$this->transactionPartner = $transactionPartner;
    		$this->category = $category;
    		$this->beginDate = $beginDate;
    		$this->endDate = $endDate;
    		$this->repeatUnit = $repeatUnit;
    		$this->repeatFrequency = $repeatFrequency;
    		$this->type = $type;
    	}
    	
    	$this->updateMode = self::UPDATE_MODE_ALL;
    	$this->otherPlannedTransaction = null;
    	$this->beginDateLocked = false;
    	$this->endDateLocked = false;
    	$this->originalTitle = $this->title; 
    }
    
	/**
	 * Returns the id.
	 * 
	 * @return integer The id of this transaction.
	 */
    public function getId() {
    	return $this->id;
    }
    
	/**
	 * Returns the title.
	 * 
	 * @return string The title of this transaction.
	 */
    public function getTitle() {
    	return $this->title;
    }
    
 	/**
 	 * Sets the title.
 	 * 
 	 * @param $title string The title of this transaction.
 	 */
 	public function setTitle($title) {
		$this->title = $title;
		
		$this->doUpdate("SET title = '" . $this->badgerDb->escapeSimple($title) . "'");
	}
	
	/**
	 * Returns the description.
	 * 
	 * @return string The description of this transaction.
	 */
    public function getDescription() {
    	return $this->description;
    }
    
 	/**
 	 * Sets the description.
 	 * 
 	 * @param $description string The description of this transaction.
 	 */
 	public function setDescription($description) {
		$this->description = $description;
		
		$this->doUpdate("SET description = '" . $this->badgerDb->escapeSimple($description) . "'");
	}
	
	/**
	 * Returns the begin date.
	 * 
	 * @return object The Date object with the begin date of this transaction.
	 */
    public function getBeginDate() {
    	return $this->beginDate;
    }
    
 	/**
 	 * Sets the begin date.
 	 * 
 	 * @param $beginDate object The Date object with the begin date of this transaction.
 	 */
 	public function setBeginDate($beginDate) {
		if (!$this->beginDateLocked) {
			$this->beginDate = $beginDate;
			
			$this->doUpdate("SET begin_date = '" . $beginDate->getDate() . "'", false);
		}
	}
	
	/**
	 * Returns the end date.
	 * 
	 * @return object The Date object with the end date of this transaction.
	 */
    public function getEndDate() {
    	return $this->endDate;
    }
    
 	/**
 	 * Sets the end date.
 	 * 
 	 * @param $endDate object The Date object with the end date of this transaction.
 	 */
 	public function setEndDate($endDate) {
 		if (!$this->endDateLocked) {
			$this->endDate = $endDate;
			
			if (!is_null($endDate)) {
				$dateVal = "'" . $endDate->getDate() . "'";
			} else {
				$dateVal = 'NULL';
			}
	
			$this->doUpdate("SET end_date = $dateVal", false);
 		}
	}
	
	/**
	 * Returns the amount.
	 * 
	 * @return object The Amount object with the amount of this transaction.
	 */
    public function getAmount() {
    	return $this->amount;
    }
    
 	/**
 	 * Sets the amount.
 	 * 
 	 * @param $amount object The Amount object with the amount of this transaction.
 	 */
 	public function setAmount($amount) {
		$this->amount = $amount;
		
		$this->doUpdate("SET amount = '" . $amount->get() . "'");
	}
	
	/**
	 * Returns the origin.
	 * 
	 * @return boolean true if this transaction is outside capital.
	 */
    public function getOutsideCapital() {
    	return $this->outsideCapital;
    }
    
 	/**
 	 * Sets the origin.
 	 * 
 	 * @param $outsideCapital boolean true if this transaction is outside capital.
 	 */
 	public function setOutsideCapital($outsideCapital) {
		$this->outsideCapital = $outsideCapital;
		
		$this->doUpdate("SET outside_capital = " . $this->badgerDb->quoteSmart($outsideCapital));
	}
	
	/**
	 * Returns the transaction partner.
	 * 
	 * @return string The transaction partner of this transaction.
	 */
    public function getTransactionPartner() {
    	return $this->transactionPartner;
    }
    
 	/**
 	 * Sets the transaction partner.
 	 * 
 	 * @param $transactionPartner string The transaction partner of this transaction.
 	 */
 	public function setTransactionPartner($transactionPartner) {
		$this->transactionPartner = $transactionPartner;
		
		$this->doUpdate("SET transaction_partner = '" . $this->badgerDb->escapeSimple($transactionPartner) . "'");
	}
	
	/**
	 * Returns the category.
	 * 
	 * @return object The Category object with the category of this transaction.
	 */
    public function getCategory() {
    	return $this->category;
    }
 
 	/**
 	 * Sets the Category.
 	 * 
 	 * @param $category object The Category object with the category of this transaction.
 	 */
 	public function setCategory($category) {
		$this->category = $category;
		
		if (is_null($category)) {
			$catId = 'NULL';
		} else {
			$catId = $category->getId();
		}
		
		$this->doUpdate("SET category_id = $catId");
	}

	/**
	 * Returns the repeat unit.
	 * 
	 * @return string The repeat unit of this transaction.
	 */
    public function getRepeatUnit() {
    	return $this->repeatUnit;
    }
 
 	/**
 	 * Sets the repeat unit.
 	 * 
 	 * @param $repeatUnit string The repeat unit of this transaction.
 	 */
 	public function setRepeatUnit($repeatUnit) {
		$this->repeatUnit = $repeatUnit;
		
		$this->doUpdate("SET repeat_unit = '" . $repeatUnit . "'", false);
	}

	/**
	 * Returns the repeat frequency.
	 * 
	 * @return integer The repeat frequency of this transaction.
	 */
    public function getRepeatFrequency() {
    	return $this->repeatFrequency;
    }
 
 	/**
 	 * Sets the repeat frequency.
 	 * 
 	 * @param $repeatFrequency int The repeat frequency of this transaction.
 	 */
 	public function setRepeatFrequency($repeatFrequency) {
		$this->repeatFrequency = $repeatFrequency;
		
		$this->doUpdate("SET repeat_frequency = " . $repeatFrequency, false);
	}
    
    public static function sanitizeId($id) {
    	if ($id{0} === 'p') {
    		$parts = explode('_', $id);
    		
    		return (int) substr($parts[0], 1);
    	} else {
    		return (int) $id;
    	}
    }
	
	/**
	 * Returns the type.
	 * 
	 * @return string The type of this transaction.
	 */
	public function getType() {
		return $this->type;
	}
	
	public function expand($lastCalcDate, $targetFutureCalcDate) {
		$now = new Date();
		$now->setHour(23);
		$now->setMinute(59);
		$now->setSecond(59);
		
		$date = new Date($this->beginDate);
		
		$accountManager = new AccountManager($this->badgerDb);
		$compareAccount = $accountManager->getAccountById($this->account->getId());
		
		$compareAccount->setFilter(array (
			array (
				'key' => 'plannedTransactionId',
				'op' => 'eq',
				'val' => $this->id
			)
		));
		$compareAccount->setOrder(array (
			array (
				'key' => 'valutaDate',
				'dir' => 'asc'
			)
		));
		
		$currentCompareTransaction = $compareAccount->getNextTransaction();
		
		$localEndDate = is_null($this->endDate) ? new Date('9999-12-31') : $this->endDate; 
		//While we have not reached targetFutureCalcDate or endDate
		while (
			$targetFutureCalcDate->after($date)
			&& !$date->after($localEndDate)
		) {
			while (
				$currentCompareTransaction !== false
				&& $date->after($currentCompareTransaction->getValutaDate())
			) {
				$currentCompareTransaction = $compareAccount->getNextTransaction();
			}
			
			if(
				$date->after($lastCalcDate)
				&& (
					($currentCompareTransaction === false)
					|| !$date->equals($currentCompareTransaction->getValutaDate())
				)
			) {
				$this->account->addFinishedTransaction(
					new Amount($this->amount),
					$this->title,
					$this->description,
					new Date($date),
					$this->transactionPartner,
					$this->category,
					$this->outsideCapital,
					false,
					true,
					$this
				);
			}
			
			
			$date = $this->nextOccurence($date);

		} //while before futureTargetCalcDate and endDate 

		if (!is_null($this->otherPlannedTransaction)) {
			$this->otherPlannedTransaction->expand($lastCalcDate, $targetFutureCalcDate);
		}
	} //function expand
	
	private function nextOccurence($date) {
		$dayOfMonth = $this->beginDate->getDay();
		
		//do the date calculation
		switch ($this->repeatUnit){
			case 'day': 
				$date->addSeconds($this->repeatFrequency * 24 * 60 * 60);
				break;
				
			case 'week':
				$date->addSeconds($this->repeatFrequency * 7 * 24 * 60 * 60);
				break;
				
			case 'month':
				//Set the month
				$date = new Date(Date_Calc::endOfMonthBySpan($this->repeatFrequency, $date->getMonth(), $date->getYear(), '%Y-%m-%d'));
				//And count back as far as the last valid day of this month
				while($date->getDay() > $dayOfMonth){
					$date->subtractSeconds(24 * 60 * 60);
				}
				break; 
			
			case 'year':
				$newYear = $date->getYear() + $this->repeatFrequency;
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
				throw new BadgerException('Account', 'IllegalRepeatUnit', $this->repeatUnit);
				exit;
		} //switch
		
		return $date;
	}

	private function previousOccurence($date) {
		$dayOfMonth = $this->beginDate->getDay();
		
		//do the date calculation
		switch ($this->repeatUnit){
			case 'day': 
				$date->subtractSeconds($this->repeatFrequency * 24 * 60 * 60);
				break;
				
			case 'week':
				$date->subtractSeconds($this->repeatFrequency * 7 * 24 * 60 * 60);
				break;
				
			case 'month':
				//Set the month
				$date = new Date(Date_Calc::endOfMonthBySpan(-$this->repeatFrequency, $date->getMonth(), $date->getYear(), '%Y-%m-%d'));
				//And count back as far as the last valid day of this month
				while($date->getDay() > $dayOfMonth){
					$date->subtractSeconds(24 * 60 * 60);
				}
				break; 
			
			case 'year':
				$newYear = $date->getYear() - $this->repeatFrequency;
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
				throw new BadgerException('Account', 'IllegalRepeatUnit', $this->repeatUnit);
				exit;
		} //switch
		
		return $date;
	}

	public function deleteOldPlannedTransactions($upTo, $force = false) {
		if (
			$this->account->getDeleteOldPlannedTransactions()
			|| $force
		) {
			$sql = "DELETE FROM finished_transaction
					WHERE planned_transaction_id = " . $this->id . "
						AND valuta_date <= '" . $upTo->getDate() . "'"
			;
	
			$dbResult =& $this->badgerDb->query($sql);
			
			if (PEAR::isError($dbResult)) {
				//echo "SQL Error: " . $dbResult->getMessage();
				throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
			}
			
			if (!is_null($this->otherPlannedTransaction)) {
				$this->otherPlannedTransaction->deleteOldPlannedTransactions($upTo);
			}
		}
	}

	public function setUpdateMode($updateMode, $splitDate) {
		$this->updateMode = $updateMode;
		$this->updateSplitDate = $splitDate;
	}
	
	private function doUpdate($sqlPart, $updateFinishedTransactions = true) {
		$sql = "UPDATE planned_transaction\n$sqlPart\nWHERE planned_transaction_id = " . $this->id;

		$dbResult =& $this->badgerDb->query($sql);
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
		}
		
		if ($updateFinishedTransactions) {
			switch ($this->updateMode) {
				case self::UPDATE_MODE_ALL:
					break;
				
				case self::UPDATE_MODE_PREVIOUS:
				case self::UPDATE_MODE_FOLLOWING:
					$this->checkOtherPlannedTransaction();
					break;
			}
		
			$sql = "UPDATE finished_transaction\n$sqlPart\nWHERE planned_transaction_id = " . $this->id;
			$dbResult =& $this->badgerDb->query($sql);
			if (PEAR::isError($dbResult)) {
				//echo "SQL Error: " . $dbResult->getMessage();
				throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
			}
		}
	}
	
	private function checkOtherPlannedTransaction() {
		if (is_null($this->otherPlannedTransaction)) {
			if ($this->updateMode == self::UPDATE_MODE_PREVIOUS) {
				$title = $this->originalTitle
					. ' ('
					. getBadgerTranslation2('plannedTransaction', 'afterTitle')
					. ' '
					. $this->updateSplitDate->getFormatted()
					. ')'
				;
				$beginDate = $this->nextOccurence($this->updateSplitDate);
				$endDate = $this->endDate;
				$cmpOperator = '>';
				
				$this->setEndDate($this->updateSplitDate);
				$this->endDateLocked = true;
			} else {
				$title = $this->originalTitle
					. ' ('
					. getBadgerTranslation2('plannedTransaction', 'beforeTitle')
					. ' '
					. $this->updateSplitDate->getFormatted()
					. ')'
				;
				$beginDate = $this->beginDate;
				$endDate = $this->previousOccurence($this->updateSplitDate);
				$cmpOperator = '<';
				
				$this->setBeginDate($this->updateSplitDate);
				$this->beginDateLocked = true;
			}
			
			$this->otherPlannedTransaction = $this->account->addPlannedTransaction(
				$title,
				$this->amount,
				$this->repeatUnit,
				$this->repeatFrequency,
				$beginDate,
				$endDate,
				$this->description,
				$this->transactionPartner,
				$this->category,
				$this->outsideCapital
			);
			
			$sql = "DELETE FROM finished_transaction
					WHERE planned_transaction_id = " . $this->id . "
						AND valuta_date $cmpOperator '" . $this->updateSplitDate->getDate() . "'"
			;
			$dbResult =& $this->badgerDb->query($sql);
			if (PEAR::isError($dbResult)) {
				//echo "SQL Error: " . $dbResult->getMessage();
				throw new BadgerException('PlannedTransaction', 'SQLError', $dbResult->getMessage());
			}
		}
	}
} //class PlannedTransaction
?>
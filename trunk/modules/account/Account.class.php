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

require_once (BADGER_ROOT . '/core/Amount.class.php');
require_once (BADGER_ROOT . '/modules/account/Currency.class.php');

/**
 * An (financial) Account.
 * 
 * @author Eni Kao, Mampfred
 * @version $LastChangedRevision$
 */
class Account {
	
	/**
	 * The ID of the account in the database.
	 * 
	 * @var integer
	 */
	private $id;
	
	/**
	 * The title of the account.
	 * 
	 * @var string
	 */
	private $title;
	
	/**
	 * The description of the account.
	 * 
	 * @var string
	 */
	private $description;
	
	/**
	 * The lower limit of the account (for alerting).
	 * 
	 * @var object Amount
	 */
	private $lowerLimit;

	/**
	 * The upper limit of the account (for alerting).
	 * 
	 * @var object Amount
	 */
	private $upperLimit;

	/**
	 * The current balance of the account.
	 * 
	 * @var object Amount
	 */
	private $balance;
	
	/**
	 * The currency of the account.
	 * 
	 * @var object Currency.
	 */
	private $currency;
	
	private $targetFutureCalcDate;
	
	private $plannedTransactions = array();
	
	private $finishedTransactions = array();
	/**
	 * The AccountManager who created this Account.
	 * 
	 * @var object AccountManager
	 */
	private $accountManager;
	
	private $badgerDb;
	

	/**
	 * Creates an Account.
	 * 
	 * @param $accountManager object The AccountManager who created this Account.
	 * @param $data array An associative array with the values out of the DB.
	 */
	function __construct(&$badgerDb, &$accountManager, $data, $title = null, $description = null, $lowerLimit = null, $upperLimit = null, $currency = null) {
		$this->badgerDb = $badgerDb;
		$this->accountManager = $accountManager;
		
		$this->targetFutureCalcDate = new Date();
		$this->targetFutureCalcDate->addSeconds(10 * 365 * 24 * 60 * 60);
		
		if (is_array($data)) {
			$this->id = $data['account_id'];
			$this->title = $data['title'];
			$this->description = $data['description'];
			$this->lowerLimit = new Amount($data['lower_limit']);
			$this->upperLimit = new Amount($data['upper_limit']);
			$this->balance = new Amount($data['balance']);
			$this->currency = new Currency($data['currency_id'], $data['currency_symbol'], $data['currency_long_name']);
		} else {
			$this->id = $data;
			$this->title = $title;
			$this->description = $description;
			$this->lowerLimit = $lowerLimit;
			$this->upperLimit = $upperLimit;
			$this->currency = $currency;
			$this->balance = new Amount(0);
		}
	}
	
	/**
	 * Returns the ID.
	 * 
	 * @return integer The ID of this account.
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * Returns the title.
	 * 
	 * @return string The title of this account.
	 */
	public function getTitle() {
		return $this->title;
	}
	
	public function setTitle($title) {
		$this->title = $title;
		
		$sql = "UPDATE account
			SET title = '" . $this->badgerDb->escapeSimple($title) . "'
			WHERE account_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the description.
	 * 
	 * @return string The description of this account.
	 */
	public function getDescription() {
		return $this->description;
	}
	
	public function setDescription($description) {
		$this->description = $description;
		
		$sql = "UPDATE account
			SET description = '" . $this->badgerDb->escapeSimple($description) . "'
			WHERE account_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the lower limit.
	 * 
	 * @return object An Amount with the lower limit of this account.
	 */
	public function getLowerLimit() {
		return $this->lowerLimit;
	}
	
	public function setLowerLimit($lowerLimit) {
		$this->lowerLimit = $lowerLimit;
		
		$sql = "UPDATE account
			SET lower_limit = '" . $lowerLimit->get() . "'
			WHERE account_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the upper limit.
	 * 
	 * @return object An Amount with the upper limit of this account.
	 */
	public function getUpperLimit() {
		return $this->upperLimit;
	}
	
	public function setUpperLimit($upperLimit) {
		$this->upperLimit = $upperLimit;
		
		$sql = "UPDATE account
			SET upper_limit = '" . $upperLimit->get() . "'
			WHERE account_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
	}
	
	/**
	 * Returns the current balance.
	 * 
	 * @return object An Amount with the current amount of this account.
	 */
	public function getBalance() {
		return $this->balance;
	}
	
	/**
	 * Returns the currency.
	 * 
	 * @return object The Currency of this account.
	 */
	public function getCurrency() {
		return $this->currency;
	}

	public function setCurrency($currency) {
		$this->currency = $currency;
		
		$sql = "UPDATE account
			SET currency_id = '" . $currency->getId() . "'
			WHERE account_id = " . $this->id;
	
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
	}
	
	public function getTargetFutureCalcDate() {
		return $this->targetFutureCalcDate;
	}
	
	public function setTargetFutureCalcDate($date) {
		$this->targetFutureCalcDate = $date;
	}
	
	private function expandPlannedTransactions(){
		$now = new Date();
		foreach($this->plannedTransactions as $currentTransaction){ 
			$date = new Date($currentTransaction['begin_date']);
			$dayOfMonth = $date->getDay();
			while($this->targetFutureCalcDate->after($date)){
				if(!($date->before($now))) {
					$this->finishedTransactions[] = new Transaction($this->badgerDb, $this, $currentTransaction, $currentTransaction['planned_transaction_id']);
				}
				switch ($currentTransaction['repeat_unit']){
					case 'day': 
						$date->addSeconds($currentTransaction['repeat_frequency'] * 24 * 60 * 60);
						break;
						
					case 'week':
						$date->addSeconds($currentTransaction['repeat_frequency'] * 7 * 24 * 60 * 60);
						break;
						
					case 'month':
						$date = new Date(Date_Calc::endOfMonthBySpan($currentTransaction['repeat_frequency'], $date->getMonth(), $date->getYear(), '%Y-%m-%d'));
						while($date->getDay() > $dayOfMonth){
							$date->subtractSeconds(24 * 60 * 60);
						}
						break; 
					
					case 'year':
						$date->setYear($date->getYear() + $currentTransaction['repeat_frequency']);
						break;
				}
			}
		} 
		// TODO: sort finished transactions
	}
	
	private function loadPlannedTransactions() {
		$sql = "SELECT pt.planned_transaction_id, pt.title, pt.description, pt.valuta_date, pt.amount, 
				pt.outside_capital, pt.transaction_partner, pt.begin_date, pt.end_date, pt.repeat_unit, 
				pt.repeat_frequency
			FROM planned_transaction pt 
			WHERE account_id = " .  $this->id . "
				AND pt.begin_date <= '". $this->targetFutureCalcDate->getDate(DATE_FORMAT_ISO) . "'
				AND pt.end_date > NOW()"; 	
			
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
		
		$row = false;
		while($dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)){
			$this->plannedTransactions[] = $row;
		}
		
	}
}
?>
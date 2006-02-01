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

require_once (BADGER_ROOT . '/core/XML/DataGridHandler.class.php');
require_once (BADGER_ROOT . '/core/Amount.class.php');
require_once (BADGER_ROOT . '/modules/account/Currency.class.php');
require_once (BADGER_ROOT . '/core/Date.php');
require_once (BADGER_ROOT . '/modules/account/AccountManager.class.php');
require_once (BADGER_ROOT . '/modules/account/FinishedTransaction.class.php');

/**
 * An (financial) Account.
 * 
 * @author Eni Kao, Mampfred
 * @version $LastChangedRevision$
 */
class Account extends DataGridHandler {
	
	/**
	 * List of valid field names.
	 * 
	 * @var array
	 */
	private $fieldNames = array (
		'transactionId',
		'title',
		'description',
		'valutaDate',
		'amount',
		'outsideCapital',
		'transactionPartner',
		'categoryId',
		'categoryTitle'
	);

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
	
	/**
	 * Have the query been executed?
	 * 
	 * @var bool
	 */
	private $dataFetched = false;
	
	private $plannedDataFetched = false;

	/**
	 * Has all data been fetched from the DB?
	 * 
	 * @var bool
	 */
	private $allDataFetched = false;

	/**
	 * The result object of the DB query.
	 * 
	 * @var object
	 */
	private $dbResult;
	
	/**
	 * Creates an Account.
	 * 
	 * @param $accountManager object The AccountManager who created this Account.
	 * @param $data array An associative array with the values out of the DB.
	 */
	function __construct(&$badgerDb, &$accountManager, $data = null, $title = null, $description = null, $lowerLimit = null, $upperLimit = null, $currency = null) {
		$this->badgerDb = $badgerDb;
		
		$this->targetFutureCalcDate = new Date();
		$this->targetFutureCalcDate->addSeconds(1 * 365 * 24 * 60 * 60);

		if (!is_string($accountManager)) {
			$this->accountManager = $accountManager;
			
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
		} else {
			$this->accountManager = new AccountManager(&$badgerDb);
			settype($accountManager, 'integer');
			$tmpAccount = $this->accountManager->getAccountById($accountManager);
			
			//echo "<pre>"; print_r($tmpAccount); echo "</pre>";

			$this->id = $tmpAccount->getId();
			$this->title = $tmpAccount->getTitle();
			$this->description = $tmpAccount->getDescription();
			$this->lowerLimit = $tmpAccount->getLowerLimit();
			$this->upperLimit = $tmpAccount->getUpperLimit();
			$this->balance = $tmpAccount->getBalance();
			$this->currency = $tmpAccount->getCurrency();
		}
	}
	
	/**
	 * Checks if a field named $fieldName exists in this object.
	 * 
	 * @param string $fieldName The name of the field in question.
	 * @return boolean true if this object has this field, false otherwise.
	 */
	public function hasField($fieldName) {
		
		return in_array($fieldName, $this->fieldNames, true);
	}

	/**
	 * Returns the field type of $fieldName.
	 * 
	 * @param string $fieldName The name of the field in question.
	 * @throws BadgerException If there is no field $fieldName.
	 * @return string The type of field $fieldName.
	 */
	public function getFieldType($fieldName) {
		$fieldTypes = array (
			'transactionId' => 'integer',
			'title' => 'string',
			'description' => 'string',
			'valutaDate' => 'date',
			'amount' => 'amount',
			'outsideCapital' => 'boolean',
			'transactionPartner' => 'string',
			'categoryId' => 'integer',
			'categoryTitle' => 'string'
		);
	
		if (!isset ($fieldTypes[$fieldName])){
			throw new BadgerException('Account', 'invalidFieldName', $fieldName); 
		}
		
		return $fieldTypes[$fieldName];    	
	}

	/**
	 * Returns all valid field names.
	 * 
	 * @return array A list of all field names.
	 */
	public function getFieldNames() {
		return $this->fieldNames;
	}

	public function getFieldSQLName($fieldName) {
		$fieldSQLNames = array (
			'transactionId' => 'ft.finished_transaction_id',
			'title' => 'ft.title',
			'description' => 'ft.description',
			'valutaDate' => 'ft.valuta_date',
			'amount' => 'ft.amount',
			'outsideCapital' => 'ft.outside_capital',
			'transactionPartner' => 'ft.transaction_parter',
			'categoryId' => 'ft.category_id',
			'categoryTitle' => 'c.title'
		);
	
		if (!isset ($fieldSQLNames[$fieldName])){
			throw new BadgerException('Account', 'invalidFieldName', $fieldName); 
		}
		
		return $fieldSQLNames[$fieldName];    	
	}
	
	/**
	 * Returns all fields in an array.
	 * 
	 * The result has the following form:
	 * array (
	 *   array (
	 *     'field name 0' => 'value of field 0',
	 *     'field name 1' => 'value of field 1'
	 *   )
	 * );
	 * 
	 * The inner array is repeated for each row.
	 * The fields need to be in the order returned by @link getFieldNames().
	 * 
	 * @return array A list of all fields.
	 */
	public function getAll() {
		$this->getTransactions();
		
		$result = array();
		
		foreach($this->finishedTransactions as $currentTransaction){
			$result[] = array (
				'transactionId' => $currentTransaction->getId(),
				'title' => $currentTransaction->getTitle(),
				'description' => $currentTransaction->getDescription(),
				'valutaDate' => ($tmp = $currentTransaction->getValutaDate()) ? $tmp->getDate() : '',
				'amount' => $currentTransaction->getAmount()->get(),
				'outsideCapital' => is_null($tmp = $currentTransaction->getOutsideCapital()) ? '' : $tmp,
				'transactionPartner' => $currentTransaction->getTransactionPartner(),
				'categoryId' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getId() : '',
				'categoryTitle' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getTitle() : ''
			);
		}
		
		return $result;
	}

	public function getFinishedTransactionById($finishedTransactionId){
		if ($this->dataFetched) {
			if (isset($this->finishedTransactions[$finishedTransactionId])) {
				return $this->finishedTransactions[$finishedTransactionId];
			}
			while ($currentTransaction = $this->fetchNextFinishedTransaction()) {
				if ($currentTransaction->getId() === $finishedTransactionId) {
					
					return $currentTransaction;
				}
			}
		}	
		$sql = "SELECT ft.finished_transaction_id, ft.title, ft.description, ft.valuta_date, ft.amount, 
				ft.outside_capital, ft.transaction_partner, ft.category_id
			FROM finished_transaction ft 
			WHERE finished_transaction_id = " .  $finishedTransactionId;
		
		//echo $sql . "\n";
		
		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $this->dbResult->getMessage());
		}
		
		$currentTransaction = $this->fetchNextFinishedTransaction();
		if($currentTransaction){
			return $currentTransaction;
		} else {
			$this->allDataFetched = false;	
			throw new BadgerException('Account', 'UnknownFinishedTransactionId', $finishedTransactionId);
		}
	}
	
	public function deleteFinishedTransaction($finishedTransactionId){
		if(isset($this->finishedTransactions[$finishedTransactionId])){
			unset($this->finishedTransactions[$finishedTransactionId]);
		}
		$sql= "DELETE FROM finished_transaction
				WHERE finished_transaction_id = $finishedTransactionId";
				
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
		
		if($this->badgerDb->affectedRows() != 1){
			throw new BadgerException('Account', 'UnknownFinishedTransactionId', $finishedTransactionId);
		}
	}
	
	public function addFinishedTransaction($title, $amount, $description = null, $valutaDate = null, $transactionPartner = null, $category = null, $outsideCapital = false) {
		$finishedTransactionId = $this->badgerDb->nextId('finishedTransactionIds');
		
		$sql = "INSERT INTO finished_transaction
			(finished_transaction_id, account_id, title, amount, outside_capital ";
			
		if($description){
			$sql .= ", description";
		}
		
		if($valutaDate){
			$sql .= ", valuta_date";
		}
		
		if($transactionPartner){
			$sql .= ", transaction_partner";
		}
		
		if ($category) {
			$sql .= ", category_id";
		}
		
		$sql .= ")
			VALUES ($finishedTransactionId, " . $this->id . ", '" . $this->badgerDb->escapeSimple($title) . "', '" . $amount->get() . "', " . $this->badgerDb->quoteSmart($outsideCapital);
	
		if($description){
			$sql .= ", '".  $this->badgerDb->escapeSimple($description) . "'";
		}
	
		if($valutaDate){
			$sql .= ", '".  $valutaDate->getDate() . "'";
		}
			
		if($transactionPartner){
			$sql .= ", '".  $this->badgerDb->escapeSimple($transactionPartner) . "'";
		}
		
		if($category) {
			$sql .= ", " . $category->getId();
		}
		
		$sql .= ")";
		
		
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
		
		if($this->badgerDb->affectedRows() != 1){
			throw new BadgerException('Account', 'insertError', $dbResult->getMessage());
		}
		
		$this->finishedTransactions[$finishedTransactionId] = new FinishedTransaction(&$this->badgerDb, &$this, $finishedTransactionId, $title, $amount, $description, $valutaDate, $transactionPartner, $category, $outsideCapital);
		
		return $this->finishedTransactions[$finishedTransactionId];	
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
	
	public function getTransactions() {
		while ($this->fetchNextFinishedTransaction());
		
		$this->fetchPlannedTransactions();
		$this->expandPlannedTransactions();
		
		uasort($this->finishedTransactions, array('Account', 'transferCompare'));
	}
	
	private function fetchNextFinishedTransaction() {
		$this->fetchFromDB();
		$row = false;
		
		if($this->dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)){
			$this->finishedTransactions[$row['finished_transaction_id']] = new FinishedTransaction(&$this->badgerDb, &$this, $row);
			return $this->finishedTransactions[$row['finished_transaction_id']];
		} else {
			$this->allDataFetched = true;
			return false;    	
		}
		
	}

	/**
	 * Prepares and executes the SQL query.
	 * 
	 * @throws BadgerException If an SQL error occured.
	 */
	private function fetchFromDB() {
		if($this->dataFetched){
			return;
		}
		
		$sql = "SELECT ft.finished_transaction_id, ft.title, ft.description, ft.valuta_date, ft.amount, 
				ft.outside_capital, ft.transaction_partner, ft.category_id
			FROM finished_transaction ft 
				LEFT OUTER JOIN category c ON ft.category_id = c.category_id
			WHERE account_id = " .  $this->id . "\n";
		
		$where = $this->getFilterSQL();
		if($where) {
			$sql .= "AND $where\n ";
		} 
		
		$order = $this->getOrderSQL();				
		if($order) {
			$sql .= "ORDER BY $order\n ";
		}
		
		//echo "<pre>$sql</pre>";

		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $this->dbResult->getMessage());
		}
		
		$this->dataFetched = true; 	
	}

	private function expandPlannedTransactions(){
		$this->fetchPlannedTransactions();

		$now = new Date();
		
		//echo "<pre>"; print_r($this->plannedTransactions); echo "</pre>";
		
		foreach($this->plannedTransactions as $currentTransaction){ 
			$date = new Date($currentTransaction['begin_date']);
			$dayOfMonth = $date->getDay();
			while($this->targetFutureCalcDate->after($date)){
				if(!($date->before($now))) {
					$this->finishedTransactions[] = new FinishedTransaction($this->badgerDb, $this, $currentTransaction, $currentTransaction['planned_transaction_id'], $date);
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
					
					default:
						echo "Illegal repeat unit";
						exit;
				}
			}
		} 
	}
	
	private function fetchPlannedTransactions() {
		if ($this->plannedDataFetched) {
			return;
		}

		$sql = "SELECT pt.planned_transaction_id, pt.title, pt.description, pt.valuta_date, pt.amount, 
				pt.outside_capital, pt.transaction_partner, pt.begin_date, pt.end_date, pt.repeat_unit, 
				pt.repeat_frequency, pt.category_id
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
		
		$this->plannedDataFetched = true;
	}

	function transferCompare($aa, $bb) {
		$tmp = 0;

		for ($run = 0; isset($this->order[$run]); $run++) {
			if ($this->order[$run]['dir'] == 'asc') {
				$a = $aa;
				$b = $bb;
			} else {
				$a = $bb;
				$b = $aa;
			}
			//echo "a: " . $a->getId() . "<br />";
			
			switch ($this->order[$run]['key']) {
				case 'transactionId':
					$tmp = $a->getId() - $b->getId();
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
						$tmp = $a->getValutaDate()->compare($a->getValutaDate(), $b->getValutaDate());
					}
					break;
				
				case 'amount':
					$tmp = $a->getAmount()->compare($b->getAmount());
					break;
		
				case 'outsideCapital':
					$tmp = $a->getOutsideCapital() - $b->getOutsideCapital();
					break;
		
				case 'transactionPartner':
					$tmp = strncasecmp($a->getTransactionPartner(), $b->getTransactionPartner(), 9999);
					break;
				
				case 'categoryId':
					$tmp = $a->getCategory()->getId() - $b->getCategory()->getId();
					break;
				
				case 'categoryTitle':
					$tmp = strncasecmp($a->getCategory()->getTitle(), $b->getCategory()->getTitle(), 9999);
					break;
			}
			
			if ($tmp != 0) {
				return $tmp;
			}
		}
	}
}
?>
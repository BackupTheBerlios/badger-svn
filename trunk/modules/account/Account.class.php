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

require_once BADGER_ROOT . '/core/common.php';
require_once BADGER_ROOT . '/core/XML/DataGridHandler.class.php';
require_once BADGER_ROOT . '/core/Amount.class.php';
require_once BADGER_ROOT . '/modules/account/Currency.class.php';
require_once BADGER_ROOT . '/core/Date.php';
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/account/FinishedTransaction.class.php';
require_once BADGER_ROOT . '/modules/account/PlannedTransaction.class.php';

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
		'transaction' => array (
			'transactionId',
			'type',
			'title',
			'description',
			'valutaDate',
			'amount',
			'outsideCapital',
			'transactionPartner',
			'categoryId',
			'categoryTitle'
		),
		'planned' => array (
			'plannedTransactionId',
			'title',
			'description',
			'amount',
			'outsideCapital',
			'transactionPartner',
			'beginDate',
			'endDate',
			'repeatUnit',
			'repeatFrequency',
			'categoryId',
			'categoryTitle'
		),
		'finished' => array (
			'finishedTransactionId',
			'title',
			'description',
			'valutaDate',
			'amount',
			'outsideCapital',
			'transactionPartner',
			'categoryId',
			'categoryTitle'
		)
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
	
	/**
	 * The date up to when we should calculate planned transactions.
	 * 
	 * @var object Date
	 */
	private $targetFutureCalcDate;
	
	/**
	 * List of planned transactions.
	 * 
	 * @var array
	 */
	private $plannedTransactions = array();
	
	/**
	 * List of finished (and, after call to expandPlannedTransactions(), the expanded planned transactions).
	 * 
	 * @var array
	 */
	private $finishedTransactions = array();
	
	/**
	 * list of all properties
	 * 
	 * @var array
	 */
	private $properties;

	/**
	 * Type of requested data (all transactions / only planned / only finished).
	 * 
	 * @var string
	 */
	private $type = null;
	
	/**
	 * The AccountManager who created this Account.
	 * 
	 * @var object AccountManager
	 */
	private $accountManager;
	
	/**
	 * Has the query to finished transactions been executed?
	 * 
	 * @var bool
	 */
	private $finishedDataFetched = false;
	
	/**
	 * Has the query to planned transactions been executed?
	 * 
	 * @var bool
	 */
	private $plannedDataFetched = false;

	/**
	 * Has all finished data been fetched from the DB?
	 * 
	 * @var bool
	 */
	private $allFinishedDataFetched = false;
	
	/**
	 * Has all planned data been fetched from the DB?
	 * 
	 * @var bool
	 */
	private $allPlannedDataFetched = false;
	
	/**
	 * Has the planned data been expanded?
	 * 
	 * @var bool
	 */
	private $plannedDataExpanded = false;
	
	/**
	 * The key of the current finished data element.
	 * 
	 * @var mixed string (if expanded) or integer  
	 */
	private $currentFinishedTransaction = null;

	/**
	 * The key of the current planned data element.
	 * 
	 * @var integer
	 */
	private $currentPlannedTransaction = null;

	/**
	 * The result object of the finished transaction DB query.
	 * 
	 * @var object
	 */
	private $dbResultFinished;

	/**
	 * The result object of the planned transaction DB query.
	 * 
	 * @var object
	 */
	private $dbResultPlanned;
	
	/**
	 * The placeholder for the current table (ft or pt) in transaction type
	 * 
	 * @var string
	 */
	const TABLE_PLACEHOLDER = '__TABLE__'; 

	/**
	 * Creates an Account.
	 * 
	 * @param $badgerDb object The DB object.
	 * @param $accountManager mixed The AccountManager object who created this Account OR the qp part out of getDataGridXML.php.
	 * @param $data mixed An associative array with the values out of the DB OR the id of the Account.
	 * @param $title string The title of the Account.
	 * @param $description string The description of the Account.
	 * @param $lowerLimit object An Amount object with the lower limit of the Account.
	 * @param $upperLimit object An Amount object with the upper limit of the Account.
	 * @param $currency object An Currency object with the currency of the Account.
	 */
	function __construct(
		&$badgerDb,
		&$accountManager,
		$data = null,
		$title = null,
		$description = null,
		$lowerLimit = null,
		$upperLimit = null,
		$currency = null
	) {
		$this->badgerDb = $badgerDb;
		
		$this->targetFutureCalcDate = new Date();
		//Standard: One Year
		$this->targetFutureCalcDate->addSeconds(1 * 365 * 24 * 60 * 60);
		$this->type = 'transaction';

		if (!is_string($accountManager)) {
			//called with data array or all parameters
			$this->accountManager = $accountManager;
			
			if (is_array($data)) {
				//called with data array
				$this->id = $data['account_id'];
				$this->title = $data['title'];
				$this->description = $data['description'];
				$this->lowerLimit = new Amount($data['lower_limit']);
				$this->upperLimit = new Amount($data['upper_limit']);
				$this->balance = new Amount($data['balance']);
				$this->currency = new Currency($data['currency_id'], $data['currency_symbol'], $data['currency_long_name']);
			} else {
				//called with all parameters
				$this->id = $data;
				$this->title = $title;
				$this->description = $description;
				$this->lowerLimit = $lowerLimit;
				$this->upperLimit = $upperLimit;
				$this->currency = $currency;
				$this->balance = new Amount(0);
			}
		} else {
			//called from getDataGridXML.php
			$this->accountManager = new AccountManager(&$badgerDb);
			
			//Filter out given parameters
			list($selectedId, $type, $targetDays) = explode(';', $accountManager . ';;');
			settype($selectedId, 'integer');
			if (in_array($type, array('transaction', 'finished', 'planned'), true)) {
				$this->type = $type; 
			}
			
			settype($targetDays, 'integer');
			if ($targetDays) {
				$this->targetFutureCalcDate = new Date();
				$this->targetFutureCalcDate->addSeconds($targetDays * 24 * 60 * 60);
			}
			
			//copy account data
			$tmpAccount = $this->accountManager->getAccountById($selectedId);
			
			$this->id = $tmpAccount->getId();
			$this->title = $tmpAccount->getTitle();
			$this->description = $tmpAccount->getDescription();
			$this->lowerLimit = $tmpAccount->getLowerLimit();
			$this->upperLimit = $tmpAccount->getUpperLimit();
			$this->balance = $tmpAccount->getBalance();
			$this->currency = $tmpAccount->getCurrency();
		}

		//Get all properties
    	$sql = "SELECT prop_key, prop_value
			FROM account_property
			WHERE account_id = " . $this->id;
		
		$res =& $badgerDb->query($sql);

		$this->properties = array();
		
		$row = array();
		
		while ($res->fetchInto($row, DB_FETCHMODE_ASSOC)) {
			$this->properties[$row['prop_key']] = $row['prop_value'];
		}
	}
	
	/**
	 * Checks if a field named $fieldName exists in this object.
	 * 
	 * @param string $fieldName The name of the field in question.
	 * @return boolean true if this object has this field, false otherwise.
	 */
	public function hasField($fieldName) {
		
		return in_array($fieldName, $this->fieldNames[$this->type], true);
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
			'plannedTransactionId' => 'integer',
			'finishedTransactionId' => 'integer',
			'type' => 'string',
			'title' => 'string',
			'description' => 'string',
			'valutaDate' => 'date',
			'beginDate' => 'date',
			'endDate' => 'date',
			'amount' => 'amount',
			'outsideCapital' => 'boolean',
			'transactionPartner' => 'string',
			'categoryId' => 'integer',
			'categoryTitle' => 'string',
			'repeatUnit' => 'string',
			'repeatFrequency' => 'integer'
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
		return $this->fieldNames[$this->type];
	}

	/**
	 * Returns the SQL name of the given field.
	 * 
	 * @param $fieldName string The field name to get the SQL name of.
	 * @throws BadgerException If an unknown field name was given.
	 * @return The SQL name of $fieldName.
	 */
	public function getFieldSQLName($fieldName) {
		$fieldSQLNames = array (
			'transaction' => array (
				'transactionId' => Account::TABLE_PLACEHOLDER . '.transaction_id',
				'type' => Account::TABLE_PLACEHOLDER . '.__TYPE__',
				'title' => Account::TABLE_PLACEHOLDER . '.title',
				'description' => Account::TABLE_PLACEHOLDER . '.description',
				'valutaDate' => Account::TABLE_PLACEHOLDER . '.valuta_date',
				'amount' => Account::TABLE_PLACEHOLDER . '.amount',
				'outsideCapital' => Account::TABLE_PLACEHOLDER . '.outside_capital',
				'transactionPartner' => Account::TABLE_PLACEHOLDER . '.transaction_parter',
				'categoryId' => Account::TABLE_PLACEHOLDER . '.category_id',
				'categoryTitle' => 'c.title'
			),
			'planned' => array (
				'plannedTransactionId' => 'pt.planned_transaction_id',
				'title' => 'pt.title',
				'description' => 'pt.description',
				'amount' => 'pt.amount',
				'outsideCapital' => 'pt.outside_capital',
				'transactionPartner' => 'pt.transaction_partner,',
				'beginDate' => 'pt.begin_date',
				'endDate' => 'pt.end_date',
				'repeatUnit' => 'pt.repeat_unit',
				'repeatFrequency' => 'pt.repeat_frequency',
				'categoryId' => 'pt.category_id',
				'categoryTitle' => 'c.title'
			),
			'finished' => array (
				'transactionId' => 'ft.transaction_id',
				'title' => 'ft.title',
				'description' => 'ft.description',
				'valutaDate' => 'ft.valuta_date',
				'amount' => 'ft.amount',
				'outsideCapital' => 'ft.outside_capital',
				'transactionPartner' => 'ft.transaction_parter',
				'categoryId' => 'ft.category_id',
				'categoryTitle' => 'c.title'
			)
		);
	
		if (!isset ($fieldSQLNames[$this->type][$fieldName])){
			throw new BadgerException('Account', 'invalidFieldName', $fieldName); 
		}
		
		return $fieldSQLNames[$this->type][$fieldName];    	
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
		$result = array();

		switch ($this->type) {
			case 'transaction':
				$this->fetchTransactions();
		
				foreach($this->finishedTransactions as $currentTransaction){
					$result[] = array (
						'transactionId' => $currentTransaction->getId(),
						'type' => getBadgerTranslation2('Account', $currentTransaction->getType()), 
						'title' => $currentTransaction->getTitle(),
						'description' => $currentTransaction->getDescription(),
						'valutaDate' => ($tmp = $currentTransaction->getValutaDate()) ? $tmp->getFormatted() : '',
						'amount' => $currentTransaction->getAmount()->getFormatted(),
						'outsideCapital' => is_null($tmp = $currentTransaction->getOutsideCapital()) ? '' : $tmp,
						'transactionPartner' => $currentTransaction->getTransactionPartner(),
						'categoryId' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getId() : '',
						'categoryTitle' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getTitle() : ''
					);
				}
				break;
			
			case 'finished':
				while ($this->fetchNextFinishedTransaction());
				
				foreach($this->finishedTransactions as $currentTransaction){
					$result[] = array (
						'finishedTransactionId' => $currentTransaction->getId(),
						'title' => $currentTransaction->getTitle(),
						'description' => $currentTransaction->getDescription(),
						'valutaDate' => ($tmp = $currentTransaction->getValutaDate()) ? $tmp->getFormatted() : '',
						'amount' => $currentTransaction->getAmount()->getFormatted(),
						'outsideCapital' => is_null($tmp = $currentTransaction->getOutsideCapital()) ? '' : $tmp,
						'transactionPartner' => $currentTransaction->getTransactionPartner(),
						'categoryId' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getId() : '',
						'categoryTitle' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getTitle() : ''
					);
				}
				break;
			
			case 'planned':
				while ($this->fetchNextPlannedTransaction());
		
				foreach($this->plannedTransactions as $currentTransaction){
					$result[] = array (
						'plannedTransactionId' => $currentTransaction->getId(),
						'title' => $currentTransaction->getTitle(),
						'description' => $currentTransaction->getDescription(),
						'amount' => $currentTransaction->getAmount()->getFormatted(),
						'outsideCapital' => is_null($tmp = $currentTransaction->getOutsideCapital()) ? '' : $tmp,
						'transactionPartner' => $currentTransaction->getTransactionPartner(),
						'beginDate' => $currentTransaction->getBeginDate()->getFormatted(),
						'endDate' => ($tmp = $currentTransaction->getEndDate()) ? $tmp->getFormatted() : '',
						'repeatUnit' => getBadgerTranslation2('Account', $currentTransaction->getRepeatUnit()),
						'repeatFrequency' => $currentTransaction->getRepeatFrequency(),
						'categoryId' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getId() : '',
						'categoryTitle' => ($tmp = $currentTransaction->getCategory()) ? $tmp->getTitle() : ''
					);
				}
				break;
		}
		
		return $result;
	}

	/**
	 * Returns the finished transaction identified by $finishedTransactionId.
	 * 
	 * @param $finishedTransactionId integer The id of the requested finished transaction.
	 * @throws BadgerException If $finishedTransactionId is unknown to the DB.
	 * @return object FinishedTransaction object of the finished transaction identified by $finishedTransactionId.
	 */
	public function getFinishedTransactionById($finishedTransactionId){
		if ($this->finishedDataFetched) {
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
		
		$this->dbResultFinished =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResultFinished)) {
			echo "SQL Error: " . $this->dbResultFinished->getMessage();
			throw new BadgerException('Account', 'SQLError', $this->dbResultFinished->getMessage());
		}
		
		$tmp = $this->finishedDataFetched;
		$this->finishedDataFetched = true;
		
		$currentTransaction = $this->fetchNextFinishedTransaction();
		
		$this->finishedDataFetched = $tmp;
		
		if($currentTransaction){
			return $currentTransaction;
		} else {
			$this->allFinishedDataFetched = false;	
			throw new BadgerException('Account', 'UnknownFinishedTransactionId', $finishedTransactionId);
		}
	}
	
	/**
	 * Deletes the finished transaction identified by $finishedTransactionId.
	 * 
	 * @param $finishedTransactionId integer The id of the finished transaction to delete.
	 * @throws BadgerException If $finishedTransactionId is unknown to the DB.
	 */
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
	
	/**
	 * Adds a new finished transaction to this account.
	 * 
	 * @param $amount object Amount object with the amount of the new finished transaction.
	 * @param $title string Title of the new finished transaction.
	 * @param $description string Description of the new finished transaction.
	 * @param $valutaDate object Date object with the valuta date of the new finished transaction.
	 * @param $transactionPartner string Transaction partner of the new finished transaction.
	 * @param $category object Category object with the category of the new finished transaction.
	 * @param $outsideCapital bool True if the new finished transaction is outside capital, false otherwise.
	 * @throws BadgerException If an error occured while inserting.
	 * @return object The new FinishedTransaction object.
	 */
	public function addFinishedTransaction(
		$amount,
		$title = null,
		$description = null,
		$valutaDate = null,
		$transactionPartner = null,
		$category = null,
		$outsideCapital = null
	) {
		$finishedTransactionId = $this->badgerDb->nextId('finishedTransactionIds');
		
		$sql = "INSERT INTO finished_transaction
			(finished_transaction_id, account_id, amount ";
			
		if ($title) {
			$sql .= ", title";
		}

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
		
		if ($outsideCapital) {
			$sql .= ", outside_capital";
		}
		
		$sql .= ")
			VALUES ($finishedTransactionId, " . $this->id . ", '" . $amount->get() . "'";
	
		if ($title) {
			$sql .= ", '" . $this->badgerDb->escapeSimple($title) . "'";
		}

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
		
		if ($outsideCapital) {
			 $sql .= ", " . $this->badgerDb->quoteSmart($outsideCapital);
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
	 * Returns the planned transaction identified by $plannedTransactionId.
	 * 
	 * @param $plannedTransactionId integer The id of the requested planned transaction.
	 * @throws BadgerException If $plannedTransactionId is unknown to the DB.
	 * @return object PlannedTransaction object of the planned transaction identified by $plannedTransactionId.
	 */
	public function getPlannedTransactionById($plannedTransactionId){
		if ($this->plannedDataFetched) {
			if (isset($this->plannedTransactions[$plannedTransactionId])) {
				return $this->plannedTransactions[$plannedTransactionId];
			}
			while ($currentTransaction = $this->fetchNextPlannedTransaction()) {
				if ($currentTransaction->getId() === $plannedTransactionId) {
					
					return $currentTransaction;
				}
			}
		}	
		$sql = "SELECT pt.planned_transaction_id, pt.title, pt.description, pt.amount, 
				pt.outside_capital, pt.transaction_partner, pt.begin_date, pt.end_date, pt.repeat_unit, 
				pt.repeat_frequency, pt.category_id
			FROM planned_transaction pt 
			WHERE planned_transaction_id = " .  $plannedTransactionId;
		
		//echo $sql . "\n";
		
		$this->dbResultPlanned =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResultPlanned)) {
			echo "SQL Error: " . $this->dbResultPlanned->getMessage();
			throw new BadgerException('Account', 'SQLError', $this->dbResultPlanned->getMessage());
		}
		
		$tmp = $this->plannedDataFetched;
		$this->plannedDataFetched = true;
		
		$currentTransaction = $this->fetchNextPlannedTransaction();
		
		$this->plannedDataFetched = $tmp;
		
		if($currentTransaction){
			return $currentTransaction;
		} else {
			$this->allPlannedDataFetched = false;	
			throw new BadgerException('Account', 'UnknownPlannedTransactionId', $plannedTransactionId);
		}
	}
	
	/**
	 * Deletes the planned transaction identified by $plannedTransactionId.
	 * 
	 * @param $plannedTransactionId integer The id of the planned transaction to delete.
	 * @throws BadgerException If $plannedTransactionId is unknown to the DB.
	 */
	public function deletePlannedTransaction($plannedTransactionId){
		if(isset($this->plannedTransactions[$plannedTransactionId])){
			unset($this->plannedTransactions[$plannedTransactionId]);
		}
		$sql= "DELETE FROM planned_transaction
				WHERE planned_transaction_id = $plannedTransactionId";
				
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('Account', 'SQLError', $dbResult->getMessage());
		}
		
		if($this->badgerDb->affectedRows() != 1){
			throw new BadgerException('Account', 'UnknownPlannedTransactionId', $plannedTransactionId);
		}
	}
	
	/**
	 * Adds a new planned transaction to this account.
	 * 
	 * @param $title string Title of the new planned transaction.
	 * @param $amount object Amount object with the amount of the new planned transaction.
	 * @param $repeatUnit string The repeat unit (day, week, month, year) of the new planned transaction.
	 * @param $repeatFrequency integer The repeat frequency of the new planned transaction.
	 * @param $beginDate object Date object with the begin date of the new planned transaction.
	 * @param $endDate object Date object with the end date of the new planned transaction.
	 * @param $description string Description of the new planned transaction.
	 * @param $transactionPartner string Transaction partner of the new planned transaction.
	 * @param $category object Category object with the category of the new planned transaction.
	 * @param $outsideCapital bool True if the new planned transaction is outside capital, false otherwise.
	 * @throws BadgerException If an error occured while inserting.
	 * @return object The new PlannedTransaction object.
	 */
	public function addPlannedTransaction(
		$title,
		$amount,
		$repeatUnit,
		$repeatFrequency,
		$beginDate,
		$endDate = null,
		$description = null,
		$transactionPartner = null,
		$category = null,
		$outsideCapital = null
	) {
		$plannedTransactionId = $this->badgerDb->nextId('plannedTransactionIds');
		
		$sql = "INSERT INTO planned_transaction
			(planned_transaction_id, account_id, title, amount, repeat_unit, repeat_frequency, begin_date ";
			
		if ($endDate) {
			$sql .= ", end_date";
		}

		if($description){
			$sql .= ", description";
		}
		
		if($transactionPartner){
			$sql .= ", transaction_partner";
		}
		
		if ($category) {
			$sql .= ", category_id";
		}
		
		if ($outsideCapital) {
			$sql .= ", outside_capital";
		}
		
		$sql .= ")
			VALUES ($plannedTransactionId, " . $this->id . ", '" . $this->badgerDb->escapeSimple($title) . "', '" . $amount->get() . "', '" . $this->badgerDb->escapeSimple($repeatUnit) . "', " . $repeatFrequency . ", '" . $beginDate->getDate() . "'";  
	
		if($endDate){
			$sql .= ", '".  $endDate->getDate() . "'";
		}
			
		if($description){
			$sql .= ", '".  $this->badgerDb->escapeSimple($description) . "'";
		}
	
		if($transactionPartner){
			$sql .= ", '".  $this->badgerDb->escapeSimple($transactionPartner) . "'";
		}
		
		if($category) {
			$sql .= ", " . $category->getId();
		}
		
		if ($outsideCapital) {
			 $sql .= ", " . $this->badgerDb->quoteSmart($outsideCapital);
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
		
		$this->plannedTransactions[$plannedTransactionId] = new PlannedTransaction(
			&$this->badgerDb,
			&$this,
			$plannedTransactionId,
			$repeatUnit,
			$repeatFrequency,
			$beginDate,
			$endDate,
			$title,
			$amount, 
			$description,
			$transactionPartner,
			$category,
			$outsideCapital
		);

    	return $this->plannedTransactions[$plannedTransactionId];	
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
	
	/**
	 * Sets the title.
	 * 
	 * @param $title string The title of this account.
	 */
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
	
	/**
	 * Sets the description.
	 * 
	 * @param $description string The description of this account.
	 */
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
	
	/**
	 * Sets the lower limit.
	 * 
	 * @param $lowerLimit object Amount object with the lower limit of this account. 
	 */
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
	
	/**
	 * Sets the upper limit.
	 * 
	 * @param $upperLimit object Amount object with the upper limit of this account. 
	 */
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

	/**
	 * Sets the currency.
	 * 
	 * @param $currency object Currency object with the currency of this account.
	 */
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
	
	/**
	 * Returns the date up to when the planned transactions will be expanded.
	 * 
	 * @return object A Date object with the date up to when the planned transactions will be expanded.
	 */
	public function getTargetFutureCalcDate() {
		return $this->targetFutureCalcDate;
	}
	
	/**
	 * Sets the date up to when the planned transactions will be expanded.
	 * 
	 * @param $date object A Date object with the date up to when the planned transactions will be expanded.
	 */
	public function setTargetFutureCalcDate($date) {
		$this->targetFutureCalcDate = $date;
	}
	
	/**
	 * Expands the planned transactions.
	 * 
	 * All occurences of planned transactions between now and the targetFutureCalcDate will be inserted
	 * in finishedTransactions. For distinction the planned transactions will have a 'p' as first character
	 * in their id.
	 * 
	 * @throws BadgerException If an illegal repeat unit is used.
	 */
	public function expandPlannedTransactions(){
		$now = new Date();
		
//		header('content-type: text/plain');
		
		//echo "<pre>"; print_r($this->plannedTransactions); echo "</pre>";
		
		foreach($this->plannedTransactions as $currentTransaction){ 
//			echo 'begin_date: ' . $currentTransaction['begin_date'] . "\n";
			$date = new Date($currentTransaction->getBeginDate());
//			echo 'date: ' . $date->getDate() . "\n";
			$dayOfMonth = $date->getDay();
//			echo 'date: ' . $date->getDate() . "\n";
			//While we have not reached targetFutureCalcDate
			while($this->targetFutureCalcDate->after($date)){
//				echo 'date: ' . $date->getDate() . "\n";
				$inRange = true;
				//Check if there is one or more valutaDate filter and apply them
				foreach ($this->filter as $currentFilter) {
					if ($currentFilter['key'] == 'valutaDate') {
						switch ($currentFilter['op']) {
							case 'eq':
								if (Date::compare($date, $currentFilter['val']) != 0) {
									$inRange = false;
								}
								break;
								
							case 'lt':
								if (Date::compare($date, $currentFilter['val']) >= 0) {
									$inRange = false;
								}
								break;
								
							case 'le':
								if (Date::compare($date, $currentFilter['val']) > 0) {
									$inRange = false;
								}
								break;
								
							case 'gt':
								if (Date::compare($date, $currentFilter['val']) <= 0) {
									$inRange = false;
								}
								break;
								
							case 'ge':
								if (Date::compare($date, $currentFilter['val']) < 0) {
									$inRange = false;
								}
								break;
								
							case 'ne':
								if (Date::compare($date, $currentFilter['val']) == 0) {
									$inRange = false;
								}
								break;
		
							case 'bw':
							case 'ew':
							case 'ct': 	
								if (strncasecmp($date->getFormatted(), $currentFilter['val']->getFormatted(), 9999) != 0) {
									$inRange = false;
								}
			    				break;
						}
						
						if (!$inRange) {
							break;
						}
					}
				}
							
				if(!($date->before($now)) && $inRange) {
					$this->finishedTransactions[] = new FinishedTransaction(
						$this->badgerDb,
						$this,
						'p' . $currentTransaction->getId(),
						$currentTransaction->getTitle(),
						$currentTransaction->getAmount(),
						$currentTransaction->getDescription(),
						new Date($date),
						$currentTransaction->getTransactionPartner(),
						$currentTransaction->getCategory(),
						$currentTransaction->getOutsideCapital(),
						'PlannedTransaction'
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

	/**
	 * Resets the internal counter of finished transactions.
	 */
	public function resetFinishedTransactions() {
		reset($this->finishedTransactions);
		$this->currentFinishedTransaction = null;
	}
	
	/**
	 * Resets the internal counter of planned transactions.
	 */
	public function resetPlannedTransaction() {
		reset($this->plannedTransactions);
		$this->currentPlannedTransaction = null;
	}
	
	/**
	 * Returns the next finished transaction.
	 * 
	 * @return mixed The next FinishedTransaction object or false if we are at the end of the list.
	 */
	public function getNextFinishedTransaction() {
		if (!$this->allFinishedDataFetched) {
			$this->fetchNextFinishedTransaction();
		}

		return nextByKey($this->finishedTransactions, $this->currentFinishedTransaction);
	}
	
	/**
	 * Returns the next planned transaction.
	 * 
	 * @return mixed The next PlannedTransaction object or false if we are at the end of the list.
	 */
	public function getNextPlannedTransaction() {
		if (!$this->allPlannedDataFetched) {
			$this->fetchNextPlannedTransaction();
		}

		return nextByKey($this->plannedTransactions, $this->currentPlannedTransaction);
	}
	
	/**
	 * Returns the next transaction.
	 * 
	 * Essentially the same as getNextFinishedTransaction, but first fetches all planned transactions
	 * and expands them.
	 * 
	 * @return mixed The next FinishedTransaction object or false if we are at the end of the list.
	 */
	public function getNextTransaction() {
		$this->fetchTransactions();
		
		return nextByKey($this->finishedTransactions, $this->currentFinishedTransaction);
	}
	
    /**
     * reads out the property defined by $key
     * 
     * @param string $key key of the requested value
     * @throws BadgerException if unknown key is passed
     * @return mixed the value referenced by $key
     */
    public function getProperty($key) {
    	//echo "<pre>"; print_r($this->properties); echo "</pre>";
    	if (isset($this->properties[$key])) {
    		return $this->properties[$key];
    	} else {
    		throw new BadgerException('Account', 'illegalPropertyKey', $key);
    	}
    }
    
    /**
     * sets property $key to $value
     * 
     * @param string $key key of the target value
     * @param string $value the value referneced by $key 
     * @return void
     */
    public function setProperty($key, $value) {
       	if (isset($this->properties[$key])) {
    		$sql = "UPDATE account_property
				SET prop_value = '" . $this->badgerDb->escapeSimple($value) . "'
				WHERE prop_key = '" . $this->badgerDb->escapeSimple($key) . "'
					AND account_id = " . $this->id;
    		
    		$this->badgerDb->query($sql);
       	} else {
       		$sql = "INSERT INTO account_property (prop_key, account_id, prop_value)
				VALUES ('" . $this->badgerDb->escapeSimple($key) . "', "
				. $this->id . ", 
				'" . $this->badgerDb->escapeSimple($value) . "')";
				
			$this->badgerDb->query($sql);	
    		
       	}

		//echo "<pre>$sql</pre>";
		//echo $this->badgerDb->getMessage();
       	$this->properties[$key] = $value;
    }

	/**
	 * deletes property $key
	 * 
	 * @param string $key key of the target value
	 * @throws BadgerException if unknown key is passed
	 * @return void 
	 */
 	public function delProperty($key) {
		if (isset($this->properties[$key])) {
    		$sql = "DELETE FROM account_property
				WHERE prop_key = '" . $this->badgerDb->escapeSimple($key) . "'
					AND account_id = " . $this->id;
				
    		
    		$this->badgerDb->query($sql);
			  		
    		unset ($this->properties[$key]);
    	} else {
    		throw new BadgerException('Account', 'illegalPropertyKey', $key);
    	}
    }

	/**
	 * Fetches all planned and finished transactions, expands the planned transactions and sorts the finishedTransaction array.
	 */
	private function fetchTransactions() {
		if ($this->allPlannedDataFetched && $this->allFinishedDataFetched && $this->plannedDataExpanded) {
			return;
		}
		while ($this->fetchNextFinishedTransaction());
		
		while ($this->fetchNextPlannedTransaction());

		$this->expandPlannedTransactions();
		
		uasort($this->finishedTransactions, array('Account', 'transactionCompare'));
		
		$this->plannedDataExpanded = true;
	}
	
	/**
	 * Fetches the next finished transaction from DB.
	 * 
	 * @return mixed The fetched FinishedTransaction object or false if there are no more.
	 */
	private function fetchNextFinishedTransaction() {
		$this->fetchFinishedFromDB();

		$row = false;
		
		if($this->dbResultFinished->fetchInto($row, DB_FETCHMODE_ASSOC)){
			$this->finishedTransactions[$row['finished_transaction_id']] = new FinishedTransaction(&$this->badgerDb, &$this, $row);
			return $this->finishedTransactions[$row['finished_transaction_id']];
		} else {
			$this->allFinishedDataFetched = true;
			return false;    	
		}
		
	}

	/**
	 * Fetches the next planned transaction from DB.
	 * 
	 * @return mixed The fetched PlannedTransaction object or false if there are no more.
	 */
	private function fetchNextPlannedTransaction() {
		$this->fetchPlannedFromDB();

		$row = false;
		
		if($this->dbResultPlanned->fetchInto($row, DB_FETCHMODE_ASSOC)){
			$this->plannedTransactions[$row['planned_transaction_id']] = new PlannedTransaction(&$this->badgerDb, &$this, $row);
			return $this->plannedTransactions[$row['planned_transaction_id']];
		} else {
			$this->allPlannedDataFetched = true;
			return false;    	
		}
		
	}

	/**
	 * Prepares and executes the SQL query for finished transactions.
	 * 
	 * @throws BadgerException If an SQL error occured.
	 */
	private function fetchFinishedFromDB() {
		if($this->finishedDataFetched) {
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
		
		if ($this->type == 'transaction') {
			$sql = str_replace(Account::TABLE_PLACEHOLDER, 'ft', $sql);
		}
		
		//echo "<pre>$sql</pre>";

		$this->dbResultFinished =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResultFinished)) {
			echo "SQL Error: " . $this->dbResultFinished->getMessage();
			throw new BadgerException('Account', 'SQLError', $this->dbResultFinished->getMessage());
		}
		
		$this->finishedDataFetched = true; 	
	}

	/**
	 * Prepares and executes the SQL query for planned transactions.
	 * 
	 * @throws BadgerException If an SQL error occured.
	 */
	private function fetchPlannedFromDB() {
		if ($this->plannedDataFetched) {
			return;
		}

		$sql = "SELECT pt.planned_transaction_id, pt.title, pt.description, pt.amount, 
				pt.outside_capital, pt.transaction_partner, pt.begin_date, pt.end_date, pt.repeat_unit, 
				pt.repeat_frequency, pt.category_id
			FROM planned_transaction pt
				LEFT OUTER JOIN category c ON pt.category_id = c.category_id
			WHERE pt.account_id = " .  $this->id . "
				AND pt.begin_date <= '". $this->targetFutureCalcDate->getDate() . "'
				AND pt.end_date > NOW()\n"; 	

		$where = $this->getFilterSQL();
		//echo $where = $where . "\n" . $where;
		$where = trim (preg_replace('/' . Account::TABLE_PLACEHOLDER . "\.valuta_date[^\\n]+?(\$|\\n)/", "1=1\n", $where));
		//echo $where;
		if($where) {
			$sql .= "AND $where\n ";
		} 
		
		$order = $this->getOrderSQL();				
		$order = trim (preg_replace('/' . Account::TABLE_PLACEHOLDER . '\.valuta_date (asc|desc),*/', '', $order));
		
		if($order) {
			$sql .= "ORDER BY $order\n ";
		}
		
		if ($this->type == 'transaction') {
			$sql = str_replace(Account::TABLE_PLACEHOLDER, 'pt', $sql);
		}
		
		//echo "<pre>$sql;</pre>";
			
		$this->dbResultPlanned =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResultPlanned)) {
			echo "SQL Error: " . $this->dbResultPlanned->getMessage();
			throw new BadgerException('Account', 'SQLError', $this->dbResultPlanned->getMessage());
		}
		
		$row = false;
		
		$this->plannedDataFetched = true;
	}

	/**
	 * Compares two transactions according to $this->order.
	 * 
	 * For use with usort type of sort functions.
	 * 
	 * @param $aa object The first FinishedTransaction object.
	 * @param $bb object The second FinishedTransaction object.
	 * 
	 * @return integer -1 if $aa is smaller than $bb, 0 if they are equal, 1 if $aa is bigger than $bb.
	 */
	function transactionCompare($aa, $bb) {
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
					}
					break;
				
				case 'categoryTitle':
					if ($a->getCategory() && $b->getCategory()) {
						$tmp = strncasecmp($a->getCategory()->getTitle(), $b->getCategory()->getTitle(), 9999);
					}
					break;
				
				case 'repeatUnit':
					$tmp = $repeatUnits[$a->getRepeatUnit()] - $repeatUnits[$b->getRepeatUnit()];
					break;
				
				case 'repeatFrequency':
					$tmp = $a->getRepeatFrequency() - $b->getRepeatFrequency();
					break;
			}
			
			if ($tmp != 0) {
				return $tmp;
			}
		}

	return $default;
	}
	
}
?>
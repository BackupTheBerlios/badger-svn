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

require_once BADGER_ROOT . '/core/common.php';
require_once BADGER_ROOT . '/core/XML/DataGridHandler.class.php';
require_once BADGER_ROOT . '/modules/account/Account.class.php';
require_once BADGER_ROOT . '/modules/account/Currency.class.php';
require_once BADGER_ROOT . '/core/Amount.class.php';

/**
 * Manages all Accounts.
 * 
 * @author Eni Kao, Mampfred
 * @version $LastChangedRevision$
 */
class AccountManager extends DataGridHandler {
	
	/**
	 * List of valid field names.
	 * 
	 * @var array
	 */
	private $fieldNames = array (
		'accountId',
		'currency',
		'title',
		'balance'
	);
		
	/**
	 * Have the query been executed?
	 * 
	 * @var bool
	 */
	private $dataFetched = false;
	
	/**
	 * Has all data been fetched from the DB?
	 * 
	 * @var bool
	 */
	private $allDataFetched = false;
	
	/**
	 * List of Accounts.
	 * 
	 * @var array of Account
	 */
	private $accounts = array();
	
	/**
	 * The key of the current data element.
	 * 
	 * @var integer  
	 */
	private $currentAccount = null;

	/**
	 * The result object of the DB query.
	 * 
	 * @var object
	 */
	private $dbResult;
	
	/**
	 * Creates an AccountManager.
	 * 
	 * @param $badgerDb object The DB object.
	 */
	function __construct($badgerDb) {
		parent::__construct($badgerDb);
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
			'accountId' => 'integer',
			'currency' => 'string',
			'title' => 'string',
			'balance' => 'Amount'    	
		);
	
		if (!isset ($fieldTypes[$fieldName])){
			throw new BadgerException('AccountManager', 'invalidFieldName', $fieldName); 
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
	
	/**
	 * Returns the SQL name of the given field.
	 * 
	 * @param $fieldName string The field name to get the SQL name of.
	 * @throws BadgerException If an unknown field name was given.
	 * @return The SQL name of $fieldName.
	 */
	public function getFieldSQLName($fieldName) {
		$fieldSQLNames = array (
			'accountId' => 'a.account_id',
			'currency' => 'c.symbol',
			'title' => 'a.title',
			'balance' => 'SUM(ft.amount)'    	
		);
	
		if (!isset ($fieldSQLNames[$fieldName])){
			throw new BadgerException('AccountManager', 'invalidFieldName', $fieldName); 
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
		while($this->fetchNextAccount());
		
		$result = array();
		
		foreach($this->accounts as $currentAccount){
			$result[] = array (
				'accountId' => $currentAccount->getId(),
				'currency' => is_null($tmp = $currentAccount->getCurrency()) ? '' : $tmp->getSymbol(),
				'title' => $currentAccount->getTitle(),
				'balance' => $currentAccount->getBalance()->getFormatted()
			);
		}
		
		return $result;
	}
	
	/**
	 * Resets the internal counter of account.
	 */
	public function resetAccounts() {
		reset($this->accounts);
		$this->currentAccount = null;
	}

	/**
	 * Returns the next Account.
	 * 
	 * @return mixed The next Account object or false if we are at the end of the list.
	 */
	public function getNextAccount() {
		if (!$this->allDataFetched) {
			$this->fetchNextAccount();
		}

		return nextByKey($this->accounts, $this->currentAccount);
	}

	/**
	 * Returns the Account identified by $accountId.
	 * 
	 * @param integer $accountId The ID of the requested Account.
	 * @throws BadgerException SQLError If an SQL Error occurs.
	 * @throws BadgerException UnknownAccountId If $accountId is not in the Database
	 * @return object The Account object identified by $accountId. 
	 */
	public function getAccountById($accountId){
		settype($accountId, 'integer');

		if ($this->dataFetched){
			if (isset($this->accounts[$accountId])) {
				return $this->accounts[$accountId];
			}
			while ($currentAccount = $this->getNextAccount()) {
				if ($currentAccount->getId() == $accountId) {
					return $currentAccount;
				}
			}
		}	
		$sql = "SELECT a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id, SUM(ft.amount) balance
			FROM account a
				INNER JOIN currency c ON a.currency_id = c.currency_id
				LEFT OUTER JOIN finished_transaction ft ON a.account_id = ft.account_id
			GROUP BY a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id
			HAVING a.account_id = $accountId";
		
		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			//echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $this->dbResult->getMessage());
		}

		$tmp = $this->dataFetched;
		$this->dataFetched = true;
		
		$currentAccount = $this->getNextAccount();
		
		$this->dataFetched = $tmp;
		
		if($currentAccount){
			return $currentAccount;
		} else {
			$this->allDataFetched = false;	
			throw new BadgerException('AccountManager', 'UnknownAccountId', $accountId);
		}
	}
	
	
	/**
	 * Deletes the Account identified by $accountId.
	 * 
	 * @param integer $accountId The ID of the Account to delete.
	 * @throws BadgerException SQLError If an SQL Error occurs.
	 * @throws BadgerException UnknownAccountId If $accountId is not in the Database
	 */
	public function deleteAccount($accountId){
		settype($accountId, 'integer');

		if(isset($this->accounts[$accountId])){
			unset($this->accounts[$accountId]);
		}
		$sql= "DELETE FROM account
				WHERE account_id = $accountId";
				
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($this->badgerDb->affectedRows() != 1){
			throw new BadgerException('AccountManager', 'UnknownAccountId', $accountId);
		}
	}
	
	/**
	 * Creates a new Account.
	 * 
	 * @param string $title The title of the new Account.
	 * @param object $currency The Currency object of the new Account.
	 * @param string $description The description of the new Account.
	 * @param object $lowerLimit The Amount object marking the lower limit of the new Account.
	 * @param object $upperLimit The Amount object marking the upper limit of the new Account. 
	 * @throws BadgerException SQLError If an SQL Error occurs.
	 * @throws BadgerException insertError If the account cannot be inserted.
	 * @return object The new Account object.
	 */
	public function addAccount($title, $currency, $description = null, $lowerLimit = null, $upperLimit = null) {
		$accountId = $this->badgerDb->nextId('accountIds');
		
		$sql = "INSERT INTO account
			(account_id, title, currency_id ";
			
		if($description){
			$sql .= ", description";
		}
		
		if($lowerLimit){
			$sql .= ", lower_limit";
		}
		
		if($upperLimit){
			$sql .= ", upper_limit";
		}
		
		$sql .= ")
			VALUES ($accountId, '" . $this->badgerDb->escapeSimple($title) . "'," . $currency->getId();
	
		if($description){
			$sql .= ", '".  $this->badgerDb->escapeSimple($description) . "'";
		}
	
		if($lowerLimit){
			$sql .= ", '".  $lowerLimit->get() . "'";
		}
			
		if($upperLimit){
			$sql .= ", '".  $upperLimit->get() . "'";
		}
		$sql .= ")";
		
		
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			//echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($this->badgerDb->affectedRows() != 1){
			throw new BadgerException('AccountManager', 'insertError', $dbResult->getMessage());
		}
		
		$this->accounts[$accountId] = new Account(&$this->badgerDb, &$this, $accountId, $title, $description, $lowerLimit, $upperLimit, $currency);
		
		return $this->accounts[$accountId];	
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
		
		$sql = "SELECT a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id, SUM(ft.amount) balance
			FROM account a
				INNER JOIN currency c ON a.currency_id = c.currency_id
				LEFT OUTER JOIN finished_transaction ft ON a.account_id = ft.account_id
			GROUP BY a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id \n";
		
		$where = $this->getFilterSQL();
		if($where) {
			$sql .= "HAVING $where\n ";
		} 
		
		$order = $this->getOrderSQL();				
		if($order) {
			$sql .= "ORDER BY $order\n ";
		}
		
		//echo "<pre>$sql</pre>";

		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			//echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$this->dataFetched = true; 	
	}

	/**
	 * Fetches the next account from DB.
	 * 
	 * @return mixed The fetched Account object or false if there are no more.
	 */
	private function fetchNextAccount() {
		$this->fetchFromDB();

		$row = false;
		
		if($this->dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)){
			$this->accounts[$row['account_id']] = new Account(&$this->badgerDb, &$this, $row);
			return $this->accounts[$row['account_id']];
		} else {
			$this->allDataFetched = true;
			return false;    	
		}
	}
}
?>
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
require_once (BADGER_ROOT . '/modules/account/Account.class.php');
require_once (BADGER_ROOT . '/modules/account/Currency.class.php');
require_once (BADGER_ROOT . '/core/Amount.class.php');

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
	 * @var object
	 */
	private $accounts = array();
	
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
		while($this->getNextAccount());
		
		$result = array();
		
		foreach($this->accounts as $currentAccount){
			$result[] = array (
				'accountId' => $currentAccount->getId(),
				'currency' => $currentAccount->getCurrency()->getSymbol(),
				'title' => $currentAccount->getTitle(),
				'balance' => $currentAccount->getBalance()->get()
			);
		}
		
		return $result;
	}
	
	/**
	 * Gets next Account from the Database.
	 * 
	 * @return mixed ID of the fetched Account if successful, false otherwise.
	 */
	public function getNextAccount() {
		if($this->allDataFetched){
			return;
		}
		
		$this->fetchFromDB();
		$row = false;
		
		if($this->dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)){
			$this->accounts[$row['account_id']] = new Account(&$this->badgerDb, &$this, $row);
			return $row['account_id'];
		} else {
			$this->allDataFetched = true;
			return false;    	
		}
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
		if ($this->dataFetched){
			if(isset($this->accounts[$accountId])) {
				return $this->accounts[$accountId];
			}
			while($currentAccount=$this->getNextAccount()){
				if($currentAccount->getId() == $accountId){
					return $currentAccount;
				}
			}
		}	
		$sql = "SELECT a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id, c.symbol currency_symbol, c.long_name currency_long_name, SUM(ft.amount) balance
			FROM account a
				INNER JOIN currency c ON a.currency_id = c.currency_id
				LEFT OUTER JOIN finished_transaction ft ON a.account_id = ft.account_id
			GROUP BY a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id, currency_symbol, currency_long_name
			HAVING a.account_id = $accountId";
		
		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$currentAccount = $this->getNextAccount();
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
	 * @return void
	 */
	public function deleteAccount($accountId){
		if(isset($this->accounts[$accountId])){
			unset($this->accounts[$accountId]);
		}
		$sql= "DELETE FROM account
				WHERE account_id = $accountId";
				
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($dbResult->affectedRows() != 1){
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
			VALUES ($accountId, '" . $this->badgerDb->escapeSimple($title) . "'," . $curreny->getId();
	
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
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($dbResult->affectedRows() != 1){
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
				a.upper_limit, a.currency_id, c.symbol currency_symbol, c.long_name currency_long_name, SUM(ft.amount) balance
			FROM account a
				INNER JOIN currency c ON a.currency_id = c.currency_id
				LEFT OUTER JOIN finished_transaction ft ON a.account_id = ft.account_id
			GROUP BY a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id, currency_symbol, currency_long_name \n";
		
		$where = $this->getFilterSQL();
		$where = str_replace('balance', 'SUM(ft.amount)', $where);
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
			echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('AccountManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$this->dataFetched = true; 	
	}
}
?>
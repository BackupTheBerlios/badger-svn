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

class AccountManager extends DataGridHandler {
	
	private $fieldNames = array (
			'accountId',
			'currency',
			'title',
			'balance'
		);
		
	private $dataFetched = false;
	private $allDataFetched = false;
	private $accounts = array();
	private $dbResult;
	
	function AccountManager($badgerDb) {
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
			throw new badgerException('AccountManager', 'invalidFieldName', $fieldName); 
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
	
	public function getNextAccount() {
		if($this->allDataFetched){
			return;
		}
		$this->fetchFromDB();
		$row = false;
		if($this->dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)){
			$this->accounts[$row['account_id']] = new Account(&$this, $row);
			return $row['account_id'];
		} else {
			$this->allDataFetched = true;
			return false;    	
		}
	}
	
	private function fetchFromDB() {
		if($this->dataFetched){
			return;
		}
		
		$sql = "SELECT a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
				a.upper_limit, a.currency_id, c.symbol currency_symbol, c.long_name currency_long_name, SUM(ft.amount) balance
			FROM account a
				INNER JOIN currency c ON a.currency_id = c.currency_id
				LEFT OUTER JOIN finished_transaction ft ON a.account_id = ft.account_id
		";
		
		$sql .= "GROUP BY a.account_id, a.currency_id, a.title, a.description, a.lower_limit, 
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
			throw new badgerException('AccountManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$this->dataFetched = true; 	
	}
}
?>
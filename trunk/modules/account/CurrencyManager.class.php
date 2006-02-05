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

require_once BADGER_ROOT . '/core/XML/DataGridHandler.class.php';
require_once BADGER_ROOT . '/modules/account/Currency.class.php';
require_once BADGER_ROOT . '/core/common.php';

class CurrencyManager extends DataGridHandler {
	/**
	 * List of valid field names.
	 * 
	 * @var array
	 */
	private $fieldNames = array (
		'currencyId',
		'symbol',
		'longName'
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
	 * The result object of the DB query.
	 * 
	 * @var object
	 */
	private $dbResult;
	
	private $currencies = array();
	
	private $currentCurrency = null;
	
	function __construct ($badgerDb) {
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
			'currencyId' => 'integer',
			'symbol' => 'string',
			'longName' => 'string'
		);
	
		if (!isset ($fieldTypes[$fieldName])){
			throw new BadgerException('CurrencyManager', 'invalidFieldName', $fieldName); 
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
		$fieldTypes = array (
			'currencyId' => 'c.currency_id',
			'symbol' => 'c.symbol',
			'longName' => 'c.long_name'
		);
	
		if (!isset ($fieldTypes[$fieldName])){
			throw new BadgerException('CurrencyManager', 'invalidFieldName', $fieldName); 
		}
		
		return $fieldTypes[$fieldName];    	
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
		while($this->fetchNextCurrency());
		
		$result = array();
		
		foreach($this->currency as $currentCurrency){
			$result[] = array (
				'currencyId' => $currentCurrency->getId(),
				'symbol' => $currentCurrency->getSymbol(),
				'longName' => $currentCurrency->getLongName()
			);
		}
		
		return $result;
	}
	
	public function resetCurrencies() {
		reset($this->currencies);
		$this->currentCurrency = null;
	}
	
	public function getCurrencyById($currencyId) {
		if ($this->dataFetched){
			if(isset($this->currencies[$currencyId])) {
				return $this->currencies[$currencyId];
			}
			while ($currentCurrency = $this->fetchNextCurrency()) {
				if($currentCurrency->getId() == $currencyId) {
					return $currentCurrency;
				}
			}
		}	
		$sql = "SELECT c.currency_id, c.symbol, c.long_name
			FROM currency c
			WHERE c.currency_id = $currencyId";

		//echo "<pre>$sql</pre>";

		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('CurrencyManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$tmp = $this->dataFetched;
		$this->dataFetched = true;
		
		$currentCurrency = $this->fetchNextCurrency();
		
		$this->dataFetched = $tmp;

		if($currentCurrency) {
			return $currentCurrency;
		} else {
			$this->allDataFetched = false;	
			throw new BadgerException('CurrencyManager', 'UnknownCurrencyId', $currencyId);
		}
	}
		
	public function getNextCurrency() {
		if (!$this->allDataFetched) {
			$this->fetchNextCurrency();
		}

		return nextByKey($this->currencies, $this->currentCurrency);
	}

	public function deleteCurrency($currencyId){
		if(isset($this->currencies[$currencyId])){
			unset($this->currencies[$currencyId]);
		}
		$sql= "DELETE FROM currency
				WHERE currency_id = $currencyId";
				
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('CurrencyManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($dbResult->affectedRows() != 1){
			throw new BadgerException('CurrencyManager', 'UnknownCurrencyId', $currencyId);
		}
	}
	
	public function addCurrency($symbol, $longName) {
		$currencyId = $this->badgerDb->nextId('currencyIds');
		
		$sql = "INSERT INTO currency
			(currency_id, symbol, long_name) VALUES ($currencyId, '" . $this->badgerDb->escapeSimple($symbol) . "', '" . $this->badgerDb->escapeSimple($longName) . "')";
			
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('CurrencyManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($dbResult->affectedRows() != 1){
			throw new BadgerException('CurrencyManager', 'insertError', $dbResult->getMessage());
		}
		
		$this->currencies[$currencyId] = new Currency(&$this->badgerDb, &$this, $currencyId, $symbol, $longName);
		
		return $this->currencies[$currencyId];	
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
		
		$sql = "SELECT c.currency_id, c.parent_id, c.title, c.description, c.outside_capital
			FROM currency c";
					
		$where = $this->getFilterSQL();
		if($where) {
			$sql .= "WHERE $where\n ";
		} 
		
		$order = $this->getOrderSQL();				
		if($order) {
			$sql .= "ORDER BY $order\n ";
		}
		
		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('CurrencyManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$this->dataFetched = true; 	
	}

	private function fetchNextCurrency() {
		$this->fetchFromDB();
		$row = false;
		
		if($this->dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)){

			//echo "<pre>"; print_r($row); echo "</pre>";

			$this->currencies[$row['currency_id']] = new Currency(&$this->badgerDb, &$this, $row);
			return $this->currencies[$row['currency_id']];
		} else {
			$this->allDataFetched = true;
			return false;    	
		}
	}
}
?>
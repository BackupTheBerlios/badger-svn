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
require_once BADGER_ROOT . '/modules/account/Account.class.php';
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';

class MultipleAccounts extends DataGridHandler {
	private $fieldNames	= array (
		'finishedTransactionId',
		'accountTitle',
		'title',
		'description',
		'valutaDate',
		'amount',
		'outsideCapital',
		'transactionPartner',
		'categoryId',
		'categoryTitle',
		'parentCategoryId',
		'parentCategoryTitle',
		'concatCategoryTitle',
		'exceptional',
		'periodical'
	);
	
	private $accountManager = null;
	
	private $accounts = array();
	
	private $transactions = array();
	
	function __construct(&$badgerDb, $params = null) {
		$this->badgerDb = $badgerDb;
		
		$this->accountManager = new AccountManager($badgerDb);
		
		$accountIds = explode(',', $params);
		foreach ($accountIds as $key => $val) {
			settype($accountIds[$key], 'integer');
		}
		
		foreach ($accountIds as $currentAccountId) {
			$account = $this->accountManager->getAccountById($currentAccountId);
			$account->setType('finished');
			$this->accounts[] = $account;
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
			'finishedTransactionId' => 'integer',
			'accountTitle' => 'string',
			'title' => 'string',
			'description' => 'string',
			'valutaDate' => 'date',
			'amount' => 'amount',
			'outsideCapital' => 'boolean',
			'transactionPartner' => 'string',
			'categoryId' => 'integer',
			'categoryTitle' => 'string',
			'parentCategoryId' => 'integer',
			'parentCategoryTitle' => 'string',
			'concatCategoryTitle' => 'string',
			'exceptional' => 'boolean',
			'periodical' => 'boolean'
		);
	
		if (!isset ($fieldTypes[$fieldName])){
			throw new BadgerException('MultipleAccounts', 'invalidFieldName', $fieldName); 
		}
		
		return $fieldTypes[$fieldName];    	
	}

	public function getIdFieldName() {
		return 'finishedTransactionId';
	}
	
	public function getAllFieldNames() {
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
			'finishedTransactionId' => 'ft.transaction_id',
			'accountTitle' => 'NULL',
			'title' => 'ft.title',
			'description' => 'ft.description',
			'valutaDate' => 'ft.valuta_date',
			'amount' => 'ft.amount',
			'outsideCapital' => 'ft.outside_capital',
			'transactionPartner' => 'ft.transaction_parter',
			'categoryId' => 'ft.category_id',
			'categoryTitle' => 'c.title',
			'parentCategoryId' => 'pc.category_id',
			'parentCategoryTitle' => 'pc.title',
			'concatCategoryTitle' => 'CONCAT(IF(NOT pc.title IS NULL, CONCAT(pc.title, \' - \'), \'\'), c.title)',
			'exceptional' => 'ft.exceptional',
			'periodical' => 'ft.periodical'
		);

		if (!isset ($fieldSQLNames[$fieldName])){
			throw new BadgerException('Account', 'invalidFieldName', $fieldName); 
		}
		
		return $fieldSQLNames[$this->type][$fieldName];    	
	}

	public function getAll() {
		foreach($this->accounts as $currentAccount) {
			$newOrder = array();
			foreach ($this->order as $currentOrder) {
				if ($currentOrder['key'] != 'accountTitle') {
					$newOrder[] = $currentOrder;
				}
			}
			$currentAccount->setOrder($newOrder);

			$newFilter = array();
			foreach ($this->filter as $currentFilter) {
				if ($currentFilter['key'] != 'accountTitle') {
					$newFilter[] = $currentFilter;
				}
			}
			$currentAccount->setFilter($newFilter);
			
			while($currentTransaction = $currentAccount->getNextFinishedTransaction()) {
				$this->transactions[] = $currentTransaction;
			}
		}
		
		$compare = new CompareTransaction($this->order);
		
		uasort($this->transactions, array($compare, 'compare'));
		
		return getAllFinishedTransactions($this->transactions, $this->selectedFields);
	}
}
?>
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
require_once BADGER_ROOT . '/modules/account/Category.class.php';
require_once BADGER_ROOT . '/core/common.php';

class CategoryManager extends DataGridHandler {
	/**
	 * List of valid field names.
	 * 
	 * @var array
	 */
	private $fieldNames = array (
		'categoryId',
		'title',
		'description',
		'outsideCapital',
		'parentId',
		'parentTitle'
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
	
	private $categories = array();
	
	private $currentCategory = null;
	
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
			'categoryId' => 'integer',
			'title' => 'string',
			'description' => 'string',
			'outsideCapital' => 'boolean',
			'parentId' => 'integer',
			'parentTitle' => 'string'
		);
	
		if (!isset ($fieldTypes[$fieldName])){
			throw new BadgerException('CategoryManager', 'invalidFieldName', $fieldName); 
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
			'categoryId' => 'c.category_id',
			'title' => 'c.title',
			'description' => 'c.description',
			'outsideCapital' => 'c.outside_capital',
			'parentId' => 'c.parent_id',
			'parentTitle' => 'p.title'
		);
	
		if (!isset ($fieldTypes[$fieldName])){
			throw new BadgerException('CategoryManager', 'invalidFieldName', $fieldName); 
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
		while($this->fetchNextCategory());
		
		$result = array();
		
		foreach($this->categories as $currentCategory){
			$result[] = array (
				'categoryId' => $currentCategory->getId(),
				'title' => $currentCategory->getTitle(),
				'description' => $currentCategory->getDescription(),
				'outsideCapital' => is_null($tmp = $currentCategory->getOutsideCapital()) ? '' : $tmp,
				'parentId' => is_null($tmp = $currentCategory->getParent()) ? '' : $tmp->getId(),
				'parentTitle' => is_null($tmp = $currentCategory->getParent()) ? '' : $tmp->getTitle()
			);
		}
		
		return $result;
	}
	
	public function resetCategories() {
		reset($this->categories);
		$this->currentCategory = null;
	}
	
	public function getCategoryById($categoryId) {
		if ($this->dataFetched){
			if(isset($this->categories[$categoryId])) {
				return $this->categories[$categoryId];
			}
			while($currentCategory=$this->getNextCategory()){
				if($currentCategory->getId() == $categoryId){
					return $currentCategory;
				}
			}
		}	
		$sql = "SELECT c.category_id, c.parent_id, c.title, c.description, c.outside_capital
			FROM category c
			WHERE c.category_id = $categoryId";

		//echo "<pre>$sql</pre>";

		$this->dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($this->dbResult)) {
			echo "SQL Error: " . $this->dbResult->getMessage();
			throw new BadgerException('CategoryManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$tmp = $this->dataFetched;
		$this->dataFetched = true;
		
		$currentCategory = $this->fetchNextCategory();
		
		$this->dataFetched = $tmp;

		if($currentCategory) {
			return $currentCategory;
		} else {
			$this->allDataFetched = false;	
			throw new BadgerException('CategoryManager', 'UnknownCategoryId', $categoryId);
		}
	}
		
	/**
	 * Gets next Account from the Database.
	 * 
	 * @return mixed ID of the fetched Account if successful, false otherwise.
	 */
	public function getNextCategory() {
		if (!$this->allDataFetched) {
			$this->fetchNextCategory();
		}

		return nextByKey($this->categories, $this->currentCategory);
	}



	public function deleteCategory($categoryId){
		if(isset($this->categories[$categoryId])){
			unset($this->categories[$categoryId]);
		}
		$sql= "DELETE FROM category
				WHERE category_id = $categoryId";
				
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('CategoryManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($dbResult->affectedRows() != 1){
			throw new BadgerException('CategoryManager', 'UnknownCategoryId', $categoryId);
		}
	}
	
	public function addCategory($title, $description = null, $outsideCapital = false) {
		$categoryId = $this->badgerDb->nextId('categoryIds');
		
		$sql = "INSERT INTO category
			(category_id, title ";
			
		if($description){
			$sql .= ", description";
		}
		
		if($outsideCapital){
			$sql .= ", outside_capital";
		}
		
		$sql .= ")
			VALUES ($categoryId, '" . $this->badgerDb->escapeSimple($title) . "'";
	
		if($description){
			$sql .= ", '".  $this->badgerDb->escapeSimple($description) . "'";
		}
	
		if($outsideCapital){
			$sql .= ", ".  $this->badgerDb->quoteSmart($outsideCapital);
		}
			
		$sql .= ")";
		
		
		$dbResult =& $this->badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			echo "SQL Error: " . $dbResult->getMessage();
			throw new BadgerException('CategoryManager', 'SQLError', $dbResult->getMessage());
		}
		
		if($dbResult->affectedRows() != 1){
			throw new BadgerException('CategoryManager', 'insertError', $dbResult->getMessage());
		}
		
		$this->categories[$categoryId] = new Category(&$this->badgerDb, &$this, $title, $description, $outsideCapital);
		
		return $this->categories[$categoryId];	
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
		
		$sql = "SELECT c.category_id, c.parent_id, c.title, c.description, c.outside_capital
			FROM category c
				LEFT OUTER JOIN category p ON c.parent_id = p.category_id
			";
					
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
			throw new BadgerException('CategoryManager', 'SQLError', $this->dbResult->getMessage());
		}
		
		$this->dataFetched = true; 	
	}

	private function fetchNextCategory() {
		$this->fetchFromDB();
		$row = false;
		
		if($this->dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)){

			//echo "<pre>"; print_r($row); echo "</pre>";

			$this->categories[$row['category_id']] = new Category(&$this->badgerDb, &$this, $row);
			return $this->categories[$row['category_id']];
		} else {
			$this->allDataFetched = true;
			return false;    	
		}
	}
}
?>
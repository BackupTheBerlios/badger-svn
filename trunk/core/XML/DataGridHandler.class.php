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

/**
 * Defines the interface required for an DataGridHandler.
 * 
 * @see getDataGridXML.php
 * @author Eni Kao
 * @version $LastChangedRevision$
 */
abstract class DataGridHandler {
	
	/**
	 * The DB object.
	 * 
	 * @var object
	 */
	protected $badgerDb;
	
	/**
	 * All order criteria, structure described in setOrder().
	 * 
	 * @var array
	 */
	protected $order;
	
	/**
	 * Initializes the DB object.
	 * 
	 * @param object $badgerDb The DB object.
	 */
	public function DataGridHandler($badgerDb) {
		$this->badgerDb = $badgerDb;
	}
	
	/**
	 * Checks if a field named $fieldName exists in this object.
	 * 
	 * @param string $fieldName The name of the field in question.
	 * @return boolean true if this object has this field, false otherwise.
	 */
	public abstract function hasField($fieldName);
	
	/**
	 * Returns the field type of $fieldName.
	 * 
	 * @param string $fieldName The name of the field in question.
	 * @throws BadgerException If there is no field $fieldName.
	 * @return string The type of field $fieldName.
	 */
	public abstract function getFieldType($fieldName);
	
	/**
	 * Sets the order to return the results.
	 * 
	 * $order has the following structure:
	 * array (
	 *   array (
	 *     'key' => 'valid field name',
	 *     'dir' => either 'asc' or 'desc'
	 *   )
	 * );
	 * 
	 * The inner array can be repeated at most twice.
	 *
	 * @param array $order The order this object should return the results, in above form.
	 * @throws BadgerException If $order has the wrong format or an invalid field name was given.
	 * @return void
	 */
	public function setOrder($order){
		$this->order = array ();
		$numOrders = 0;
	
		if (!is_array($order)){
			throw new badgerException('DataGridHandler', 'paramNoArray'); 
		}
	
		foreach ($order as $key => $val){
	   		if (!is_array($val)){
				throw new badgerException('DataGridHandler', 'arrayElementNoArray', $key); 
			}
			if(!isset($val['key'])){
				throw new badgerException('DataGridHandler', 'keyIndexNotDefined', $key);
			}
			if(!isset($val['dir'])){
				throw new badgerException('DataGridHandler', 'dirIndexNotDefined', $key);
			}
			
			if(!$this-> hasField($val['key'])){
				throw new badgerException('DataGridHandler', 'illegalField', $val['key']);
			}
			if(strtolower($val['dir'])  != 'asc' and strtolower($val['dir'])  != 'desc'){
				throw new badgerException('DataGridHandler', 'illegalDirection', $val['dir']);
			}
			
			$this->order[] = array (
				'key' => $val['key'],
				'dir' => $val['dir']
			);
			
			$numOrders++;
			
			if ($numOrders >= 3) {
				break;
			}
		}
	}
	
	
	/**
	 * Sets the filter(s) to limit the results to.
	 * 
	 * $filter has the following structure:
	 * array (
	 *   array (
	 *     'key' => 'valid field name',
	 *     'op' => 'valid operator'
	 *     'val => comparison value
	 *   )
	 * );
	 * 
	 * The inner array can be repeated.
	 * 
	 * @param array $filter The filter(s) this object should return the results, in above form.
	 * @throws BadgerException If $filter has the wrong format or an invalid field name was given.
	 * @return void
	 */
	public function setFilter($filter) {
		$this->filter = array ();
	
		if (!is_array($filter)){
			throw new badgerException('DataGridHandler', 'paramNoArray'); 
		}
	
		foreach ($filter as $key => $val){
	   		if (!is_array($val)){
				throw new badgerException('DataGridHandler', 'arrayElementNoArray', $key); 
			}
			if(!isset($val['key'])){
				throw new badgerException('DataGridHandler', 'keyIndexNotDefined', $key);
			}
			if(!isset($val['op'])){
				throw new badgerException('DataGridHandler', 'opIndexNotDefined', $key);
			}
			if(!isset($val['val'])){
				throw new badgerException('DataGridHandler', 'valIndexNotDefined', $key);
			}

			if(!$this->hasField($val['key'])){
				throw new badgerException('DataGridHandler', 'illegalField', $val['key']);
			}
	
			//We trust the caller to check op and val
			
			$this->filter[] = array (
				'key' => $val['key'],
				'op' => $val['op'],
				'val' => $val['val']
			);
		}
		
	}
	
	/**
	 * Returns all valid field names.
	 * 
	 * @return array A list of all field names.
	 */
	public abstract function getFieldNames();
	
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
	public abstract function getAll();
	
	protected function getOrderSQL() {
		$result = '';
		$firstrun = true;
		
		foreach ($this->order as $val){
			if($firstrun) {
				$firstrun = false;
			} else {
				$result .= ', ';
			}
			$result .= $val['key'] . ' ' . $val['dir'];
		}	
		
		return $result;    	
	}
	
	protected function getFilterSQL() {
		$result = '';
		$firstrun = true;
		
		foreach ($this->filter as $val){
			if($firstrun) {
				$firstrun = false;
			} else {
				$result .= ' AND ';
			}
			
			if ($val['op'] == 'bw'
				|| $val['op'] == 'ew'
				|| $val['op'] == 'ct'
			) {
				$result .= "LOWER(CONVERT($val[key], CHAR)) LIKE ";
				
				if ($val['val'] instanceof Amount) {
					$stringVal = $val['val']->get();
				} else {
					$stringVal = (string) strtolower($val['val']);
				}
				
				switch ($val['op']) {	
					case 'bw':
	    				$result .= "'" . addslashes($stringVal) . "%'";
	    				break;
	    				
					case 'ew':
	    				$result .= "'%" . addslashes($stringVal) . "'";
	    				break;
	    				
					case 'ct': 	
	    				$result .= "'%" . addslashes($stringVal) . "%'";
	    				break;
				}
			} else {
				$result .= $val['key'];

				switch ($val['op']) {
					case 'eq':
						$result .= ' = ';
						break;
						
					case 'lt':
						$result .= ' < ';
						break;
						
					case 'le':
						$result .= ' <= ';
						break;
						
					case 'gt':
						$result .= ' > ';
						break;
						
					case 'ge':
						$result .= ' >= ';
						break;
						
					case 'ne':
						$result .= ' != ';
						break;
				}
				$result .= $this->formatForDB($val['val'], $this->getFieldType($val['key']));
			}
		}	
		
		return $result;
	}
	
	protected function formatForDB($val, $type) {
		switch ($type) {
			case 'int':
			case 'integer':
			case 'boolean':
			case 'bool':
			case 'float':
			case 'double':
				return $val;
			
			case 'string':
				return "'" . addslashes($val) . "'";
				
			case 'Amount':
				return "'" . $val->get() . "'";
				break;
		}
	}
}
?>
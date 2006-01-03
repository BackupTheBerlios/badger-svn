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

/**
 * uses Serializer.php to structure Data in XML format.
 */
require_once 'Serializer.php';


/**
 * Supplies the data to a DataGrid in XML.
 * 
 * @author Eni Kao, Para Phil 
 * @version $LastChangedRevision$
 */
class DataGridXML {

	/**
	 * Exception Code if the serializer returns an errror.  
	 */
	const XML_SERIALIZER_EXCEPTION = 4711;
	
	/**
	 * Exception Code if getXML called with undefined columns.  
	 */
	const UNDEFINED_COLUMNS = 4712;
	
	/**
	 * The XML Serializer Object.
	 * 
	 * @var object
	 */
	private $serializer;
	
	/**
	 * All column heads in the table
	 * 
	 * @var simple string array of column names
	 */
	private $columns;
	
	/**
	 * The single rows in the table.
	 * 
	 * @var array array of array of cell data
	 */
	private $rows;

	/**
	 * Initializes serializer and sets inital column and row data if given.
	 * 
	 * @param $columns array the columns to initialize the Object withh
	 * @param $rows array the rows of the Object
	 */
	function __construct() {
		$this->initSerializer();
		$numArgs = func_num_args();
		
		// The constructor can't be overloaded the usual way so we have to use this workaround.		
		if ($numArgs == 2) {
			
			// func_get_arg() can't be used as a function parameter.
			$columns = func_get_arg(0);
			$rows = func_get_arg(1);
			$this->setData($columns, $rows);
		}
	}
	
	/**
	 * Initialize the Serializer.
	 * 
	 * @return void
	 */
	private function initSerializer() {

		// Makes the Serializer use sensible tag names.   
		$options = array (
			XML_SERIALIZER_OPTION_ROOT_NAME => "datatable",
			XML_SERIALIZER_OPTION_DEFAULT_TAG => array (
				'columns' => 'column',
				'rows' => 'row',
				'row' => 'cell'
			)
		);
		
    	// If the debug mode is on the xml will be formatted to a more pretty format.
    	if (defined('BADGER_DEBUG')) {
    		$options[XML_SERIALIZER_OPTION_INDENT] = '    ';
			$options[XML_SERIALIZER_OPTION_LINEBREAKS] = "\n";
    	}

		$this->serializer = new XML_Serializer($options);
	}
	
	/**
	 * Sets the column head and row data.
	 * 
	 * @param $columns array array of strings of cloumn names
	 * @param $rows array array of array of cell data
	 * @return void 
	 */
	public function setData($columns, $rows) {
		$this->setColumns($columns);
		$this->setRows($rows);
	}

	/**
	 * Sets the column head.
	 * 
	 * @param $columns array array of strings of cloumn names
	 * @return void
	 */
	public function setColumns($columns) {
		if (is_array($columns)) {
			$this->columns = $columns;
		}
	}

	/**
	 * Sets the rows.
	 *
	 * @param $rows array array of array of cell data
	 * @return void 
	 */
	public function setRows($rows) {
		if (is_array($rows)) {
			$this->rows = $rows;
		}
	}

	/**
	 * Adds several rows.
	 * 
	 * Although works if no data has been given yet.
	 * 
	 * @param $rows array array of array of cell data
	 * @return void 
	 */
	public function addRows($rows) {
		
		// checks if we have already data 
		if (is_array($this->rows)) {
			if (is_array($rows)) {
				$this->rows = array_merge($this->rows, $rows);
			}
		} else {
			$this->setRows($rows);
		}
	}
	
	/**
	 * Adds a single row.
	 * 
	 * @param $row array array of cell data
	 * @return void
	 */
	public function addRow($row) {
		if (is_array($row)) {
			$this->rows[] = $row;
		}
	} 
	
	/**
	 * All rows will be erased.
	 * 
	 * @return void 
	 */
	public function emptyRows() {
		$this->rows = null;
	}		
	
	/**
	 * Returns the XML structure.
	 * 
	 * @return string XML Structure
	 */
	public function getXML() {
		if (!is_array($this->columns)) {
			throw new Exception('getXML called with undefined columns', DataGridXML::UNDEFINED_COLUMNS);
		}
		
		$data = array (
			'columns' => $this->columns,
			'rows' => $this->rows 
		);
	
		$result = $this->serializer->serialize($data);
		
		if ($result === true) {
			return $this->serializer->getSerializedData();
		} else {
			throw new Exception('Error while calling XML Serializer', DataGridXML::XML_SERIALIZER_EXCEPTION);
		}
	}
}
?>
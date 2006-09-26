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
/**
 * DataGrid Class
 * - add DataGrid to the doc
 * - add all necessary javascript variables to the doc
 *  
 * @author Sepp
 */
class DataGrid {
	/**
	 * TemplateEngine Object
	 * @var object
	 */
	private $tpl;
	
	/**
	 * Loading Message in the footer of the dataGrid table
	 * @var string
	 */
	private $LoadingMessage;
	
	/**
	 * Unique ID of the dataGrid
	 * @var string
	 */
	public $UniqueId;
	
	/**
	 * Name of the dataGrid columns
	 * @var array
	 */
	public $headerName = array();

	/**
	 * column order (column name must be in the xml!)
	 * @var array
	 */
	public $columnOrder = array();
	
	/**
	 * size of the columns in px
	 * @var array
	 */
	public $headerSize = array();
	
	/**
	 * align of the cells (left, right)
	 * @var array
	 */
	public $cellAlign = array();
	
	/**
	 * text of the javascript alert, confirm deleting
	 * @var string
	 */
	public $deleteMsg;

	/**
	 * text of the javascript alert when the user want to edit a row without selecting one before
	 * @var string
	 */	
	public $noRowSelectedMsg;

	/**
	 * refresh type after deletion of one record in data grid
	 * @var string
	 */
	public $deleteRefreshType;
		
	/**
	 * php-page called for deletion
	 * @var string
	 */
	public $deleteAction;
	
	/**
	 * php-page called for editing
	 * @var string
	 */
	public $editAction;
	
	/**
	 * php-page called for insertion
	 * @var string
	 */
	public $newAction;
	
	/**
	 * text after the number of rows
	 * @var string
	 */
	public $rowCounterName;
	
	/**
	 * width of the datagrid (e.g. 100px, 20em, 100%)
	 * @var string
	 */	
	public $width;
	
	/**
	 * height of the datagrid (e.g. 100px, 20em, 100%)
	 * @var string
	 */	
	public $height;

	/**
	 * function function __construct($tpl)
	 * @param object template engine
	 */
	public function __construct($tpl) {
		$this->tpl = $tpl;
		$this->LoadingMessage = getBadgerTranslation2('dataGrid', 'LoadingMessage');
		$this->deleteMsg = getBadgerTranslation2('dataGrid', 'deleteMsg');
		$this->rowCounterName = getBadgerTranslation2('dataGrid', 'rowCounterName');
		$this->noRowSelectedMsg = getBadgerTranslation2('dataGrid', 'NoRowSelectedMsg');
		
		$tpl->addCss("Widgets/dataGrid/dataGridPrint.css", "print");
		$tpl->addCss("Widgets/dataGrid/dataGrid.css", "screen");
	}
	
	/**
	 * function writeDataGrid ()
	 * @return string complete dataGrid skeleton (without rows)
	 */
	public function writeDataGrid() {
		if($this->width) $this->width = ' style="width:'.$this->width.';" '; 
		if($this->height) $this->height = ' style="height:'.$this->height.';" '; 
		
		$output = '<form id="dgForm"><div id="dataGrid" '.$this->width.'>
					<table id="dgTableHead" cellpadding="2" cellspacing="0">
						<tr>
							<td style="width: 25px"><input id="dgSelector" type="checkbox" /></td>';
			for ($i=0; $i < count($this->headerName); $i++) {
				$output .= '<td class="dgColumn" id="dgColumn'.$this->columnOrder[$i].'" style="width: '.$this->headerSize[$i].'px">'.
							$this->headerName[$i].'&nbsp;'.
						   '<img src="'.BADGER_ROOT.'/tpl/'.$this->tpl->getThemeName().'/Widgets/dataGrid/dropEmpty.gif" id="dgImg'.$this->columnOrder[$i].'" /></td>';
			}
		$output .= '		<td>&nbsp;</td>
						</tr>
					</table>';
					
		$output .= '<div id="dgDivScroll" '.$this->height.'>
					<table id="dgTableData" cellpadding="2" cellspacing="0">
						<tbody></tbody>
					</table>
					</div>
							
					<table id="dgTableFoot" cellpadding="2" cellspacing="0">						
						<tr>
							<td style="width: 110px"><span id="dgCount">0</span> '.$this->rowCounterName.'&nbsp;</td>
							<td style="width: 23px"><span id="dgFilterStatus"><img src="'.BADGER_ROOT.'/tpl/'.$this->tpl->getThemeName().'/Widgets/dataGrid/filter.gif"></span></td>
							<td><span id="dgMessage"></span></td>
						</tr>
					</table>
					</div></form>';
		return $output;		
	}
	
	/**
	 * function initDataGridJS ()
	 */
	public function initDataGridJS() {
		global $badgerDb;
		global $tpl;
		
		$us = new UserSettings($badgerDb);
		
		$this->tpl->addJavaScript("js/dataGrid.js");
		$this->tpl->addOnLoadEvent('badgerRoot = "'. $tpl->getBadgerRoot() .'";');
		
		$this->tpl->addOnLoadEvent('dataGrid = new classDataGrid();');
		$this->tpl->addOnLoadEvent('dataGrid.uniqueId = "'. $this->UniqueId .'";');
		$this->tpl->addOnLoadEvent('dataGrid.sourceXML = "'.$this->sourceXML.'";');

		$this->tpl->addOnLoadEvent('dataGrid.headerName = new Array("'.implode('","',$this->headerName).'");');
		$this->tpl->addOnLoadEvent('dataGrid.columnOrder = new Array("'.implode('","',$this->columnOrder).'");');
		$this->tpl->addOnLoadEvent('dataGrid.headerSize = new Array('.implode(',',$this->headerSize).');');
		$this->tpl->addOnLoadEvent('dataGrid.cellAlign = new Array("'.implode('","',$this->cellAlign).'");');
		$this->tpl->addOnLoadEvent('dataGrid.noRowSelectedMsg = "'. $this->noRowSelectedMsg .'";');
		$this->tpl->addOnLoadEvent('dataGrid.deleteMsg = "'. $this->deleteMsg .'";');
		$this->tpl->addOnLoadEvent('dataGrid.deleteRefreshType = "'. $this->deleteRefreshType .'";');
		$this->tpl->addOnLoadEvent('dataGrid.deleteAction = "'. $this->deleteAction .'";');
		$this->tpl->addOnLoadEvent('dataGrid.editAction = "'. $this->editAction .'";');
		$this->tpl->addOnLoadEvent('dataGrid.newAction = "'. $this->newAction .'";');

		$this->tpl->addOnLoadEvent('dataGrid.tplPath = "'.BADGER_ROOT.'/tpl/'.$this->tpl->getThemeName().'/Widgets/dataGrid/";');
		$this->tpl->addOnLoadEvent('dataGrid.loadingMessage = "'.$this->LoadingMessage.'";');

		try {$dgParameter = $us->getProperty('dgParameter'.$this->UniqueId); } catch(BadgerException $e) {};
		if ( isset($dgParameter) ) {		
				$this->tpl->addOnLoadEvent("dataGrid.init('".$dgParameter."');");
		}else {
			$this->tpl->addOnLoadEvent("dataGrid.init();");
		}	
		
		$this->tpl->addOnLoadEvent('Behaviour.register(dataGrid.behaviour);');
		$this->tpl->addOnLoadEvent('Behaviour.apply();');
		$this->tpl->addOnLoadEvent('Event.observe($("dataGrid"), \'keypress\', dataGrid.KeyEvents, false);');
		
	}
	
	public static function getFilterSelectArray() {
		$filterArray = array ();
		
		$filterArray["eq"] = "=";
		$filterArray["lt"] = "<";
		$filterArray["le"] = "<=";
		$filterArray["gt"] = ">";
		$filterArray["ge"] = ">=";
		$filterArray["ne"] = "<>";
		//TODO Translation
		$filterArray["bw"] = "begins with";
		$filterArray["ew"] = "ends with";
		$filterArray["ct"] = "contains";
		
		return $filterArray;
	}
	
	public static function getDateFilterSelectArray() {
		$filterArray = array ();
		
		//TODO Translation
		$filterArray["eq"] = "equal to";
		$filterArray["lt"] = "before";
		$filterArray["le"] = "before or equal to";
		$filterArray["gt"] = "after";
		$filterArray["ge"] = "equal to or after";
		$filterArray["ne"] = "not equal to";
		
		return $filterArray;
	}
	
}
?>
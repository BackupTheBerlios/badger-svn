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
	 * initial sort column name
	 * @var string
	 */	
	public $initialSortColumn;
	
	/**
	 * initial sort direction
	 * @var string
	 */	
	public $initialSortDirection;
	
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
		global $print;
		$this->tpl = $tpl;
		$this->LoadingMessage = getBadgerTranslation2('dataGrid', 'LoadingMessage');
		$this->deleteMsg = getBadgerTranslation2('dataGrid', 'deleteMsg');
		$this->rowCounterName = getBadgerTranslation2('dataGrid', 'rowCounterName');
		$this->noRowSelectedMsg = getBadgerTranslation2('dataGrid', 'NoRowSelectedMsg');
		
		if($print){
			$tpl->addCss("Widgets/dataGrid/dataGridPrint.css", "print");
		} else {
			$tpl->addCss("Widgets/dataGrid/dataGrid.css", "screen");
		}
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
							<td><span id="dgCount">0</span> '.$this->rowCounterName.'&nbsp;&nbsp;<span id="dgMessage"></span></td>
						</tr>
					</table>
					</div></form>';
		return $output;		
	}
	
	/**
	 * function initDataGridJS ()
	 */
	public function initDataGridJS() {
		$this->tpl->addJavaScript("js/dataGrid.0.9.js");
		$this->tpl->addOnLoadEvent('dgHeaderName = new Array("'.implode('","',$this->headerName).'");');
		$this->tpl->addOnLoadEvent('dgColumnOrder = new Array("'.implode('","',$this->columnOrder).'");');
		$this->tpl->addOnLoadEvent('dgHeaderSize = new Array('.implode(',',$this->headerSize).');');
		$this->tpl->addOnLoadEvent('dgCellAlign = new Array("'.implode('","',$this->cellAlign).'");');
		$this->tpl->addOnLoadEvent('dgNoRowSelectedMsg = "'. $this->noRowSelectedMsg .'";');
		$this->tpl->addOnLoadEvent('dgDeleteMsg = "'. $this->deleteMsg .'";');
		$this->tpl->addOnLoadEvent('dgDeleteRefreshType = "'. $this->deleteRefreshType .'";');
		$this->tpl->addOnLoadEvent('dgDeleteAction = "'. $this->deleteAction .'";');
		$this->tpl->addOnLoadEvent('dgEditAction = "'. $this->editAction .'";');
		$this->tpl->addOnLoadEvent('dgNewAction = "'. $this->newAction .'";');
		$this->tpl->addOnLoadEvent('dgSourceXML = "'.$this->sourceXML.'";');
		$this->tpl->addOnLoadEvent('dgTplPath = "'.BADGER_ROOT.'/tpl/'.$this->tpl->getThemeName().'/Widgets/dataGrid/";');
		$this->tpl->addOnLoadEvent('dgLoadingMessage = "'.$this->LoadingMessage.'";');
		$this->tpl->addOnLoadEvent('addNewSortOrder("'.$this->initialSort.'", "'.$this->initialSortDirection.'");');
		$this->tpl->addOnLoadEvent('loadData(dgSourceXML + serializeParameter());');
		$this->tpl->addOnLoadEvent('Behaviour.register(behaviour);');
		$this->tpl->addOnLoadEvent('Behaviour.apply();');
		$this->tpl->addOnLoadEvent('Event.observe($("dataGrid"), \'keypress\', dgKeyProcess, false);');
		
	}
}
?>
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
 * 
 * @author Sepp
 */
class DataGrid {
private $tpl;
public $headerName = array();
public $columnOrder = array();
public $headerSize = array();
public $deleteMsg;
public $deleteAction;
public $editAction;
public $newAction;
public $cellAlign;
public $rowCounterName;
public $initialSort;
public $width = "99.9%";

	public function __construct($tpl) {
		$this->tpl = $tpl;
	}
	
	public function writeDataGrid() {
		//toDo: Use templateEngine
		$output = '<form id="dgForm"><div id="dataGrid" style="width:'.$this->width.'">
					<table id="dgTableHead" cellpadding="2" cellspacing="0">
						<tr>
							<td width="20">&nbsp;</td>';
			for ($i=0; $i < count($this->headerName); $i++) {
				$output .= '<td id="dgColumn'.$this->columnOrder[$i].'" width="'.$this->headerSize[$i].'">'.
							$this->headerName[$i].'&nbsp;'.
						   '<img src="'.BADGER_ROOT.'/tpl/'.$this->tpl->getThemeName().'/Widgets/dataGrid/dropEmpty.png" id="dgImg'.$this->columnOrder[$i].'" /></td>';
			}
		$output .= '		<td></td>
						</tr>
					</table>';
					
		$output .= '<div id="dgScroll">
					<table id="dgData" cellpadding="2" cellspacing="0">
					</table>
					</div>
					<table id="dgTableFoot" cellpadding="2" cellspacing="0">
						<tr>
							<td><span id="dgCount">0</span> '.$this->rowCounterName.'</td>
						</tr>
					</table>
					</div></form>';
		return $output;		
	}
	
	public function initDataGridJS() {
		$this->tpl->addJavaScript("js/dataGrid.js");
		$this->tpl->addOnLoadEvent('dgHeaderName = new Array("'.implode('","',$this->headerName).'");');
		$this->tpl->addOnLoadEvent('dgColumnOrder = new Array("'.implode('","',$this->columnOrder).'");');
		$this->tpl->addOnLoadEvent('dgHeaderSize = new Array('.implode(',',$this->headerSize).');');
		$this->tpl->addOnLoadEvent('dgCellAlign = new Array("'.implode('","',$this->cellAlign).'");');
		$this->tpl->addOnLoadEvent('dgDeleteMsg = "'. $this->deleteMsg .'";');
		$this->tpl->addOnLoadEvent('dgDeleteAction = "'. $this->deleteAction .'";');
		$this->tpl->addOnLoadEvent('dgEditAction = "'. $this->editAction .'";');
		$this->tpl->addOnLoadEvent('dgNewAction = "'. $this->newAction .'";');
		$this->tpl->addOnLoadEvent('dgSourceXML = "'.$this->sourceXML.'";');
		$this->tpl->addOnLoadEvent('dgTplPath = "'.BADGER_ROOT.'/tpl/'.$this->tpl->getThemeName().'/Widgets/dataGrid/";');
		$this->tpl->addOnLoadEvent('addNewSortOrder("'.$this->initialSort.'");');
		$this->tpl->addOnLoadEvent('loadData(dgSourceXML + serializeParameter());');
		$this->tpl->addOnLoadEvent('Behaviour.register(behaviour);');
		$this->tpl->addOnLoadEvent('Behaviour.apply();');
		
	}
}
?>
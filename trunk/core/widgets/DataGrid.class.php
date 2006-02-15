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
private $LoadingMessage;
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
public $width;
public $height;

	public function __construct($tpl) {
		global $print;
		$this->tpl = $tpl;
		$this->LoadingMessage = getBadgerTranslation2('dataGrid', 'LoadingMessage');
		if($print){
			$tpl->addCss("Widgets/dataGrid/dataGridPrint.css");
		} else {
			$tpl->addCss("Widgets/dataGrid/dataGrid.css");
		}
	}
	
	public function writeDataGrid() {
		//toDo: Use templateEngine
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
	
	public function initDataGridJS() {
		$this->tpl->addJavaScript("js/dataGrid.0.9.js");
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
		$this->tpl->addOnLoadEvent('dgLoadingMessage = "'.$this->LoadingMessage.'";');
		$this->tpl->addOnLoadEvent('addNewSortOrder("'.$this->initialSort.'");');
		$this->tpl->addOnLoadEvent('loadData(dgSourceXML + serializeParameter());');
		$this->tpl->addOnLoadEvent('Behaviour.register(behaviour);');
		$this->tpl->addOnLoadEvent('Behaviour.apply();');
		
	}
}
?>
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
public $headerSize = array();
public $deleteMsg;
public $deleteAction;
public $editAction;

	public function __construct($tpl) {
		$this->tpl = $tpl;
	}
	
	public function writeHeader() {
		//toDo: Use templateEngine
		
		$output = '<div id="dataGrid">
					<table id="dgTableHead" cellpadding="2" cellspacing="0">
						<tr>
							<td width="20">&nbsp;</td>';
			for ($i=0; $i < count($this->headerName); $i++) {
				$output .= '<td width="'.$this->headerSize[$i].'">'.$this->headerName[$i].'</td>';
			}
		$output .= '		<td></td>
						</tr>
					</table>';
					
		$output .= '<div id="dgScroll">
					<table id="dgData" cellpadding="2" cellspacing="0" rules="row">
						<tbody>
					
						</tbody>
					</table>
					</div>
					<table id="dgTableFoot" cellpadding="2" cellspacing="0">
						<tr>
							<td>xx Datensätze</td>
						</tr>
					</table>
					</div>';
		return $output;		
	}
	
	public function initDataGridJS() {
		$this->tpl->addJavaScript("js/dataGrid.js");
		$this->tpl->addOnLoadEvent('dgHeaderName = new Array("'.implode('","',$this->headerName).'");');
		$this->tpl->addOnLoadEvent('dgHeaderSize = new Array('.implode(',',$this->headerSize).');');
		$this->tpl->addOnLoadEvent('dgDeleteMsg = "'. $this->deleteMsg .'";');
		$this->tpl->addOnLoadEvent('dgDeleteAction = "'. $this->deleteAction .'";');
		$this->tpl->addOnLoadEvent('dgEditAction = "'. $this->editAction .'";');
		$this->tpl->addOnLoadEvent('dgInit();');
		$this->tpl->addOnLoadEvent('Behaviour.register(behaviour);');
		$this->tpl->addOnLoadEvent('Behaviour.apply();');
		
	}
}
?>
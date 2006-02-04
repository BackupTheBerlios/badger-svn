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
define("BADGER_ROOT", "../../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once(BADGER_ROOT . "/core/widgets/DataGrid.class.php");

$tpl->addCss("Widgets/dataGrid.css");
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
$dataGrid->headerName = array("currency","title","balance"); 
$dataGrid->headerSize = array(150,200,180);
$dataGrid->cellAlign = array("left","left","right");
$dataGrid->deleteMsg = "Wollen sie die Datensätze wirklich löschen?"; //TODO Translation
$dataGrid->deleteAction = "xyp.php";
$dataGrid->editAction = "xyp.php?id=";
$dataGrid->initDataGridJS();
echo $tpl->getHeader("DataGrid");

echo $dataGrid->writeHeader();
?>
<a href="javascript:nd();" onclick="dgInit('../../../core/XML/getDataGridXML.php?q=AccountManager');">reload</a>
<a href="javascript:nd();" onclick="emptyDataGrid();">clear</a>
<script>

function emptyDataGrid() {
	dgDataGrid = document.getElementById("dgData");
	dgRows = dgDataGrid.getElementsByTagName("tr");

	toBeDeleted = new Array();
	for (id=0; id<dgRows.length; id++) {
		toBeDeleted[id] = dgRows[id].id;
	}
	for (id=0; id<toBeDeleted.length; id++) {
		dgDataGrid.removeChild(document.getElementById(toBeDeleted[id]));
	}
}

function dgInit(url) {
	//emptyDataGrid();
	xmlDoc = loadData(url);
	xmlRows = xmlDoc.getElementsByTagName("row");
	
	dgData = document.getElementById("dgData"); //.getElementsByTagName("tbody")[0];
	dgRows = dgData.getElementsByTagName("tr");
	if (dgRows.length>0) {emptyDataGrid()};
	
	for (j=0; j<xmlRows.length; j++) {
		cells = xmlRows[j].getElementsByTagName("cell");
		rowID = cells[0].textContent;
		
		newRow = document.createElement("tr");
		newRow.className = "dgRow";
		newRow.id=rowID;
		
		//checkbox
		checkTD = document.createElement("td");
		checkTD.width="20";
		checkBox = document.createElement("input");
		checkBox.id = "check" + rowID;
		checkBox.name = "check" + rowID;
		checkBox.type = "checkbox";
		checkTD.appendChild(checkBox);
		newRow.appendChild(checkTD);
		
		//values
		for (i=0; i<dgHeaderSize.length; i++) {
			cell = document.createElement("td");
			cell.width = dgHeaderSize[i];
			cell.align = dgCellAlign[i];
			cell.innerHTML = cells[i+1].textContent;			
			newRow.appendChild(cell);
		} 
		
		//empty cell
		lastTD = document.createElement("td");
		lastTD.innerHTML = "&nbsp;";
		newRow.appendChild(lastTD);
		
		dgData.appendChild(newRow);
		Behaviour.apply();
	}
}



</script>


</body>
</html>
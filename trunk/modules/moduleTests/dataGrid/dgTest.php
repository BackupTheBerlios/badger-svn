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
$dataGrid->headerName = array("accountId","currency","title","balance"); 
$dataGrid->headerSize = array(100,150,200,180);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
$dataGrid->initDataGridJS();
echo $tpl->getHeader("DataGrid");

echo $dataGrid->writeHeader();
?>
<div id="dgScroll">
<table id="dgData" cellpadding="2" cellspacing="0" rules="row">
	<tbody>
		<tr class="dgRow" id="0">
			<td width="20"><input type="checkbox" name="1" value="ON" id="check1"/></td>
			<td width="100">Name1</td>
			<td width="150">Vorname</td>
			<td width="200">Alter</td>
			<td width="180">Kontostand</td>
			<td>&nbsp;</td>
		</tr>
	</tbody>
</table>
</div>
<table id="dgTableFoot" cellpadding="2" cellspacing="0">
	<tr>
		<td>xx Datensätze</td>
	</tr>
</table>
</div>
<script>
window.setTimeout('dgInit()', 1000);

function dgInit() {
	//xmlDoc = loadData('<?php echo $dataGrid->sourceXML?>');
	//alert(xmlDoc);
	
	rowID = 10;
	dgData = document.getElementById("dgData");
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
		
		cell.innerHTML = dgHeaderSize[i];
		
		newRow.appendChild(cell);
	} 
	
	dgData.appendChild(newRow);

}
</script>


</body>
</html>
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
define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once(BADGER_ROOT . "/core/widgets/DataGrid.class.php");

$widgets = new WidgetEngine($tpl);
$tpl->addCss("Widgets/dataGrid.css");
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
$dataGrid->initialSort = "&ok0=title&od0=a";
$dataGrid->headerName = array("Title","Balance","Currency");
$dataGrid->columnOrder = array("title","balance","currency");  
$dataGrid->headerSize = array(200,150,30);
$dataGrid->cellAlign = array("left","right","left");
$dataGrid->deleteMsg = "Wollen sie die Datensätze wirklich löschen?"; //TODO Translation
$dataGrid->rowCounterName = "Datensätze";
$dataGrid->deleteAction = "deleteXYZ.php";
$dataGrid->editAction = "editXYZ.php?id=";
$dataGrid->newAction = "newXYZ.php";
$dataGrid->initDataGridJS();
echo $tpl->getHeader("DataGrid");

echo $widgets->createButton("btnNew", "Neu", "dgNew()", "Widgets/table_add.gif");
echo $widgets->createButton("btnDelete", "Löschen", "dgDelete()", "Widgets/table_delete.gif");
		
echo $dataGrid->writeDataGrid();
?>
</body>
</html>
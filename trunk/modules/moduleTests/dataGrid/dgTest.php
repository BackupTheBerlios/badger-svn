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

$widgets = new WidgetEngine($tpl);
$tpl->addCss("Widgets/dataGrid.css");
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
$dataGrid->headerName = array("currency","title","balance"); 
$dataGrid->headerSize = array(150,200,180);
$dataGrid->cellAlign = array("left","left","right");
$dataGrid->deleteMsg = "Wollen sie die Datens�tze wirklich l�schen?"; //TODO Translation
$dataGrid->rowCounterName = "Datens�tze";
$dataGrid->deleteAction = "deleteXYZ.php";
$dataGrid->editAction = "editXYZ.php?id=";
$dataGrid->newAction = "newXYZ.php";
$dataGrid->initDataGridJS();
echo $tpl->getHeader("DataGrid");

echo $widgets->createButton("btnNew", "Neu", "dgNew()", "Widgets/table_add.gif");
echo $widgets->createButton("btnDelete", "L�schen", "dgDelete()", "Widgets/table_delete.gif");
		
echo $dataGrid->writeHeader();
?>
<a href="javascript:void(0)" onclick="loadData('../../../core/XML/getDataGridXML.php?q=AccountManager');">normal</a><br />
<a href="javascript:void(0)" onclick="loadData('../../../core/XML/getDataGridXML.php?q=AccountManager&ok0=title&od0=a');">title aufsteigend</a> <a href="javascript:void(0)" onclick="loadData('../../../core/XML/getDataGridXML.php?q=AccountManager&ok0=title&od0=d');">title absteigend</a><br />
<a href="javascript:void(0)" onclick="loadData('../../../core/XML/getDataGridXML.php?q=AccountManager&ok0=balance&od0=a');">balance aufsteigend</a> <a href="javascript:void(0)" onclick="loadData('../../../core/XML/getDataGridXML.php?q=AccountManager&ok0=balance&od0=d');">balance absteigend</a><br />
<a href="javascript:void(0)" onclick="emptyDataGrid();">clear dataGrid</a>
</body>
</html>
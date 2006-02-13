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

$pageTitle = getBadgerTranslation2('accountCategory', 'pageTitle');

$widgets = new WidgetEngine($tpl);
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=CategoryManager";
$dataGrid->headerName = array(getBadgerTranslation2('accountCategory', 'colparentTitle'),getBadgerTranslation2('accountCategory', 'colTitle'),getBadgerTranslation2('accountCategory', 'colDescription'),getBadgerTranslation2('accountCategory', 'colOutsideCapital'));
$dataGrid->columnOrder = array("parentTitle","title","description","outsideCapital");
//$dataGrid->initialSort = "title";
$dataGrid->initialSort = "parentTitle";
$dataGrid->headerSize = array(200,200,300,120);
$dataGrid->cellAlign = array("left","left","left","right");
$dataGrid->height = "400px";
$dataGrid->deleteMsg = getBadgerTranslation2('dataGrid', 'deleteMsg');
$dataGrid->rowCounterName = getBadgerTranslation2('dataGrid', 'rowCounterName');
$dataGrid->deleteAction = "CategoryManager.php?action=delete&ID=";
$dataGrid->editAction = "CategoryManager.php?action=edit&ID=";
$dataGrid->newAction = "CategoryManager.php?action=new";
$dataGrid->initDataGridJS();

$widgets->addNavigationHead();
echo $tpl->getHeader($pageTitle);
echo $widgets->getNavigationBody(); 

echo $widgets->createButton("btnNew", getBadgerTranslation2('dataGrid', 'new'), "dgNew()", "Widgets/table_add.gif");
echo $widgets->createButton("btnDelete", getBadgerTranslation2('dataGrid', 'delete'), "dgDelete()", "Widgets/table_delete.gif");
		
echo $dataGrid->writeDataGrid();
?>
</body>
</html>
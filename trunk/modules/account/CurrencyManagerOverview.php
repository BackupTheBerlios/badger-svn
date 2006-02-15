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

$pageTitle = getBadgerTranslation2('accountCurrency', 'pageTitleOverview');

$widgets = new WidgetEngine($tpl);
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=CurrencyManager";
$dataGrid->headerName = array(getBadgerTranslation2('accountCurrency', 'colSymbol'),getBadgerTranslation2('accountCurrency', 'colLongName'));
$dataGrid->columnOrder = array("symbol","longName");
$dataGrid->initialSort = "symbol";  
$dataGrid->headerSize = array(80,120);
$dataGrid->cellAlign = array("left","left");
$dataGrid->width = "220px";
$dataGrid->height = "200px";
$dataGrid->deleteMsg = getBadgerTranslation2('dataGrid', 'deleteMsg');
$dataGrid->rowCounterName = getBadgerTranslation2('dataGrid', 'rowCounterName');
$dataGrid->deleteAction = "CurrencyManager.php?action=delete&ID=";
$dataGrid->editAction = "CurrencyManager.php?action=edit&ID=";
$dataGrid->newAction = "CurrencyManager.php?action=new";
$dataGrid->initDataGridJS();

$widgets->addNavigationHead();
echo $tpl->getHeader($pageTitle);

echo "<h1>$pageTitle</h1>";

echo $widgets->createButton("btnNew", getBadgerTranslation2('dataGrid', 'new'), "dgNew()", "Widgets/table_add.gif");
echo $widgets->createButton("btnDelete", getBadgerTranslation2('dataGrid', 'delete'), "dgDelete()", "Widgets/table_delete.gif");
		
echo $dataGrid->writeDataGrid();

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
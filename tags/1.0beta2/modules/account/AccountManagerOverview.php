<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
**/
define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once(BADGER_ROOT . "/core/widgets/DataGrid.class.php");
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

handleOldFinishedTransactions(new AccountManager($badgerDb));

$pageTitle = getBadgerTranslation2('accountAccount', 'pageTitleOverview');

$widgets = new WidgetEngine($tpl);
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
$dataGrid->headerName = array(getBadgerTranslation2('accountAccount', 'colTitle'), getBadgerTranslation2('accountAccount', 'colBalance'),getBadgerTranslation2('accountAccount', 'colCurrency'));
$dataGrid->columnOrder = array("title","balance","currency"); 
$dataGrid->deleteMsg = getBadgerTranslation2('accountAccount', 'deleteMsg');
$dataGrid->deleteRefreshType = "refreshPage";
$dataGrid->initialSort = "title";
$dataGrid->initialSortDirection = "asc";
$dataGrid->headerSize = array(200,150,100);
$dataGrid->cellAlign = array("left","right","left");
$dataGrid->height = "350px";
$dataGrid->deleteAction = "AccountManager.php?action=delete&ID=";
$dataGrid->editAction = "AccountManager.php?action=edit&ID=";
$dataGrid->newAction = "AccountManager.php?action=new";
$dataGrid->initDataGridJS();

$widgets->addNavigationHead();
echo $tpl->getHeader($pageTitle);

echo "<h1>$pageTitle</h1>";

echo $widgets->createButton("btnNew", getBadgerTranslation2('dataGrid', 'new'), "dgNew()", "Widgets/table_add.gif");
echo ' ';
echo $widgets->createButton("btnEdit", getBadgerTranslation2('dataGrid', 'edit'), "dgEdit()", "Widgets/table_edit.gif");
echo ' ';
echo $widgets->createButton("btnDelete", getBadgerTranslation2('dataGrid', 'delete'), "dgDelete()", "Widgets/table_delete.gif");
		
echo $dataGrid->writeDataGrid();

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
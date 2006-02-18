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
require_once(BADGER_ROOT . '/modules/account/AccountManager.class.php');

$pageTitle = getBadgerTranslation2 ('accountOverview','pageTitle');

$widgets = new WidgetEngine($tpl);
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
if (isset($_GET['accountID'])) {
	$accountID=$_GET['accountID'];
} else {
	throw new badgerException('accountOverview', 'noAccountID', '');
}
$am = new AccountManager($badgerDb);
$account = $am->getAccountById($accountID);
$pageTitle .= ": ".$account->getTitle();

$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=Account&qp=$accountID";
$dataGrid->headerName = array(getBadgerTranslation2('accountOverview', 'colValutaDate'),getBadgerTranslation2('accountOverview', 'colTitle'), getBadgerTranslation2('accountOverview', 'colType'),getBadgerTranslation2('accountOverview', 'colAmount'),getBadgerTranslation2('accountOverview', 'colSum'),getBadgerTranslation2('accountOverview', 'colCategoryTitle'));
$dataGrid->columnOrder = array("valutaDate","title","type","amount","sum","categoryTitle");  
$dataGrid->initialSort = "valutaDate";
$dataGrid->initialSortDirection = "asc";
$dataGrid->height = "350px";
$dataGrid->headerSize = array(120,210,39,80,120,200);
$dataGrid->cellAlign = array("left","left","center","right","right","left");
$dataGrid->deleteAction = "Transaction.php?action=delete&accountID=$accountID&ID=";
$dataGrid->editAction = "Transaction.php?action=edit&accountID=$accountID&ID=";
$dataGrid->newAction = "Transaction.php?action=new&accountID=$accountID";
$dataGrid->initDataGridJS();

$widgets->addNavigationHead();
echo $tpl->getHeader($pageTitle);

echo "<h1>$pageTitle</h1>";

echo $widgets->createButton("btnNewFinished", getBadgerTranslation2('accountTransaction', 'newFinishedTrans'), "dgNew('type=finished')", "Account/planned_transaction_new.gif");
echo ' ';
echo $widgets->createButton("btnNewPlanned", getBadgerTranslation2('accountTransaction', 'newPlannedTrans'), "dgNew('type=planned')", "Account/finished_transaction_new.gif");
echo ' ';
echo $widgets->createButton("btnDelete", getBadgerTranslation2('dataGrid', 'delete'), "dgDelete()", "Widgets/table_delete.gif");

echo $dataGrid->writeDataGrid();

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
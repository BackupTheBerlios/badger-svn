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
require_once(BADGER_ROOT . '/modules/account/AccountManager.class.php');
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

if (isset($_GET['accountID'])) {
	$accountID=$_GET['accountID'];
} else {
	throw new badgerException('accountOverview', 'noAccountID', '');
}

$am = new AccountManager($badgerDb);
$account = $am->getAccountById($accountID);

transferFormerFinishedTransactions($account);

$pageTitle = getBadgerTranslation2 ('accountOverview','pageTitle');
$pageTitle .= ": ".$account->getTitle();

$widgets = new WidgetEngine($tpl);
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");
$widgets->addToolTipJS();
$widgets->addCalendarJS();

$dataGrid = new DataGrid($tpl);
$dataGrid->UniqueId = "Account$accountID";
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=Account&qp=$accountID";
$dataGrid->headerName = array(
	getBadgerTranslation2('accountOverview', 'colValutaDate'),
	getBadgerTranslation2('accountOverview', 'colTitle'), 
	getBadgerTranslation2('accountOverview', 'colType'),
	getBadgerTranslation2('accountOverview', 'colAmount'),
	getBadgerTranslation2('accountOverview', 'colSum'),
	getBadgerTranslation2('accountOverview', 'colCategoryTitle'));
$dataGrid->columnOrder = array("valutaDate","title","type","amount","sum","categoryTitle");  
$dataGrid->height = "350px";
$dataGrid->headerSize = array(90,350,39,80,120,200);
$dataGrid->cellAlign = array("left","left","center","right","right","left");
$dataGrid->deleteRefreshType = "refreshDataGrid";
$dataGrid->deleteAction = "Transaction.php?action=delete&accountID=$accountID&ID=";
$dataGrid->editAction = "Transaction.php?action=edit&accountID=$accountID&ID=";
$dataGrid->newAction = "Transaction.php?action=new&accountID=$accountID";
$dataGrid->initDataGridJS();

$widgets->addNavigationHead();
echo $tpl->getHeader($pageTitle);

// DataGrid Filter
$legendFilter = getBadgerTranslation2('dataGrid', 'filterLegend');

$datagGridFilterArray = $dataGrid->getFilterSelectArray();
	
$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountTransaction', 'title'), false);
$titleField = $widgets->createField("title", 30, "", "", false, "text", "");
$titleFilterOperator = $widgets->createSelectField("titleFilter", $datagGridFilterArray, "", "", false, "style='width: 95px;'");
	
$valutaDateLabel = $widgets->createLabel("valutaDate", getBadgerTranslation2('accountTransaction', 'valutaDate'), false);
$valutaDateField = $widgets->addDateField("valutaDate", "");
$valutaDateFilterOperator = $widgets->createSelectField("valutaDateFilter", $datagGridFilterArray, "", "", false, "style='width: 95px;'");
	
$amountLabel = $widgets->createLabel("amount", getBadgerTranslation2('accountTransaction', 'amount'), false);
$amountField = $widgets->createField("amount", 14, "", "", false, "text", "");
$amountFilterOperator = $widgets->createSelectField("amountFilter", $datagGridFilterArray, "", "", false, "style='width: 95px;'");	

$categoryLabel = $widgets->createLabel("categoryId", getBadgerTranslation2('accountTransaction', 'category'), false, "");
$categoryField = $widgets->createSelectField("categoryId", getCategorySelectArray(), "", "", false, "style='width: 210px;'");

$btnFilterOkay = $widgets->createButton("btnFilterOkay", getBadgerTranslation2('dataGrid', 'setFilter'), "dgSetFilterFields(['title','amount','valutaDate','categoryId'])", "Widgets/dataGrid/filter.gif");
$btnFilterReset = $widgets->createButton("btnFilterReset", getBadgerTranslation2('dataGrid', 'resetFilter'), "dgResetFilter(['title','amount','valutaDate','categoryId'])", "Widgets/cancel.gif");

// DataGrid 
$btnNewFinished = $widgets->createButton("btnNewFinished", getBadgerTranslation2('accountTransaction', 'newFinishedTrans'), "dgNew('type=finished')", "Account/finished_transaction_new.gif");
$btnNewPlanned = $widgets->createButton("btnNewPlanned", getBadgerTranslation2('accountTransaction', 'newPlannedTrans'), "dgNew('type=planned')", "Account/planned_transaction_new.gif");
$btnEdit = $widgets->createButton("btnEdit", getBadgerTranslation2('dataGrid', 'edit'), "dgEdit()", "Widgets/table_edit.gif");
$btnDelete = $widgets->createButton("btnDelete", getBadgerTranslation2('dataGrid', 'delete'), "dgDelete()", "Widgets/table_delete.gif");
$btnShowPlannedTransactions = $widgets->createButton("btnShowPlannedTransactions", getBadgerTranslation2('accountOverview', 'showPlannedTrans'),  "location.href = location.href.replace(/AccountOverview\.php/, 'AccountOverviewPlanned.php');", "Account/planned_transaction.png");

$dgHtml = $dataGrid->writeDataGrid();

$legend = getBadgerTranslation2('dataGrid', 'legend');

$finishedTransactionText = getBadgerTranslation2('Account', 'finishedTransaction');
$finishedTransactionImage = $widgets->addImage('Account/finished_transaction.png', 'title="' . $finishedTransactionText . '"');

$plannedTransactionText = getBadgerTranslation2('Account', 'plannedTransaction');
$plannedTransactionImage = $widgets->addImage('Account/planned_transaction.png', 'title="' . $plannedTransactionText . '"');

eval('echo "' . $tpl->getTemplate('Account/AccountOverview') . '";');

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
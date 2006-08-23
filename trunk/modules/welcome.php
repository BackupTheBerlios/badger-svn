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
define("BADGER_ROOT", "..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once(BADGER_ROOT . "/core/widgets/DataGrid.class.php");
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

$widgets = new WidgetEngine($tpl); 
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
$dataGrid->headerName = array(getBadgerTranslation2('accountAccount', 'colTitle'), getBadgerTranslation2('accountAccount', 'colBalance'),getBadgerTranslation2('accountAccount', 'colCurrency'));
$dataGrid->columnOrder = array("title","balance","currency"); 
//$dataGrid->deleteMsg = getBadgerTranslation2('accountAccount', 'deleteMsg');
//$dataGrid->deleteRefreshType = "refreshPage";
$dataGrid->initialSort = "title";
$dataGrid->initialSortDirection = "asc";
$dataGrid->headerSize = array(200,150,100);
$dataGrid->cellAlign = array("left","right","left");
$dataGrid->height = "130px";
$dataGrid->width = "520px";
$dataGrid->editAction = "account/AccountOverview.php?accountID=";
$dataGrid->initDataGridJS();


//$accountOverview = getBadgerTranslation2 ('accountAccount', 'pageTitleOverview');
$categoryOverview = getBadgerTranslation2 ('accountCategory', 'pageTitleOverview');
$statistics = getBadgerTranslation2 ('statistics','pageTitle');
$backup = getBadgerTranslation2 ('importExport', 'askTitle');
$userPrefrences = getBadgerTranslation2 ('UserSettingsAdmin', 'title');


$widgets->addNavigationHead();
echo $tpl->getHeader("Badger");

$accountOverviewHeader = getBadgerTranslation2('accountAccount', 'pageTitleOverview');
$accountOverviewGrid = $dataGrid->writeDataGrid();

$btnOpen =  $widgets->createButton("btnNew", getBadgerTranslation2('dataGrid', 'open'), "dgEdit()", "Widgets/table_go.gif");

eval("echo \"".$tpl->getTemplate("badgerOverview")."\";");
eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");

require_once(BADGER_ROOT . "/includes/fileFooter.php");

?>

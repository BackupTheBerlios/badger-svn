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
define ('BADGER_ROOT', '../..');

require_once BADGER_ROOT . '/includes/fileHeaderFrontEnd.inc.php';
require_once BADGER_ROOT . '/modules/account/accountCommon.php';
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/core/widgets/DataGrid.class.php';

$FILTER_ID_MARKER = '__FILTER_ID__';

/*
if (isset($_REQUEST['mode'])) {
	$mode = getGPC($_REQUEST, 'mode');
} else {
	$mode = 'selectPage';
}
*/

handleOldFinishedTransactions(new AccountManager($badgerDb));

$widgets = new WidgetEngine($tpl); 

$widgets->addCalendarJS();
$widgets->addToolTipJS();
$widgets->addTwistieSectionJS();
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");
$tpl->addJavaScript("js/statistics2.js");

$dataGrid = new DataGrid($tpl);
$dataGrid->UniqueId = "Statistics2Test";
$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=Account&qp=1";
$dataGrid->headerName = array(
	getBadgerTranslation2('accountOverview', 'colValutaDate'),
	getBadgerTranslation2('accountOverview', 'colTitle'), 
	getBadgerTranslation2('accountOverview', 'colType'),
	getBadgerTranslation2('accountOverview', 'colAmount'),
	getBadgerTranslation2('accountOverview', 'colSum'),
	getBadgerTranslation2('accountOverview', 'colCategoryTitle'));
$dataGrid->columnOrder = array("valutaDate","title","type","amount","sum","concatCategoryTitle");  
$dataGrid->height = "350px";
$dataGrid->headerSize = array(90,350,39,80,120,200);
$dataGrid->cellAlign = array("left","left","center","right","right","left");
$dataGrid->deleteRefreshType = "refreshDataGrid";
$dataGrid->deleteAction = "Transaction.php?action=delete&accountID=1&ID=";
$dataGrid->editAction = "Transaction.php?action=edit&accountID=1&ID=";
$dataGrid->newAction = "Transaction.php?action=new&accountID=1";
$dataGrid->initDataGridJS();

$widgets->addNavigationHead();

echo $tpl->getHeader('TRANSLATE: Statistics2');
	
$widgets->addToolTipLayer();

$datagGridFilterArray = DataGrid::getFilterSelectArray();
$datagGridDateFilterArray = DataGrid::getDateFilterSelectArray();

$filters['unselected'] = '';
$filters['title'] =
	'Title is ' 
	. $widgets->createSelectField("titleOperator$FILTER_ID_MARKER", $datagGridFilterArray, "", "", false, "style='width: 95px;'")
	. '&nbsp;'
	. $widgets->createField("title$FILTER_ID_MARKER", 30, "", "", false, "text", "")
	;
$filters['description'] = 
	'Description is '
	. $widgets->createSelectField("descriptionOperator$FILTER_ID_MARKER", $datagGridFilterArray, "", "", false, "style='width: 95px;'")
	. '&nbsp;'
	. $widgets->createField("description$FILTER_ID_MARKER", 30, "", "", false, "text", "")
	;
$filters['valutaDate'] =
	'Valuta date is '
	. $widgets->createSelectField("valutaDateOperator$FILTER_ID_MARKER", $datagGridDateFilterArray, "", "", false, "style='width: 95px;'")
	. '&nbsp;'
	.$widgets->addDateField("valutaDate$FILTER_ID_MARKER", "")
	;
$filters['valutaDateBetween'] =
	'Valuta date is between '
	. $widgets->addDateField("valutaDateStart$FILTER_ID_MARKER", "")
	. ' and '
	. $widgets->addDateField("valutaDateEnd$FILTER_ID_MARKER", "")
	. '(both inclusive)'
	;
$filters['valutaDateAgo'] = 
	'Valuta date at most '
	. $widgets->createField("valutaDateAgo$FILTER_ID_MARKER", 3, "", "", false, "integer", "")
	. ' days ago'
	;
$filters['amount'] =
	'Amount is '
	. $widgets->createSelectField("amountOperator$FILTER_ID_MARKER", $datagGridFilterArray, "", "", false, "style='width: 95px;'")
	. '&nbsp'
	. $widgets->createField("amount$FILTER_ID_MARKER", 3, "", "", false, "integer", "")
	;
$filters['outsideCapital'] =
	'Source is '
	. $widgets->createField("outsideCapital$FILTER_ID_MARKER", null, 'outside', '', false, 'radio')
	. $widgets->createLabel("outsideCapital$FILTER_ID_MARKER", 'outside capital')
	. '&nbsp;'
	. $widgets->createField("outsideCapital$FILTER_ID_MARKER", null, 'inside', '', false, 'radio')
	. $widgets->createLabel("outsideCapital$FILTER_ID_MARKER", 'inside capital')
	;
$filters['transactionPartner'] =
	'Transaction partner is '
	. $widgets->createSelectField("transactionPartnerOperator$FILTER_ID_MARKER", $datagGridFilterArray, "", "", false, "style='width: 95px;'")
	. '&nbsp;'
	. $widgets->createField("transactionPartner$FILTER_ID_MARKER", 30, "", "", false, "text", "")
	;
$filters['category'] =
	'Category is '
	. $widgets->createSelectField("categoryId$FILTER_ID_MARKER", getCategorySelectArray(), "", "", false, "style='width: 210px;'")
	;

$availableFilters = array (
	'unselected' => 'Please choose a filter',
	'title' => 'Title',
	'description' => 'Description',
	'valutaDate' => 'Valuta date',
	'valutaDateBetween' => 'Valuta date between',
	'valutaDateAgo' => 'Valuta date last days',
	'amount' => 'Amount',
	'outsideCapital' => 'Outside capital',
	'transactionPartner' => 'Transaction partner',
	'category' => 'Category',
	'delete' => 'Delete Filter'
);

echo '<form id="mainform" name="mainform">';

echo $widgets->createField('dateFormat', null, $us->getProperty('badgerDateFormat'), null, false, 'hidden');

$content = "<div style=\"float: left;\">";
$content .= $widgets->createSelectField("filterSelect$FILTER_ID_MARKER", $availableFilters, "", "", false, "onchange=\"setFilterContent('$FILTER_ID_MARKER');\"");
$content .= "</div><div id=\"filterContent$FILTER_ID_MARKER\"></div>";
echo "<div id='filterLineEmpty' style='display:none;'>$content</div>";

foreach ($filters as $currentName => $currentFilter) {
	echo "<div id='{$currentName}Empty' style='display:none;'>$currentFilter</div>";
}

$filterBox = '<div><div>Filters';
$filterBox .= $widgets->createButton('addFilter', 'Add Filter', 'addFilterX();');
$filterBox .= '</div>';
$filterBox .= '<div id="filterContent" style="overflow: auto; height: 10em; border: 1px solid blue;">';
$filterBox .= '</div>';
$filterBox .= '<div>';
$filterBox .= $widgets->createButton('applyFilter', 'Apply Filter', 'applyFilterX();');
$filterBox .= '</div>';
$filterBox .= '</div>';

echo $widgets->addTwistieSection('Input', $filterBox, null, true);
echo '</form>';

echo $widgets->addTwistieSection('Output', $dataGrid->writeDataGrid(), null, false);

eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
?>
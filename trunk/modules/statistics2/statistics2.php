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

$widgets = new WidgetEngine($tpl); 

$widgets->addCalendarJS();
$widgets->addToolTipJS();
$widgets->addTwistieSectionJS();
$tpl->addJavaScript("js/behaviour.js");
$tpl->addJavaScript("js/prototype.js");
$tpl->addJavaScript("js/statistics2.js");

$dgAccounts = new DataGrid($tpl, 'Statistics2Accounts');
$dgAccounts->sourceXML = BADGER_ROOT . '/core/XML/getDataGridXML.php?q=AccountManager';
$dgAccounts->headerName = array(
	getBadgerTranslation2('statistics','accColTitle'), 
	getBadgerTranslation2('statistics','accColBalance'), 
	getBadgerTranslation2('statistics','accColCurrency'));
$dgAccounts->columnOrder = array('title', 'balance', 'currency');  
$dgAccounts->headerSize = array(160, 100, 75);
$dgAccounts->cellAlign = array('left', 'right', 'left');
$dgAccounts->width = '30em';
$dgAccounts->height = '7em';
$dgAccounts->initDataGridJS();


$dgResult = new DataGrid($tpl, 'Statistics2Result');
$dgResult->sourceXML = BADGER_ROOT . '/core/XML/getDataGridXML.php?q=MultipleAccounts&qp=1';
$dgResult->headerName = array(
	'Account',
	getBadgerTranslation2('accountOverview', 'colValutaDate'),
	getBadgerTranslation2('accountOverview', 'colTitle'), 
	getBadgerTranslation2('accountOverview', 'colAmount'),
	getBadgerTranslation2('accountOverview', 'colCategoryTitle'));
$dgResult->columnOrder = array('accountTitle', 'valutaDate', 'title', 'amount', 'concatCategoryTitle');  
$dgResult->height = "350px";
$dgResult->headerSize = array(200, 90,350,80,200);
$dgResult->cellAlign = array('left', 'left', 'left', 'right', 'left');
$dgResult->deleteRefreshType = 'refreshDataGrid';
$dgResult->initDataGridJS();

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
	. $widgets->createField("outsideCapital$FILTER_ID_MARKER", null, '1', '', false, 'radio')
	. $widgets->createLabel("outsideCapital$FILTER_ID_MARKER", 'outside capital')
	. '&nbsp;'
	. $widgets->createField("outsideCapital$FILTER_ID_MARKER", null, '0', '', false, 'radio')
	. $widgets->createLabel("outsideCapital$FILTER_ID_MARKER", 'inside capital')
	;
$filters['transactionPartner'] =
	'Transaction partner is '
	. $widgets->createSelectField("transactionPartnerOperator$FILTER_ID_MARKER", $datagGridFilterArray, "", "", false, "style='width: 95px;'")
	. '&nbsp;'
	. $widgets->createField("transactionPartner$FILTER_ID_MARKER", 30, "", "", false, "text", "")
	;
$filters['category'] =
	'Category '
	. $widgets->createField("categoryOp$FILTER_ID_MARKER", null, 'eq', '', false, 'radio')
	. $widgets->createLabel("categoryOp$FILTER_ID_MARKER", 'is')
	. '&nbsp;'
	. $widgets->createField("categoryOp$FILTER_ID_MARKER", null, 'ne', '', false, 'radio')
	. $widgets->createLabel("categoryOp$FILTER_ID_MARKER", 'is not')
	. '&nbsp;'
	. $widgets->createSelectField("categoryId$FILTER_ID_MARKER", getCategorySelectArray(true), "", "", false, "style='width: 210px;'")
	;
$filters['exceptional'] =
	'Transaction is '
	. $widgets->createField("exceptional$FILTER_ID_MARKER", null, '1', '', false, 'radio')
	. $widgets->createLabel("exceptional$FILTER_ID_MARKER", 'exceptional')
	. '&nbsp;'
	. $widgets->createField("exceptional$FILTER_ID_MARKER", null, '0', '', false, 'radio')
	. $widgets->createLabel("exceptional$FILTER_ID_MARKER", 'not exceptional')
	;
$filters['periodical'] =
	'Transaction is '
	. $widgets->createField("periodical$FILTER_ID_MARKER", null, '1', '', false, 'radio')
	. $widgets->createLabel("periodical$FILTER_ID_MARKER", 'periodical')
	. '&nbsp;'
	. $widgets->createField("periodical$FILTER_ID_MARKER", null, '0', '', false, 'radio')
	. $widgets->createLabel("periodical$FILTER_ID_MARKER", 'not periodical')
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
	'exceptional' => 'Exceptional',
	'periodical' => 'Periodical',
	'delete' => '&lt;Delete Filter&gt;'
);

echo $widgets->createField('dateFormat', null, $us->getProperty('badgerDateFormat'), null, false, 'hidden');

$content = "<div style=\"float: left;\">";
$content .= $widgets->createSelectField("filterSelect$FILTER_ID_MARKER", $availableFilters, "", "", false, "onchange=\"setFilterContent('$FILTER_ID_MARKER');\"");
$content .= "</div><div id=\"filterContent$FILTER_ID_MARKER\"></div>";
echo "<div id='filterLineEmpty' style='display:none;'>$content</div>";

foreach ($filters as $currentName => $currentFilter) {
	echo "<div id='{$currentName}Empty' style='display:none;'>$currentFilter</div>";
}

$filterBox = '<div>'
	. '<div style="position: absolute; left: 52em; margin-top: 1.2em;">'
	. $dgAccounts->writeDataGrid()
	. '</div>'
	. '<div>Filters'
	. $widgets->createButton('addFilter', 'Add Filter', 'addFilterLineX();')
	. '</div>'
	. '<form name="mainform" id="mainform">'
	. '<div id="filterContent" style="overflow: auto; height: 10em; border: 1px solid blue; width: 50em;">'
	. '</div>'
	. '</form>'
	. '</div>';

echo $widgets->addTwistieSection('Input', $filterBox, null, true);

$ACTIVE_OS_MARKER = '__ACTIVE_OS__';

echo '<div id="outputSelections" style="display:none;">';
$outputSelectionTrend = '<div id="outputSelectionTrend" style="display: inline; vertical-align: top;">'
	. '<fieldset style="display: inline; vertical-align: top;">'
	. '<legend>Start Value</legend>'
	. '<p>'
	. $widgets->createField("outputSelectionTrendStart$ACTIVE_OS_MARKER", null, '0', '', false, 'radio', 'checked="checked"')
	. $widgets->createLabel("outputSelectionTrendStart$ACTIVE_OS_MARKER", '0 (zero)')
	. '</p><p>'
	. $widgets->createField("outputSelectionTrendStart$ACTIVE_OS_MARKER", null, 'b', '', false, 'radio')
	. $widgets->createLabel("outputSelectionTrendStart$ACTIVE_OS_MARKER", 'Balance')
	. '</p>'
	. '</fieldset>'
	. '<fieldset style="display: inline; vertical-align: top;">'
	. '<legend>Tick labels</legend>'
	. '<p>'
	. $widgets->createField("outputSelectionTrendTicks$ACTIVE_OS_MARKER", null, 's', '', false, 'radio', 'checked="checked"')
	. $widgets->createLabel("outputSelectionTrendTicks$ACTIVE_OS_MARKER", 'show')
	. '</p><p>'
	. $widgets->createField("outputSelectionTrendTicks$ACTIVE_OS_MARKER", null, 'h', '', false, 'radio')
	. $widgets->createLabel("outputSelectionTrendTicks$ACTIVE_OS_MARKER", 'hide')
	. '</p>'
	. '</fieldset>'
	. '</div>';	
echo $outputSelectionTrend; 

$outputSelectionCategory = '<div id="outputSelectionCategory">'
	. '<fieldset style="display: inline; vertical-align: top;">'
	. '<legend>Category Type</legend>'
	. '<p>'
	. $widgets->createField("outputSelectionCategoryType$ACTIVE_OS_MARKER", null, 'i', '', false, 'radio', 'checked="checked"')
	. $widgets->createLabel("outputSelectionCategoryType$ACTIVE_OS_MARKER", 'Input')
	. '</p><p>'
	. $widgets->createField("outputSelectionCategoryType$ACTIVE_OS_MARKER", null, 'o', '', false, 'radio')
	. $widgets->createLabel("outputSelectionCategoryType$ACTIVE_OS_MARKER", 'Output')
	. '</p>'
	. '</fieldset>'
	. '<fieldset style="display: inline; vertical-align: top;">'
	. '<legend>Sub-Categories</legend>'
	. '<p>'
	. $widgets->createField("outputSelectionCategorySummarize$ACTIVE_OS_MARKER", null, 't', '', false, 'radio', 'checked="checked"')
	. $widgets->createLabel("outputSelectionCategorySummarize$ACTIVE_OS_MARKER", 'Summarize sub-categories')
	. '</p><p>'
	. $widgets->createField("outputSelectionCategorySummarize$ACTIVE_OS_MARKER", null, 'f', '', false, 'radio')
	. $widgets->createLabel("outputSelectionCategorySummarize$ACTIVE_OS_MARKER", 'Don not summarize')
	. '</p>'
	. '</fieldset>'
	. '</div>';
echo $outputSelectionCategory;

$outputSelectionTimespan = '<div id="outputSelectionTimespan">'
	. '<fieldset style="display: inline; vertical-align: top;">'
	. '<legend>Type</legend>'
	. '<p>'
	. $widgets->createField("outputSelectionTimespanType$ACTIVE_OS_MARKER", null, 'w', '', false, 'radio')
	. $widgets->createLabel("outputSelectionTimespanType$ACTIVE_OS_MARKER", 'Week')
	. '</p><p>'
	. $widgets->createField("outputSelectionTimespanType$ACTIVE_OS_MARKER", null, 'm', '', false, 'radio')
	. $widgets->createLabel("outputSelectionTimespanType$ACTIVE_OS_MARKER", 'Month')
	. '</p><p>'
	. $widgets->createField("outputSelectionTimespanType$ACTIVE_OS_MARKER", null, 'q', '', false, 'radio', 'checked="checked"')
	. $widgets->createLabel("outputSelectionTimespanType$ACTIVE_OS_MARKER", 'Quarter')
	. '</p><p>'
	. $widgets->createField("outputSelectionTimespanType$ACTIVE_OS_MARKER", null, 'y', '', false, 'radio')
	. $widgets->createLabel("outputSelectionTimespanType$ACTIVE_OS_MARKER", 'Year')
	. '</p>'
	. '</fieldset>'
	. '<fieldset style="display: inline; vertical-align: top;">'
	. '<legend>Sub-Categories</legend>'
	. '<p>'
	. $widgets->createField("outputSelectionTimespanSummarize$ACTIVE_OS_MARKER", null, 't', '', false, 'radio', 'checked="checked"')
	. $widgets->createLabel("outputSelectionTimespanSummarize$ACTIVE_OS_MARKER", 'Summarize sub-categories')
	. '</p><p>'
	. $widgets->createField("outputSelectionTimespanSummarize$ACTIVE_OS_MARKER", null, 'f', '', false, 'radio')
	. $widgets->createLabel("outputSelectionTimespanSummarize$ACTIVE_OS_MARKER", 'Don not summarize')
	. '</p>'
	. '</fieldset>'
	. '</div>';
echo $outputSelectionTimespan;
echo '</div>';

$outputSelectionContent = '<fieldset style="width: 8em; display: inline; vertical-align: top;">'
	. '<legend>Graph Type</legend>'
	. '<p>'
	. $widgets->createField("outputSelectionType", null, 'Trend', '', false, 'radio', 'checked="checked" onchange="updateOutputSelection();"')
	. $widgets->createLabel("outputSelectionType", 'Trend')
	. '</p><p>'
	. $widgets->createField("outputSelectionType", null, 'Category', '', false, 'radio', 'onchange="updateOutputSelection();"')
	. $widgets->createLabel("outputSelectionType", 'Category')
	. '</p><p>'
	. $widgets->createField("outputSelectionType", null, 'Timespan', '', false, 'radio', 'onchange="updateOutputSelection();"')
	. $widgets->createLabel("outputSelectionType", 'Timespan')
	. '</p>'
	. '</fieldset>'
	. "<div id='outputSelectionContent' style='display: inline; vertical-align: top;'>"
	. str_replace($ACTIVE_OS_MARKER, '', $outputSelectionTrend)
	. '</div>';

echo $widgets->addTwistieSection('Output Selection', $outputSelectionContent, null, true);	
echo '<div>';
echo $widgets->createButton('applyFilter', 'Analyse', 'applyFilterX();');
echo '</div>';

echo $widgets->addTwistieSection('Graph', '<div id="graphContent"></div>', null, true);

echo $widgets->addTwistieSection('Output', $dgResult->writeDataGrid(), null, true);
eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
?>
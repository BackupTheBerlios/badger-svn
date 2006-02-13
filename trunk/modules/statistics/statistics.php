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
require_once BADGER_ROOT . '/includes/charts/charts.php';
require_once BADGER_ROOT . '/core/Date/Span.php';
require_once BADGER_ROOT . '/core/widgets/DataGrid.class.php';

if (isset($_GET['mode'])) {
	$mode = $_GET['mode'];
} else if (isset($_POST['mode'])) {
	$mode =$_POST['mode'];
} else {
	$mode = 'selectPage';
}

switch ($mode) {
	case 'selectPage':
	default:
		showSelectPage();
		break;

	case 'trendPage':
		printTrendPage();
		break;
	
	case 'trendData':
		showTrendData();
		break;

	case 'categoryPage':
		printCategoryPage();
		break;
	
	case 'categoryData':
		showCategoryData();
		break;
}

function showSelectPage() {
	global $tpl;
	global $us;
	
	$widgets = new WidgetEngine($tpl); 

	$widgets->addCalendarJS();
	$widgets->addToolTipJS();
	$tpl->addJavaScript("js/behaviour.js");
	$tpl->addJavaScript("js/prototype.js");
	$tpl->addJavaScript("js/statistics.js");
	
	$dataGrid = new DataGrid($tpl);
	$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
	$dataGrid->headerName = array(getBadgerTranslation2('statistics','accColTitle'), getBadgerTranslation2('statistics','accColBalance'), getBadgerTranslation2('statistics','accColCurrency'));
	$dataGrid->columnOrder = array("title", "balance", 'currency');  
	$dataGrid->initialSort = "title";
	$dataGrid->headerSize = array(180, 100, 100);
	$dataGrid->cellAlign = array("left", 'right', 'right');
	$dataGrid->width = '30em';
	$dataGrid->height = '7em';
	$dataGrid->rowCounterName = getBadgerTranslation2('dataGrid', 'rowCounterName');
	$dataGrid->initDataGridJS();

	$widgets->addNavigationHead();

	$selectTitle = getBadgerTranslation2('statistics','pageTitle');
	echo $tpl->getHeader($selectTitle);
	
	echo $widgets->getNavigationBody();
	$widgets->addToolTipLayer();

	$selectFormAction = BADGER_ROOT . '/modules/statistics/statistics.php';
	
	$graphTypeText = getBadgerTranslation2('statistics','type');
	$categoryTypeText = getBadgerTranslation2('statistics','category');
	$timeFrameText = getBadgerTranslation2('statistics','period');
	$summarizeCategoriesText = getBadgerTranslation2('statistics','catMerge');
	$accountsText = getBadgerTranslation2('statistics','accounts');
	$differentCurrencyWarningText = getBadgerTranslation2('statistics','attention');
	$fromText = getBadgerTranslation2('statistics','from');
	$toText = getBadgerTranslation2('statistics','to');
	
	$trendRadio = $widgets->createField('mode', null, 'trendPage', '', false, 'radio', 'checked="checked"');
	$trendLabel = $widgets->createLabel('mode', 'Trend');
	
	$categoryRadio = $widgets->createField('mode', null, 'categoryPage', '', false, 'radio');
	$categoryLabel = $widgets->createLabel('mode', 'Kategorien');

	$accountSelect = $dataGrid->writeDataGrid();
	$accountField = $widgets->createField('accounts', null, null, '', false, 'hidden');

	$monthArray = array (
		'fullYear' => 'Ganzes Jahr',
		'1' => getBadgerTranslation2('statistics','jan'),
		'2' => getBadgerTranslation2('statistics','feb'),
		'3' => getBadgerTranslation2('statistics','mar'),
		'4' => getBadgerTranslation2('statistics','apr'),
		'5' => getBadgerTranslation2('statistics','may'),
		'6' => getBadgerTranslation2('statistics','jun'),
		'7' => getBadgerTranslation2('statistics','jul'),
		'8' => getBadgerTranslation2('statistics','aug'),
		'9' => getBadgerTranslation2('statistics','sep'),
		'10' => getBadgerTranslation2('statistics','oct'),
		'11' => getBadgerTranslation2('statistics','nov'),
		'12' => getBadgerTranslation2('statistics','dec'),
	);
	$monthSelect = $widgets->createSelectField('monthSelect', $monthArray, 'fullYear', '', false, 'onchange="updateDateRange();"');

	$now = new Date();
	$beginOfYear = new Date();
	$beginOfYear->setMonth(1);
	$beginOfYear->setDay(1);
	
	$yearInput = $widgets->createField('yearSelect', 4, $now->getYear(),'', false, 'text', 'onchange="updateDateRange();"');
	
	$startDateField = $widgets->addDateField("startDate", $beginOfYear->getFormatted());
	$endDateField = $widgets->addDateField("endDate", $now->getFormatted());
	
	$inputRadio = $widgets->createField('type', null, 'i', '', false, 'radio', 'checked="checked"');
	$inputLabel = $widgets->createLabel('type', getBadgerTranslation2('statistics','income'));
	
	$outputRadio = $widgets->createField('type', null, 'o', '', false, 'radio');
	$outputLabel = $widgets->createLabel('type', getBadgerTranslation2('statistics','expenses'));

	$summarizeRadio = $widgets->createField('summarize', null, 't', '', false, 'radio', 'checked="checked"');
	$summarizeLabel = $widgets->createLabel('summarize', getBadgerTranslation2('statistics','subCat'));

	$distinguishRadio = $widgets->createField('summarize', null, 'f', '', false, 'radio');
	$distinguishLabel = $widgets->createLabel('summarize', getBadgerTranslation2('statistics','subCat2'));

	$dateFormatField = $widgets->createField('dateFormat', null, $us->getProperty('badgerDateFormat'), null, false, 'hidden');
	$errorMsgAccountMissingField = $widgets->createField('errorMsgAccountMissing', null, getBadgerTranslation2('statistics','errorMissingAcc'), null, false, 'hidden');
	$errorMsgStartBeforeEndField = $widgets->createField('errorMsgStartBeforeEnd', null, getBadgerTranslation2('statistics','errorDate'), null, false, 'hidden');
	$errorMsgEndInFutureField = $widgets->createField('errorMsgEndInFuture', null, getBadgerTranslation2('statistics','errorEndDate'), null, false, 'hidden');

	$submitButton = $widgets->createButton('submit', 'Anzeigen', 'submitSelect();', "Widgets/accept.gif");

	eval('echo "' . $tpl->getTemplate('statistics/select') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function printTrendPage() {
	global $tpl;
	global $badgerDb;
	
	$widgets = new WidgetEngine($tpl); 
	
	$widgets->addNavigationHead();

	$trendTitle = 'Trendanzeige'; 
	echo $tpl->getHeader($trendTitle);

	echo $widgets->getNavigationBody();
	
	if (!isset($_POST['accounts']) || !isset($_POST['startDate']) || !isset($_POST['endDate'])) {
		throw new BadgerException('statistics', 'missingParameter');
	}
	
	$accountIds = explode(';', $_POST['accounts']);
	$accountIdsClean = '';
	$first = true;
	foreach($accountIds as $key => $val) {
		settype($accountIds[$key], 'integer');
		
		if (!$first) {
			$accountIdsClean .= ';';
		} else {
			$first = false;
		}
		$accountIdsClean .= $accountIds[$key];
	}
	
	$startDate = new Date($_POST['startDate'], true);
	$endDate = new Date($_POST['endDate'], true);
	
	$accountManager = new AccountManager($badgerDb);
	
	$accountList = '';
	
	foreach($accountIds as $currentAccountId) {
		$currentAccount = $accountManager->getAccountById($currentAccountId);
		
		$accountTitle = $currentAccount->getTitle();
		eval('$accountList .= "' . $tpl->getTemplate('statistics/trendAccountLine') . '";');
	}	
	
	$startDateFormatted = $startDate->getFormatted();
	$endDateFormatted = $endDate->getFormatted();

	$trendChart = InsertChart(
		BADGER_ROOT . "/includes/charts/charts.swf",
		BADGER_ROOT . "/includes/charts/charts_library",
		BADGER_ROOT . "/modules/statistics/statistics.php?mode=trendData&accounts=$accountIdsClean&startDate=" . $startDate->getDate() . '&endDate=' . $endDate->getDate(),
		750,
		500,
		'99cc00',
		true
	);

	eval('echo "' . $tpl->getTemplate('statistics/trend') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function showTrendData() {
	global $badgerDb;
	global $logger;
	
	$logger->log('statistics::showTrendData: REQUEST_URI: ' . $_SERVER['REQUEST_URI']);

	if (!isset($_GET['accounts']) || !isset($_GET['startDate']) || !isset($_GET['endDate'])) {
		throw new BadgerException('statistics', 'missingParameter');
	}
	
	$accountIds = explode(';', $_GET['accounts']);
	foreach($accountIds as $key => $val) {
		settype($accountIds[$key], 'integer');
	}
	
	$startDate = new Date($_GET['startDate']);
	$endDate = new Date($_GET['endDate']);
	
	$now = new Date();
	$now->setHour(0);
	$now->setMinute(0);
	$now->setSecond(0);
	
	if ($endDate->after($now)) {
		$endDate = $now;
	}

	$accountManager = new AccountManager($badgerDb);

	$totals = array();
	$accounts = array();

	$currentAccountIndex = 0;

	foreach($accountIds as $currentAccountId) {
		$currentAccount = $accountManager->getAccountById($currentAccountId);
		
		$accounts[$currentAccountIndex][0] = $currentAccount->getTitle();
		
		$currentBalances = getDailyAmount($currentAccount, $startDate, $endDate);
		
		foreach ($currentBalances as $balanceKey => $balanceVal) {
			if (isset($totals[$balanceKey])) {
				$totals[$balanceKey]->add($balanceVal);
			} else {
				$totals[$balanceKey] = $balanceVal;
			}
			
			$accounts[$currentAccountIndex][] = $balanceVal->get();
		}
		
		$currentAccountIndex++;
	}
	
	$numDates = count($totals);
	
	$chart = array ();
	$chart['chart_type'] = 'line';
	$chart['axis_category']['skip'] = $numDates / 16;
	$chart['axis_category']['size'] = 14;
	$chart['axis_category']['orientation'] = 'diagonal_up';
	$chart['axis_value']['size'] = 14;
	$chart['chart_pref']['point_shape'] = 'none';
	$chart[ 'legend_rect' ] = array ( 'x'=>-100, 'y'=>-100, 'width'=>10, 'height'=>10, 'margin'=>10 ); 
	
	$chart['chart_data'] = array();
	
	$chart['chart_data'][0][0] = '';
	if (count($accounts) > 1) {
		$chart['chart_data'][1][0] = 'Gesamt';
	} else {
		$chart['chart_data'][1][0] = $accounts[0][0];
	}
	
	foreach($totals as $key => $val) {
		$tmp = new Date($key);
		$chart['chart_data'][0][] = $tmp->getFormatted();
		$chart['chart_data'][1][] = $val->get();
	}
	
	if (count($accounts) > 1) {
		foreach($accounts as $val) {
			$chart['chart_data'][] = $val;
		}
	}
	
	SendChartData($chart);
}

function printCategoryPage() {
	global $tpl;
	global $badgerDb;
	
	$widgets = new WidgetEngine($tpl); 
	
	$widgets->addNavigationHead();

	$categoryTitle = 'Kategorieanzeige'; 
	echo $tpl->getHeader($categoryTitle);

	echo $widgets->getNavigationBody();
	
	if (!isset($_POST['accounts']) || !isset($_POST['startDate']) || !isset($_POST['endDate']) || !isset($_POST['type']) || !isset($_POST['summarize'])) {
		throw new BadgerException('statistics', 'missingParameter');
	}
	
	$accountIds = explode(';', $_POST['accounts']);
	$accountIdsClean = '';
	$first = true;
	foreach($accountIds as $key => $val) {
		settype($accountIds[$key], 'integer');
		
		if (!$first) {
			$accountIdsClean .= ';';
		} else {
			$first = false;
		}
		$accountIdsClean .= $accountIds[$key];
	}
	
	$startDate = new Date($_POST['startDate'], true);
	$endDate = new Date($_POST['endDate'], true);
	
	$type = $_POST['type'];
	if ($type !== 'o') {
		$type = 'i';
		$typeText = 'Einnahmen';
	} else {
		$typeText = 'Ausgaben';
	}
	
	$summarize = $_POST['summarize'];
	if ($summarize !== 't') {
		$summarize = 'f';
		$summarizeText = 'Unterkategorien werden eigenständig aufgeführt.';
	} else {
		$summarizeText = 'Unterkategorien werden unter den Hauptkategorien zusammengefasst.';
	}

	$accountManager = new AccountManager($badgerDb);
	
	$accountList = '';
	
	foreach($accountIds as $currentAccountId) {
		$currentAccount = $accountManager->getAccountById($currentAccountId);
		
		$accountTitle = $currentAccount->getTitle();
		eval('$accountList .= "' . $tpl->getTemplate('statistics/categoryAccountLine') . '";');
	}	
	
	$startDateFormatted = $startDate->getFormatted();
	$endDateFormatted = $endDate->getFormatted();

	$categoryChart = InsertChart(
		BADGER_ROOT . "/includes/charts/charts.swf",
		BADGER_ROOT . "/includes/charts/charts_library",
		BADGER_ROOT . "/modules/statistics/statistics.php?mode=categoryData&accounts=$accountIdsClean&startDate=" . $startDate->getDate() . '&endDate=' . $endDate->getDate() . '&type=' . $type . '&summarize=' . $summarize,
		750,
		500,
		'99cc00',
		true
	);

	$categories = gatherCategories($accountIds, $startDate, $endDate, $type, $summarize == 't');
	
	$categoryHead = 'Kategorie';
	$countHead = 'Anzahl';
	$amountHead = 'Summe';
	
	$categoryTableBody = '';

	foreach ($categories as $currentCategory) {
		$categoryTitle = $currentCategory['title'];
		$categoryCount = $currentCategory['count'];
		$categoryAmount = $currentCategory['amount']->getFormatted();
		
		eval('$categoryTableBody .= "' . $tpl->getTemplate('statistics/categoryCategoryTableRow') . '";');
	}
	
	eval('echo "' . $tpl->getTemplate('statistics/category') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function showCategoryData() {
	global $badgerDb;
	
	if (!isset($_GET['accounts']) || !isset($_GET['startDate']) || !isset($_GET['endDate']) || !isset($_GET['type']) || !isset($_GET['summarize'])) {
		throw new BadgerException('statistics', 'missingParameter');
	}
	
	$accounts = explode(';', $_GET['accounts']);
	foreach($accounts as $key => $val) {
		settype($accounts[$key], 'integer');
	}
	
	$startDate = new Date($_GET['startDate']);
	$endDate = new Date($_GET['endDate']);
	
	$type = $_GET['type'];
	if ($type !== 'o') {
		$type = 'i';
	}

	if ($_GET['summarize'] !== 't') {
		$summarize = false;
	} else {
		$summarize = true;
	}
	
	$categories = gatherCategories($accounts, $startDate, $endDate, $type, $summarize);

	$chart = array();
	$chart['chart_type'] = '3d pie';
	//$chart['axis_category']['size'] = 14;
	$chart['axis_value']['size'] = 14;
	$chart['legend_label']['size'] = 14;
	$chart['legend_label']['bold'] = false;
	
	$chart['chart_data'] = array();
	
	$chart['chart_data'][0][0] = '';
	$chart['chart_data'][1][0] = '';
	
	foreach($categories as $key => $val) {
		$chart['chart_data'][0][] = $val['title'];
		if ($type == 'i') {
			$chart['chart_data'][1][] = $val['amount']->get();
		} else {
			$chart['chart_data'][1][] = $val['amount']->mul(-1)->get();
		}
	}
	
	SendChartData($chart);
}

function gatherCategories($accountIds, $startDate, $endDate, $type, $summarize) {
	global $badgerDb;

	$accountManager = new AccountManager($badgerDb);

	$categories = array(
		'none' => array (
			'title' => '(nicht zugeordnet)',
			'count' => 0,
			'amount' => new Amount(0)
		)
	);

	foreach($accountIds as $currentAccountId) {
		$currentAccount = $accountManager->getAccountById($currentAccountId);
		
		//echo 'Account: ' . $currentAccount->getTitle() . '<br />';

		$currentAccount->setFilter(array (
			array (
				'key' => 'valutaDate',
				'op' => 'ge',
				'val' => $startDate
			),
			array (
				'key' => 'valutaDate',
				'op' => 'le',
				'val' => $endDate
			)
		));

		while ($currentTransaction = $currentAccount->getNextFinishedTransaction()) {
			if ($type == 'i') {
				if ($currentTransaction->getAmount()->compare(0) < 0) {
					continue;
				}
			} else {
				if ($currentTransaction->getAmount()->compare(0) > 0) {
					continue;
				}
			}
			
			if ($category = $currentTransaction->getCategory()) {
				if ($summarize && $category->getParent()) {
					$category = $category->getParent();
				}

				if (isset($categories[$category->getId()])) {
					$categories[$category->getId()]['count']++;
					$categories[$category->getId()]['amount']->add($currentTransaction->getAmount());
				} else {
					$categories[$category->getId()] = array (
						'title' => $category->getTitle(),
						'count' => 1,
						'amount' => $currentTransaction->getAmount()
					);
				}
			} else {
				$categories['none']['count']++;
				$categories['none']['amount']->add($currentTransaction->getAmount());
			}
		}
	}
	
	//uasort($categories, 'compareCategories');

	if ($categories['none']['count'] == 0) {
		unset($categories['none']);
	}
	
	return $categories;
}

function compareCategories($a, $b) {
	return $a['amount']->compare($b['amount']);
}
?>
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
	$widgets = new WidgetEngine($tpl); 

	$widgets->addCalendarJS();
	$widgets->addToolTipJS();
	$tpl->addCss("Widgets/dataGrid.css");
	$tpl->addJavaScript("js/behaviour.js");
	$tpl->addJavaScript("js/prototype.js");
	$tpl->addJavaScript("js/statistics.js");
	
	$dataGrid = new DataGrid($tpl);
	$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
	$dataGrid->headerName = array("Titel","Kontostand");
	$dataGrid->columnOrder = array("title","balance");  
	$dataGrid->initialSort = "title";
	$dataGrid->headerSize = array(180,100);
	$dataGrid->cellAlign = array("left","right");
	$dataGrid->width = '290px';
	$dataGrid->rowCounterName = getBadgerTranslation2('dataGrid', 'rowCounterName');
	$dataGrid->initDataGridJS();

	$widgets->addNavigationHead();

	$selectTitle = 'Statistik-Auswahl';
	echo $tpl->getHeader($selectTitle);
	
	echo $widgets->getNavigationBody();
	$widgets->addToolTipLayer();

	$selectFormAction = BADGER_ROOT . '/modules/statistics/statistics.php';
	
	$trendRadio = $widgets->createField('mode', null, 'trendPage', '', false, 'radio');
	$trendLabel = $widgets->createLabel('mode', 'Trend');
	
	$categoryRadio = $widgets->createField('mode', null, 'categoryPage', '', false, 'radio');
	$categoryLabel = $widgets->createLabel('mode', 'Kategorien');

	$accountSelect = $dataGrid->writeDataGrid();
	$accountField = $widgets->createField('accounts', null, null, '', false, 'hidden');

	$monthArray = array (
		'fullYear' => 'Ganzes Jahr',
		'jan' => 'Januar',
		'feb' => 'Februar',
		'mar' => 'M�rz',
		'apr' => 'April',
		'may' => 'Mai',
		'jun' => 'Juni',
		'jul' => 'Juli',
		'aug' => 'August',
		'sep' => 'September',
		'oct' => 'October',
		'nov' => 'November',
		'dec' => 'December'
	);
	$monthSelect = $widgets->createSelectField('monthSelect', $monthArray, 'fullYear', 'Description', false);
	$yearInput = $widgets->createField('yearSelect', 4, 2006, 'Beschreibung');
	
	$startDateField = $widgets->addDateField("startDate", "01.01.2006");
	$endDateField = $widgets->addDateField("endDate", "31.12.2006");
	
	$inputRadio = $widgets->createField('type', null, 'i', '', false, 'radio');
	$inputLabel = $widgets->createLabel('type', 'Eingaben');
	
	$outputRadio = $widgets->createField('type', null, 'o', '', false, 'radio');
	$outputLabel = $widgets->createLabel('type', 'Ausgaben');

	$summarizeRadio = $widgets->createField('summarize', null, 't', '', false, 'radio');
	$summarizeLabel = $widgets->createLabel('summarize', 'Unterkategorien unter der Hauptkategorie zusammenfassen');

	$distinguishRadio = $widgets->createField('summarize', null, 'f', '', false, 'radio');
	$distinguishLabel = $widgets->createLabel('summarize', 'Unterkategorien eigenständig aufführen');

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
		'99cc00'
	);

	eval('echo "' . $tpl->getTemplate('statistics/trend') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function showTrendData() {
	global $badgerDb;
	
	if (!isset($_GET['accounts']) || !isset($_GET['startDate']) || !isset($_GET['endDate'])) {
		throw new BadgerException('statistics', 'missingParameter');
	}
	
	$accounts = explode(';', $_GET['accounts']);
	foreach($accounts as $key => $val) {
		settype($accounts[$key], 'integer');
	}
	
	$startDate = new Date($_GET['startDate']);
	$endDate = new Date($_GET['endDate']);
	
	$accountManager = new AccountManager($badgerDb);

	$totals = array();

	foreach($accounts as $currentAccountId) {
		$currentAccount = $accountManager->getAccountById($currentAccountId);
		
		$currentBalances = getDailyAmount($currentAccount, $startDate, $endDate);
		
		foreach ($currentBalances as $balanceKey => $balanceVal) {
			if (isset($totals[$balanceKey])) {
				$totals[$balanceKey]->add($balanceVal);
			} else {
				$totals[$balanceKey] = $balanceVal;
			}
		}
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
	$chart['chart_data'][1][0] = ''; //Kontostandverlauf zwischen dem ' . $startDate->getFormatted() . ' und dem ' . $endDate->getFormatted();
	
	foreach($totals as $key => $val) {
		$tmp = new Date($key);
		$chart['chart_data'][0][] = $tmp->getFormatted();
		$chart['chart_data'][1][] = $val->get();
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
		'99cc00'
	);

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

	$summarize = $_GET['summarize'];
	if ($summarize !== 't') {
		$summarize = 'f';
	}

	$accountManager = new AccountManager($badgerDb);

	$categories = array(
		'none' => array (
			'title' => '(nicht zugeordnet)',
			'count' => 0,
			'amount' => new Amount(0)
		)
	);

	foreach($accounts as $currentAccountId) {
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
				if ($summarize == 't' && $category->getParent()) {
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

	$chart = array ();
	$chart['chart_type'] = '3d pie';
	//$chart['axis_category']['size'] = 14;
	$chart['axis_value']['size'] = 14;
	$chart['legend_label']['size'] = 14;
	$chart['legend_label']['bold'] = false;
	
	$chart['chart_data'] = array();
	
	$chart['chart_data'][0][0] = '';
	$chart['chart_data'][1][0] = ''; //Kontostandverlauf zwischen dem ' . $startDate->getFormatted() . ' und dem ' . $endDate->getFormatted();
	
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

function compareCategories($a, $b) {
	return $a['amount']->compare($b['amount']);
}
?>
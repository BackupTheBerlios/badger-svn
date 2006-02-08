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
	$tpl->addCss("Widgets/dataGrid.css");
	$tpl->addJavaScript("js/behaviour.js");
	$tpl->addJavaScript("js/prototype.js");
	$tpl->addJavaScript("js/statistics.js");
	
	$dataGrid = new DataGrid($tpl);
	$dataGrid->sourceXML = BADGER_ROOT."/core/XML/getDataGridXML.php?q=AccountManager";
	$dataGrid->headerName = array("Titel","Kontostand");
	$dataGrid->columnOrder = array("title","balance");  
	$dataGrid->initialSort = "title";
	$dataGrid->headerSize = array(200,150);
	$dataGrid->cellAlign = array("left","right");
	$dataGrid->rowCounterName = getBadgerTranslation2('dataGrid', 'rowCounterName');
	$dataGrid->initDataGridJS();

	$widgets->addNavigationHead();

	$selectTitle = 'Statistik-Auswahl';
	echo $tpl->getHeader($selectTitle);
	
	echo $widgets->getNavigationBody();

	$selectFormAction = BADGER_ROOT . '/modules/statistics/statistics.php';
	
	$trendRadio = $widgets->createField('mode', null, 'trendPage', '', false, 'radio');
	$trendLabel = $widgets->createLabel('mode', 'Trend');
	
	$categoryRadio = $widgets->createField('mode', null, 'categoryPage', '', false, 'radio');
	$categoryLabel = $widgets->createLabel('mode', 'Kategorien');

	$accountSelect = $dataGrid->writeDataGrid();
	$accountField = $widgets->createField('accounts', null, null, '', false, 'hidden');

	$startDateField = $widgets->addDateField("beginDate", "01.01.2006");
	$endDateField = $widgets->addDateField("endDate", "01.01.2006");
	
	$submitButton = $widgets->createButton('submit', 'Anzeigen', 'submitSelect();');

	eval(' echo "' . $tpl->getTemplate('statistics/select') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function printTrendPage() {
	echo InsertChart(BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/statistics/statistics.php?mode=trendData&accounts=1;2&startDate=2006-01-01&endDate=2006-12-31", 750, 500, '99cc00');
}

function showTrendData() {
	global $badgerDb;
	
	if (!isset($_GET['accounts']) || !isset($_GET['startDate']) || !isset($_GET['endDate'])) {
		throw new BadgerException('accountStatistics', 'missingParameter');
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
	echo InsertChart(BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/statistics/statistics.php?mode=categoryData&accounts=1;2&startDate=2006-02-01&endDate=2006-02-28&type=i", 750, 500, '99cc00');
}

function showCategoryData() {
	global $badgerDb;
	
	if (!isset($_GET['accounts']) || !isset($_GET['startDate']) || !isset($_GET['endDate']) || !isset($_GET['type'])) {
		throw new BadgerException('accountStatistics', 'missingParameter');
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
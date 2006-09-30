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

require_once BADGER_ROOT . '/includes/fileHeaderBackEnd.inc.php';
require_once BADGER_ROOT . '/core/XML/dataGridCommon.php';
require_once BADGER_ROOT . '/includes/jpGraph/src/jpgraph.php';
require_once BADGER_ROOT . '/includes/jpGraph/src/jpgraph_line.php';
require_once BADGER_ROOT . '/includes/jpGraph/src/jpgraph_date.php';
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/statistics2/colors.php';

$graph = new Graph(800, 400);
$graph->setScale('datlin');

$accountIds = getGPC($_GET, 'accounts', 'integerList');

$accountManager = new AccountManager($badgerDb);

$totals = array();
$labels = array();
$values = array();

$orderMin = array (
	array (
		'key' => 'valutaDate',
		'dir' => 'asc'
	)
);

$orderMax = array (
	array (
		'key' => 'valutaDate',
		'dir' => 'desc'
	)
);

if (getGPC($_GET, 'start') !== 'b') {
	$startWithBalance = false;
} else {
	$startWithBalance = true;
}

if (getGPC($_GET, 'ticks') !== 's') {
	$showTickMarks = false;
} else {
	$showTickMarks = true;
}

$displayStartDate = null;
$displayEndDate = null;
$displayStartDateFound = false;
$displayEndDateFound = false;
$valutaDateFilterAnalysed = false;

foreach ($accountIds as $currentAccountId) {
	$currentAccount = $accountManager->getAccountById($currentAccountId);

	$labels[$currentAccount->getId()] = $currentAccount->getTitle();

	$filter = getDataGridFilter($currentAccount);
	
	if (!$valutaDateFilterAnalysed) {
		foreach ($filter as $currentFilter) {
			if ($currentFilter['key'] == 'valutaDate') {
				switch ($currentFilter['op']) {
					case 'lt':
					case 'le':
						$displayEndDate = earlierDate($displayEndDate, $currentFilter['val']);
						$displayEndDateFound = true;
						break;
					
					case 'gt':
					case 'ge':
						$displayStartDate = laterDate($displayStartDate, $currentFilter['val']);
						$displayStartDateFound = true;
						break;
					
					case 'eq':
						$displayEndDate = earlierDate($displayEndDate, $currentFilter['val']);
						$displayStartDate = laterDate($displayStartDate, $currentFilter['val']);
						$displayEndDateFound = true;
						$displayStartDateFound = true;
						break;
				} //switch op
			} //if valutaDate
		} //foreach filter
		
		$valutaDateFilterAnalysed = true;
	}
	
	if (!$displayStartDateFound) {
		$currentAccount->setOrder($orderMin);
		$currentAccount->setFilter($filter);

		while ($currentTransaction = $currentAccount->getNextTransaction()) {
			$startDate = $currentTransaction->getValutaDate();
			if (!is_null($startDate)) {
				$displayStartDate = laterDate($displayStartDate, $startDate);
				break;
			}
		}
	}
} //foreach account

if (!$displayEndDateFound) {
	$accountManager = new AccountManager($badgerDb);
	
	foreach ($accountIds as $currentAccountId) {
		$currentAccount = $accountManager->getAccountById($currentAccountId);

		$currentAccount->setOrder($orderMax);
		$currentAccount->setFilter($filter);

		while ($currentTransaction = $currentAccount->getNextTransaction()) {
			$endDate = $currentTransaction->getValutaDate();
			if (!is_null($endDate)) {
				$displayEndDate = earlierDate($displayEndDate, $endDate);
				break;
			}
		}
	}
}

$accountManager = new AccountManager($badgerDb);

$totals = array();
$values = array();
$valueTargets = array();
$valueNames = array();

if (is_null($displayStartDate) || is_null($displayEndDate)) {
	echo 'No transactions match your criteria';

	require_once BADGER_ROOT . "/includes/fileFooter.php";
	exit;
}

foreach($accountIds as $currentAccountId) {
	$currentAccount = $accountManager->getAccountById($currentAccountId);

	$filter = getDataGridFilter($currentAccount);
	$currentAccount->setFilter($filter);

	$currentBalances = getDailyAmount($currentAccount, $displayStartDate, $displayEndDate, false, $startWithBalance);
		
	$previousAmount = null;

	foreach ($currentBalances as $balanceKey => $balanceVal) {
		if (isset($totals[$balanceKey])) {
			$totals[$balanceKey]->add($balanceVal);
		} else {
			$totals[$balanceKey] = $balanceVal;
		}
		
		$values[$currentAccount->getId()][] = $balanceVal->compare($previousAmount) != 0 ? $balanceVal->get() : '-';
		if ($showTickMarks) { 
			$date = new Date($balanceKey);
			$valueTargets[$currentAccount->getId()][] = "javascript:reachThroughTrend('" . $date->getFormatted() . "', '$currentAccountId');";
			$valueNames[$currentAccount->getId()][] = $date->getFormatted() . ': ' . $balanceVal->getFormatted(); 
		}
		
		$previousAmount = new Amount($balanceVal);
	}
}

$xdata = array_keys($totals);
$numDates = count($totals);

$colorIndex = 0;

foreach ($values as $currentAccountId => $currentValues) {
	$line = new LinePlot(array_values($currentValues), $xdata);
	if ($showTickMarks) { 
		$line->SetCSIMTargets(array_values($valueTargets[$currentAccountId]), array_values($valueNames[$currentAccountId]));
		$line->mark->SetType(MARK_UTRIANGLE);
		$line->mark->SetColor($chartColors[$colorIndex % count($chartColors)]);
		$line->mark->SetFillColor($chartColors[$colorIndex % count($chartColors)]);
	}
	$line->SetColor($chartColors[$colorIndex % count($chartColors)]);
	$line->SetStepStyle();
	$line->SetLegend($labels[$currentAccountId]);
	$colorIndex++;
	$graph->add($line);
}

if (count($values) > 1) {
	$data = array();
	$dataTargets = array();
	$dataNames = array();
	
	$previousAmount = null;

	foreach($totals as $key => $currentAmount) {
		$data[] = $currentAmount->compare($previousAmount) != 0 ? $currentAmount->get() : '-';
		if ($showTickMarks) { 
			$date = new Date($key);
			$dataTargets[] = "javascript:reachThroughTrend('" . $date->getFormatted() . "', '" . implode(',', $accountIds) . "');";
			$dataNames[] = $date->getFormatted() . ': ' . $currentAmount->getFormatted(); 
		}
		
		$previousAmount = new Amount($currentAmount);
	}
	
	$line = new LinePlot($data, $xdata);
	if ($showTickMarks) { 
		$line->SetCSIMTargets(array_values($dataTargets), array_values($dataNames));
		$line->mark->SetType(MARK_UTRIANGLE);
		$line->mark->SetColor($chartColors[$colorIndex % count($chartColors)]);
		$line->mark->SetFillColor($chartColors[$colorIndex % count($chartColors)]);
	}
	$line->SetColor($chartColors[$colorIndex % count($chartColors)]);
	$line->SetStepStyle();
	$line->SetLegend(getBadgerTranslation2('statistics','trendTotal'));
	$line->SetWeight(2);
	$colorIndex++;
	$graph->add($line);
}


$graph->xaxis->SetFont(FF_VERA);
$graph->xaxis->SetTextLabelInterval(4);
$graph->xaxis->SetLabelFormatCallback('xAxisCallback');
//$graph->xaxis->scale->SetDateAlign(MONTHADJ_1, MONTHADJ_1);
$graph->yaxis->SetFont(FF_VERA);
$graph->yaxis->SetLabelFormatCallback('yAxisCallback');
$graph->legend->SetFont(FF_VERA);
$graph->legend->SetLayout(LEGEND_HOR);
$graph->legend->Pos(0.5, 0.03, 'center', 'top');
$graph->legend->SetReverse();
$graph->SetMargin(80, 20, 50, 40);

$graph->StrokeCSIM(basename(__FILE__));

require_once BADGER_ROOT . "/includes/fileFooter.php";

function xAxisCallback($timestamp) {
	$date = new Date($timestamp);
	return $date->getFormatted();
}

function yAxisCallback($number) {
	global $us;
	
	$amount = new Amount($number);
	list ($whole, $fraction) = explode($us->getProperty('badgerDecimalSeparator'), $amount->getFormatted());
	if (floor($number) == $number) {
		return $whole;
	} else {
		return $amount->getFormatted();
	}
}

function earlierDate($d1, $d2) {
	if (is_null($d1) && is_null($d2)) {
		return null;
	}
	if (is_null($d1)) {
		return new Date($d2);
	}
	if (is_null($d2)) {
		return new Date($d1);
	}
	
	if ($d1->before($d2)) {
		return new Date($d1);
	} else {
		return new Date($d2);
	}
}

function laterDate($d1, $d2) {
	if (is_null($d1) && is_null($d2)) {
		return null;
	}
	if (is_null($d1)) {
		return new Date($d2);
	}
	if (is_null($d2)) {
		return new Date($d1);
	}
	
	if ($d1->after($d2)) {
		return new Date($d1);
	} else {
		return new Date($d2);
	}
}
?>
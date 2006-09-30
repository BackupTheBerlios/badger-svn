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
require_once BADGER_ROOT . '/includes/jpGraph/src/jpgraph_pie.php';
require_once BADGER_ROOT . '/includes/jpGraph/src/jpgraph_pie3d.php';
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/statistics2/colors.php';

$graph = new PieGraph(800, 400);

$accountIds = getGPC($_GET, 'accounts', 'integerList');

$accountManager = new AccountManager($badgerDb);

$type = getGPC($_GET, 'type');
if ($type !== 'o') {
	$type = 'i';
}

if (getGPC($_GET, 'summarize') !== 't') {
	$summarize = false;
} else {
	$summarize = true;
}

$amounts = array();
$amounts['none'] = new Amount(0);
$labels = array();
$labels['none'] = getBadgerTranslation2('statistics', 'noCategoryAssigned');

foreach($accountIds as $currentAccountId) {
	$currentAccount = $accountManager->getAccountById($currentAccountId);
	
	$filter = getDataGridFilter($currentAccount);
	$currentAccount->setFilter($filter);

	while ($currentTransaction = $currentAccount->getNextTransaction()) {
		if ($type == 'i') {
			if ($currentTransaction->getAmount()->compare(0) < 0) {
				continue;
			}
		} else {
			if ($currentTransaction->getAmount()->compare(0) > 0) {
				continue;
			}
		}
		
		if (!is_null($category = $currentTransaction->getCategory())) {
			if ($summarize && $category->getParent()) {
				$category = $category->getParent();
			}

			if (isset($labels[$category->getId()])) {
				$amounts[$category->getId()]->add($currentTransaction->getAmount());
			} else {
				$labels[$category->getId()] = $category->getTitle();
				$amounts[$category->getId()] = new Amount($currentTransaction->getAmount());
			}
		} else {
			$amounts['none']->add($currentTransaction->getAmount());
		}
	}
}

if ($amounts['none']->compare(0) == 0) {
	unset($amounts['none']);
	unset($labels['none']);
}

if (count($amounts) == 0) {
	echo 'No transactions match your criteria';

	require_once BADGER_ROOT . "/includes/fileFooter.php";
	exit;
}

$data = array();
$dataNames = array();

foreach ($amounts as $currentAmount) {
	$data[] = $currentAmount->mul($type == 'i' ? 1 : -1)->get();
	$dataNames[] = $currentAmount->getFormatted();
}

$legends = array();
foreach ($labels as $currentKey => $currentLabel) {
	$legends[] = $currentLabel . ' - ' . $amounts[$currentKey]->getFormatted();
}

$targets = array();
foreach($labels as $currentId => $currentLabel) {
	if ($currentId != 'none') {
		$targets[] = "javascript:reachThroughCategory('$currentId');";
	} else {
		$targets[] = '';
	}
}

$pie = new PiePlot3D($data);
$pie->SetLegends($legends);
$pie->SetCSIMTargets($targets, $dataNames);
$pie->value->SetFont(FF_VERA);
$pie->value->SetFormatCallback('formatPercentage');
$pie->SetCenter(0.33, 0.5);
//$pie->SetSliceColors($chartColors);

$graph->Add($pie);

$graph->legend->SetFont(FF_VERA);
$graph->legend->SetPos(0.03, 0.05);
$graph->SetMargin(10, 10, 10, 10);
$graph->SetShadow();
$graph->SetAntiAliasing();

$graph-> StrokeCSIM(basename(__FILE__));

require_once BADGER_ROOT . "/includes/fileFooter.php";

function formatPercentage($val) {
	global $us;

	$str = sprintf('%1.2f %%', $val);
	
	return str_replace('.', $us->getProperty('badgerDecimalSeparator'), $str);
}
?>
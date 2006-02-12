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
#require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
#require_once BADGER_ROOT . '/modules/account/accountCommon.php';

$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();

$widgets->addNavigationHead();
echo $tpl->getHeader("Charts");
echo $widgets->getNavigationBody();

#$am = new AccountManager($badgerDb);
#$account = $am->getAccountById(1);
#$startDate = new Date('2006-03-10');
#$endDate = new Date('2010-12-12');

#$values = getDailyAmount($account, $startDate, $endDate);

#print_r($values);

//include charts.php to access the InsertChart function

?>
<?


require_once(BADGER_ROOT . "/includes/charts/charts.php");
echo "<br /><br /><br />";

echo InsertChart ( BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/forecast/forecastChart.php", 800, 400, "ECE9D8", true);

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>

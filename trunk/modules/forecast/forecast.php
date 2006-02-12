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

//include charts.php to access the InsertChart function
require_once(BADGER_ROOT . "/includes/charts/charts.php");
echo "<br /><br /><br />";
$endDate = "2006-03-28";
$account = 1;
$savingTarget = 1000;
$pocketmoney1 = 12;
$pocketMoney2 = 30;

?>
<div class="flashContainer">
<?php
echo InsertChart ( BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/forecast/forecastChart.php?endDate=$endDate&account=$account&savingTarget=$savingTarget&pocketMoney1=$pocketmoney1&pocketMoney2=$pocketMoney2", 800, 400, "ECE9D8", true);
?>
</div>
<div class="flashClear"></div>
<?php

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>

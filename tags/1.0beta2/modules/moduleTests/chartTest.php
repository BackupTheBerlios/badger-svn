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
define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();

$widgets->addNavigationHead();
echo $tpl->getHeader("Charts");

//include charts.php to access the InsertChart function
require_once(BADGER_ROOT . "/includes/charts/charts.php");

echo InsertChart ( BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/moduleTests/chartTestBar.php" );
echo "<br /><br />";
echo InsertChart ( BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/moduleTests/chartTestLine.php" );
echo "<br /><br />";
echo InsertChart ( BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/moduleTests/chartTestPie.php" );
echo "<br /><br />";

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>

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
define("BADGER_ROOT", "..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

$tpl->addCSS("style.css");
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();

$widgets->addNavigationHead();
echo $tpl->getHeader("Badger");
echo $widgets->getNavigationBody();

$Modul1 = "<a href='".BADGER_ROOT."/modules/statistics/statistics.php'>Statistiken</a>";
$Modul2 = "<a href='".BADGER_ROOT."modules/importExport/importExport.php?mode=export'>Backup</a>";
$Modul3 = "";
$Modul4 = "";
$Modul5 = "";
eval("echo \"".$tpl->getTemplate("badgerOverview")."\";");


eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>

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
define("BADGER_ROOT", "..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

$tpl->addCSS("style.css");
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();

$accountOverview = getBadgerTranslation2 ('accountAccount', 'pageTitleOverview');
$categoryOverview = getBadgerTranslation2 ('accountCategory', 'pageTitleOverview');
$statistics = getBadgerTranslation2 ('statistics','pageTitle');
$backup = getBadgerTranslation2 ('importExport', 'askTitle');
$userPrefrences = getBadgerTranslation2 ('UserSettingsAdmin', 'title');


$widgets->addNavigationHead();
echo $tpl->getHeader("Badger");

$Modul1 = "<a href='".BADGER_ROOT."/modules/account/AccountManagerOverview.php'>$accountOverview</a>";
$Modul2 = "<a href='".BADGER_ROOT."/modules/account/CategoryManagerOverview.php'>$categoryOverview</a>";
$Modul3 = "<a href='".BADGER_ROOT."/modules/statistics/statistics.php'>$statistics</a>";
$Modul4 = "<a href='".BADGER_ROOT."/modules/importExport/importExport.php?mode=export'>$backup</a>";
$Modul5 = "<a href='".BADGER_ROOT."/core/UserSettingsAdmin/UserSettingsAdmin.php'>$userPrefrences</a>";

eval("echo \"".$tpl->getTemplate("badgerOverview")."\";");


eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>

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

$Kontenübersicht = getBadgerTranslation2 ('accountAccount', 'pageTitleOverview');
$Kategorienübersicht = getBadgerTranslation2 ('accountCategory', 'pageTitleOverview');
$Statistiken = getBadgerTranslation2 ('statistics','pageTitle');
$Backup = getBadgerTranslation2 ('importExport', 'askTitle');
$Benutzereinstellungen = getBadgerTranslation2 ('UserSettingsAdmin', 'title');


$widgets->addNavigationHead();
echo $tpl->getHeader("Badger");

$Modul1 = "<a href='".BADGER_ROOT."/modules/account/AccountManagerOverview.php'>$Kontenübersicht</a>";
$Modul2 = "<a href='".BADGER_ROOT."/modules/account/CategoryManagerOverview.php'>$Kategorienübersicht</a>";
$Modul3 = "<a href='".BADGER_ROOT."/modules/statistics/statistics.php'>$Statistiken</a>";
$Modul4 = "<a href='".BADGER_ROOT."/modules/importExport/importExport.php?mode=export'>$Backup</a>";
$Modul5 = "<a href='".BADGER_ROOT."/core/UserSettingsAdmin/UserSettingsAdmin.php'>$Benutzereinstellungen</a>";

eval("echo \"".$tpl->getTemplate("badgerOverview")."\";");


eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>

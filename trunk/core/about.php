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
define ('BADGER_ROOT', '..');

require_once BADGER_ROOT . '/includes/fileHeaderFrontEnd.inc.php';

$widgets = new WidgetEngine($tpl); 
$widgets->addNavigationHead();

$aboutTitle = 'Über BADGER finance';

echo $tpl->getHeader($aboutTitle);

echo $widgets->getNavigationBody();

$badgerImage = $widgets->addImage('badger-logo.gif');
$version = BADGER_VERSION;
$from = 'vom';
$dateObj = new Date(filemtime(BADGER_ROOT . '/includes/includes.php'));
$date = $dateObj->getFormatted();
$releasedUnder = 'Veröffentlicht unter';
$copyrightBy = 'den Mitgliedern des Entwicklungs-Teams.';
$developerTeam = 'Entwicklungs-Team';
$usedComponents = 'Verwendete Programme und Komponenten';
$by = 'von';

eval('echo "' . $tpl->getTemplate('about') . '";');
eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
?>
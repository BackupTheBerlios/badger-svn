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
require_once(BADGER_ROOT . '/modules/account/CurrencyManager.class.php');

$cm = new CurrencyManager($badgerDb);
$widgets = new WidgetEngine($tpl);
$widgets->addToolTipJS();

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			if (isset($_GET['ID'])) {
				$ID = $_GET['ID'];
				$cm->deleteCurrency($ID);
				//Fehler
			} else {
				//Fehler keine ID	
			}			
			break;		
		case 'new':
			echo $tpl->getHeader("CurrencyManager - New");
			echo $widgets->addToolTipLayer();
			//formular ausgeben
			$symbolLabel = $widgets->createLabel("symbol", "Symbol", true);
			$symbolField = $widgets->createField("symbol", 20, "", "ToolTip I18N", true, "text", "");
			$longnameLabel = $widgets->createLabel("longname", "LongName", true);
			$longnameField = $widgets->createField("longname", 20, "", "ToolTip I18N", true, "text", "");
			$submitBtn = $widgets->createButton("submit", "Speichern I18N", "submit", "Widgets/accept.gif");

			eval("echo \"".$tpl->getTemplate("Account/Currency")."\";");
			
			break;
		case 'edit':
			if (isset($_GET['ID'])) {
				$ID = $_GET['ID'];
				//formular ausgeben und mit werten füllen
			} else {
				//Fehler keine ID	
			}
			break;
	}	
	
}
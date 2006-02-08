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
		case 'edit':
			echo $tpl->getHeader("CurrencyManager - New");
			echo $widgets->addToolTipLayer();
			if (isset($_GET['ID'])) {
				$ID = $_GET['ID'];
				//load data for this ID
				$currency = $cm->getCurrencyById($ID);
				$symbolValue = $currency->getSymbol();
				$langnameValue = $currency->getLongName();				
			} else {
				$ID = "";
				$symbolValue = "";
				$langnameValue = ""; 
			}
			//set vars with values
			$FormAction = $_SERVER['PHP_SELF'];
			$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
			$symbolLabel = $widgets->createLabel("symbol", getBadgerTranslation2('accountCurrency', 'symbol'), true);
			$symbolField = $widgets->createField("symbol", 20, $symbolValue, "ToolTip I18N", true, "text", "");
			$longnameLabel = $widgets->createLabel("longname", getBadgerTranslation2('accountCurrency', 'longname'), true);
			$longnameField = $widgets->createField("longname", 20, $langnameValue, "ToolTip I18N", true, "text", "");
			$submitBtn = $widgets->createButton("submit", "Speichern I18N", "submit", "Widgets/accept.gif");

			//print site
			eval("echo \"".$tpl->getTemplate("Account/Currency")."\";");

			break;
	}	
	
}
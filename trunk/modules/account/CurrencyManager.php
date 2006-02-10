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

$redirectPageAfterSave = "CurrencyManagerOverview.php";
$pageTitle = "Currency Manager"; //I18N

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			//background delete
			//called by dataGrid
			if (isset($_GET['ID'])) {
				$ID = $_GET['ID'];				
				//check if we can delete this item (or is the currency used)
				$cm->deleteCurrency($ID);
				//catch error?
			} else {
				//error: no ID	
			}			
			break;
		case 'save':
			//add record, update record
			updateRecord();
			break;		
		case 'new':
		case 'edit':
			//frontend form for edit or insert
			printFrontend();
			break;
	}	
}
function printFrontend() {
	global $pageTitle;
	global $tpl;
	global $badgerDb;
	$widgets = new WidgetEngine($tpl);
	$widgets->addToolTipJS();
	$cm = new CurrencyManager($badgerDb);
	
	echo $tpl->getHeader($pageTitle);
	echo $widgets->addToolTipLayer();
	if (isset($_GET['ID'])) {
		//edit: load values for this ID
		$ID = $_GET['ID'];
		$currency = $cm->getCurrencyById($ID);
		$symbolValue = $currency->getSymbol();
		$langnameValue = $currency->getLongName();				
	} else {
		//new: empty values
		$ID = "new";
		$symbolValue = "";
		$langnameValue = ""; 
	}
	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$symbolLabel = $widgets->createLabel("symbol", getBadgerTranslation2('accountCurrency', 'symbol'), true);
	$symbolField = $widgets->createField("symbol", 20, $symbolValue, "", true, "text", "maxlength='3'");
	$longnameLabel = $widgets->createLabel("longname", getBadgerTranslation2('accountCurrency', 'longname'), true);
	$longnameField = $widgets->createField("longname", 20, $langnameValue, "", true, "text", "");
	$submitBtn = $widgets->createButton("submit", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif");

	//add vars to template, print site
	eval("echo \"".$tpl->getTemplate("Account/Currency")."\";");
}


function updateRecord() {
	global $redirectPageAfterSave;
	global $badgerDb;
	$cm = new CurrencyManager($badgerDb);
	
	if (isset($_POST['hiddenID'])) {
		switch ($_POST['hiddenID']) {
		case 'new':
			//add new record
			//check if $_POST['symbol'], $_POST['longName'] is set?????
			$ID = $cm->addCurrency($_POST['symbol'], $_POST['longname']);
			break;
		default:
			//update record
			$currency = $cm->getCurrencyById($_POST['hiddenID']);
			$currency->setSymbol($_POST['symbol']);
			$currency->setLongName($_POST['longname']);
			$ID = $currency->getId();
		}
		//REDIRECT
		header("Location: $redirectPageAfterSave");
	}	
}
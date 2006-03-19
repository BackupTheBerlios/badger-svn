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
require_once(BADGER_ROOT . '/modules/account/AccountManager.class.php');

$redirectPageAfterSave = "CurrencyManagerOverview.php";
$pageTitle = getBadgerTranslation2('accountCurrency','pageTitle');

$cm = new CurrencyManager($badgerDb);
$am = new AccountManager($badgerDb);

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			//background delete
			//called by dataGrid
			if (isset($_GET['ID'])) {
				$IDs = explode(",",$_GET['ID']);
						
				foreach($IDs as $ID){
					//check if we can delete this item
					$currencyCanBeDeleted = true;
					while( $account = $am->getNextAccount() ) {
						if ($account->getCurrency()->getId() == $ID) {
							$currencyCanBeDeleted = false;
						}
					}					
					if ($currencyCanBeDeleted) {
						//delete currency
						$cm->deleteCurrency($ID);
					} else {
						//error message
						echo getBadgerTranslation2('accountCurrency','currencyIsStillUsed');
					}
				}
			} else {
				echo "no ID was transmitted!";	
			}			
			break;
		case 'save':
			//add record, update record
			if (isset($_POST['hiddenID'])) {
				updateRecord();
			} else {
				header("Location: $redirectPageAfterSave");
			}
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
	global $cm;
	global $redirectPageAfterSave;
	$widgets = new WidgetEngine($tpl);
	$widgets->addToolTipJS();
	$widgets->addJSValMessages();
	
	$widgets->addNavigationHead();
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
	$legend = getBadgerTranslation2('accountCurrency', 'legend');
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$pageHeading = $pageTitle;
	
	//Fields & Labels
	$symbolLabel = $widgets->createLabel("symbol", getBadgerTranslation2('accountCurrency', 'symbol'), true);
	$symbolField = $widgets->createField("symbol", 20, $symbolValue, "", true, "text", "maxlength='3'");
	
	$longnameLabel = $widgets->createLabel("longname", getBadgerTranslation2('accountCurrency', 'longname'), false);
	$longnameField = $widgets->createField("longname", 20, $langnameValue, "", false, "text", "");
	
	//Buttons
	$submitBtn = $widgets->createButton("submit", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif", "accesskey='s'");
	$backBtn = $widgets->createButton("back", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPageAfterSave';return false;", "Widgets/back.gif");

	//add vars to template, print site
	eval("echo \"".$tpl->getTemplate("Account/Currency")."\";");
}


function updateRecord() {
	global $redirectPageAfterSave;
	global $cm;
	
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
		//$ID = $currency->getId();
	}
	//REDIRECT
	header("Location: $redirectPageAfterSave");

}
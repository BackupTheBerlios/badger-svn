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
require_once(BADGER_ROOT . '/modules/account/AccountManager.class.php');

$redirectPageAfterSave = "AccountManagerOverview.php";
$pageTitle = "Account Manager"; //I18N

$am = new AccountManager($badgerDb);

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			//background delete
			//called by dataGrid
			if (isset($_GET['ID'])) {
				$IDs = explode(",",$_GET['ID']);				
				//check if we can delete this item (or is the currency used)
				foreach($IDs as $ID){
					$am->deleteAccount($ID);
				}
				//dg should show this message!!!! ToDo
				echo "deletion was successful!";
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
	global $am;
	global $redirectPageAfterSave;
	$widgets = new WidgetEngine($tpl);
	$widgets->addToolTipJS();
	
	$widgets->addNavigationHead();
	echo $tpl->getHeader($pageTitle);
	echo $widgets->getNavigationBody();	
	
	echo $widgets->addToolTipLayer();
	if (isset($_GET['ID'])) {
		//edit: load values for this ID
		$ID = $_GET['ID'];
		$account = $am->getAccountById($ID);
		$titleValue = $account->getTitle();
		$descriptionValue = $account->getDescription();
		$lowerLimitValue = $account->getLowerLimit();
		$upperLimitValue = $account->getUpperLimit();
		$balanceValue = $account->getBalance();
		$currencyValue = $account->getCurrency();
		$targetFutureCalcDateValue = $account->getTargetFutureCalcDate();
	} else {
		//new: empty values
		$ID = "new";
		$symbolValue = "";
		$langnameValue = ""; 
	}
	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountAccount', 'title'), true);
	$titleField = $widgets->createField("title", 20, $titleValue, "", true, "text", "");
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountAccount', 'description'), false);
	$descriptionField = $widgets->createField("description", 20, $descriptionValue, "", false, "text", "");
	$lowerLimitLabel = $widgets->createLabel("lowerLimit", getBadgerTranslation2('accountAccount', 'lowerLimit'), false);
	$lowerLimitField = $widgets->createField("lowerLimit", 20, $lowerLimitValue, "", false, "text", "");
	$upperLimitLabel = $widgets->createLabel("upperLimit", getBadgerTranslation2('accountAccount', 'upperLimit'), false);
	$upperLimitField = $widgets->createField("upperLimit", 20, $upperLimitValue, "", false, "text", "");
	$balanceLabel = $widgets->createLabel("balance", getBadgerTranslation2('accountAccount', 'balance'), false);
	$balanceField = $widgets->createField("balance", 20, $balanceValue, "", false, "text", "");
	$currencyLabel = $widgets->createLabel("currency", getBadgerTranslation2('accountAccount', 'currency'), false);
	$currencyField = $widgets->createField("currency", 20, $currencyValue, "", false, "text", "");
	$targetFutureCalcDateLabel = $widgets->createLabel("targetFutureCalcDate", getBadgerTranslation2('accountAccount', 'targetFutureCalcDate'), false);
	$targetFutureCalcDateField = $widgets->createField("targetFutureCalcDate", 20, $targetFutureCalcDateValue, "", false, "text", "");
	
	
	//Buttons
	$submitBtn = $widgets->createButton("submit", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif");
	$backBtn = $widgets->createButton("back", getBadgerTranslation2('dataGrid', 'back'), "location.href=$redirectPageAfterSave", "Widgets/back.gif");

	//add vars to template, print site
	eval("echo \"".$tpl->getTemplate("Account/Account")."\";");
}


function updateRecord() {
	global $redirectPageAfterSave;
	global $am;
	
	if (isset($_POST['hiddenID'])) {
		switch ($_POST['hiddenID']) {
		case 'new':
			//add new record
			//check if $_POST['symbol'], $_POST['longName'] is set?????
			$ID = $am->addCurrency($_POST['symbol'], $_POST['longname']);
			break;
		default:
			//update record
			$account = $am->getCurrencyById($_POST['hiddenID']);
			$account->setSymbol($_POST['symbol']);
			$account->setLongName($_POST['longname']);
			$ID = $account->getId();
		}
		//REDIRECT
		header("Location: $redirectPageAfterSave");
	}	
}
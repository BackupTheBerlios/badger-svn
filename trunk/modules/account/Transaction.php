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
require_once(BADGER_ROOT . '/modules/account/Account.class.php');
require_once(BADGER_ROOT . '/modules/account/AccountManager.class.php');
require_once BADGER_ROOT . '/modules/account/FinishedTransaction.class.php';
require_once BADGER_ROOT . '/modules/account/PlannedTransaction.class.php';

$redirectPageAfterSave = "AccountOverview.php?accountID=".$_GET['accountID'];
$pageTitle = getBadgerTranslation2('accountTransaction','pageTitle');; //I18N

$am = new AccountManager($badgerDb);
$catm = new CategoryManager($badgerDb);

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			//background delete
			//called by dataGrid
			if (isset($_GET['ID']) || isset($_GET['accountID'])) {
				$IDs = explode(",",$_GET['ID']);				
				//check if we can delete this item (or is the currency used)
				foreach($IDs as $ID){
					$acc = $am->getAccountById($_GET['accountID']);
					$acc->deleteFinishedTransaction($ID);
					$acc->deletePlannedTransaction($ID);
				}
				//dg should show this message!!!! ToDo
				echo "deletion was successful!";
			} else {
				echo "no ID/accIDwas transmitted!";	
			}			
			break;
		case 'save':
			//add record, update record
			if (isset($_POST['hiddenID'])) {
				updateRecord($_GET['accountID'], $_POST['hiddenID'], $_POST['hiddenType']);
			} else {
				header("Location: $redirectPageAfterSave");
			}
			break;		
		case 'new':
		case 'edit':
			//frontend form for edit or insert
			if (isset($_GET['accountID'])) {
				$accountID = $_GET['accountID'];
				if (isset($_GET['ID'])) {
					$ID = $_GET['ID'];
					if(substr($ID,0,1)=="p") {
						$pos = strpos($ID,"_");				
						$ID = substr($ID,1,$pos-1);
						printFrontendPlanned($accountID, $ID);
					} else {
						printFrontendFinished($accountID, $ID);
					}
				} else {
					switch($_GET['type']) {
					case 'finished':
						printFrontendFinished($accountID, "new");
						break;
					case 'planned':
						printFrontendPlanned($accountID, "new");
						break;
					}
				}
			}
			break;
	}	
}
function printFrontendFinished($AccountID, $ID) {
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
	
	if($ID!="new") {
		$acc = $am->getAccountById($AccountID);
		$transactionType = "finished";
		$transaction = $acc->getFinishedTransactionById($ID);
		
		$titleValue = $transaction->getTitle();
		$descriptionValue = $transaction->getDescription();
		$valutaDateValue = is_null($tmp = $transaction->getValutaDate()) ? '' : $tmp->getFormatted();
		$amountValue = is_null($tmp = $transaction->getAmount()) ? '' : $tmp->getFormatted();
		$outsideCapitalValue = ($transaction->getOutsideCapital()==true) ? 'checked' : '';
		$transactionPartnerValue = $transaction->getTransactionPartner();
		$categoryValue = is_null($tmp = $transaction->getCategory()) ? '' : $tmp;
		$exceptionalValue = ($transaction->getExceptional()==true) ? 'checked' : '';
		$periodicalValue = ($transaction->getPeriodical()==true) ? 'checked' : '';
	} else {
		//new: empty values
		$titleValue = "";
		$descriptionValue = "";
		$valutaDateValue = "";
		$amountValue = "";
		$transactionPartnerValue = "";
		$outsideCapitalValue = "";
		$categoryValue = "NULL";
		$exceptionalValue = false;
		$periodicalValue = false;
	}

	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	$transactionType = "finished";
	$hiddenAccID = $widgets->createField("hiddenAccID", 20, $AccountID, "", false, "hidden");
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$hiddenType = $widgets->createField("hiddenType", 20, $transactionType, "", false, "hidden");
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountTransaction', 'title'), true);
	$titleField = $widgets->createField("title", 30, $titleValue, "", true, "text", "");
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountTransaction', 'description'), true);
	$descriptionField = $widgets->createField("description", 30, $descriptionValue, "", true, "text", "");
	$valutaDateLabel = $widgets->createLabel("valutaDate", getBadgerTranslation2('accountTransaction', 'valutaDate'), true);
	$valutaDateField = $widgets->createField("valutaDate", 30, $valutaDateValue, "", true, "text", "");
	$amountLabel = $widgets->createLabel("amount", getBadgerTranslation2('accountTransaction', 'amount'), true);
	$amountField = $widgets->createField("amount", 30, $amountValue, "", true, "text", "");
	$transactionPartnerLabel = $widgets->createLabel("transactionPartner", getBadgerTranslation2('accountTransaction', 'transactionPartner'), true);
	$transactionPartnerField = $widgets->createField("transactionPartner", 30, $transactionPartnerValue, "", true, "text", "");
	$outsideCapitalLabel = $widgets->createLabel("outsideCapital", getBadgerTranslation2('accountTransaction', 'outsideCapital'), true);
	$outsideCapitalField = $widgets->createField("outsideCapital", 30, "on", "", true, "checkbox", $outsideCapitalValue);
	$categoryLabel = $widgets->createLabel("category", getBadgerTranslation2('accountTransaction', 'category'), true);
	//$categoryField = $widgets->createField("category", 30, $categoryValue, "", true, "text", "");
	$categoryField = $widgets->createSelectField("category", getCategorySelectArray(), $categoryValue, "", false);
	$exceptionalLabel = $widgets->createLabel("exceptional", getBadgerTranslation2('accountTransaction', 'exceptional'), true);
	$exceptionalField = $widgets->createField("exceptional", 30, "on", "", true, "checkbox", $exceptionalValue);
	$periodicalLabel = $widgets->createLabel("periodical", getBadgerTranslation2('accountTransaction', 'periodical'), true);
	$periodicalField = $widgets->createField("periodical", 30, "on", "", true, "checkbox", $periodicalValue);

	//Buttons
	$submitBtn = $widgets->createButton("submit", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif");
	$backBtn = $widgets->createButton("back", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPageAfterSave';return false;", "Widgets/back.gif");

	//add vars to template, print site
	$pageHeading = $pageTitle;
	eval("echo \"".$tpl->getTemplate("Account/FinishedTransaction")."\";");
}

function printFrontendPlanned($AccountID, $ID) {
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
	
	$transactionType = "planned";
	if($ID!="new") {
		$acc = $am->getAccountById($AccountID);		
		$transaction = $acc->getPlannedTransactionById($ID);
		
		$titleValue = $transaction->getTitle();
		$descriptionValue = $transaction->getDescription();
		$beginDateValue = is_null($tmp = $transaction->getBeginDate()) ? '' : $tmp->getFormatted();
		$endDateValue = is_null($tmp = $transaction->getEndDate()) ? '' : $tmp->getFormatted();
		$amountValue = is_null($tmp = $transaction->getAmount()) ? '' : $tmp->getFormatted();
		$outsideCapitalValue = is_null($tmp = $transaction->getOutsideCapital()) ? '' : $tmp->getFormatted();
		$transactionPartnerValue = $transaction->getTransactionPartner();
		$categoryValue = is_null($tmp = $transaction->getCategory()) ? '' : $tmp;
		$repeatUnitValue = $transaction->getRepeatUnit();
    	$repeatFrequencyValue = $transaction->getRepeatFrequency();
	} else {
		//new: empty values
		$titleValue = "";
		$descriptionValue = "";
		$beginDateValue = "";
		$endDateValue = "";
		$amountValue = "";
		$outsideCapitalValue = "";
		$transactionPartnerValue = "";		
		$categoryValue = "";
		$repeatUnitValue = "";
    	$repeatFrequencyValue = "";
	}

	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	$hiddenAccID = $widgets->createField("hiddenAccID", 20, $AccountID, "", false, "hidden");
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$hiddenType = $widgets->createField("hiddenType", 20, $transactionType, "", false, "hidden");
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountTransaction', 'title'), true);
	$titleField = $widgets->createField("title", 30, $titleValue, "", true, "text", "");	
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountTransaction', 'description'), true);
	$descriptionField = $widgets->createField("description", 30, $descriptionValue, "", true, "text", "");
	$beginDateLabel = $widgets->createLabel("beginDate", getBadgerTranslation2('accountTransaction', 'beginDate'), true);
	$beginDateField = $widgets->createField("beginDate", 30, $beginDateValue, "", true, "text", "");
	$endDateLabel = $widgets->createLabel("endDate", getBadgerTranslation2('accountTransaction', 'endDate'), true);
	$endDateField = $widgets->createField("endDate", 30, $endDateValue, "", true, "text", "");
	$amountLabel = $widgets->createLabel("amount", getBadgerTranslation2('accountTransaction', 'amount'), true);
	$amountField = $widgets->createField("amount", 30, $amountValue, "", true, "text", "");
	$transactionPartnerLabel = $widgets->createLabel("transactionPartner", getBadgerTranslation2('accountTransaction', 'transactionPartner'), true);
	$transactionPartnerField = $widgets->createField("transactionPartner", 30, $transactionPartnerValue, "", true, "text", "");
	$outsideCapitalLabel = $widgets->createLabel("outsideCapital", getBadgerTranslation2('accountTransaction', 'outsideCapital'), true);
	$outsideCapitalField = $widgets->createField("outsideCapital", 30, "on", "", true, "checkbox", $outsideCapitalValue);
	
	$categoryLabel = $widgets->createLabel("category", getBadgerTranslation2('accountTransaction', 'category'), true);
	//$categoryField = $widgets->createField("category", 30, $categoryValue, "", true, "text", "");
	$categoryField = $widgets->createSelectField("category", getCategorySelectArray(), $categoryValue, "", false);
	$repeatUnitLabel = $widgets->createLabel("repeatUnit", getBadgerTranslation2('accountTransaction', 'repeatUnit'), true);
	$repeatUnitField = $widgets->createField("repeatUnit", 30, $repeatUnitValue, "", true, "text", "");
	$repeatFrequencyLabel = $widgets->createLabel("repeatFrequency", getBadgerTranslation2('accountTransaction', 'repeatFrequency'), true);
	$repeatFrequencyField = $widgets->createField("repeatFrequency", 30, $repeatFrequencyValue, "", true, "text", "");
	
	//Buttons
	$submitBtn = $widgets->createButton("submit", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif");
	$backBtn = $widgets->createButton("back", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPageAfterSave';return false;", "Widgets/back.gif");

	//add vars to template, print site
	$pageHeading = $pageTitle;
	eval("echo \"".$tpl->getTemplate("Account/PlannedTransaction")."\";");
}

function updateRecord($accountID, $ID, $transactionType) {
	global $redirectPageAfterSave;
	global $am;
	global $catm;
	
	$account = $am->getAccountById($accountID);
	if (isset($_POST['category']) && $_POST['category']!="NULL") {
		$category = $catm->getCategoryById($_POST['category']);
	} else {
		$category = NULL;
	}
	switch ($ID) {
	case 'new':
		//add new record
		switch ($transactionType) {
		case 'planned':
			$ID = $account->addPlannedTransaction(
					$_POST['title'],
					new Amount($_POST['amount'], true),
					$_POST['repeatUnit'],
					$_POST['repeatFrequency'],
					new Date($_POST['beginDate'], true),
					new Date($_POST['endDate'], true), //= null,
					$_POST['description'], // = null,
					$_POST['transactionPartner'], // = null,
					$category, // = null,
					($_POST['outsideCapital']=="on")?true:false); // = null
			break;
		case 'finished':
			$ID = $account->addFinishedTransaction(
				new Amount($_POST['amount'], true),
				$_POST['title'], // = null,
				$_POST['description'], // = null,
				new Date($_POST['valutaDate'], true), // = null,
				$_POST['transactionPartner'], // = null,
				$category, // = null,
				($_POST['outsideCapital']=="on")?true:false, // = null
				($_POST['exceptional']=="on")?true:false, // = null,
				($_POST['periodical']=="on")?true:false); //= null 
			break;
		}
		break;
	default:
		//update record
		switch ($transactionType) {
		case 'planned':
			$transaction = $account->getPlannedTransactionById($ID);
			$transaction->setTitle($_POST['title']);
			$transaction->setDescription($_POST['description']);
			$transaction->setBeginDate(new Date($_POST['beginDate'], true));
			$transaction->setEndDate(new Date($_POST['endDate'], true));
			$transaction->setAmount(new Amount($_POST['amount'], true));
			$transaction->setOutsideCapital((isset($_POST['outsideCapital']) && $_POST['outsideCapital']=="on")?true:false);
			$transaction->setTransactionPartner($_POST['transactionPartner']);
			$transaction->setCategory($category);
			$transaction->setRepeatUnit($_POST['repeatUnit']);
	    	$transaction->setRepeatFrequency($_POST['repeatFrequency']);
			break;
		case 'finished':
			$transaction = $account->getFinishedTransactionById($ID);
			$transaction->setTitle($_POST['title']);
			$transaction->setDescription($_POST['description']);
			$transaction->setValutaDate(new Date($_POST['valutaDate'], true));
			$transaction->setAmount(new Amount($_POST['amount'], true));
			$transaction->setOutsideCapital((isset($_POST['outsideCapital']) && $_POST['outsideCapital']=="on")?true:false);
			$transaction->setTransactionPartner($_POST['transactionPartner']);
			$transaction->setCategory($category); // ID
			$transaction->setExceptional((isset($_POST['exceptional']) && $_POST['exceptional']=="on")?true:false ); //checkbox
			$transaction->setPeriodical((isset($_POST['periodical']) && $_POST['periodical']=="on")?true:false ); //checkbox
			break;
		}

	}
	//REDIRECT
	header("Location: $redirectPageAfterSave");

}

function getCategorySelectArray() {
	global $badgerDb;
	$cm = new CategoryManager($badgerDb);
	$order = array ( 
	array(
       'key' => 'title',
       'dir' => 'asc'
       )
 	);
	$cm->setOrder($order);
 
 	$parentCats = array();
 	$parentCats['NULL'] = "";
	while ($cat = $cm->getNextCategory()) { 
		$parentCats[$cat->getId()] = $cat->getTitle();
	};
	return $parentCats;
}
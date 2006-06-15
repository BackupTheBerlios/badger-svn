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
define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once(BADGER_ROOT . '/modules/account/Account.class.php');
require_once(BADGER_ROOT . '/modules/account/AccountManager.class.php');
require_once BADGER_ROOT . '/modules/account/FinishedTransaction.class.php';
require_once BADGER_ROOT . '/modules/account/PlannedTransaction.class.php';
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

$redirectPage = "";
$pageTitle = getBadgerTranslation2('accountTransaction','pageTitle');

$am = new AccountManager($badgerDb);
$catm = new CategoryManager($badgerDb);

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			deleteRecord();		
			break;
			
		case 'save':
			$accountID = $_POST['hiddenAccID'];
			if (isset($_POST['hiddenID'])) {
				//add record, update record
				updateRecord($accountID, $_POST['hiddenID'], $_POST['hiddenType']);							
				$redirectPage = getRedirectPage($accountID);
				header("Location: $redirectPage");
			}
			break;

		case 'new':
		case 'edit':
			//frontend form for edit or insert
			
			if (isset($_GET['accountID'])) {
				// account was selected previously
				$accountID = $_GET['accountID'];
				$redirectPage = getRedirectPage($accountID);
			} else {
				// no account was selected previously
				// -> user has to choose one
				$accountID = "choose";	
			}
			
			if (isset($_GET['ID'])) {
				$ID = $_GET['ID'];
				
				//check if ID is from planned or finished transaction
				if(substr($ID, 0, 1) == "p") {
					$pos = strpos($ID, "_");				
					$ID = substr($ID, 1, $pos - 1);
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
			break;
	}	
}

//background delete
//called by dataGrid
function deleteRecord() {
	global $am;
	
	if (isset($_GET['ID']) || isset($_GET['accountID'])) {
		$IDs = explode(",",$_GET['ID']); 	
					
		//check if we can delete this item
		$acc = $am->getAccountById($_GET['accountID']);
		
		$processedPlannedTransactions = array();
		
		foreach($IDs as $ID){
			if(substr($ID,0,1)=="p") {
				$pos = strpos($ID,"_");				
				$ID = substr($ID,1,$pos-1);

				//Prevent try to delete one plannedTransaction several times if it was expanded to
				//more than one occurence
				if (array_key_exists($ID, $processedPlannedTransactions)) {
					continue;
				} else {
					$processedPlannedTransactions[$ID] = true;
				}

				$acc->deletePlannedTransaction($ID);
			} else {
				$acc->deleteFinishedTransaction($ID);
			}
		}
		echo "";
	} else {
		echo "no ID/accID was transmitted!";	
	}	
	
}

function printFrontendFinished($AccountID, $ID) {
	global $pageTitle;
	global $tpl;
	global $am;
	global $redirectPage;
	$widgets = new WidgetEngine($tpl);
	$widgets->addToolTipJS();
	$widgets->addCalendarJS();
	$widgets->addJSValMessages();
	
	$widgets->addNavigationHead();
	echo $tpl->getHeader($pageTitle);
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
		$categoryValue = is_null($tmp = $transaction->getCategory()) ? 'NULL' : $tmp->getId();
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
	if($AccountID=="choose") {
		$AccountLabel = $widgets->createLabel("hiddenAccID", getBadgerTranslation2('accountTransaction', 'Account'), true);
		$hiddenAccID = $widgets->createSelectField("hiddenAccID", getAccountsSelectArray(), $AccountID, "", false, "style='width: 213px;'");
	} else {
		$AccountLabel = "";
		$hiddenAccID = $widgets->createField("hiddenAccID", 20, $AccountID, "", false, "hidden");
	}
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$hiddenType = $widgets->createField("hiddenType", 20, $transactionType, "", false, "hidden");
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountTransaction', 'title'), true);
	$titleField = $widgets->createField("title", 30, $titleValue, "", true, "text", "");
	
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountTransaction', 'description'), false);
	$descriptionField = $widgets->createField("description", 30, $descriptionValue, "", false, "text", "");
	
	$valutaDateLabel = $widgets->createLabel("valutaDate", getBadgerTranslation2('accountTransaction', 'valutaDate'), true);
	$valutaDateField = $widgets->addDateField("valutaDate", $valutaDateValue);
	
	$amountLabel = $widgets->createLabel("amount", getBadgerTranslation2('accountTransaction', 'amount'), true);
	$amountField = $widgets->createField("amount", 30, $amountValue, "", true, "text", "");
	
	$transactionPartnerLabel = $widgets->createLabel("transactionPartner", getBadgerTranslation2('accountTransaction', 'transactionPartner'), false);
	$transactionPartnerField = $widgets->createField("transactionPartner", 30, $transactionPartnerValue, "", false);
	
	$outsideCapitalLabel = $widgets->createLabel("outsideCapital", getBadgerTranslation2('accountTransaction', 'outsideCapital'), false);
	$outsideCapitalField = $widgets->createField("outsideCapital", 30, "on", "", false, "checkbox", $outsideCapitalValue);
	$outsideToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "outsideCapitalToolTip"));
	
	$categoryLabel = $widgets->createLabel("category", getBadgerTranslation2('accountTransaction', 'category'), false, "style='width: 213px;'");
	$categoryField = $widgets->createSelectField("category", getCategorySelectArray(), $categoryValue, "", false, "style='width: 213px;'");
	
	$exceptionalLabel = $widgets->createLabel("exceptional", getBadgerTranslation2('accountTransaction', 'exceptional'), false);
	$exceptionalField = $widgets->createField("exceptional", 30, "on", "", false, "checkbox", $exceptionalValue);
	$exceptionalToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "ExceptionalToolTip"));
	
	$periodicalLabel = $widgets->createLabel("periodical", getBadgerTranslation2('accountTransaction', 'periodical'), false);
	$periodicalField = $widgets->createField("periodical", 30, "on", "", false, "checkbox", $periodicalValue);
	$periodicalToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "periodicalToolTip"));

	//Buttons
	$submitBtn = $widgets->createButton("submitBtn", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif", "accesskey='s'");
	if($redirectPage) {
		$backBtn = $widgets->createButton("backBtn", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPage';return false;", "Widgets/back.gif");
	} else { $backBtn=""; };
	//add vars to template, print site
	$pageHeading = getBadgerTranslation2('accountTransaction', 'headingTransactionFinished');
	eval("echo \"".$tpl->getTemplate("Account/FinishedTransaction")."\";");
}

function printFrontendPlanned($AccountID, $ID) {
	global $pageTitle;
	global $tpl;
	global $am;
	global $redirectPage;
	$widgets = new WidgetEngine($tpl);
	$widgets->addToolTipJS();
	$widgets->addCalendarJS();
	$widgets->addJSValMessages();

	$widgets->addNavigationHead();
	echo $tpl->getHeader($pageTitle);
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
		$outsideCapitalValue = is_null($tmp = $transaction->getOutsideCapital()) ? '' : $tmp;
		$transactionPartnerValue = $transaction->getTransactionPartner();
		$categoryValue = is_null($tmp = $transaction->getCategory()) ? '' : $tmp->getId();
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
    	$repeatFrequencyValue = "1";
	}

	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	if($AccountID=="choose") {
		$AccountLabel = $widgets->createLabel("hiddenAccID", getBadgerTranslation2('accountTransaction', 'Account'), true);
		$hiddenAccID = $widgets->createSelectField("hiddenAccID", getAccountsSelectArray(), $AccountID, "", false, "style='width: 213px;'");
	} else {
		$AccountLabel = "";
		$hiddenAccID = $widgets->createField("hiddenAccID", 20, $AccountID, "", false, "hidden");
	}
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$hiddenType = $widgets->createField("hiddenType", 20, $transactionType, "", false, "hidden");
	
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountTransaction', 'title'), true);
	$titleField = $widgets->createField("title", 30, $titleValue, "", true, "text", "");	
	
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountTransaction', 'description'), false);
	$descriptionField = $widgets->createField("description", 30, $descriptionValue, "", false, "text", "");
	
	$beginDateLabel = $widgets->createLabel("beginDate", getBadgerTranslation2('accountTransaction', 'beginDate'), true);
	$beginDateField = $widgets->addDateField("beginDate", $beginDateValue);
	
	$endDateLabel = $widgets->createLabel("endDate", getBadgerTranslation2('accountTransaction', 'endDate'), true);
	$endDateField = $widgets->addDateField("endDate", $endDateValue);

	$amountLabel = $widgets->createLabel("amount", getBadgerTranslation2('accountTransaction', 'amount'), true);
	$amountField = $widgets->createField("amount", 30, $amountValue, "", true, "text", "");

	$transactionPartnerLabel = $widgets->createLabel("transactionPartner", getBadgerTranslation2('accountTransaction', 'transactionPartner'), false);
	$transactionPartnerField = $widgets->createField("transactionPartner", 30, $transactionPartnerValue, "", false, "text", "");

	$outsideCapitalLabel = $widgets->createLabel("outsideCapital", getBadgerTranslation2('accountTransaction', 'outsideCapital'), false);
	$outsideCapitalField = $widgets->createField("outsideCapital", 30, "on", "", false, "checkbox", $outsideCapitalValue);
	
	$categoryLabel = $widgets->createLabel("category", getBadgerTranslation2('accountTransaction', 'category'), false);
	$categoryField = $widgets->createSelectField("category", getCategorySelectArray(), $categoryValue, "", false, "style='width: 213px;'");
	
	$repeatUnitLabel = $widgets->createLabel("repeatUnit", getBadgerTranslation2('accountTransaction', 'repeatFrequency'), true);
	$repeatUnitField = $widgets->createSelectField("repeatUnit", getIntervalUnitsArray(), $repeatUnitValue, "", true, "style='width: 104px;'");

	$everyLabel = getBadgerTranslation2('intervalUnits', 'every');
	$repeatFrequencyField = $widgets->createField("repeatFrequency", 1, $repeatFrequencyValue, "", true, "text", "");
	
	//Buttons
	$submitBtn = $widgets->createButton("submitBtn", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif", "accesskey='s'");
	if($redirectPage) {
		$backBtn = $widgets->createButton("backBtn", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPage';return false;", "Widgets/back.gif");
	} else { $backBtn=""; };
	
	//add vars to template, print site
	$pageHeading = getBadgerTranslation2('accountTransaction', 'headingTransactionPlanned');
	eval("echo \"".$tpl->getTemplate("Account/PlannedTransaction")."\";");
}

function updateRecord($accountID, $ID, $transactionType) {
	global $redirectPage;
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
			$tmp = trim($_POST['endDate']);
			$endDate = empty($tmp) ? null : new Date($tmp, true);
		
			$newPlannedTransaction = $account->addPlannedTransaction(
					$_POST['title'],
					new Amount($_POST['amount'], true),
					$_POST['repeatUnit'],
					$_POST['repeatFrequency'],
					new Date($_POST['beginDate'], true),
					$endDate, //= null,
					$_POST['description'], // = null,
					$_POST['transactionPartner'], // = null,
					$category, // = null,
					(isset($_POST['outsideCapital']) && $_POST['outsideCapital']=="on")?true:false); // = null

			transferFinishedTransactions($account, $newPlannedTransaction);
			break;
			
		case 'finished':
			$ID = $account->addFinishedTransaction(
				new Amount($_POST['amount'], true),
				$_POST['title'], // = null,
				$_POST['description'], // = null,
				new Date($_POST['valutaDate'], true), // = null,
				$_POST['transactionPartner'], // = null,
				$category, // = null,
				(isset($_POST['outsideCapital']) && $_POST['outsideCapital']=="on")?true:false, // = null
				(isset($_POST['exceptional']) && $_POST['exceptional']=="on")?true:false, // = null,
				(isset($_POST['periodical']) && $_POST['periodical']=="on")?true:false); //= null 
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
			$transaction->setCategory($category);
			$transaction->setExceptional((isset($_POST['exceptional']) && $_POST['exceptional']=="on")?true:false ); //checkbox
			$transaction->setPeriodical((isset($_POST['periodical']) && $_POST['periodical']=="on")?true:false ); //checkbox
			break;
		}
	}
	//REDIRECT
	header("Location: $redirectPage");

}

function getAccountsSelectArray() {
	global $badgerDb;
	$am = new AccountManager($badgerDb);
	$order = array ( 
	array(
       'key' => 'title',
       'dir' => 'asc'
       )
 	);
	$am->setOrder($order);

	$Accounts = array();
	while ($account = $am->getNextAccount()) { 
		$Accounts[$account->getId()] = $account->getTitle();
	};
	return $Accounts;
}

function getIntervalUnitsArray(){
	$units = array(
		'day'	=> getBadgerTranslation2('intervalUnits','day'), 
		'week'	=> getBadgerTranslation2('intervalUnits','week'),
		'month'	=> getBadgerTranslation2('intervalUnits','month'),
		'year'	=> getBadgerTranslation2('intervalUnits','year')
	);
	return $units;
};

function getRedirectPage($accountId) {
	if (isset($_GET['backTo'])) {
		if ($_GET['backTo'] === 'planned') {
			return 'AccountOverviewPlanned.php?accountID=' . $accountId;
		}
	}
	
	return 'AccountOverview.php?accountID=' . $accountId;
}

function transferFinishedTransactions ($account, $plannedTransaction) {
	$now = new Date();

	$date = new Date($plannedTransaction->getBeginDate());
	$dayOfMonth = $date->getDay();
	
	//While we are before now and the end date of this transaction
	while(
		!$date->after($now)
		&& !$date->after(is_null($tmp = $plannedTransaction->getEndDate()) ? new Date('9999-12-31') : $tmp)
	){

		$account->addFinishedTransaction(
			$plannedTransaction->getAmount(),
			$plannedTransaction->getTitle(),
			$plannedTransaction->getDescription(),
			new Date($date),
			$plannedTransaction->getTransactionPartner(),
			$plannedTransaction->getCategory(),
			$plannedTransaction->getOutsideCapital(),
			false,
			true
		);

		//do the date calculation
		switch ($plannedTransaction->getRepeatUnit()){
			case 'day': 
				$date->addSeconds($plannedTransaction->getRepeatFrequency() * 24 * 60 * 60);
				break;
				
			case 'week':
				$date->addSeconds($plannedTransaction->getRepeatFrequency() * 7 * 24 * 60 * 60);
				break;
				
			case 'month':
				//Set the month
				$date = new Date(Date_Calc::endOfMonthBySpan($plannedTransaction->getRepeatFrequency(), $date->getMonth(), $date->getYear(), '%Y-%m-%d'));
				//And count back as far as the last valid day of this month
				while($date->getDay() > $dayOfMonth){
					$date->subtractSeconds(24 * 60 * 60);
				}
				break; 
			
			case 'year':
				$newYear = $date->getYear() + $plannedTransaction->getRepeatFrequency();
				if (
					$dayOfMonth == 29
					&& $date->getMonth() == 2
					&& !Date_Calc::isLeapYear($newYear)
				) {
					$date->setDay(28);
				} else {
					$date->setDay($dayOfMonth);
				}
				
				$date->setYear($newYear);
				break;
			
			default:
				throw new BadgerException('Account', 'IllegalRepeatUnit', $plannedTransaction->getRepeatUnit());
				exit;
		}
	}
}
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
	switch (getGPC($_GET, 'action')) {
		case 'delete':
			deleteRecord();		
			break;
			
		case 'save':
			$accountID = getGPC($_POST, 'hiddenAccID', 'integer');
			if (isset($_POST['hiddenID'])) {
				//add record, update record
				updateRecord($accountID, getGPC($_POST, 'hiddenID'), getGPC($_POST, 'hiddenType'));							
				$redirectPage = getRedirectPage($accountID);
				header("Location: $redirectPage");
			}
			break;

		case 'new':
		case 'edit':
			//frontend form for edit or insert
			
			if (isset($_GET['accountID'])) {
				// account was selected previously
				$accountID = getGPC($_GET, 'accountID', 'integer');
				$redirectPage = getRedirectPage($accountID);
			} else {
				// no account was selected previously
				// -> user has to choose one
				$accountID = "choose";	
			}
			
			if (isset($_GET['ID'])) {
				$ID = getGPC($_GET, 'ID');
				
				//check if ID is from planned or finished transaction
				if(substr($ID, 0, 1) == "p") {
					list($plannedTransactionId, $finishedTransactionId) = explode('_', substr($ID, 1));
					settype($plannedTransactionId, 'integer');
					settype($finishedTransactionId, 'integer');
					printFrontendPlanned($accountID, $plannedTransactionId, $finishedTransactionId);
				} else {
					settype($ID, 'integer');
					printFrontendFinished($accountID, $ID);
				}
			} else {
				
				switch(getGPC($_GET, 'type')) {
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
	
	if (isset($_GET['ID']) && isset($_GET['accountID'])) {
		$IDs = getGPC($_GET, 'ID', 'stringList'); 	
					
		//check if we can delete this item
		$acc = $am->getAccountById(getGPC($_GET, 'accountID', 'integer'));
		
		$processedPlannedTransactions = array();
		
		foreach($IDs as $ID){
			if(substr($ID,0,1)=="p") {
				list($plannedTransactionId, $finishedTransactionId) = explode('_', substr($ID, 1));
				settype($plannedTransactionId, 'integer');

				if ($finishedTransactionId == 'X') {
					//Prevent try to delete one plannedTransaction several times if it was expanded to
					//more than one occurence
					if (array_key_exists($plannedTransactionId, $processedPlannedTransactions)) {
						continue;
					} else {
						$processedPlannedTransactions[$plannedTransactionId] = true;
					}
	
					$acc->deletePlannedTransaction($plannedTransactionId);
				} else {
					settype($finishedTransactionId, 'integer');
					$acc->deleteFinishedTransaction($finishedTransactionId);
				}
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
	$tpl->addJavaScript("js/prototype.js");
	$tpl->addOnLoadEvent("Form.focusFirstElement('mainform')");
	
	$widgets->addNavigationHead();
	echo $tpl->getHeader($pageTitle);
	echo $widgets->addToolTipLayer();
	
	$now = new Date();
	
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
		$valutaDateValue = $now->getFormatted();
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

	$

	//Buttons
	$submitBtn = $widgets->createButton("submitBtn", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif", "accesskey='s'");
	if($redirectPage) {
		$backBtn = $widgets->createButton("backBtn", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPage';return false;", "Widgets/back.gif");
	} else { $backBtn=""; };
	//add vars to template, print site
	$pageHeading = getBadgerTranslation2('accountTransaction', 'headingTransactionFinished');
	eval("echo \"".$tpl->getTemplate("Account/FinishedTransaction")."\";");
}

function printFrontendPlanned($AccountID, $plannedTransactionId, $finishedTransactionId = null) {
	global $pageTitle;
	global $tpl;
	global $am;
	global $redirectPage;
	$widgets = new WidgetEngine($tpl);
	$widgets->addToolTipJS();
	$widgets->addCalendarJS();
	$widgets->addJSValMessages();

	$tpl->addJavaScript("js/prototype.js");
	$tpl->addOnLoadEvent("Form.focusFirstElement('mainform')");

	$widgets->addNavigationHead();
	echo $tpl->getHeader($pageTitle);
	echo $widgets->addToolTipLayer();
	
	$now = new Date();
	
	$transactionType = "planned";
	if($plannedTransactionId != "new") {
		$acc = $am->getAccountById($AccountID);		
		$transaction = $acc->getPlannedTransactionById($plannedTransactionId);
		
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
		$beginDateValue = $now->getFormatted();
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
	$hiddenID = $widgets->createField("hiddenID", 20, $plannedTransactionId, "", false, "hidden");
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
	$outsideToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "outsideCapitalToolTip"));
	
	$categoryLabel = $widgets->createLabel("category", getBadgerTranslation2('accountTransaction', 'category'), false);
	$categoryField = $widgets->createSelectField("category", getCategorySelectArray(), $categoryValue, "", false, "style='width: 213px;'");
	
	$repeatUnitLabel = $widgets->createLabel("repeatUnit", getBadgerTranslation2('accountTransaction', 'repeatFrequency'), true);
	$repeatUnitField = $widgets->createSelectField("repeatUnit", getIntervalUnitsArray(), $repeatUnitValue, "", true, "style='width: 104px;'");

	$everyLabel = getBadgerTranslation2('intervalUnits', 'every');
	$repeatFrequencyField = $widgets->createField("repeatFrequency", 1, $repeatFrequencyValue, "", true, "text", "");
	
	if (
		!is_null($finishedTransactionId)
		&& $finishedTransactionId != 'X'
	) {
		$hiddenFinishedTransactionID = $widgets->createField('hiddenFinishedTransactionID', 20, $finishedTransactionId, '', false, 'hidden');
		$rangeLabel = getBadgerTranslation2('accountTransaction', 'range');
		$rangeUnit = getBadgerTranslation2('accountTransaction', 'rangeUnit');
		
		$rangeAllField = $widgets->createField('range', null, 'all', '', false, 'radio', 'checked="checked"');
		$rangeAllLabel = $widgets->createLabel('range', getBadgerTranslation2('accountTransaction', 'rangeAll'));
		$rangeThisField = $widgets->createField('range', null, 'this', '', false, 'radio');
		$rangeThisLabel = $widgets->createLabel('range', getBadgerTranslation2('accountTransaction', 'rangeThis'));
		$rangePreviousField = $widgets->createField('range', null, 'previous', '', false, 'radio');
		$rangePreviousLabel = $widgets->createLabel('range', getBadgerTranslation2('accountTransaction', 'rangePrevious'));
		$rangeFollowingField = $widgets->createField('range', null, 'following', '', false, 'radio');
		$rangeFollowingLabel = $widgets->createLabel('range', getBadgerTranslation2('accountTransaction', 'rangeFollowing'));
		
		//$deleteBtn = $widgets->createButton('deleteBtn', getBadgerTranslation2('dataGrid', 'delete'), 'submit', 'Widgets/cancel.gif', "accesskey='d'");
		$deleteBtn = '';
	} else {
		$hiddenFinishedTransactionID = '';
		$rangeLabel = '';
		$rangeUnit = '';
		$rangeAllField = $widgets->createField('range', 20, 'all', '', false, 'hidden');
		$rangeAllLabel = '';
		$rangeThisField = '';
		$rangeThisLabel = '';
		$rangePreviousField = '';
		$rangePreviousLabel = '';
		$rangeFollowingField = '';
		$rangeFollowingLabel = '';
		$deleteBtn = '';
	}

	//Buttons
	$submitBtn = $widgets->createButton("submitBtn", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif", "accesskey='s'");
	if($redirectPage) {
		$backBtn = $widgets->createButton("backBtn", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPage';return false;", "Widgets/back.gif");
	} else {
		$backBtn = '';
	}
	
	//add vars to template, print site
	$pageHeading = getBadgerTranslation2('accountTransaction', 'headingTransactionPlanned');
	eval("echo \"".$tpl->getTemplate("Account/PlannedTransaction")."\";");
}

function updateRecord($accountID, $ID, $transactionType) {
	global $am;
	global $catm;
	global $us;
	
	$account = $am->getAccountById($accountID);
	if (isset($_POST['category']) && getGPC($_POST, 'category') != "NULL") {
		$category = $catm->getCategoryById(getGPC ($_POST, 'category', 'integer'));
	} else {
		$category = NULL;
	}
	switch ($ID) {
	case 'new':
		//add new record
		switch ($transactionType) {
			case 'planned':
				$tmp = trim(getGPC($_POST, 'endDate'));
				$endDate = empty($tmp) ? null : new Date($tmp, true);
			
				$newPlannedTransaction = $account->addPlannedTransaction(
					getGPC($_POST, 'title'),
					getGPC($_POST, 'amount', 'AmountFormatted'),
					getGPC($_POST, 'repeatUnit'),
					getGPC($_POST, 'repeatFrequency', 'integer'),
					getGPC($_POST, 'beginDate', 'DateFormatted'),
					$endDate, //= null,
					getGPC($_POST, 'description'), // = null,
					getGPC($_POST, 'transactionPartner'), // = null,
					$category, // = null,
					getGPC($_POST, 'outsideCapital', 'checkbox') // = null
				);
	
				$newPlannedTransaction->expand(new Date('1000-01-01'), getTargetFutureCalcDate());
				break;
				
			case 'finished':
				$ID = $account->addFinishedTransaction(
					getGPC($_POST, 'amount', 'AmountFormatted'),
					getGPC($_POST, 'title'), // = null,
					getGPC($_POST, 'description'), // = null,
					getGPC($_POST, 'valutaDate', 'DateFormatted'), // = null,
					getGPC($_POST, 'transactionPartner'), // = null,
					$category, // = null,
					getGPC($_POST, 'outsideCapital', 'checkbox'), // = null
					getGPC($_POST, 'exceptional', 'checkbox'), // = null,
					getGPC($_POST, 'periodical', 'checkbox') //= null
				); 
				break;
		}
		break;
	default:
		//update record
		
		settype($ID, 'integer');
		switch ($transactionType) {
			case 'planned':
				$transaction = $account->getPlannedTransactionById($ID);

				$range = getGPC($_POST, 'range');
				if ($range == 'previous') {
					$finishedTransaction = $account->getFinishedTransactionById(getGPC($_POST, 'hiddenFinishedTransactionID', 'integer'));
					$transaction->setUpdateMode(PlannedTransaction::UPDATE_MODE_PREVIOUS, $finishedTransaction->getValutaDate());
				} else if ($range == 'following') {
					$finishedTransaction = $account->getFinishedTransactionById(getGPC($_POST, 'hiddenFinishedTransactionID', 'integer'));
					$transaction->setUpdateMode(PlannedTransaction::UPDATE_MODE_FOLLOWING, $finishedTransaction->getValutaDate());
				}
				
				if ($range != 'this') {
					$transaction->setTitle(getGPC($_POST, 'title'));
					$transaction->setDescription(getGPC($_POST, 'description'));
					$transaction->setBeginDate(getGPC($_POST, 'beginDate', 'DateFormatted'));
					$transaction->setEndDate(($tmp = getGPC($_POST, 'endDate')) ? new Date($tmp, true) : null);
					$transaction->setAmount(getGPC($_POST, 'amount', 'AmountFormatted'));
					$transaction->setOutsideCapital(getGPC($_POST, 'outsideCapital', 'checkbox'));
					$transaction->setTransactionPartner(getGPC($_POST, 'transactionPartner'));
					$transaction->setCategory($category);
					$transaction->setRepeatUnit(getGPC($_POST, 'repeatUnit'));
			    	$transaction->setRepeatFrequency(getGPC($_POST, 'repeatFrequency', 'integer'));

					$transaction->deleteOldPlannedTransactions(new Date('9999-12-31'), true);					

					$targetFutureCalcDate = getTargetFutureCalcDate();
					$transaction->expand(new Date('1000-01-01'), $targetFutureCalcDate);
				} else {
					$transaction = $account->getFinishedTransactionById(getGPC($_POST, 'hiddenFinishedTransactionID', 'integer'));
					$transaction->setTitle(getGPC($_POST, 'title'));
					$transaction->setDescription(getGPC($_POST, 'description'));
					$transaction->setAmount(getGPC($_POST, 'amount', 'AmountFormatted'));
					$transaction->setOutsideCapital(getGPC($_POST, 'outsideCapital', 'checkbox'));
					$transaction->setTransactionPartner(getGPC($_POST, 'transactionPartner'));
					$transaction->setCategory($category);
					$transaction->setPeriodical(false);
					$transaction->setPlannedTransaction(null);
				}
				break;
				
			case 'finished':
				$transaction = $account->getFinishedTransactionById($ID);
				$transaction->setTitle(getGPC($_POST, 'title'));
				$transaction->setDescription(getGPC($_POST, 'description'));
				$transaction->setValutaDate(getGPC($_POST, 'valutaDate', 'DateFormatted'));
				$transaction->setAmount(getGPC($_POST, 'amount', 'AmountFormatted'));
				$transaction->setOutsideCapital(getGPC($_POST, 'outsideCapital', 'checkbox'));
				$transaction->setTransactionPartner(getGPC($_POST, 'transactionPartner'));
				$transaction->setCategory($category);
				$transaction->setExceptional(getGPC($_POST, 'exceptional', 'checkbox')); //checkbox
				$transaction->setPeriodical(getGPC($_POST, 'periodical', 'checkbox')); //checkbox
				break;
		}
	}
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
		if (getGPC($_GET, 'backTo') === 'planned') {
			return 'AccountOverviewPlanned.php?accountID=' . $accountId;
		}
	}
	
	return 'AccountOverview.php?accountID=' . $accountId;
}
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
require_once BADGER_ROOT . '/core/navi/naviTools.php';
require_once BADGER_ROOT . '/core/Translation2/translationTools.php';

$redirectPageAfterSave = "AccountManagerOverview.php";
$pageTitle = getBadgerTranslation2('accountAccount','pageTitleProp');

$am = new AccountManager($badgerDb);
$curMan = new CurrencyManager($badgerDb);

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
					deleteFromNavi($us->getProperty("accountNaviId_$ID"));
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
	
	echo $widgets->addToolTipLayer();
	if (isset($_GET['ID'])) {
		//edit: load values for this ID
		$ID = $_GET['ID'];
		$account = $am->getAccountById($ID);
		$titleValue = $account->getTitle();
		$descriptionValue = $account->getDescription();
		$lowerLimitValue = is_null($tmp = $account->getLowerLimit()) ? '' : $tmp->getFormatted();
		$upperLimitValue = is_null($tmp = $account->getUpperLimit()) ? '' : $tmp->getFormatted();
		$balanceValue = is_null($tmp = $account->getBalance()) ? '' : $tmp->getFormatted();
		$currencyValue = $account->getCurrency()->getId();
		$targetFutureCalcDateValue = is_null($account->getTargetFutureCalcDate()) ? '' : $tmp->getFormatted();;
	} else {
		//new: empty values
		$ID = "new";
		$account = "";
		$titleValue = "";
		$descriptionValue = "";
		$lowerLimitValue = "";
		$upperLimitValue = "";
		$balanceValue = "";
		$currencyValue = "";
		$targetFutureCalcDateValue = "";
	}
	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	$legend = getBadgerTranslation2('accountAccount', 'legend');
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$pageHeading = $pageTitle;
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountAccount', 'title'), true);
	$titleField = $widgets->createField("title", 20, $titleValue, "", true, "text", "");
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountAccount', 'description'), false);
	$descriptionField = $widgets->createField("description", 20, $descriptionValue, "", false, "text", "");
	$lowerLimitLabel = $widgets->createLabel("lowerLimit", getBadgerTranslation2('accountAccount', 'lowerLimit'), false);
	$lowerLimitField = $widgets->createField("lowerLimit", 20, $lowerLimitValue, "", false, "text", "class='inputNumber'");
	$upperLimitLabel = $widgets->createLabel("upperLimit", getBadgerTranslation2('accountAccount', 'upperLimit'), false);
	$upperLimitField = $widgets->createField("upperLimit", 20, $upperLimitValue, "", false, "text", "class='inputNumber'");

	$currencyLabel = $widgets->createLabel("currency", getBadgerTranslation2('accountAccount', 'currency'), false);
	
	$currencies = getCurrencyArray('symbol');
	
	$currencyField = $widgets->createSelectField("currency", $currencies, $default=$currencyValue);
	
	//Buttons
	$submitBtn = $widgets->createButton("submit", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif");
	$backBtn = $widgets->createButton("back", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPageAfterSave';return false;", "Widgets/back.gif");

	//add vars to template, print site
	eval("echo \"".$tpl->getTemplate("Account/Account")."\";");
}


function updateRecord() {
	global $redirectPageAfterSave;
	global $am; //Account Manager
	global $curMan; //Currency Manager
	global $us;
	
	if (isset($_POST['hiddenID'])) {
		switch ($_POST['hiddenID']) {
		case 'new':
			//add new record
			$ID = $am->addAccount(
				$_POST['title'],
				$curMan->getCurrencyById($_POST['currency']),
				$_POST['description'],
				new Amount($_POST['lowerLimit'] , true),
				new Amount($_POST['upperLimit'] , true));
			
			$naviId = addToNavi(
				$us->getProperty('accountNaviParent'),
				$us->getProperty('accountNaviNextPosition'),
				'item',
				'Account' . $ID->getId(),
				'account.gif',
				'{BADGER_ROOT}/modules/account/AccountOverview.php?accountID=' . $ID->getId()
			);
			$us->setProperty('accountNaviId_' . $ID->getId(), $naviId);
			$us->setProperty('accountNaviNextPosition', $us->getProperty('accountNaviNextPosition') + 1);
			addTranslation('Navigation', 'Account' . $ID->getId(), $_POST['title'], $_POST['title']);
			break;
			
		default:
			//update record
			$account = $am->getAccountById($_POST['hiddenID']);
			$account->setTitle($_POST['title']);
			$account->setDescription($_POST['description']);
			//print("<br/>".$curMan->getCurrencyById($_POST['currency'])->getLongName()."<br/>");
			$account->setCurrency($curMan->getCurrencyById($_POST['currency']));
			$account->setLowerLimit(new Amount($_POST['lowerLimit']));
			$account->setUpperLimit(new Amount($_POST['upperLimit']));

			modifyTranslation('Navigation', 'Account' . $account->getId(), $_POST['title'], $_POST['title']);
		}
		//REDIRECT
		header("Location: $redirectPageAfterSave");
	}	
}

function getCurrencyArray($sortBy){
	
	global $badgerDb;
	
	$curMan = new CurrencyManager($badgerDb);
	$order = array ( 
	      array(
	           'key' => $sortBy,
	           'dir' => 'asc'
	           )
	 );
      
 	$curMan->setOrder($order);
	
	$curs = array();
	while ($cur = $curMan->getNextCurrency()) {
		$curs[$cur->getId()] = $cur->getLongName();
	};
	
	return $curs;
}

function removeAccountFromNavi($id) {
	global $us;
	
	
	$sql = "DELETE FROM navi
		WHERE navi_id = $naviId";
	
	
}
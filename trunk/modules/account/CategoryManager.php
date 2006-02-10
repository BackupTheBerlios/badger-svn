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
require_once(BADGER_ROOT . '/modules/account/CategoryManager.class.php');

$redirectPageAfterSave = "CategoryManagerOverview.php";
$pageTitle = "Category Manager"; //I18N

$cm = new CategoryManager($badgerDb);

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			//background delete
			//called by dataGrid
			if (isset($_GET['ID'])) {
				$IDs = explode(",",$_GET['ID']);				
				//check if we can delete this item (or is the currency used)
				foreach($IDs as $ID){
					$cm->deleteCategory($ID);
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
	global $cm;
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
		$category = $cm->getCategoryById($ID);
		$titleValue = $category->getTitle();
		$descriptionValue = $category->getDescription();
		$outsideCapitalValue = $category->getOutsideCapital();
		if($category->getParent()) {
			$parentValue = $category->getParent()->getTitle();
		} else {
			$parentValue = "";
		}
	} else {
		//new: empty values
		$ID = "new";
		$titleValue = "";
		$descriptionValue = "";
		$outsideCapitalValue = "";
		$parentValue = "";
	}
	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountCategory', 'title'), true);
	$titleField = $widgets->createField("title", 30, $titleValue, "", true, "text", "");
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountCategory', 'description'), false);
	$descriptionField = $widgets->createField("description", 30, $descriptionValue, "", false, "text", "");
	$parentLabel = $widgets->createLabel("parent", getBadgerTranslation2('accountCategory', 'parent'), false);
	$parentField = $widgets->createField("parent", 30, $parentValue, "", false, "text", "");
	$outsideCapitalLabel = $widgets->createLabel("outsideCapital", getBadgerTranslation2('accountCategory', 'outsideCapital'), false);
	$outsideCapitalField = $widgets->createField("outsideCapital", 30, $outsideCapitalValue, "", false, "text", "");
	
	//Buttons
	$submitBtn = $widgets->createButton("submit", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif");
	$backBtn = $widgets->createButton("back", getBadgerTranslation2('dataGrid', 'back'), "location.href=$redirectPageAfterSave", "Widgets/back.gif");

	//add vars to template, print site
	eval("echo \"".$tpl->getTemplate("Account/Category")."\";");
}


function updateRecord() {
	global $redirectPageAfterSave;
	global $cm;
	
	switch ($_POST['hiddenID']) {
	case 'new':
		//add new record
		//check if $_POST['symbol'], $_POST['longName'] is set?????
		//$ID = $cm->addCurrency($_POST['symbol'], $_POST['longname']);
		break;
	default:
		//update record
		//$currency = $cm->getCurrencyById($_POST['hiddenID']);
		//$currency->setSymbol($_POST['symbol']);
		//$currency->setLongName($_POST['longname']);
		//$ID = $currency->getId();
	}
	//REDIRECT
	header("Location: $redirectPageAfterSave");

}
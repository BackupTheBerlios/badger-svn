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
require_once(BADGER_ROOT . '/modules/account/CategoryManager.class.php');
require_once(BADGER_ROOT . '/modules/account/AccountManager.class.php');


$redirectPageAfterSave = "CategoryManagerOverview.php";
$pageTitle = getBadgerTranslation2 ('accountCategory','pageTitle');

$cm = new CategoryManager($badgerDb);
$am = new AccountManager($badgerDb);

$order = array (
	array(
		'key' => 'parentTitle',
		'dir' => 'asc'
		)
);	
$cm->setOrder($order);

if (isset($_GET['action'])) {
	switch ($_GET['action']) {
		case 'delete':
			//background delete
			//called by dataGrid
			if (isset($_GET['ID'])) {
				$IDs = explode(",",$_GET['ID']);
							
				//for all categories which should be deleted
				foreach($IDs as $ID){
					//for all accounts:
					$am = new AccountManager($badgerDb);
					while( $account = $am->getNextAccount() ) {					
						//set filter: get all transaction with this category
			 			$filter = array (
							array (
								'key' => 'categoryId',
								'op' => 'eq',
								'val' => $ID
							)
						);
						$account->setFilter($filter);
						$account2 = clone $account;
						//flush category
						while($ta = $account->getNextFinishedTransaction() ) {
							$ta->setCategory(NULL);
						} //transactions
						while($ta = $account2->getNextPlannedTransaction() ) {
							$ta->setCategory(NULL);
						} //transactions
					} //accounts
					
					//delete category
					$cm->deleteCategory($ID);
				}

				echo "";
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
	global $order;
	global $redirectPageAfterSave;
	
	$widgets = new WidgetEngine($tpl);
	$widgets->addToolTipJS();	
	$widgets->addJSValMessages();
	
	$tpl->addJavaScript("js/prototype.js");
	$tpl->addOnLoadEvent("Form.focusFirstElement('mainform')");

	$widgets->addNavigationHead();
	echo $tpl->getHeader($pageTitle);
	echo $widgets->addToolTipLayer();
	
	if (isset($_GET['ID'])) {
		//edit: load values for this ID
		$ID = $_GET['ID'];
		$category = $cm->getCategoryById($ID);
		$titleValue = $category->getTitle();
		$descriptionValue = $category->getDescription();
		if($category->getOutsideCapital() == "1"){
			$outsideCapitalValue = "checked";
		}else{
			$outsideCapitalValue = "";
		};
		if($category->getParent()) {
			$parentValue = $category->getParent()->getTitle();
			$parentId = $category->getParent()->getId();
		} else {
			$parentValue = "";
			$parentId = "";
		}
	} else {
		//new: empty values
		$ID = "new";
		$titleValue = "";
		$descriptionValue = "";
		$outsideCapitalValue = "";
		$parentValue = "";
		$parentId = "";
	}
	//set vars with values
	$FormAction = $_SERVER['PHP_SELF'];
	$legend = getBadgerTranslation2('accountCategory', 'legend');
	$hiddenID = $widgets->createField("hiddenID", 20, $ID, "", false, "hidden");
	$pageHeading = $pageTitle;
	
	//Fields & Labels
	$titleLabel = $widgets->createLabel("title", getBadgerTranslation2('accountCategory', 'title'), true);
	$titleField = $widgets->createField("title", 30, $titleValue, "", true, "text", "");
	
	$descriptionLabel = $widgets->createLabel("description", getBadgerTranslation2('accountCategory', 'description'), false);
	$descriptionField = $widgets->createField("description", 30, $descriptionValue, "", false, "text", "");
	
	$parentLabel = $widgets->createLabel("parent", getBadgerTranslation2('accountCategory', 'parent'), false);
	$parentCats = array(""=>getBadgerTranslation2('CategoryManager','no_parent'));
	while ($cat = $cm->getNextCategory()) {
		$cat->getParent();
	}
	
	$cm->resetCategories();
	
	while ($cat = $cm->getNextCategory()) {
		if(is_null($cat->getParent())){
			$parentCats[$cat->getId()] = $cat->getTitle();
		};
	};
	$parentField = $widgets->createSelectField("parent", $parentCats, $default=$parentId);
	
	$outsideCapitalLabel = $widgets->createLabel("outsideCapital", getBadgerTranslation2('accountCategory', 'outsideCapital'), false);
	$outsideCapitalField = $widgets->createField("outsideCapital", 30,"on", "", false, "checkbox", $outsideCapitalValue);
	
	//Buttons
	$submitBtn = $widgets->createButton("submitBtn", getBadgerTranslation2('dataGrid', 'save'), "submit", "Widgets/accept.gif", "accesskey='s'");
	$backBtn = $widgets->createButton("backBtn", getBadgerTranslation2('dataGrid', 'back'), "location.href='$redirectPageAfterSave';return false;", "Widgets/back.gif");

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
		
		
		
		if(isset($_POST['outsideCapital']) && $_POST['outsideCapital'] == "on"){
			$_POST['outsideCapital'] = 1;
		}else{
			$_POST['outsideCapital'] = 0;
		};

		$Cat = $cm->addCategory($_POST['title'], $_POST['description'], $_POST['outsideCapital']);
		if(isset($_POST['parent']) && $_POST['parent'] != ""){
			$Cat->setParent($cm->getCategoryById($_POST['parent']));
		};
		break;
	default:
		//update record
		$Cat = $cm->getCategoryById($_POST['hiddenID']);
		$Cat->setTitle($_POST['title']);
		$Cat->setDescription($_POST['description']);
		
		if(isset($_POST['outsideCapital']) && $_POST['outsideCapital'] == "on"){
			$_POST['outsideCapital'] = 1;
		}else{
			$_POST['outsideCapital'] = 0;
		};
		$Cat->setOutsideCapital($_POST['outsideCapital']);
		
		if(isset($_POST['parent']) && $_POST['parent'] != ""){
			$Cat->setParent($cm->getCategoryById($_POST['parent']));
		};//elseif(isset($_POST['parent']) && $_POST['parent'] == ""){
			//$Cat->setParent(null);
		//};
		
	}
	//REDIRECT
	header("Location: $redirectPageAfterSave");

}
<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Finance Management
* Visit http://badger.berlios.org 
*
**/
define("BADGER_ROOT", "../.."); 
require_once(BADGER_ROOT . '/includes/fileHeaderFrontEnd.inc.php');
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/account/CategoryManager.class.php';
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

$pageHeading = getBadgerTranslation2('csv', 'title');
$legend = getBadgerTranslation2('csv','legend');

//include widget functionalaty
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addJSValMessages();

$widgets->addNavigationHead();
echo $tpl->getHeader($pageHeading);
echo $widgets->addToolTipLayer();

//create account manger object
$am = new AccountManager($badgerDb);
$us->setProperty("csvImportStandardParser", "");
$us->setProperty("csvImportStandardAccount", "");
$us->setProperty("forecastStandardAccount", "");
//if no Upload yet, show form
if (!isset($_POST['btnSubmit'])){
	if (!isset($_POST['Upload'])){	
		
		$fileLabel =  $widgets->createLabel("", getBadgerTranslation2("importCsv", "selectFile").":", true);
		# widget for browse field has to be developed
		//$fileField = $widgets->createField("file", 50, "", "description", true);
		$fileField = "<input name=\"file\" type=\"file\" size=\"50\" required = \"1\" />";
		
		$selectParserLabel =  $widgets->createLabel("parserSelect", getBadgerTranslation2("importCsv", "selectParser").":", true);
    		//sql to get CSV Parsers
	    	$sql = "SELECT * FROM csv_parser";
	      	$parser = array();
	      	$res =& $badgerDb->query($sql);
	      	while ($res->fetchInto ($row)){ 
	      		$parser[$row[2]] = $row[1];
	      	}
      	$selectParserFile = $widgets->createSelectField("parserSelect", $parser, $us->getProperty("csvImportStandardParser"), getBadgerTranslation2("importCsv", "toolTipParserSelect"));
		
		$accountSelectLabel =  $widgets->createLabel("accountSelect", getBadgerTranslation2("importCsv", "targetAccount").":", true);					      	
		
			//get accounts
			$account = array();
	    	while ($currentAccount = $am->getNextAccount()) {
	    		$account[$currentAccount->getId()] = $currentAccount->getTitle();	
	    	}
      	
	    $accountSelectFile = $widgets->createSelectField("accountSelect", $account, $us->getProperty("csvImportStandardAccount"), getBadgerTranslation2("importCsv", "toolTopAccountSelect"));  

		$uploadButton = $widgets->createButton("Upload", getBadgerTranslation2("importCsv", "upload"), "submit", "Widgets/table_save.gif");
		//use tempate engine
		eval("echo \"".$tpl->getTemplate("CsvImport/csvImportSelectFileForm")."\";");
		
	}
}
if (isset($_POST['Upload'])){
	// for every file
	foreach($_FILES as $file_name => $file_array) {
		//if a file is chosen
		if (isset($_POST["file"])){
		 	#eval("echo \"".$tpl->getTemplate("CsvImport/csvImportWarning")."\";");
		}
		if (is_uploaded_file($file_array['tmp_name'])) {
			//open file
			$fp = fopen($file_array['tmp_name'], "r");
	 		//open selected parser
	 		require_once(BADGER_ROOT . "/modules/csvImport/parser/" . $_POST["parserSelect"]);
	 		//save last used parser
	 		$us->setProperty("csvImportStandardParser", $_POST["parserSelect"]);
	 		$accountId = $_POST["accountSelect"];
	 		//save last used account
	 		$us->setProperty("csvImportStandardAccount", $accountId);
	 		
	 		//call to parse function
	 		$foundTransactions = parseToArray($fp, $accountId);
	 		//delete existing transactions, criteria are accountid, date & amount
	 		$LookupTransactionNumber = count($foundTransactions);
	 		$filteredTransactions = 0;
	 		$importedTransactionNumber = 0;
	 		$importedTransactions = NULL;
	 		//for every transaction received from the parser
	 		for ($foundTransactionNumber = 0; $foundTransactionNumber < $LookupTransactionNumber; $foundTransactionNumber++) {
	 			$am4 = new AccountManager($badgerDb);
	 			$account4 = $am4->getAccountById($foundTransactions[$foundTransactionNumber]["accountId"]);
	 			//set filter to read existing transactions from database
	 			$account4->setFilter(array (
					array (
						'key' => 'valutaDate',
						'op' => 'eq',
						'val' => $foundTransactions[$foundTransactionNumber]["valutaDate"]
					),
					array (
						'key' => 'amount',
						'op' => 'eq',
						'val' => $foundTransactions[$foundTransactionNumber]["amount"]
					)
				));
				//if there is a transaction with same amount & valutaDate in the database
				if ($existing = $account4->getNextFinishedTransaction()) {
					$filteredTransactions++;
				} else {
					$importedTransactions[$importedTransactionNumber] = $foundTransactions[$foundTransactionNumber];
 					$importedTransactionNumber++;
				}
	 		}
	 		if ($filteredTransactions != 0){	
	 			//feedback to user about filtered transactions
	 			echo $filteredTransactions . " " . getBadgerTranslation2("importCsv", "echoFilteredTransactionNumber");
	 		}
	 		
	 		$transactionNumber = count($importedTransactions);
			$tplOutput = NULL;
	 		//show content of the array, using the template engine
	 		if ($transactionNumber > 0){  		
   				$tableHeadSelect = $widgets->createLabel("", getBadgerTranslation2("importCsv", "select"), true);
   				$HeadSelectToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "selectToolTip"));
   				$tableHeadCategory = $widgets->createLabel("", getBadgerTranslation2("importCsv", "category"), true);
				$HeadCategoryToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "categoryToolTip"));
				$tableHeadValutaDate = $widgets->createLabel("", getBadgerTranslation2("importCsv", "valutaDate"), true);
   				$HeadValueDateToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "valuedateToolTip"));
   				$tableHeadTitle = $widgets->createLabel("", getBadgerTranslation2("importCsv", "title"), true);
   				$HeadTitleToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "titleToolTip"));
   				$tableHeadAmount = $widgets->createLabel("", getBadgerTranslation2("importCsv", "amount"), true);
   				$HeadAmountToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "amountToolTip"));
   				$tableHeadTransactionPartner = $widgets->createLabel("", getBadgerTranslation2("importCsv", "transactionPartner"), true);
   				$HeadTransactionPartnerToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "transactionPartnerToolTip"));
   				$tableHeadDescription = $widgets->createLabel("", getBadgerTranslation2("importCsv", "description"), true);
   				$HeadDescriptionToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "descriptionToolTip"));
   				$tableHeadPeriodical = $widgets->createLabel("", getBadgerTranslation2("importCsv", "periodical"), true);
   				$HeadPeriodicalToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "periodicalToolTip"));
   				$tableHeadExceptional = $widgets->createLabel("", getBadgerTranslation2("importCsv", "Exceptional"), true);
   				$HeadExceptionalToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "ExceptionalToolTip"));
   				$tableHeadOutside = $widgets->createLabel("", getBadgerTranslation2("importCsv", "outsideCapital"), true);
   				$HeadOutsideToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "outsideCapitalToolTip"));
   				$tableHeadAccount = $widgets->createLabel("", getBadgerTranslation2("importCsv", "account"), true);
				$HeadAccountToolTip =  $widgets->addToolTip(getBadgerTranslation2("importCsv", "accountToolTip"));
				
   				for ($outputTransactionNumber = 0; $outputTransactionNumber < $transactionNumber; $outputTransactionNumber++) {
   					
					$tableSelectCheckbox = "<input type=\"checkbox\" name=\"select" . $outputTransactionNumber . "\" value=\"select\" checked=\"checked\" />";

					$tableSelectCategory= $widgets->createSelectField("categorySelect".$outputTransactionNumber, getCategorySelectArray(),"");
						    	
				    $tableValutaDate = $widgets->addDateField("valutaDate".$outputTransactionNumber, $importedTransactions[$outputTransactionNumber]["valutaDate"]->getFormatted());
				    						    
					$tableTitle = $widgets->createField("title".$outputTransactionNumber, 30, $importedTransactions[$outputTransactionNumber]["title"]);
   							
					$tableAmount = $widgets->createField("amount".$outputTransactionNumber, 8, $importedTransactions[$outputTransactionNumber]["amount"]->getFormatted());
   							
					$tableTransactionPartner = $widgets->createField("transactionPartner".$outputTransactionNumber, 15, $importedTransactions[$outputTransactionNumber]["transactionPartner"]);
   							
					$tableDescription = $widgets->createField("description".$outputTransactionNumber, 12, $importedTransactions[$outputTransactionNumber]["description"]);
   							
					$tablePeriodicalCheckbox = "<input type=\"checkbox\" name=\"periodical" . $outputTransactionNumber . "\" value=\"select\" />";
   							
					$tableExceptionalCheckbox = "<input type=\"checkbox\" name=\"exceptional" . $outputTransactionNumber . "\" value=\"select\" />";
   					
   					$tableOutsideCheckbox = "<input type=\"checkbox\" name=\"outside" . $outputTransactionNumber . "\" value=\"select\" />";
   						//get accounts	
						$am1 = new AccountManager($badgerDb);
						$account1 = array();
				    	while ($currentAccount = $am1->getNextAccount()) {
				    		$account1[$currentAccount->getId()] = $currentAccount->getTitle();	
				    	}
						//get selected account
						$am2 = new AccountManager($badgerDb);
						$account2 = NULL;
						while ($currentAccount = $am2->getNextAccount()) {
				    		if ($currentAccount->getID() == $importedTransactions[$outputTransactionNumber]["accountId"]){
				    			$account2 = $currentAccount->getID();
				    		}			
				    	}
			    	$tableSelectAccount= $widgets->createSelectField("account2Select".$outputTransactionNumber, $account1, $account2); 	
					//echo ("\$tplOutput .= \"".$tpl->getTemplate("CsvImport/csvImportSelectTransactions2")."\";");
					eval("\$tplOutput .= \"".$tpl->getTemplate("CsvImport/csvImportSelectTransactions2")."\";");
   				}
   				$hiddenField = "<input type=\"hidden\" name=\"tableRows\" value=\"" . $transactionNumber . " \">";
				$buttonSubmit = $widgets->createButton("btnSubmit", getBadgerTranslation2("importCsv", "save"), "submit", "Widgets/table_save.gif");
				
	 			eval("echo \"".$tpl->getTemplate("CsvImport/csvImportSelectTransactions1")."\";");
	 		} else{
	 			echo " " . getBadgerTranslation2("importCsv", "noNewTransactions");
	 		}	
	  	
		}
	}	
}
if (isset($_POST['btnSubmit'])){		
	// create array with the selected transaction from the form above
	// to count number of selected transactions
	$selectedTransaction = 0;
	//initalise array
	$writeToDbArray = NULL;
	//for all rows
	for ($selectedTransactionNumber = 0; $selectedTransactionNumber < $_POST['tableRows']; $selectedTransactionNumber++) {
		//reset tableRowArray
		$tableRowArray = NULL;
		// if the transaction was selected
		if (isset($_POST["select" . $selectedTransactionNumber])){
			// set periodical flag
			if (isset ($_POST["periodical" . $selectedTransactionNumber])){
				$periodical = true;
			}else {
				$periodical = false;
			}
			// set periodical flag
			if (isset ($_POST["exceptional" . $selectedTransactionNumber])){
				$exceptional = true;
			}else {
				$exceptional = false;
			}
			if (isset ($_POST["outside" . $selectedTransactionNumber])){
				$outside = true;
			}else {
				$outside = false;
			}
			//create array with one transaction
			$amount1 = new Amount($_POST['amount' . $selectedTransactionNumber],true);
			$valutaDate1 = new Date ($_POST['valutaDate' . $selectedTransactionNumber], true);
			$cm1 = new CategoryManager($badgerDb);
			$transactionCategory = NULL;
			#echo $_POST['categorySelect' . $selectedTransactionNumber];
			if (!$_POST['categorySelect' . $selectedTransactionNumber] == NULL){
				if ($_POST['categorySelect' . $selectedTransactionNumber] != "NULL"){
					$transactionCategory = $cm1->getCategoryById($_POST['categorySelect' . $selectedTransactionNumber]);
					#echo $transactionCategory;
				}
			}
			#echo $transactionCategory;
			$tableRowArray = array(
				"categoryId" => $transactionCategory,
				"account" => $_POST['account2Select' . $selectedTransactionNumber],
				"title" => $_POST['title' . $selectedTransactionNumber], 
				"description" => $_POST['description' . $selectedTransactionNumber],
				"valutaDate" => $valutaDate1,
				"amount" => $amount1,
				"transactionPartner" => $_POST['transactionPartner' . $selectedTransactionNumber],
				"periodical" => $periodical,
				"exceptional" => $exceptional,
				"outside" => $outside
			);	
		}
		//if a array with one transaction exist
		if ($tableRowArray){
			//add the transaction to the multidimensional array
			$writeToDbArray[$selectedTransaction] = $tableRowArray;
			//increment number of selected transactions
			$selectedTransaction++;
		}
	}
	//write array to db
	if ($writeToDbArray){
		for ($arrayRow = 0; $arrayRow < count($writeToDbArray); $arrayRow++) {
			$am3 = new AccountManager($badgerDb);
			$account3 = $am3->getAccountById($writeToDbArray[$arrayRow]['account']);
			$writeCategory = $writeToDbArray[$arrayRow]['categoryId'];
			$writeTitle = $writeToDbArray[$arrayRow]['title']; 
			$writeDescription = $writeToDbArray[$arrayRow]['description'];
			$writeValutaDate = $writeToDbArray[$arrayRow]['valutaDate'];
			$writeAmount = $writeToDbArray[$arrayRow]['amount'];
			$writeTransactionPartner = $writeToDbArray[$arrayRow]['transactionPartner'];
			$writePeriodical = $writeToDbArray[$arrayRow]['periodical'];
			$writeExceptional = $writeToDbArray[$arrayRow]['exceptional'];
			$writeOutside = $writeToDbArray[$arrayRow]['outside'];
			$account3->addFinishedTransaction($writeAmount, $writeTitle, $writeDescription, $writeValutaDate, $writeTransactionPartner, $writeCategory, $writeOutside, $writeExceptional, $writePeriodical);
		}
		// echo success message & number of written transactions
		echo "<br/>" . count($writeToDbArray) . " ". getBadgerTranslation2("importCsv", "successfullyWritten");
	}else {
		//echo no transactions selected
		echo getBadgerTranslation2("importCsv", "noTransactionSelected");
	}
} 		
eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
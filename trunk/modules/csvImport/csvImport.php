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
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/account/CategoryManager.class.php';

$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$tpl->getHeader("CSV-Import");
echo $widgets->addToolTipLayer();

$am = new AccountManager($badgerDb);

//if no Upload yet, show form
if (!isset($_POST['btnSubmit'])){
	if (!isset($_POST['Upload'])){	
		$fileLabel =  $widgets->createLabel("file", getBadgerTranslation2("importCsv", "selectFile").":", true);
		# description internatiolisierne--> ist tooltip
		# sepp muss durchsuchen widget machen
		//$fileField = $widgets->createField("file", 50, "", "description", true);
		$fileField = "<input name=\"file\" type=\"file\" size=\"50\" />";
		
		$selectParserLabel =  $widgets->createLabel("parserSelect", getBadgerTranslation2("importCsv", "selectParser").":", true);
    	
    	$sql = "SELECT * FROM csv_parser";
      	$parser = array();
      	$res =& $badgerDb->query($sql);
      	while ($res->fetchInto ($row)){ 
      		$parser[$row[2]] = $row[1];
      	}
      	#description internationalisieren
      	$selectParserFile = $widgets->createSelectField("parserSelect", $parser, "", getBadgerTranslation2("importCsv", "toolTipParserSelect"));
		
		$accountSelectLabel =  $widgets->createLabel("accountSelect", getBadgerTranslation2("importCsv", "targetAccount").":", true);					      	
		
		$account = array();
    	while ($currentAccount = $am->getNextAccount()) {
    		$account[$currentAccount->getId()] = $currentAccount->getTitle();	
    	}
      	
      	
	    $accountSelectFile = $widgets->createSelectField("accountSelect", $account, "", getBadgerTranslation2("importCsv", "toolTopAccountSelect"));  

		$uploadButton = $widgets->createButton("Upload", getBadgerTranslation2("importCsv", "upload"), "submit", "Widgets/table_save.gif");


		eval("echo \"".$tpl->getTemplate("CsvImport/csvImportSelectFileForm")."\";");
		
	}
}
if (isset($_POST['Upload'])){
	// for every file
	foreach($_FILES as $file_name => $file_array) {
		//if a file is chosen
		if (is_uploaded_file($file_array['tmp_name'])) {
			//open file
			$fp = fopen($file_array['tmp_name'], "r");
	 		//open selected parser
	 		require_once(BADGER_ROOT . "/modules/csvImport/parser/" . $_POST["parserSelect"]);
	 		$accountId = $_POST["accountSelect"];
	 		//call to parse function
	 		$importedTransactions = parseToArray($fp, $accountId);
	 		//delete existing transactions, criteria are accountid, date & amount
	 		#$bereinigete Transaktionen = 
	 		for ($importedTransactionNumber = 0; $importedTransactionNumber < count($importedTransactions); $importedTransactionNumber++) {
	 			#$amount = $importedTransactions[$importedTransactionNumber]["amount"];
	 			#$date = $importedTransactions[$importedTransactionNumber]["valutaDate"];
	 			#$accountId = $importedTransactions[$importedTransactionNumber]["accountId"];
	 		} 
	 		#importedTransactions umstellen auf bereinigte Transaktionen
	 		$transactionNumber = count($importedTransactions);
			$tplOutput = NULL;
	 		//show content of the array
	 		if ($transactionNumber > 0){  		
   				$tableHeadSelect = $widgets->createLabel("", getBadgerTranslation2("importCsv", "select"), true);
   				$tableHeadCategory = $widgets->createLabel("", getBadgerTranslation2("importCsv", "category"), true);
				$tableHeadValutaDate = $widgets->createLabel("", getBadgerTranslation2("importCsv", "valutaDate"), true);
   				$tableHeadTitle = $widgets->createLabel("", getBadgerTranslation2("importCsv", "title"), true);
   				$tableHeadAmount = $widgets->createLabel("", getBadgerTranslation2("importCsv", "amount"), true);
   				$tableHeadTransactionPartner = $widgets->createLabel("", getBadgerTranslation2("importCsv", "transactionPartner"), true);
   				$tableHeadDescription = $widgets->createLabel("", getBadgerTranslation2("importCsv", "description"), true);
   				$tableHeadPeriodical = $widgets->createLabel("", getBadgerTranslation2("importCsv", "periodical"), true);
   				$tableHeadExceptional = $widgets->createLabel("", getBadgerTranslation2("importCsv", "Exceptional"), true);
   				$tableHeadAccount = $widgets->createLabel("", getBadgerTranslation2("importCsv", "account"), true);
				
   				for ($outputTransactionNumber = 0; $outputTransactionNumber < $transactionNumber; $outputTransactionNumber++) {
   					
					$tableSelectCheckbox = "<input type=\"checkbox\" name=\"select" . $outputTransactionNumber . "\" value=\"select\" checked=\"checked\" />";
   							
						$cm = new CategoryManager($badgerDb);
						$category = array();
				    	$category[""]= "";
				    	while ($currentCategory = $cm->getNextCategory()) {
				    		$category[$currentCategory->getId()] = $currentCategory->getTitle();	
				    	}
			    	$tableSelectCategory= $widgets->createSelectField("categorySelect".$outputTransactionNumber, $category,"");
						    	
				    #darstellungsbreite übergeben, sepp töten
			    	$tableValutaDate = $widgets->createField("valutaDate".$outputTransactionNumber, 8, $importedTransactions[$outputTransactionNumber]["valutaDate"]);
						    
					$tableTitle = $widgets->createField("title".$outputTransactionNumber, 30, $importedTransactions[$outputTransactionNumber]["title"]);
   							
					$tableAmount = $widgets->createField("amount".$outputTransactionNumber, 8, $importedTransactions[$outputTransactionNumber]["amount"]);
   							
					$tableTransactionPartner = $widgets->createField("transactionPartner".$outputTransactionNumber, 15, $importedTransactions[$outputTransactionNumber]["transactionPartner"]);
   							
					$tableDescription = $widgets->createField("description".$outputTransactionNumber, 12, $importedTransactions[$outputTransactionNumber]["description"]);
   							
					$tablePeriodicalCheckbox = "<input type=\"checkbox\" name=\"periodical" . $outputTransactionNumber . "\" value=\"select\" />";
   							
					$tableExceptionalCheckbox = "<input type=\"checkbox\" name=\"exceptional" . $outputTransactionNumber . "\" value=\"select\" />";
   							
						$am1 = new AccountManager($badgerDb);
						$account1 = array();
				    	while ($currentAccount = $am1->getNextAccount()) {
				    		$account1[$currentAccount->getId()] = $currentAccount->getTitle();	
				    	}
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
	 			echo "doof";
	 			#Echo, dass keine neuen Datensätze gefunden wurden
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
				$periodical = "JA";
			}else {
				$periodical = "NEIN";
			}
			// set periodical flag
			if (isset ($_POST["exceptional" . $selectedTransactionNumber])){
				$exceptional = "JA";
			}else {
				$exceptional = "NEIN";
			}
			//create array with one transaction
			$tableRowArray = array(
				"categoryId" => $_POST['categorySelect' . $selectedTransactionNumber],
				"account" => $_POST['account2Select' . $selectedTransactionNumber],
				"title" => $_POST['title' . $selectedTransactionNumber], 
				"description" => $_POST['description' . $selectedTransactionNumber],
				"valutaDate" => $_POST['valutaDate' . $selectedTransactionNumber],
				"amount" => $_POST['amount' . $selectedTransactionNumber],
				"transactionPartner" => $_POST['transactionPartner' . $selectedTransactionNumber],
				"periodical" => $periodical,
				"exceptional" => $exceptional
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
	if ($writeToDbArray){
		# array in db schreiben
		for ($arrayRow = 0; $arrayRow < count($writeToDbArray); $arrayRow++) {
			echo $writeToDbArray[$arrayRow]['categoryId'];
			echo $writeToDbArray[$arrayRow]['account'];
			echo $writeToDbArray[$arrayRow]['title']; 
			echo $writeToDbArray[$arrayRow]['description'];
			echo $writeToDbArray[$arrayRow]['valutaDate'];
			echo $writeToDbArray[$arrayRow]['amount'];
			echo $writeToDbArray[$arrayRow]['transactionPartner'];
			echo $writeToDbArray[$arrayRow]['periodical'];
			echo $writeToDbArray[$arrayRow]['exceptional'];
			echo "<br />";
		}
		// echo success message & number of written transactions
		echo "<br/>" . count($writeToDbArray) . " ". getBadgerTranslation2("importCsv", "successfullyWritten");
	}else {
		//echo no transactions selected
		echo getBadgerTranslation2("importCsv", "noTransactionSelected");
	}
} 		
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
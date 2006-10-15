<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Finance Management
* Visit http://www.badger-finance.org 
*
**/
define("BADGER_ROOT", "../.."); 
require_once(BADGER_ROOT . '/includes/fileHeaderFrontEnd.inc.php');
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/account/CategoryManager.class.php';
require_once BADGER_ROOT . '/modules/account/accountCommon.php';
require_once BADGER_ROOT . '/core/Date/Calc.php';

$pageHeading = getBadgerTranslation2('csv', 'title');
$legend = getBadgerTranslation2('csv','legend');

//include widget functionalaty
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addJSValMessages();
$tpl->addJavaScript('/js/prototype.js');
$tpl->addJavaScript('/js/csvImport.js');

$widgets->addNavigationHead();
echo $tpl->getHeader($pageHeading);
echo $widgets->addToolTipLayer();

//create account manger object
$am = new AccountManager($badgerDb);
//if no Upload yet, show form
if (!isset($_POST['btnSubmit'])){
	if (!isset($_POST['Upload'])){	
		
		$fileLabel =  $widgets->createLabel("", getBadgerTranslation2("importCsv", "selectFile").":", true);
		# widget for browse field has to be developed
		//$fileField = $widgets->createField("file", 50, "", "description", true);
		$fileField = "<input name=\"file\" type=\"file\" size=\"50\" required=\"required\" />";
		
		$accountSelectLabel =  $widgets->createLabel("accountSelect", getBadgerTranslation2("importCsv", "targetAccount").":", true);					      	
		//get accounts
		$account = array();
	    $accountParserJS = "var accountParsers = new Array();\n";  
		$accountParsers = array();
    	while ($currentAccount = $am->getNextAccount()) {
    		$account[$currentAccount->getId()] = $currentAccount->getTitle();
    		$accountParsers[$currentAccount->getId()] = $currentAccount->getCsvParser(); 
    		$accountParserJS .= "accountParsers['" . $currentAccount->getId() . "'] = '" . $currentAccount->getCsvParser() . "';\n";
    	}
    	$accountParserJS .= "updateParser()\n";
    	
		try {
			$standardAccount = $us->getProperty("csvImportStandardAccount");
		} catch (BadgerException $ex) {
			$standardAccount = '';
		}

	    $accountSelectFile = $widgets->createSelectField("accountSelect", $account, $standardAccount, getBadgerTranslation2("importCsv", "toolTopAccountSelect"), true, "onchange='updateParser();'");

		$selectParserLabel =  $widgets->createLabel("parserSelect", getBadgerTranslation2("importCsv", "selectParser").":", true);
/*
    		//sql to get CSV Parsers
	    	$sql = "SELECT * FROM csv_parser";
	      	$parser = array();
	      	$res =& $badgerDb->query($sql);
	      	while ($res->fetchInto ($row)){ 
	      		$parser[$row[2]] = $row[1];
	      	}
*/	      	
		$parser = getParsers();

      	$selectParserFile = $widgets->createSelectField("parserSelect", $parser, null, getBadgerTranslation2("importCsv", "toolTipParserSelect"));
		

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
	 		require_once(BADGER_ROOT . "/modules/csvImport/parser/" . getGPC($_POST, 'parserSelect'));
	 		//save last used parser
	 		$accountId = getGPC($_POST, 'accountSelect', 'integer');
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
					$importedTransactions[$importedTransactionNumber] = importMatching($foundTransactions[$foundTransactionNumber], $accountId);
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
				$tableHeadMatching = $widgets->createLabel('', getBadgerTranslation2('importCsv', 'matchingHeader'), true);
				$HeadMatchingToolTip = $widgets->addToolTip(getBadgerTranslation2('importCsv', 'matchingToolTip'));
				
				//get accounts	
				$am1 = new AccountManager($badgerDb);
				$account1 = array();
		    	while ($currentAccount = $am1->getNextAccount()) {
		    		$account1[$currentAccount->getId()] = $currentAccount->getTitle();	
		    	}

   				for ($outputTransactionNumber = 0; $outputTransactionNumber < $transactionNumber; $outputTransactionNumber++) {
   					
					$tableSelectCheckbox = "<input type=\"checkbox\" name=\"select" . $outputTransactionNumber . "\" value=\"select\" checked=\"checked\" />";

					$tableSelectCategory= $widgets->createSelectField("categorySelect".$outputTransactionNumber, getCategorySelectArray(), $importedTransactions[$outputTransactionNumber]['categoryId']);
						    	
				    $tableValutaDate = $widgets->addDateField("valutaDate".$outputTransactionNumber, $importedTransactions[$outputTransactionNumber]["valutaDate"]->getFormatted());
				    						    
					$tableTitle = $widgets->createField("title".$outputTransactionNumber, 30, $importedTransactions[$outputTransactionNumber]["title"]);
   							
					$tableAmount = $widgets->createField("amount".$outputTransactionNumber, 8, $importedTransactions[$outputTransactionNumber]["amount"]->getFormatted());
   							
					$tableTransactionPartner = $widgets->createField("transactionPartner".$outputTransactionNumber, 15, $importedTransactions[$outputTransactionNumber]["transactionPartner"]);
   							
					$tableDescription = $widgets->createField("description".$outputTransactionNumber, 12, $importedTransactions[$outputTransactionNumber]["description"]);
   							
					$tablePeriodicalCheckbox = "<input type=\"checkbox\" name=\"periodical" . $outputTransactionNumber . "\" value=\"select\" />";
   							
					$tableExceptionalCheckbox = "<input type=\"checkbox\" name=\"exceptional" . $outputTransactionNumber . "\" value=\"select\" />";
   					
   					$tableOutsideCheckbox = "<input type=\"checkbox\" name=\"outside" . $outputTransactionNumber . "\" value=\"select\" />";

			    	$tableSelectAccount= $widgets->createSelectField("account2Select".$outputTransactionNumber, $account1, $importedTransactions[$outputTransactionNumber]["accountId"]);
			    	
			    	$matchingTransactions = array();
			    	if (isset($importedTransactions[$outputTransactionNumber]['similarTransactions'])) {
			    		foreach($importedTransactions[$outputTransactionNumber]['similarTransactions'] as $similarity => $currentTransaction) {
			    			$matchingTransactions[$currentTransaction->getId()] = $currentTransaction->getTitle() . $similarity; //sprintf(' (%1.1f %%)', $similarity); 
			    		}
			    	}
		    		$matchingTransactions['none'] = getBadgerTranslation2('importCsv', 'dontMatchTransaction');
			    	
			    	$tableSelectMatchingTransaction = $widgets->createSelectField('matchingTransactionSelect' . $outputTransactionNumber, $matchingTransactions);
			    	
					//echo ("\$tplOutput .= \"".$tpl->getTemplate("CsvImport/csvImportSelectTransactions2")."\";");
					eval("\$tplOutput .= \"".$tpl->getTemplate("CsvImport/csvImportSelectTransactions2")."\";");
   				}
   				$hiddenField = "<input type=\"hidden\" name=\"tableRows\" value=\"" . $transactionNumber . " \">";
   				$hiddenAccountId = $widgets->createField('hiddenAccountId', 0, $accountId, null, false, 'hidden');
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

	$am3 = new AccountManager($badgerDb);

	$baseAccountId = getGPC($_POST, 'hiddenAccountId', 'integer');
	$baseAccount = $am3->getAccountById($baseAccountId); 

	$cm1 = new CategoryManager($badgerDb);

	//for all rows
	for ($selectedTransactionNumber = 0; $selectedTransactionNumber < getGPC($_POST, 'tableRows', 'integer'); $selectedTransactionNumber++) {
		//reset tableRowArray
		$tableRowArray = NULL;
		// if the transaction was selected
		if (isset($_POST["select" . $selectedTransactionNumber])){
			// set periodical flag
			$periodical = getGPC($_POST, 'periodical' . $selectedTransactionNumber, 'checkbox');
			// set periodical flag
			$exceptional = getGPC($_POST, "exceptional" . $selectedTransactionNumber, 'checkbox');
			$outside = getGPC($_POST, "outside" . $selectedTransactionNumber, 'checkbox');
			//create array with one transaction
			$amount1 = getGPC($_POST, 'amount' . $selectedTransactionNumber, 'AmountFormatted');
			$valutaDate1 = getGPC($_POST, 'valutaDate' . $selectedTransactionNumber, 'DateFormatted');
			$transactionCategory = NULL;
			#echo $_POST['categorySelect' . $selectedTransactionNumber];
			if (!getGPC($_POST, 'categorySelect' . $selectedTransactionNumber) == NULL){
				if (getGPC($_POST, 'categorySelect' . $selectedTransactionNumber) != "NULL"){
					$transactionCategory = $cm1->getCategoryById(getGPC($_POST, 'categorySelect' . $selectedTransactionNumber, 'integer'));
					#echo $transactionCategory;
				}
			}
			$matchingTransactionId = getGPC($_POST, 'matchingTransactionSelect' . $selectedTransactionNumber);
			#echo $transactionCategory;
			$tableRowArray = array(
				"categoryId" => $transactionCategory,
				"account" => getGPC($_POST, 'account2Select' . $selectedTransactionNumber, 'integer'),
				"title" => getGPC($_POST, 'title' . $selectedTransactionNumber), 
				"description" => getGPC($_POST, 'description' . $selectedTransactionNumber),
				"valutaDate" => $valutaDate1,
				"amount" => $amount1,
				"transactionPartner" => getGPC($_POST, 'transactionPartner' . $selectedTransactionNumber),
				"periodical" => $periodical,
				"exceptional" => $exceptional,
				"outside" => $outside,
				'matchingTransactionId' => $matchingTransactionId
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
	if ($writeToDbArray) {
		for ($arrayRow = 0; $arrayRow < count($writeToDbArray); $arrayRow++) {
			if ($writeToDbArray[$arrayRow]['matchingTransactionId'] == 'none') {
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
			} else {
				//Update existing transaction
				$transaction = $baseAccount->getFinishedTransactionById($writeToDbArray[$arrayRow]['matchingTransactionId']);
				$transaction->setTitle($transaction->getTitle() . ' - ' . $writeToDbArray[$arrayRow]['title']);
				$transaction->setDescription(
					$transaction->getDescription()
					. "\n"
					. $writeToDbArray[$arrayRow]['description']
					. "\n"
					. getBadgerTranslation2('importCsv', 'descriptionFieldImportedPartner')
					. $writeToDbArray[$arrayRow]['transactionPartner']
					. "\n"
					. getBadgerTranslation2('importCsv', 'descriptionFieldOrigValutaDate')
					. $transaction->getValutaDate()->getFormatted()
					. "\n"
					. getBadgerTranslation2('importCsv', 'descriptionFieldOrigAmount')
					. $transaction->getAmount()->getFormatted()
				);
				$transaction->setValutaDate($writeToDbArray[$arrayRow]['valutaDate']);
				$transaction->setAmount($writeToDbArray[$arrayRow]['amount']);
				if (strpos($transaction->getType(), 'Planned') !== false) {
					$transaction->setPlannedTransaction(null);
				}
			}
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

function getParsers() {
	$baseDir = BADGER_ROOT . '/modules/csvImport/parser/';

	$parsers = array();

	$parserDir = dir($baseDir);
	while (false !== ($parserFileName = $parserDir->read())) {
		if (is_file($baseDir . $parserFileName)) {
			$parserFile = fopen($baseDir . $parserFileName, "r");
			while (!feof($parserFile)) {
				$line = fgets($parserFile);
				if (preg_match('/[\s]*\/\/[\s]*BADGER_REAL_PARSER_NAME[\s]+([^\n]+)/', $line, $match)) {
					$parsers[$parserFileName] = $match[1];
					break;
				}
			}
			fclose($parserFile);
		}
	}
	$parserDir->close();
	
	asort($parsers);

	return $parsers;
}

function importMatching($importedTransaction, $accountId) {
	global $us;
	global $badgerDb;

	static $dateDelta = null;
	static $amountDelta = null;
	static $textSimilarity = null;
	static $categories = null;
	
	if (is_null($dateDelta)) {
		try {
			$dateDelta = $us->getProperty('matchingDateDelta');
		} catch (BadgerException $ex) {
			$dateDelta = 5;
		}
		
		try {
			$amountDelta = $us->getProperty('matchingAmountDelta');
		} catch (BadgerException $ex) {
			$amountDelta = 0.1;
		}
		
		try {
			$textSimilarity = $us->getProperty('matchingTextSimilarity');
		} catch (BadgerException $ex) {
			$textSimilarity = 0.75;
		}
		
		$categoryManager = new CategoryManager($badgerDb);
		while ($currentCategory = $categoryManager->getNextCategory()) {
			$categories[$currentCategory->getId()] = preg_split('/[\s]+/', $currentCategory->getKeywords(), -1, PREG_SPLIT_NO_EMPTY);
		}
	}
	
	if (!$importedTransaction['valutaDate']) {
		return $importedTransaction;
	}

	$minDate = new Date($importedTransaction['valutaDate']);
	$minDate->subtractSeconds($dateDelta * 24 * 60 * 60);
	
	$maxDate = new Date($importedTransaction['valutaDate']);
	$maxDate->addSeconds($dateDelta * 24 * 60 * 60);
	
	if (!$importedTransaction['amount']) {
		return $importedTransaction;
	}
	
	$minAmount = new Amount($importedTransaction['amount']);
	$minAmount->mul(1 - $amountDelta);
	
	$maxAmount = new Amount($importedTransaction['amount']);
	$maxAmount->mul(1 + $amountDelta);
	
	$accountManager = new AccountManager($badgerDb);
	$account = $accountManager->getAccountById($accountId);
	
	$account->setFilter(array (
		array (
			'key' => 'valutaDate',
			'op' => 'ge',
			'val' => $minDate
		),
		array (
			'key' => 'valutaDate',
			'op' => 'le',
			'val' => $maxDate
		),
		array (
			'key' => 'amount',
			'op' => 'ge',
			'val' => $minAmount
		),
		array (
			'key' => 'amount',
			'op' => 'le',
			'val' => $maxAmount
		)
	));
	
	$similarTransactions = array();

	while ($currentTransaction = $account->getNextTransaction()) {
		$titleSimilarity = getSimilarity($importedTransaction['title'], $currentTransaction->getTitle(), $textSimilarity);
		$descriptionSimilarity = getSimilarity($importedTransaction['description'], $currentTransaction->getDescription(), $textSimilarity);
		$transactionPartnerSimilarity = getSimilarity($importedTransaction['transactionPartner'], $currentTransaction->getTransactionPartner(), $textSimilarity);
		
		$currDate = $currentTransaction->getValutaDate();
		$impDate = $importedTransaction['valutaDate'];
		$dateSimilarity = 1 - (abs(Date_Calc::dateDiff(
			$currDate->getDay(), $currDate->getMonth(), $currDate->getYear(),
			$impDate->getDay(), $impDate->getMonth(), $impDate->getYear())
		) / $dateDelta);
		
		$cmpAmount = new Amount($currentTransaction->getAmount());
		$impAmount = new Amount($importedTransaction['amount']);
		$cmpAmount->sub($impAmount);
		$cmpAmount->abs();
		$impAmount->mul($amountDelta);
		$impAmount->abs();
		$amountSimilarity = 1 - $cmpAmount->div($impAmount)->get();
		
		$currentTextSimilarity = ($titleSimilarity + $descriptionSimilarity + $transactionPartnerSimilarity) / 3;

//		if ($currentTextSimilarity >= $textSimilarity) {
			$overallSimilarity = ($titleSimilarity + $descriptionSimilarity + $transactionPartnerSimilarity + $dateSimilarity + $amountSimilarity) / 5;
			
			$similarTransactions["$overallSimilarity t:$titleSimilarity d:$descriptionSimilarity tp:$transactionPartnerSimilarity vd:$dateSimilarity a:$amountSimilarity"] = $currentTransaction;
//		}
	}
	
	krsort($similarTransactions);
	
	if (count($similarTransactions)) {
		$importedTransaction['similarTransactions'] = $similarTransactions;
		
		return $importedTransaction;
	}
	
	if ($importedTransaction['categoryId']) {
		return $importedTransaction;
	}
	
	$transactionStrings = array (
		$importedTransaction['title'],
		$importedTransaction['description'],
		$importedTransaction['transactionPartner']
	);

	foreach ($transactionStrings as $currentTransactionString) {
		foreach ($categories as $currentCategoryId => $keywords) {
			foreach ($keywords as $keyword) {
				if (stripos($currentTransactionString, $keyword) !== false) {
					$importedTransaction['categoryId'] = $currentCategoryId;
					
					break 3;
				} //if keyword found
			} //foreach keywords
		} //foreach categories
	} //foreach transactionStrings
	
	return $importedTransaction;
}

function getSimilarity ($haystack, $needle, $default) {
	if ($haystack == '' || $needle == '') {
		return $default;
	}

	if (stripos($haystack, $needle) !== false) {
		return 1;
	}

	$result = 0;
	similar_text(strtolower($haystack), strtolower($needle), $result);
	return $result / 100;	
}
?>
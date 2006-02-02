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
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$tpl->getHeader("CSV-Import");
echo $widgets->addToolTipLayer();
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
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
		$sql = "SELECT * FROM account";
		
		$account = array();
    	while ($currentAccount = $am->getNextAccount()) {
    		$account[$currentAccount->getId()] = $currentAccount->getTitle();	
    	}
      	
      	#description internationalisieren
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
	 			$amount = $importedTransactions[$importedTransactionNumber]["amount"];
	 			$date = $importedTransactions[$importedTransactionNumber]["valutaDate"];
	 			$accountId = $importedTransactions[$importedTransactionNumber]["accountId"];
	 		} 
	 		#importedTransactions umstellen auf bereinigte Transaktionen
	 		$transactionNumber = count($importedTransactions);
	 		//show content of the array
	 		if ($transactionNumber > 0){ 
		 		
	 			?>
		 			<form action="" method="post" enctype="multipart/form-data" name="Selection" id="Selection">
		 				<div id="scroll">	
				   			<table border = 1 cellpadding = 0, cellspacing = 5>
				   				<th><?php echo getBadgerTranslation2("importCsv", "select"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "category"); ?> </th>
								<th><?php echo getBadgerTranslation2("importCsv", "valutaDate"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "title"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "amount"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "transactionPartner"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "description"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "periodical"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "Exceptional"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "account"); ?> </th>				   				
				   				<?php 
				   				for ($outputTransactionNumber = 0; $outputTransactionNumber < $transactionNumber; $outputTransactionNumber++) {?>
				   					<tr>
				   						<td>
				   							<center
				   							<input type="checkbox" name="select<?php echo $outputTransactionNumber?>" value="select" checked="checked"> </input>
				   							</center>
				   						</td>
				   						<td>
				   							<select name="categorySelect<?php echo $outputTransactionNumber?>" size="1">"
										    	<option> </option>;
										    	<?php $sql = "SELECT * FROM category";
												$res =& $badgerDb->query($sql);
												while ($res->fetchInto ($row)){
													echo "<option value=\"".$row[0]."\">". $row[2] . "</option>";
												}?>
					    					</select>
				   						</td>
			   							<td>
				   							<input name="valutaDate<?php echo $outputTransactionNumber?>" type="text" size="8" maxlength="99" value="<?php echo $importedTransactions[$outputTransactionNumber]["valutaDate"]?>">
			   							</td>
			   							<td>
				   							<input name="title<?php echo $outputTransactionNumber?>" type="text" size="30" maxlength="99" value="<?php echo $importedTransactions[$outputTransactionNumber]["title"]?>">
			   							</td>
			   							<td>
				   							<input name="amount<?php echo $outputTransactionNumber?>" type="text" size="8" maxlength="99" value="<?php echo $importedTransactions[$outputTransactionNumber]["amount"]?>">
			   							</td>
			   							<td>
				   							<input name="transactionPartner<?php echo $outputTransactionNumber?>" type="text" size="15" maxlength="99" value="<?php echo $importedTransactions[$outputTransactionNumber]["transactionPartner"]?>">
			   							</td>
			   							<td>
				   							<input name="description<?php echo $outputTransactionNumber?>" type="text" size="12" maxlength="99" value="<?php echo $importedTransactions[$outputTransactionNumber]["description"]?>">
			   							</td>
			   							<td>
				   							<center
				   							<input type="checkbox" name="periodical<?php echo $outputTransactionNumber?>" value="select"> </input>
				   							</center>
				   						</td>
				   						<td>
				   							<center>
				   							<input type="checkbox" name="exceptional<?php echo $outputTransactionNumber?>" value="select"> </input>
				   							</center>
				   						</td>
				   						<td>
				   							<select name="account2Select<?php echo $outputTransactionNumber?>" size="1">
				   								<?php
				   								$sql = "SELECT * FROM account WHERE account_id =". $importedTransactions[$outputTransactionNumber]["accountId"];
				   								$res =& $badgerDb->query($sql);
				   								while ($res->fetchInto ($row)){
													echo "<option value=\"".$row[0]."\" selected = \"selected\">". $row[2] . "</option>";
												}
				   								$sql = "SELECT * FROM account WHERE account_id <>" . $importedTransactions[$outputTransactionNumber]["accountId"];
				   								$res =& $badgerDb->query($sql);
				   								while ($res->fetchInto ($row)){
													echo "<option value=\"".$row[0]."\">". $row[2] . "</option>";
												}
												?>
				   							</select>
		   								</td>	
				   					</tr>
				   				<?php
				   				}
				   				?>
				   			</table>
			   			</div>	
		<!-- innere schleifen die variable $tplOutput... diese dann in das äußere template rein
		//vor die for schleife: $tplOutput = "";
		//in die for schleife: for
		//eval("\$tplOutput .= \"".$tpl->getTemplate("csvImportInputForm")."\";");
		//end for
		//nach der for schleife: echo $tplOutput; -->
	   				
	   				<?php
	   				echo "<input type=\"hidden\" name=\"tableRows\" value=\"" . $transactionNumber . " \">";
					echo $widgets->createButton("btnSubmit", getBadgerTranslation2("importCsv", "save"), "submit", "Widgets/table_save.gif");
				echo "</form>";
	 		
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
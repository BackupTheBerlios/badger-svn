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
/**
 * transform csv to array
 * 
 * @param $fp filepointer, $accountId
 * @return array (categoryId, accountId, title, description, valutaDate, amount, transactionPartner)
 */
function parseToArray($fp, $accountId){
		/**
		 * count Rows of csv
		 * 
		 * @var int 
		 */
		$csvRow = 0;
		//for every line
		while (!feof($fp)) {
			//read one line
			$line = fgets($fp, 1024);
			if (strstr($line, ";")) { //if line is not empty
					$transactionArray = explode(";", $line);
					$valutaDate = explode(".", $transactionArray[0]); //Valuta Date
					$valutaDate[2] = $valutaDate[2];
					$valutaDate[4] = $valutaDate[2] . "-" . $valutaDate[1] . "-" . $valutaDate[0];
					$transactionArray[2] = str_replace("\"","",$transactionArray[2]); //title
					//format amount data
					$transactionArray[3] = str_replace(",",".",$transactionArray[3]); 
					$transactionArray[4] = str_replace(",",".",$transactionArray[4]);
					//if transactionArray[3] == "", it is an expenditure
					if ($transactionArray[3]=="") { $amount = $transactionArray[4];}
					else {$amount = "-" . $transactionArray[3];}				
					/**
					 * transaction array
					 * 
					 * @var array
					 */
					$rowArray = array (
					   "categoryId" => "",
					   "accountId" => $accountId,
					   "title" => str_replace("\"","",$transactionArray[2]),
					   "description" => "",
					   "valutaDate" => $valutaDate[4],
					   "amount" => $amount,
					   "transactionPartner" => ""
					);

			}
			$csvRow++;
			/**
			 * array of all transaction arrays
			 * 
			 * @var array
			 */
			$importedTransactions[$csvRow] = $rowArray;
			echo $csvRow;

		}
		return $importedTransactions;
}
?>

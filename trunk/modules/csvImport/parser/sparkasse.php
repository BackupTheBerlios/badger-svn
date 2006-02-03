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
* Parse .csv files from Sparkasse Rhein-Neckar-Nord (Germany). Tested with files from 30.01.2006
* It should work with files from every Sparkasse in Germany, but it was not tested with others
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
		/**
		 * is set true, a line contains ";" , but not the correct number for this parser (5)
		 * 
		 * @var boolean 
		 */
		$noValidFile = NULL;
		/**
		 * is set true, after the header was ignored
		 * 
		 * @var boolean 
		 */
		$headerIgnored = NULL;
		//for every line
		while (!feof($fp)) {
			//read one line
			$rowArray = NULL;
			//ignore header (first line)
			if (!$headerIgnored){
				for ($headerLine = 0; $headerLine < 1; $headerLine++) {
					$garbage = fgets($fp, 1024);
					//to ignore this code on the next loop run
					$headerIgnored = true;
				}
			}
			//read one line
			$line = fgets($fp, 1024);
			//if line is not empty or is no header
			if (strstr($line, ";")) { 
				//if line contains excactly 10 ';', to ensure it is a valid Sparkasse csv file
				if (substr_count ($line, ";")==10){ 
					// divide String to an array
					$transactionArray = explode(";", $line);
					//format date YY-MM-DD or YYYY-MM-DD
					//delete " in the date, because it could cause problems
					$transactionArray[2] = str_replace("\"","",$transactionArray[2]);
					$valutaDate = explode(".", $transactionArray[2]); //Valuta Date
					$valutaDate[4] = $valutaDate[2] . "-" . $valutaDate[1] . "-" . $valutaDate[0];
					//avoid " & \ in the title, transactionpartner & description, those characters could cause problems
					$transactionArray[3] = str_replace("\"","",$transactionArray[3]);
					$transactionArray[3] = str_replace("\\","",$transactionArray[3]);	
					$transactionArray[4] = str_replace("\"","",$transactionArray[4]);
					$transactionArray[4] = str_replace("\\","",$transactionArray[4]);					
					$transactionArray[5] = str_replace("\"","",$transactionArray[5]);
					$transactionArray[5] = str_replace("\\","",$transactionArray[5]);
					//format amount data to sql format, decimal sign is a .
					$transactionArray[8] = str_replace(",",".",$transactionArray[8]);
					$transactionArray[8] = str_replace("\"","",$transactionArray[8]); 		
					/**
					 * transaction array
					 * 
					 * @var array
					 */
					$rowArray = array (
					   "categoryId" => "",
					   "accountId" => $accountId,
					   "title" => substr($transactionArray[4],0,99),// cut title with more than 100 chars
					   "description" => $transactionArray[3],
					   "valutaDate" => $valutaDate[4],
					   "amount" => $transactionArray[8],
					   "transactionPartner" => $transactionArray[5]
					);
				} else{
					$noValidFile = 'true';
				}
			}
			// if a row contains valid data
			if ($rowArray){
				/**
				 * array of all transaction arrays
				 * 
				 * @var array
				 */
				$importedTransactions[$csvRow] = $rowArray;
				$csvRow++;
			}

		}
		if ($noValidFile) {
			throw new badgerException('importCsv', 'wrongSeperatorNumber');
			//close file
			fclose ($fp);
		} else {
			if ($csvRow == 0){
				throw new badgerException('importCsv', 'noSeperator');
				//close file
				fclose ($fp);
			} else{
				//close file
				fclose ($fp);
				return $importedTransactions;
			}
		}
}
?>
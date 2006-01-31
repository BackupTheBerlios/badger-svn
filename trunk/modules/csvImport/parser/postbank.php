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
* Parse .csv files from Postbank (Germany). Tested with files from 30.01.2006
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
		 * is set true, a line contains "\t" (tabs), but not the correct number for this parser (5)
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
			//ignore header (first 11 lines)
			if (!$headerIgnored){
				for ($headerLine = 0; $headerLine < 11; $headerLine++) {
					$garbage = fgets($fp, 1024);
					//to ignore this code on the next loop run
					$headerIgnored = true;
				}
			}
			//read one line
			$line = fgets($fp, 1024);
			//if line is not empty or is no header
			if (strstr($line, "\t")) { // \t = tab
				//if line contains excactly 7 '\t', to ensure it is a valid Postbank csv file
				if (substr_count ($line, "\t")==7){ 
					// divide String to an array
					$transactionArray = explode("\t", $line);
					//format date YY-MM-DD or YYYY-MM-DD
					$valutaDate = explode(".", $transactionArray[1]); //Valuta Date
					$valutaDate[4] = $valutaDate[2] . "-" . $valutaDate[1] . "-" . $valutaDate[0];
					//avoid " & \ in the title & description, those characters could cause problems
					$transactionArray[2] = str_replace("\"","",$transactionArray[2]);
					$transactionArray[2] = str_replace("\\","",$transactionArray[2]);
					$transactionArray[3] = str_replace("\"","",$transactionArray[3]);
					$transactionArray[3] = str_replace("\\","",$transactionArray[3]);					
					//format amount data to sql format, decimal sign is a .
					$transactionArray[6] = str_replace(",",".",$transactionArray[6]); 
					//if transactionArray[6]is a negative amount (expenditure), the transaction partner is the receiver, ele the sender is the transaction partner 
					if (strstr($transactionArray[6], "-")){ 
						$transactionPartner = $transactionArray[5];
					} else {
						$transactionPartner = $transactionArray[4];
					}				
					/**
					 * transaction array
					 * 
					 * @var array
					 */
					$rowArray = array (
					   "categoryId" => "",
					   "accountId" => $accountId,
					   "title" => substr($transactionArray[3],0,99),// cut title with more than 100 chars
					   "description" => $transactionArray[2],
					   "valutaDate" => $valutaDate[4],
					   "amount" => $transactionArray[6],
					   "transactionPartner" => $transactionPartner
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
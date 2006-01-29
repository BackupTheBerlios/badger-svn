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
		/**
		 * is set true, a line contains ; , but not the correct number for this parser (5)
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
			//ignore header (first 5 lines)
			if (!$headerIgnored){
				for ($headerLine = 0; $headerLine < 5; $headerLine++) {
					$garbage = fgets($fp, 1024);
					//to ignore this code on the next loop run
					$headerIgnored = true;
				}
			}
			//read one line
			$line = fgets($fp, 1024);
			//if line is not empty or is no header
			if (strstr($line, ";")) { 
				//if line contains excactly 5 ';', to ensure it is a valid Deutsche Bank csv file
				if (substr_count ($line, ";")==5){ 
					// divide String to an array
					$transactionArray = explode(";", $line);
					//format date
					$valutaDate = explode(".", $transactionArray[0]); //Valuta Date
					$valutaDate[2] = $valutaDate[2];
					$valutaDate[4] = $valutaDate[2] . "-" . $valutaDate[1] . "-" . $valutaDate[0];
					//to avoid '/' in the title
					$transactionArray[2] = str_replace("\"","",$transactionArray[2]); //title
					//format amount data
					$transactionArray[3] = str_replace(",",".",$transactionArray[3]); 
					$transactionArray[4] = str_replace(",",".",$transactionArray[4]);
					//if transactionArray[3] == "", it is an expenditure, else income
					if ($transactionArray[3]=="") { 
						$amount = $transactionArray[4];
					} else {
						$amount = $transactionArray[3];
					}				
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
				//delete footer (1 line)
				unset($importedTransactions[$csvRow-1]);
				//close file
				fclose ($fp);
				return $importedTransactions;
			}
		}
}
?>
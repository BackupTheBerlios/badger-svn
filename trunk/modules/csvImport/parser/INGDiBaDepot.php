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
* Parse .csv files from DIBA - DEPOT. Tested with an example from forum 28.11.2006
* written by juergen
**/
// The next line determines the displayed name of this parser.
// BADGER_REAL_PARSER_NAME ING DiBa (Depot)

define ('HEADER_END_MARKER', 'ISIN');
define ('FOOTER_END_MARKER', 'Depot-Gesamtwert');

/**
 * transform csv to array
 * Filename: badger/modules/csvImport/parser/INGDiBaDepot.php
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
        $headerIgnored = false;

        //for every line
        while (!feof($fp)) {
            //read one line
            $rowArray = NULL;
            $transactionArray = "";
            $line = fgets($fp, 1024);
           
         //skip header
         if (!$headerIgnored) {
            $tmp = strpos($line, HEADER_END_MARKER);
            if ($tmp !== false && $tmp <= 2) {
               $headerIgnored = true;
            }
           
            continue;
         }
         
         //break on first empty line, e. g. if we are done with the transactions
         if (trim($line) === '') {
            break;
         }
         

            //if line is not empty or is no header
            if (strstr($line, ";")) { 

              //false , until the last line contains a string, wich is as defined above (by juergen)
              if (stristr($line, FOOTER_END_MARKER) === FALSE) {

                //if line contains excactly 8 ;, to ensure it is a valid DIBA-DEPOT.csv file
                if (substr_count ($line, ";") == 8 ){
                  // divide String to an array
                  $transactionArray = explode(";", $line);

                  // added, while ";"NoValidData";"  //jh ack as juergen
                  $transactionArray[0] = str_replace("\"","",$transactionArray[0]);
                  if (!empty ($transactionArray[0]) ) {

                    //format date YY-MM-DD or YYYY-MM-DD
                    $transactionArray[6] = str_replace("\"","",$transactionArray[6]);
                    $transactionArray[6] = substr($transactionArray[6],0,8);
                    $valutaDate = explode(".",$transactionArray[6] ); //Valuta Date
                    // check if year==YY add a "20YY" else year==YYYY do nothing //jh
                    if (strlen($valutaDate[2]) == 2) {
                      $valutaDate[2 ] = "20".$valutaDate[2];
                    }
                    $valutaDate[4] = $valutaDate[2] . "-" . $valutaDate[1] . "-" . $valutaDate[0];
                    $valutaDate1 = new Date($valutaDate[4]);

                    //avoid " & \ in the title & description, those characters could cause problems
                    $transactionArray[1] = str_replace("\"","",$transactionArray[1]);
                    $transactionArray[2] = str_replace("\"","",$transactionArray[2]);
                    $transactionArray[3] = str_replace("\"","",$transactionArray[3]);
                    $transactionArray[4] = str_replace("\"","",$transactionArray[4]);
                    $transactionArray[5] = str_replace("\"","",$transactionArray[5]);
                    $transactionArray[7] = str_replace("\"","",$transactionArray[7]);
                    $transactionArray[8] = str_replace("\"","",$transactionArray[8]);
                    $description = $transactionArray[1].", ".$transactionArray[0].", ".$transactionArray[2]." ".$transactionArray[3]." á ".$transactionArray[4]." ".$transactionArray[5]." = ".$transactionArray[7]." ".$transactionArray[8];
                    $transactionPartner = "Diba - Depot";       

                    //format amount to usersettings
                    $transactionArray[7] = str_replace(".", "",$transactionArray[7]);
                    $transactionArray[7] = str_replace(",",".",$transactionArray[7]);
                    $amount1 = new Amount($transactionArray[7]);
                    /**
                     * transaction array
                     *
                     * @var array
                     */
                    $rowArray = array (
                       "categoryId" => "",
                       "accountId" => $accountId,
                       "title" => substr($transactionArray[1],0,99),// cut title with more than 100 chars
                       "description" => $description,
                       "valutaDate" => $valutaDate1,
                       "amount" => $amount1,
                       "transactionPartner" => $transactionPartner
                    );
                  } // added, while ";"NoValidData";"  //jh ack as juergen
                } else{
                    $noValidFile = 'true';
                }
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
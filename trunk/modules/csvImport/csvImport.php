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
global $importCsvFileName, $upload;


 echo $tpl->getHeader("CSV-Import"); 
if (!isset($_POST['Upload'])){
	

?>
<form action="" method="post" enctype="multipart/form-data" name="Import" id="Import">
  <p>
    <input name="file" type="file" size="100" />
    <br />
    <input type="submit" name="Upload" value="Upload" />
  </p>
</form>
<?php
}

foreach($_FILES as $file_name => $file_array) {
	print "path: ".$file_array['tmp_name']."<br>\n";
	print "name: ".$file_array['name']."<br>\n";
	print "type: ".$file_array['type']."<br>\n";
	print "size: ".$file_array['size']."<br>\n";

	$counter = 0;

	if (is_uploaded_file($file_array['tmp_name'])) {
		$fp = fopen($file_array['tmp_name'], "r") or die("Couldn't open $filename");
 		$csvContent = "";
 		while (!feof($fp)) {
      		$line = fgets($fp, 1024);
      		$csvContent = $csvContent . $line;
      		
 		}
 		//echo $csvContent;
 		//$csvContent = nl2br($csvContent);	
		//move_uploaded_file($file_array['tmp_name'],
		//	"$file_dir/$file_array[name]") or die ("Couldn't copy");
		
		?>
		<!--<textarea name="csv_content" cols="100" rows="10" readonly><?=$csvContent?></textarea>-->
		<!--<div id="hf" style="width:700px; height:300px; border: thin solid black; overflow:scroll;" align="center"><?=$csvContent?></div>-->
		<div id="scroll">
		<?php
		
		print "<table cellspacing='0' cellpadding='2'>";
		/*
		print "<tr><th>Buchungstag</th>";
		print "<th>Soll</th>";
		print "<th>Haben</th>";
		print "<th>Verwendungszweck</th>";
		print "<th>&nbsp;</th></tr>";
		*/
		//$fp = fopen($file_dir ."/". $file_array['name'], "r") or die("Couldn't open $filename");
		$fp = fopen($file_array['tmp_name'], "r") or die("Couldn't open $filename");
 		while (!feof($fp)) {
      		$line = fgets($fp, 1024);
      		print "<tr><td nowrap='nowrap'>$line</td><tr>";
			/*if (strstr($line, ";")) { //if line is not empty
				$counter += 1;
				$csv_array = explode(";", $line);
				$buchungsdatum = explode(".", $csv_array[0]); //Buchungsdatum
				echo $buchungsdatum;
				$buchungsdatum[2] = $buchungsdatum[2];
				$buchungsdatum[4] = $buchungsdatum[2] . "-" . $buchungsdatum[1] . "-" . $buchungsdatum[0];
				$csv_array[2] = str_replace("\"","",$csv_array[2]); //Verwendungszweck
				$csv_array[3] = str_replace(",",".",$csv_array[3]); //Soll
				if ($csv_array[3]=="") { $csv_array[3]=0;};
				$csv_array[4] = str_replace(",",".",$csv_array[4]); //Haben
				if ($csv_array[4]=="") { $csv_array[4]=0;};

				schauen, ob schon ein Datensatz existiert
				$query = "SELECT * FROM ttransaktionen WHERE " .
							"Buchungstag = '" . $buchungsdatum[4] . "' AND " .
							"Verwendungszweck = '" . $csv_array[2] . "';";
				$Transaktionen = mysql_query($query, $Geld) or die(mysql_error());
				$totalRows_Transaktionen = mysql_num_rows($Transaktionen);
				
				//0: kein DS existert
				if ($totalRows_Transaktionen>0) {
					$result = "Datensatz existiert";
				} else {
					//schöner wäre es eine checkbox für jeden DS zu erstellen und nicht sofort zu importieren
					$query = "INSERT INTO ttransaktionen (Buchungstag, Verwendungszweck, Soll, Haben) " .
								" VALUES ('".$buchungsdatum[4]."' , '".$csv_array[2]."', ".$csv_array[3].", ".$csv_array[4].");";
					 mysql_query($query, $Geld) or die(mysql_error());
					$result = "Importiert";
				};
				print "<tr class='row". bcmod($counter,2) . "'>";
				print "<td>" . $buchungsdatum[4] ."</td>";
				print "<td align='right'>" . $csv_array[3] . "</td>";
				print "<td align='right'>" . $csv_array[4] . "</td>";
				print "<td>" . $csv_array[2] . "</td>";
				//print "<td>" . $result . "</td></tr>";
			}*/
		}
		print "</table>";
		print "</div>";
	}
}
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
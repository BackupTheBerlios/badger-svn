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
echo $tpl->getHeader("CSV-Import"); 
//if no Upload yet, show form
$widgets = new WidgetEngine($tpl);

if (!isset($_POST['btnSubmit'])){
	if (!isset($_POST['Upload'])){	
	?>
	<form action="" method="post" enctype="multipart/form-data" name="Import" id="Import">
	  <p>
	    <table border = 0 cellpadding = 5, cellspacing = 5>
				<tr>
					<td>
						<?php echo getBadgerTranslation2("importCsv", "selectFile") . ":"; ?>
					</td>
					<td>
						<input name="file" type="file" size="50" />
					</td>
				</tr>
				<tr>
					<td>
						<?php echo getBadgerTranslation2("importCsv", "selectParser") . ":"; ?>
					</td>
					<td>
						<select name="parserSelect" size="1">
					      <?php
					      	$sql = "SELECT * FROM csv_parser";
							
							$res =& $badgerDb->query($sql);
							while ($res->fetchInto ($row)){
								echo "<option value=\"".$row[2]."\">". $row[1] . "</option>";
							}
					      ?>
	    				</select>
					</td>
				</tr>
				<tr>
					<td>
						<?php echo getBadgerTranslation2("importCsv", "targetAccount") . ":"; ?>
					</td>
					<td>
					 	<select name="accountSelect" size="1">
					      <?php
					      	$sql = "SELECT * FROM account";
							
							$res =& $badgerDb->query($sql);
							while ($res->fetchInto ($row)){
								echo "<option value=\"".$row[0]."\">". $row[2] . "</option>";
							}
					      ?>
				    	</select>
					</td>
				</tr>
				<tr>
					<td>
						<input type="submit" name="Upload" value="<?php echo getBadgerTranslation2("importCsv", "upload"); ?>" />
					</td>
					<td>
					</td>
				</tr>
		</table>
	  </p>
	</form>
	<?php
	}
}
// for every file
if (isset($_POST['Upload'])){
	foreach($_FILES as $file_name => $file_array) {
		$counter = 0;
		//if a file is chosen
		if (is_uploaded_file($file_array['tmp_name'])) {
			//open file
			$fp = fopen($file_array['tmp_name'], "r");
	 		//open selected parser
	 		require_once(BADGER_ROOT . "/modules/csvImport/parser/" . $_POST["parserSelect"]);
	 		$accountId = $_POST["accountSelect"];
	 		//call to parse function
	 		$importedTransactions = parseToArray($fp, $accountId);
	 		//look for existing transaction
	 		#$bereinigete Transaktionen = 
	 		
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
				   				<th><?php echo getBadgerTranslation2("importCsv", "account"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "title"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "description"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "valutaDate"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "amount"); ?> </th>
				   				<th><?php echo getBadgerTranslation2("importCsv", "transactionPartner"); ?> </th>
				   				<?php for ($outputTransactionNumber = 0; $outputTransactionNumber < $transactionNumber; $outputTransactionNumber++) {
				   					echo "<tr>";
				   						//select transaction
				   						echo "<td>";
				   							echo "<center";
				   							echo "<input type=\"checkbox\" name=\"select\" value=\"select\" checked=\"checked\"> </input>";
				   							echo "</center>";
				   						echo "</td>";
				   						//select category
				   						echo "<td>";
				   							echo "<select name=\"categorySelect\" size=\"1\">";
										    	echo "<option>". "" . "</option>";
										    	$sql = "SELECT * FROM category";
												$res =& $badgerDb->query($sql);
												while ($res->fetchInto ($row)){
													echo "<option value=\"".$row[0]."\">". $row[2] . "</option>";
												}
					    					echo "</select>";
				   						echo "</td>";
				   						//account
				   						echo "<td>";
				   							echo "<select name=\"account2Select\" size=\"1\">";
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
				   							echo "</select>";
		   								echo "</td>";
		   								//title
		   								echo "<td>";
				   							echo "<input name=\"title\" type=\"text\" size=\"30\" maxlength=\"99\" value=\"". $importedTransactions[$outputTransactionNumber]["title"] ."\">";
			   							echo "</td>";
			   							//description
		   								echo "<td>";
				   							echo "<input name=\"description\" type=\"text\" size=\"12\" maxlength=\"99\" value=\"". $importedTransactions[$outputTransactionNumber]["description"] ."\">";
			   							echo "</td>";
			   							//valuta date
			   							echo "<td>";
				   							echo "<input name=\"description\" type=\"text\" size=\"8\" maxlength=\"99\" value=\"". $importedTransactions[$outputTransactionNumber]["valutaDate"] ."\">";
			   							echo "</td>";
			   							//amount
			   							echo "<td>";
				   							echo "<input name=\"description\" type=\"text\" size=\"8\" maxlength=\"99\" value=\"". $importedTransactions[$outputTransactionNumber]["amount"] ."\">";
			   							echo "</td>";
			   							echo "<td>";
				   							echo "<input name=\"description\" type=\"text\" size=\"15\" maxlength=\"99\" value=\"". $importedTransactions[$outputTransactionNumber]["transactionPartner"] ."\">";
			   							echo "</td>";
				   					echo "</tr>";
				   				}?>
				   			</table>
			   			</div>	
	   				<?php
					echo $widgets->createButton("btnSubmit", getBadgerTranslation2("importCsv", "save"), "submit", "Widgets/table_save.gif");
				echo "</form>";
	 		
	 		} else{
	 			echo "doof";
	 			//Exception, dass keine neuen Datensätze gefunden wurden
	 		}	
	  	
		}
	}	
}
if (isset($_POST['btnSubmit'])){
		echo "doof";
	 				# array anlegen, dass alle angekreuzten variablen enthält
	 				# array in db schreiben
} 		
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
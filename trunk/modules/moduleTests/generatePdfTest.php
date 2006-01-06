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
require_once(BADGER_ROOT . "/includes/includes.php");

global $URL;
global $Linktext;
global $Image;
global $Dateiname;
global $submit;



?>
 	<h1>Formular </h1> <br /> 
	<form method = "post" action = "<?=$PHP_SELF?>">	
		<table border = 0 cellpadding = 5, cellspacing = 5>
			<tr>
				<td>
					URL:
				</td>
				<td>
					<input type = "text" name = "URL" />
				</td>
			</tr>
			<tr>
				<td>
					Linktext:
				</td>
				<td>
					<input type = "text" name = "Linktext" />
				</td>
			</tr>
			<tr>
				<td>
					Dateiname:
				</td>
				<td>
					<input type = "text" name = "Dateiname" />
				</td>
			</tr>
			<tr>
				<td>
					Image:
				</td>
				<td>
					<input type = "text" name = "Image" />
				</td>
			</tr>
			<tr>
				<td colspan = 2>
					<input type = "submit" value = "PDF Link generieren" name = "submit"" />
				</td>
			</tr>
		</table>
	</form>

<?php

if ($submit){
	echo "<h1>Generierter Link</h1>";
	try{
		throw new BadgerException('html2pdf.missing_url', "hjhjhgj"); 
		//$doof = generatePdf($URL, $Linktext, $Dateiname, $Image);
	 
	                                          
	}catch (Exception $e) {
	   handleBadgerException($e);
	}
}

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
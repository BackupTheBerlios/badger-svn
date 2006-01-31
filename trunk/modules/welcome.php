<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://badger.berlios.org 
*
**/
define("BADGER_ROOT", "..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

$widgets = new WidgetEngine($tpl); 
echo $tpl->getHeader("Seitenname"); //write header */
?>
<center>
	<table height="90%">
		<tr>
			<td valign="middle"><img  src="<?php echo BADGER_ROOT?>/img/badger_comic.gif" /></td><td>Modul 1</td>
		</tr>
	</table>
</center>

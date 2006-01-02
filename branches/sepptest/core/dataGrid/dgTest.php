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
define("BADGER_ROOT", "../../"); 
?>
<?php echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>Title</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<link href="dataGrid.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="../../js/behaviour.js"></script>
<script type="text/javascript" src="../../js/prototype.js"></script>
<script type="text/javascript" src="dataGrid.js"></script>
</head>

<body>
<div id="dataGrid">
<table id="dgTableHead" cellpadding="2" cellspacing="0">
	<tr>
		<td>&nbsp;</td>
		<td>Name</td>
		<td>Vorname</td>
		<td>Alter</td>
		<td>Kontostand</td>
		<td></td>
	</tr>
</table>
<div id="dgScroll">
<table id="dgData" cellpadding="2" cellspacing="0">
	<tr class="dgRow" id="1">
		<td><input type="checkbox" name="1" value="ON" id="check1"/></td>
		<td>Name1</td>
		<td>Vorname</td>
		<td>Alter</td>
		<td>Kontostand</td>
	</tr>
	<tr class="dgRow" id="2">
		<td><input type="checkbox" name="2" value="ON" id="check2"/></td>
		<td>Name2</td>
		<td>Vorname</td>
		<td>Alter</td>
		<td>Kontostand</td>
	</tr>
	<tr class="dgRow" id="3">
		<td><input type="checkbox" name="3" value="ON" id="check3"/></td>
		<td>Name3</td>
		<td>Vorname</td>
		<td>Alter</td>
		<td>Kontostand</td>
	</tr>
	<tr class="dgRow" id="4">
		<td><input type="checkbox" name="4" value="ON" id="check4"/></td>
		<td>Name4</td>
		<td>Vorname</td>
		<td>Alter</td>
		<td>Kontostand</td>
	</tr>
	<tr class="dgRow" id="5">
		<td><input type="checkbox" name="5" value="ON" id="check5"/></td>
		<td>Name5</td>
		<td>Vorname</td>
		<td>Alter</td>
		<td>Kontostand</td>
	</tr>	
</table>

</div>
<table id="dgTableFoot" cellpadding="2" cellspacing="0">
	<tr>
		<td>xx Datensätze</td>
	</tr>
</table>
</div>



</body>
</html>
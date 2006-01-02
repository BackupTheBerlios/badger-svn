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
		<td width="20">&nbsp;</td>
		<td width="100">Name</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td></td>
	</tr>
</table>
<div id="dgScroll">
<table id="dgData" cellpadding="2" cellspacing="0">
	<tr class="dgRow" id="1">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check1"/></td>
		<td width="100">Name1</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="dgRow" id="2">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check2"/></td>
		<td width="100">Name2</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="dgRow" id="3">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check3"/></td>
		<td width="100">Name3</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="dgRow" id="4">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check4"/></td>
		<td width="100">Name4</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="dgRow" id="5">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check5"/></td>
		<td width="100">Name5</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="dgRow" id="6">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check6"/></td>
		<td width="100">Name6</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="dgRow" id="7">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check7"/></td>
		<td width="100">Name7</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
	</tr>
	<tr class="dgRow" id="8">
		<td width="20"><input type="checkbox" name="1" value="ON" id="check8"/></td>
		<td width="100">Name8</td>
		<td width="150">Vorname</td>
		<td width="100">Alter</td>
		<td width="180">Kontostand</td>
		<td>&nbsp;</td>
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
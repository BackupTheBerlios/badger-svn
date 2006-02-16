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
define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();

$widgets->addNavigationHead();
echo $tpl->getHeader("Seitenname");
?>
<script>
/***************************************************************
** Standard translation
*****************************************************************/
function _jsVal_Language() {
    this.err_form = "Please enter/select values for the following fields:\n\n";
    this.err_select = "Please select a valid \"%FIELDNAME%\"";
    this.err_enter = "Please enter a valid \"%FIELDNAME%\"";
};

</script>
	<form name="mainform" onSubmit="return validateCompleteForm(this, 'error');">
		<?php
		
		echo "<br />";
		echo $widgets->addDateField("testdate", "01.01.2006");
		echo "<br />";
		echo $widgets->addDateField("testDT34"); //heutiges Datum als StandardValue
		echo "<br /><br />";
		//echo $widgets->addAutoCompleteField("Suggest");
		//echo "<br />";
		echo $widgets->addToolTipLayer();
		echo $widgets->addToolTip("Description - this is ...");
		echo "<br />";
		echo $widgets->createLabel("fieldname", "name:", true);
		echo "&nbsp;";
		echo $widgets->createField("fieldname", 25, "value", "description", true);
		echo "<br /><br />";
		echo $widgets->createField("passwortfeld", 25, "value", "description", true, "password", "minlength='30''");
		echo "<br /><br />";	
		echo $widgets->createButton("button", "klick mich", "submit", "navigation/cancel.gif");
		echo "<br /><br />";
		echo $widgets->addImage("navigation/cancel.gif");
		echo "<br /><br />";
		$arrNames = array(
			1 => "Jan",
			2 => "Feb",
			3 => "Mar",
			4 => "April");
		echo $widgets->createSelectField("selField", $arrNames, 3, "description", true);
		?>
	</form>
<?php
eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>
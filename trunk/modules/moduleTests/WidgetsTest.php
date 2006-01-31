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
//$widgets->addAutoCompleteJS();
echo $tpl->getHeader("Seitenname"); //write header */
?>
	<form name="mainform">
		<?php
		echo "<br />";
		echo $widgets->addDateField("testdate", "01.01.2006");
		echo "<br />";
		echo $widgets->addDateField("testDT34"); //heutiges Datum als StandardValue
		echo "<br /><br />";
		//echo $widgets->addAutoCompleteField("Suggest");
		//echo "<br />";
		echo $widgets->addToolTipLayer();
		echo $widgets->addToolTipLink("javascript:void(0);", "Description - this is ...", "ToolTip Test");
		echo "<br />";
		echo $widgets->createLabel("fieldname", "name:", true);
		echo "&nbsp;";
		echo $widgets->createField("fieldname", 25, "value", "description", true);
		echo "<br /><br />";		
		echo $widgets->createButton("btnName", "klick mich", "", "navigation/cancel.gif");
		echo "<br /><br />";
		echo $widgets->addImage("navigation/cancel.gif");
		echo "<br /><br />";
		$arrNames = array("Jan", "Feb", "Mar", "April");
		$arrAlias = array(1, 2, 3, 4);
		echo $widgets->createSelectField("selName", $arrAlias, $arrNames, 3,"description", true);
		?>
	</form>
<?php
eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>
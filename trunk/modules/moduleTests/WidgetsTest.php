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
require_once(BADGER_ROOT . "/includes/includes.php");
require_once(BADGER_ROOT . "/core/UserSettings.class.php"); // sollte das nicht auch in die Includes??

$settings = new UserSettings($badgerDb);
//$settings->setProperty("badgerTemplate", "Standard");
//$settings->setProperty("badgerSiteName", "BADGER Finance");
//$settings->setProperty("DateFormat", "dd.mm.yyyy");
$tpl = new TemplateEngine($settings, BADGER_ROOT);
$tpl->addCSS("style.css");
$widgets = new WidgetEngine($tpl);
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addAutoCompleteJS();
echo $tpl->getHeader("Seitenname"); //write header */
?>
	<form name="mainform">
		<?php
		echo $widgets->addDateField("testdate", "");
		echo "<br /><br />";
		echo $widgets->addAutoCompleteField("Suggest");
		echo "<br />";
		echo $widgets->addToolTipLayer();
		echo $widgets->addToolTipLink("javascript:void(0);", "Description - this is ...", "ToolTip Test");
		?>
	</form>
</body>
</html>
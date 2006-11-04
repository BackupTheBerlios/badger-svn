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
$widgets->addPageSettingsJS();
echo $tpl->getHeader("test");

echo <<<EOT
<script>
	var pageSettings = new PageSettings();
	
	pageSettings.setSettingRaw("test1", "test1", "öalksdfjösaldfkjösladfkjasödlfkj");
	pageSettings.setSettingSer("test1", "test2", new Array("hallo", 2, false));
	
	pageSettings.getSettingNamesList("callbackX", "test1");
	
	pageSettings.getSettingRaw("callbackX", "test1", "test1");
	pageSettings.getSettingSer("callbackX", "test1", "test2");
	
	function callbackX(settingNamesList) {
		alert(settingNamesList);

		var msg = "";
		
		for (var i = 0; i < settingNamesList.length; i++) {
			msg += " " + settingNamesList[i];
		}
		
		alert(msg);
	}
</script>
EOT;
?>
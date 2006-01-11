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
define("BADGER_TEMPLATE", "Standard"); 
require_once(BADGER_ROOT . "/includes/includes.php");
  
$tpl = new TemplateEngine(BADGER_TEMPLATE, BADGER_ROOT);
$tpl->addCSS("style.css");
$widgets = new WidgetEngine($tpl);
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addAutoCompleteJS();
echo $tpl->getHeader("BADGER Finance Management - Seitenname"); //write header
?>
<body>
<form>
<?php
echo $widgets->addDateField("testdate", "2006-01-01");
echo $widgets->addAutoCompleteField("FeldName");
echo $widgets->addToolTipLayer();
echo $widgets->addToolTipLink("javascript:void(0);", "Description - sdijgfsodjf ", "click here");

/*
* Offene Punkte:
* - Kalendar sollte mit Montag beginnen
* - Internationalisierung der KalendarValues -> JS durch PHP generieren
* - evtl. wird bei manchen Eingaben der Tag nicht benötigt -> eigenes Element (wenn benötigt)
* - Fehlerbehandlung mit Exceptions
* - Style von ToolTip auslagern(?)
* - BackendLogik von Autocomplete
*/
?>

</form>
</body>
</html>
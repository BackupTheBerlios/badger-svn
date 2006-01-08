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
  
$tpl = new TemplateEngine("Standard", BADGER_ROOT);
$tpl->addCSS("/style.css"); // -> /tpl/themeName/style.css
$tpl->addJavaScript("/js/prototype.js");
$tpl->addJavaScript("/js/behaviour.js");
$tpl->addJavaScript("/js/datagrid.js");
echo $tpl->getHeader("BADGER Finance Management - Seitenname"); //write header
?>
<body>
<?
// Beispiel: Einfügen des aktuellen Datum in das Template
$aktuellesDatum = date("d.m.Y");
eval("echo \"".$tpl->getTemplate("templateTest")."\";");

echo "Themename: ".$tpl->getThemeName(); //falls man irgendwo anzeigen muss, welches theme gerade verwendet wird ...
eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>
</body>
</html>
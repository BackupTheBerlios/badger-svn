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

print("<b>error auf en:</b><br>");
echo getBadgerTranslation2('badger_basics', 'error');
echo "<br><br>";

print("<b>out_of_money auf en:</b><br>");
echo getBadgerTranslation2('badger_basics', 'out_of_money');
echo "<br>";

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
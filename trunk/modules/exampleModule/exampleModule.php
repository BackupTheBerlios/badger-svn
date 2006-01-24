<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Finance Management
* Visit http://badger.berlios.org 
*
**/
define("BADGER_ROOT", "../.."); 
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
// hier wird die ID Dummy aus dem Modul Navigation internationalisiert ausgegeben. 
echo getBadgerTranslation2('Navigation', 'Dummy') . "<br />";
// hier wird ein Element der User Settings ausgelesen (hier die Sprache)
echo $us->getProperty('badgerLanguage') . "<br />\n";
// wenn nicht auskommentiert, wird hier eine Exception geworfen. Dazu muss in der i18n Datenbank ein Eintrag mit:
// page_id = exampleModule
// id = exampleException
// en = Englische Fehlerbeschreibung
// de = deutsche Fehlerbeschreibung
#throw new badgerException('exampleModule', 'exampleException', 'Additional Information');                                            

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
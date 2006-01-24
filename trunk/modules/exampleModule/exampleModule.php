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
  

// Hier der Programmcode rein. Der sollte Exceptions werfen
// nach dem Muster: 

echo "Hello World!" . "<br />";

echo getBadgerTranslation2('Navigation', 'Dummy') . "<br />";
echo $us->getProperty('badgerLanguage') . "<br />\n";

#throw new badgerException('exampleModule', 'exampleException', 'Additional Information');                                            

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
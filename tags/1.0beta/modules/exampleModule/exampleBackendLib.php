<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Finance Management
* Visit http://www.badger-finance.org 
*
**/
define("BADGER_ROOT", "../.."); 
require_once(BADGER_ROOT . "/includes/fileHeaderBackEnd.inc.php");
  

// Hier der Programmcode rein. Der sollte Exceptions werfen
// nach dem Muster: 
function badgerEchoHelloWorld(){
	echo "Hello World!";
}

#throw new badgerException('exampleModule.exampleException', 'Additional Information');                                            

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
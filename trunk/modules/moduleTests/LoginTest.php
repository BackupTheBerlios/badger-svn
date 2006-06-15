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
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");


print("If you can see this text, you are logged in.<br>");
print("<p onclick=\"location.href='../../core/LoginTest.php'\">java</p>");
//print("<a href=\"".BADGER_ROOT."/modules/moduleTests/LoginTest2.php\">Link</a>");

if (isset($_POST)){
	print("<pre>");
	print_r($_POST);
	print("</pre>");
};

require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
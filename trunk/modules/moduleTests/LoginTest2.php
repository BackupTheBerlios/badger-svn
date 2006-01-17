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
require_once(BADGER_ROOT . "/includes/includes.php");

print("<form action=\"LoginTest.php\" method=\"post\">");
print("<input type=\"text\" name=\"test\"><br/>");
print("<input type=\"submit\" value=\"submit\">");
print("</form>");
require_once(BADGER_ROOT . "/includes/fileFooter.php");

?>
<?php

// Initiate Badger Root constant
define("BADGER_ROOT", "../..");

//Include Session Management
include(BADGER_ROOT . "/core/SessionManager/session.ses.php");

echo "Variables:<br>";
print_r($_session);
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
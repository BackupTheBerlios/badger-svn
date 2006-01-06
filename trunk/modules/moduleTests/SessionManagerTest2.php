<?php

// Initiate Badger Root constant
define("BADGER_ROOT_SESSIONMANAGERTEST2", "../../");

//Include Session Management
include(BADGER_ROOT_SESSIONMANAGERTEST2 . "/core/SessionManager/session.ses.php");

echo "Variables:<br>";
print_r($_session);

?>
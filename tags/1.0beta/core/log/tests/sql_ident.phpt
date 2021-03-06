--TEST--
Log: SQL setIdent()
--FILE--
<?php

require_once 'Log.php';

$ident = '12345678901234567890';

$logger = &Log::singleton('sql', 'log_table', $ident, array('dsn' => ''));
echo $logger->getIdent() . "\n";

$logger->setIdent($ident);
echo $logger->getIdent() . "\n";

--EXPECT--
1234567890123456
1234567890123456

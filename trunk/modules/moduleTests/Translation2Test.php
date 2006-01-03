<?php
// set the parameters to connect to your db
$dbinfo = array(
    'hostspec' => DB_HOST,
    'database' => DB_DATABASE_NAME,
    'phptype'  => DB_TYPE,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD
);

$driver = 'DB';

require_once 'Translation2.php';
$tr =& Translation2::factory($driver, $dbinfo);

print("<b>error auf en:</b><br>");
echo $tr->get('error', 'badger_basics', 'en');
echo "<br>";

print("<b>error auf de:</b><br>");
echo $tr->get('error', 'badger_basics', 'de');
echo "<br><br>";

print("<b>out_of_money auf en:</b><br>");
echo $tr->get('out_of_money', 'badger_basics', 'en');
echo "<br>";

print("<b>out_of_money auf de:</b><br>");
echo $tr->get('out_of_money', 'badger_basics', 'de');
echo "<br><br>";

?>
<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
*
Normal method:
$user = "root";
$pwd = "";
$server = "localhost";
$db = "moduleTest";
$table = "test1";
$con = mysql_connect($server, $user, $pwd);
$query = "SELECT * FROM test1";
mysql_select_db ($db);
$res = mysql_query ($query,$con);
while($row = mysql_fetch_array($res)){
print_r($row);
}

##############################
Wer untenstehendes testen will:

DROP TABLE IF EXISTS `test1`;
CREATE TABLE `test1` (
  `ID` int(11) NOT NULL auto_increment,
  `Name` varchar(20) NOT NULL default '',
  `FirstName` varchar(20) NOT NULL default '',
  `Age` float NOT NULL default '0',
  `Height` float NOT NULL default '0',
  PRIMARY KEY  (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- 
-- Dumping data for table `test1`
-- 

INSERT INTO `test1` (`ID`, `Name`, `FirstName`, `Age`, `Height`) VALUES (1, 'Heini', 'Holgi', 23, 155),
(2, 'Volzi', 'Clemi', 21, 186);

 */
define("BADGER_ROOT", "../.."); 
require_once BADGER_ROOT.'/includes/fileHeaderFrontEnd.inc.php';

$sql = "SELECT * FROM user_settings";

$res =& $badgerDb->query($sql);
while ($res->fetchInto ($row)){
	echo $row[1] . "<br />";
}
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
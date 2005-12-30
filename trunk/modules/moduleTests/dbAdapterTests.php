<?php
/*
 * Created on 21.12.2005
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
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
require_once '../../core/dbAdapter/DB.php';
$dsn = array(
	'phptype'=>'mysql',
	'username'=>'root',
	'password'=>'',
	'hostspec'=>'localhost',
	'database'=>'moduleTest'
);

/*
$options = array(
	'portability'=> 'DB_PORTABILITY_ALL'
);
*/

$db =& DB::Connect($dsn);
if (PEAR::isError($db)){
	die($db->getMessage());	
}
$sql = "SELECT * FROM test1";
//$data = array('test1');
$res =& $db->query($sql,$data);
while ($res->fetchInto ($row)){
	echo $row[1] . "<br />";
}
$db->disconnect();
?>

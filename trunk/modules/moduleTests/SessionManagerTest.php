<?
/*
 Copyright (C) 2003
 Alberto Alcocer Medina-Mora
 root@b3co.com

 This program is free software; you can redistribute it and/or
 modify it under the terms of the GNU General Public License
 as published by the Free Software Foundation; either version 2
 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

// Initiate Badger Root constant
define("BADGER_ROOT", "../..");

//Include Session Management
include(BADGER_ROOT . "/core/SessionManager/session.ses.php");

// If form was sent (marked with value s == 1)
// then set a new session variable with the 
// transmitted name and value
if($_GET['s']==1){
	set_session_var($_GET['variable'],$_GET['value']);
}
set_session_var("agent",$_SERVER['HTTP_USER_AGENT']);
?>

<b>Session Handler Tester</b><br>
Sid: <?=$sess?><br>
You have been logged for <?=get_session_length()?> seconds.<br>
Variables:<br><?print_r($_session)?>
<br>
<br>
<?php

?>
<br>
<br>
<form method=get action=SessionManagerTest.php>
variable:<input type=text name=variable><br>
value&nbsp;&nbsp;&nbsp;&nbsp;:<input type=text name=value><br>
<input type=hidden name=s value=1>
<input type="submit" name="submit" value="Submit">
</form>
<br>
<br>
<br>
<a href=SessionManagerEnd.php>End session without flush</a><br>
<a href=SessionManagerEnd.php?f=1>End session flushing</a>
<br><a href=SessionManagerTest2.php>Test a linked File</a>
<?php

/*
Die wichtigsten Befehle:

set_session_var("testvar","testwert");
	setzt eine neue Variable namens "testvar" auf den Wert "testwert"
echo $_session['testvar'];
	Liest die Variable "testvar" aus. Ausgabe ist "testwert".
*/
require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>

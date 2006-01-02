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

include '//core/SessionManager/session.ses.php';
if($_GET['s']==1){
	set_session_var($_GET['variable'],$_GET['valor']);
}
set_session_var("agent",$_SERVER['HTTP_USER_AGENT']);
?>
<b>Session Handler Tester</b><br>
Sid: <?=$sess?><br>
You have been logged for <?=get_session_length()?> seconds.<br>
Variables:<br><?print_r($_session)?>
<br>
<br>
<br>
<br>
<form method=get action=test>
variable:<input type=text name=variable><br>
valor&nbsp;&nbsp;&nbsp;&nbsp;:<input type=text name=valor><br>
<input type=hidden name=s value=1>
</form>
<br>
<br>
<br>
<br>
<br>
<br>
<a href=SessionManagerEnd.php>End session without flush</a><br>
<a href=SessionManagerEnd.php?f=1>End session flushing</a>

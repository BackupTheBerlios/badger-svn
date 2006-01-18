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

//Include Session Management
include(BADGER_ROOT . "/core/SessionManager/session.ses.php");

$us = new UserSettings($badgerDb);

//Retrieve md5�ed password from user settings
$readoutpassword = $us->getProperty('badgerPassword');
$passwordcorrect = false;
if (isset($_session['password']) && $readoutpassword == $_session['password'])
	{$passwordcorrect = true;
	}
	elseif(isset($_POST['password']) && md5($_POST['password']) == $readoutpassword )
		{
			$passwordcorrect = true;
			//create session variable
			set_session_var('password',md5($_POST['password']));
		};

if($passwordcorrect == false)
	{
		print("<b>Please enter your Password:</b><br>");
		print("<form method=\"post\" action=\"".$PHP_SELF."\">");
		print("<input name=\"password\" id=\"password\" size=\"50\" maxlength=\"150\" value=\"\" type=\"password\"><br>");
		foreach( $_POST as $key=>$value ){
			if($key != "password") print("<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\">");
		};
		print("<input value=\"Go!\" name=\"submit\" type=\"submit\">");
		print("</form><br/><br/>");
		if(isset($_POST['password'])){ print("Wrong password.<br/><br/>"); };
		exit();
	};


require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
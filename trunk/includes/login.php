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
//include(BADGER_ROOT . "/core/SessionManager/session.ses.php");

//UserSettings object named "$us" is already existing
//TemplateEngine object named "$tpl" is already existing


//Check if user wants to logout. If so, log him out
if(isset($_GET['logout']) && $_GET['logout']==true){
	session_flush();
};



//Check how many times the user tried to log in, stop working after 10 times

if(isset($_session['locked_out_since']))
{
	$locked_out_since = $_session['locked_out_since'];
}else{
	$locked_out_since = 0;
};

if( $locked_out_since != 0){
	if($locked_out_since - time() + 60 <= 0){
		set_session_var('locked_out_since','0');
		set_session_var('number_of_login_attempts','0');
	}
	else{
		die(getBadgerTranslation2('badger_login','locked_out_part_1') . ($locked_out_since - time() + 60) . getBadgerTranslation2('badger_login','locked_out_part_2'));
	};
};

if(isset($_session['number_of_login_attempts']))
{
	$attempts = $_session['number_of_login_attempts'];
}else{
	$attempts = 0;
};

if($attempts >= 9){
	set_session_var('locked_out_since',time());
};

//Retrieve md5´ed password from user settings
$readoutpassword = $us->getProperty('badgerPassword');
$passwordcorrect = false;
if (isset($_session['password']) && $readoutpassword == $_session['password'])
	{
		$passwordcorrect = true;
	}
	elseif(isset($_POST['password']) && md5($_POST['password']) == $readoutpassword )
		{
			$passwordcorrect = true;
			//create session variable
			set_session_var('password',md5($_POST['password']));
		};

if($passwordcorrect == false)
	{
		$tpl->addCSS("style.css"); // -> /tpl/themeName/style.css
		echo $tpl->getHeader(getBadgerTranslation2('badger_login', 'header')); //write header
		
		set_session_var('number_of_login_attempts',$attempts + 1);
		print("<div class=\"LSPrompt\">" . getBadgerTranslation2('badger_login', 'enter_password') . "</div><br />");
		print("<form method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">");
		print("<input name=\"password\" id=\"password\" size=\"50\" maxlength=\"150\" value=\"\" type=\"password\" /><br />");
		foreach( $_POST as $key=>$value ){
			if($key != "password") print("<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\" />");
		};
		print("<input value=\"".getBadgerTranslation2('badger_login', 'submit_button')."\" name=\"submit\" type=\"submit\" />");
		
		$signature = "";
		if(isset($_GET)){
			$signature = $signature."?";
			foreach( $_GET as $key=>$value ){
				if($key != "send_password"){
					if($signature != "?"){
						$signature = $signature . "&";
					};
				$signature = $signature . $key . "=" . $value;
				};
			};
		};
		
		if($signature != "?"){
			$signature = $signature."&";
		};
		
		print("<br/><a href=\"".$_SERVER['PHP_SELF'].$signature."send_password=true\">".getBadgerTranslation2('badger_login', 'forgot_password')."</a>");
		print("</form><br />");
		if(isset($_POST['password']) && $_POST['password'] == ""){
			print(getBadgerTranslation2('badger_login', 'empty_password')."<br /><br />");
		}elseif(isset($_POST['password'])){
			print(getBadgerTranslation2('badger_login', 'wrong_password')."<br /><br />");
		};
		
		if(isset($_GET['send_password']) && $_GET['send_password'] == "true"){
			print(getBadgerTranslation2('badger_login', 'ask_really_send')."<br/>");
			print("<a href=\"".$_SERVER['PHP_SELF'].$signature."send_password=truetrue\">".getBadgerTranslation2('badger_login', 'ask_really_send_link')."</a><br/>");
		};
		
		if(isset($_GET['send_password']) && $_GET['send_password'] == "truetrue"){
			//send an E-Mail with a new password to the email adress read from the user settings object
			$newpassword = rand ( 0 , 16000 );
			$newpassword = md5($newpassword);
			$newpassword = substr ( $newpassword, 0, 12 );
			if(mail ( $us->getProperty('badgerPassword'), getBadgerTranslation2('badger_login', 'password_sent_mail_subject'), getBadgerTranslation2('badger_login', 'password_sent_mail_part_1').$newpassword.getBadgerTranslation2('badger_login', 'password_sent_mail_part_2'), 'From: forgottenpassword@donotreply.com') ){
				print(getBadgerTranslation2('badger_login', 'sent_password')."<br /><br />");
			} else {
				print(getBadgerTranslation2('badger_login', 'sent_password_failed')."<br/>");
			};
		};
		
		exit();
	}
	else{
		set_session_var('number_of_login_attempts', 0);
	};


require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
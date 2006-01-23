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
$tpl = new TemplateEngine($us, BADGER_ROOT);

$tpl->addCSS("style.css"); // -> /tpl/themeName/style.css
echo $tpl->getHeader(getBadgerTranslation2('badger_login', 'header')); //write header

//Check how many times the user tried to log in, stop working after 10 times

$locked_out_since = $us->getProperty('locked_out_since');
if( $locked_out_since != 0){
	if($locked_out_since - time() + 5 <= 0){
		$us->setProperty('locked_out_since','0');
		$us->setProperty('number_of_login_attempts','0');
	}
	else{
		die(getBadgerTranslation2('badger_login','locked_out_part_1') . ($locked_out_since - time() + 60) . getBadgerTranslation2('badger_login','locked_out_part_2'));
	};
};

$attempts = $us->getProperty('number_of_login_attempts');
if($attempts >= 9){
	$us->setProperty('locked_out_since',time());
};

//Retrieve md5´ed password from user settings
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
		$us->setProperty('number_of_login_attempts', $attempts  + 1);
		print("<div class=\"LSPrompt\">" . getBadgerTranslation2('badger_login', 'enter_password') . "</div><br />");
		print("<form method=\"post\" action=\"".$PHP_SELF."\">");
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
		
		print("<br/><a href=\"".$PHP_SELF.$signature."send_password=true\">".getBadgerTranslation2('badger_login', 'forgot_password')."</a>");
		print("</form><br />");
		if(isset($_POST['password']) && $_POST['password'] == ""){
			print(getBadgerTranslation2('badger_login', 'empty_password')."<br /><br />");
		}elseif(isset($_POST['password'])){
			print(getBadgerTranslation2('badger_login', 'wrong_password')."<br /><br />");
		};
		
		if(isset($_GET['send_password']) && $_GET['send_password'] == "true"){
			print(getBadgerTranslation2('badger_login', 'ask_really_send')."<br/>");
			print("<a href=\"".$PHP_SELF.$signature."send_password=truetrue\">".getBadgerTranslation2('badger_login', 'ask_really_send_link')."</a><br/>");
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
		$us->setProperty('number_of_login_attempts', 0);
	};


require_once(BADGER_ROOT . "/includes/fileFooter.php");
?>
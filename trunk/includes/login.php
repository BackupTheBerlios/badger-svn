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




// check if this is a mandatory password change
// because of standard password

if(isset($_POST['SubmitMandatoryChangePassword'])){
	// Validate submitted values
	// Is yet to be implemented

	$validation_change_password = true;
	$validation_change_password_errors = "";
	
	if( md5($_POST['OldPassword']) != $us->getProperty('badgerPassword')){
		$validation_change_password = false;
		$validation_change_password_errors = $validation_change_password_errors.getBadgerTranslation2('UserSettingsAdmin','error_old_password_not_correct')."<br>";
	};
	
	if( $_POST['NewPassword'] != $_POST['NewPasswordConfirm']){
		$validation_change_password = false;
		$validation_change_password_errors = $validation_change_password_errors.getBadgerTranslation2('UserSettingsAdmin','error_confirm_failed')."<br>";
	};
	
	if( $_POST['NewPassword'] == ""){
		$validation_change_password = false;
		$validation_change_password_errors = $validation_change_password_errors.getBadgerTranslation2('UserSettingsAdmin','error_empty_password')."<br>";
	};
	
	if( $_POST['NewPassword'] == "badger"){
		$validation_change_password = false;
		$validation_change_password_errors = $validation_change_password_errors.getBadgerTranslation2('UserSettingsAdmin','error_standard_password')."<br>";
	};
	
	if($validation_change_password == true){
		$us->setProperty('badgerPassword',md5($_POST['NewPassword']));
	};
	
	if(isset($validation_change_password ) && $validation_change_password == true){
		// Initialization
		$tpl->addCSS("style.css");
		$widgets = new WidgetEngine($tpl); 
		$widgets->addToolTipJS();
		$widgets->addCalendarJS();
		$widgets->addAutoCompleteJS();
		echo $widgets->addToolTipLayer();
		echo $tpl->getHeader(getBadgerTranslation2('UserSettingsAdmin','site_name')); //write header */
		//end of Initialization
		
		$us->setProperty('badgerPassword',md5($_POST['NewPassword']));
		echo getBadgerTranslation2('UserSettingsAdmin','password_change_commited')."<br/>";
		echo "<a href\"".$_SERVER['PHP_SELF']."\">".getBadgerTranslation2('UserSettingsAdmin','linktext_after_successful_mandatory_change')."</a>";
	};
	
	if(isset($validation_change_password ) && $validation_change_password != true){
		// Initialization
		$tpl->addCSS("style.css");
		$widgets = new WidgetEngine($tpl); 
		$widgets->addToolTipJS();
		$widgets->addCalendarJS();
		$widgets->addAutoCompleteJS();
		echo $widgets->addToolTipLayer();
		echo $tpl->getHeader(getBadgerTranslation2('UserSettingsAdmin','site_name')); //write header */
		//end of Initialization
		
		echo($validation_change_password_errors."<br/>");
		echo("<a href\"".$_SERVER['PHP_SELF']."\">".getBadgerTranslation2('UserSettingsAdmin','linktext_after_failed_mandatory_change')."</a>");
	};
	
	exit;
};

// If user is trying to logout, log him out
if(isset($_GET['logout']) && $_GET['logout']==true){
	session_flush();
	unset($_session['password']);
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

// Check how many times the user tried to log in, stop working after x times

if(isset($_session['number_of_login_attempts']) )
{
	$attempts = $_session['number_of_login_attempts'];
}else{
	$attempts = 0;
};



if($attempts >= $us->getProperty('badgerMaxLoginAttempts') ){
	set_session_var('locked_out_since',time());
};

//--

if(isset($_session['locked_out_since']) && $passwordcorrect != true)
{
	$locked_out_since = $_session['locked_out_since'];
}else{
	$locked_out_since = 0;
};

//---


if( $locked_out_since != 0){
	if($locked_out_since - time() + $us->getProperty('badgerLockOutTime') <= 0){
		set_session_var('locked_out_since','0');
		set_session_var('number_of_login_attempts','0');
	}
	else{
		// if user locked himself out
		// because of too many login attempts
		echo $tpl->getHeader(getBadgerTranslation2('badger_login', 'header')); //write header
		echo "<br/>";
		print (getBadgerTranslation2('badger_login','locked_out_part_1') . ($locked_out_since - time() + $us->getProperty('badgerLockOutTime')) . getBadgerTranslation2('badger_login','locked_out_part_2'));
		echo "<br/><br/>";
		
		if(isset($_GET)){
			$signature = "?";
			foreach( $_GET as $key=>$value ){
				if($key != "send_password"){
					if($signature != "?"){
						$signature = $signature . "&";
					};
				$signature = $signature . $key . "=" . $value;
				};
			};
		}else{
			$signature = "";
		};
		set_session_var('number_of_login_attempts','0');
		die("<a href=\"".$_SERVER['PHP_SELF'].$signature."\">".getBadgerTranslation2('badger_login', 'locked_out_refresh')."</a>");
	};
};

//---

//check if standard password or empty password is used.
//if so, ask user to change his password

if (
isset($_POST['password']) && md5($_POST['password']) == $us->getProperty('badgerPassword') && md5($_POST['password']) == '7e59cb5b2f52c763bc846471fe5942e4' || (isset($_session['password']) && $_session['password'] == $us->getProperty('badgerPassword')	&& $_session['password'] == '7e59cb5b2f52c763bc846471fe5942e4')){
	// Initialization

	$tpl->addCSS("style.css");
	$widgets = new WidgetEngine($tpl); 
	$widgets->addToolTipJS();
	$widgets->addCalendarJS();
	$widgets->addAutoCompleteJS();
	echo $widgets->addToolTipLayer();
	echo $tpl->getHeader(getBadgerTranslation2('UserSettingsAdmin','site_name')); //write header */
	
	$passwordcorrect = false;
	//end of Initialization
	
	print("<form name=\"MandatoryChangePassword\" method=\"post\" action=\"".$_SERVER['PHP_SELF']."\">");
	echo "<div class=\"USAHeading\">".getBadgerTranslation2('UserSettingsAdmin','mandatory_change_password_heading')."</div><br/>";
	
	foreach( $_POST as $key=>$value ){
		if($key != "password") print("<input type=\"hidden\" name=\"".$key."\" value=\"".$value."\" />");
	};
	
	echo $widgets->createLabel("OldPassword", getBadgerTranslation2('UserSettingsAdmin','old_password_name'), true);
	echo "&nbsp;";
	echo $widgets->createField("OldPassword", 50, "", getBadgerTranslation2('UserSettingsAdmin','old_password_description'), true);
	
	echo "<br/><br/>";
	
	echo $widgets->createLabel("NewPassword", getBadgerTranslation2('UserSettingsAdmin','new_password_name'), true);
	echo "&nbsp;";
	echo $widgets->createField("NewPassword", 50, "", getBadgerTranslation2('UserSettingsAdmin','new_password_description'), true);
	
	echo "<br/><br/>";
	
	echo $widgets->createLabel("NewPasswordConfirm", getBadgerTranslation2('UserSettingsAdmin','new_password_confirm_name'), true);
	echo "&nbsp;";
	echo $widgets->createField("NewPasswordConfirm", 50, "", getBadgerTranslation2('UserSettingsAdmin','new_password_confirm_description'), true);
	
	echo "<br/><br/>";
	
	echo "<input name=\"SubmitMandatoryChangePassword\" value=\"".getBadgerTranslation2('UserSettingsAdmin','submit_button')."\" type=\"submit\">";
	
	echo "</form>";
	
	exit();
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
		if(isset($_GET['logout']) && $_GET['logout']==true){
			echo getBadgerTranslation2('badger_login', 'you_are_logout');
		};
		
		exit();
	}
	else{
		set_session_var('number_of_login_attempts', 0);
	};

?>
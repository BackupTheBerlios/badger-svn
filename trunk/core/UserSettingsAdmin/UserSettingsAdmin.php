<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://badger.berlios.org 
*
**/
define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");

// Initialization

$tpl->addCSS("style.css");
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addAutoCompleteJS();
echo $tpl->getHeader(getBadgerTranslation2('UserSettingsAdmin','site_name')); //write header */
echo $widgets->addToolTipLayer();
// Was the form for change of User Settings sent?
if( isset( $_POST['SubmitUserSettings'] ) ){
	// Validate submitted values
	// Is yet to be implemented
	// So let´s just say that all is well for now

	$validation_user_settings = true;
	$validation_user_settings_errors = "";
	
	
	//This is not final!
	if($_POST['Template'] != "Standard"){
		$validation_user_settings = false;
		$validation_user_settings_errors = $validation_user_settings_errors."Function temporarily disabled.";
	};
	
	//---
	if( $validation_user_settings == true ){
		// If validation returns a good result, commit the changes
		$us->setProperty('badgerTemplate',$_POST['Template']);
		$us->setProperty('badgerLanguage',$_POST['Language']);
		$us->setProperty('badgerDateFormat',$_POST['DateFormat']);
		$us->setProperty('badgerMaxLoginAttempts',$_POST['MaximumLoginAttempts']);
		$us->setProperty('badgerLockOutTime',$_POST['LockOutTime']);
		if($_POST['Seperators'] == ".,"){
			$us->setProperty('badgerDecimalSeperator',",");
			$us->setProperty('badgerThousandSeperator',".");
		}else{
			$us->setProperty('badgerDecimalSeperator',".");
			$us->setProperty('badgerThousandSeperator',",");
		};
	};
	
};

// Was the form for change of password sent?
if( isset( $_POST['SubmitChangePassword'] ) ){
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
};


// Print form for change of User Settings.

?>

	<form name="UserSettings" method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<?php
		
		echo "<br/><div class=\"USAHeading\">".getBadgerTranslation2('UserSettingsAdmin','user_settings_heading')."</div><br/>";
		
		echo $widgets->createLabel("Template", getBadgerTranslation2('UserSettingsAdmin','template_name'), true);
		echo "&nbsp;";
		echo $widgets->createField("Template", 50, $us->getProperty('badgerTemplate'), getBadgerTranslation2('UserSettingsAdmin','template_description'), true);
		
		echo "<br/><br/>";
		
		$langs = $tr->getLangs();
		echo $widgets->createLabel("Language", getBadgerTranslation2('UserSettingsAdmin','language_name'), true);
		echo "&nbsp;";
		echo $widgets->createSelectField("Language", $langs, $default=$us->getProperty('badgerLanguage'), $description=getBadgerTranslation2('UserSettingsAdmin','language_description'), $mandatory=true);
		
		echo "<br/><br/><br/>";
		
		$date_formats = array(
			"dd.mm.yyyy" => getBadgerTranslation2('DateFormats','dd.mm.yyyy'),
			"dd/mm/yyyy" => getBadgerTranslation2('DateFormats','dd/mm/yyyy'),
			"dd-mm-yyyy" => getBadgerTranslation2('DateFormats','dd-mm-yyyy'),
			"yyyy-mm-dd" => getBadgerTranslation2('DateFormats','yyyy-mm-dd'),
			"yyyy/mm/dd" => getBadgerTranslation2('DateFormats','yyyy/mm/dd')
		);
		
		echo $widgets->createLabel("DateFormat", getBadgerTranslation2('UserSettingsAdmin','date_format_name'), true);
		echo "&nbsp;";
		echo $widgets->createSelectField("DateFormat", $date_formats, $default=$us->getProperty('badgerDateFormat'), $description=getBadgerTranslation2('UserSettingsAdmin','date_format_description'), $mandatory=true);
		
		echo "<br/><br/>";
		
		$seperators = array(
			".," => "12.345,67",
			",." => "12,345.67"
		);
		
		if($us->getProperty('badgerDecimalSeperator') == ","){
			$seperators_default = ".,";
		}else{
			$seperators_default = ",.";
		};
		
		echo $widgets->createLabel("Seperators", getBadgerTranslation2('UserSettingsAdmin','seperators_name'), true);
		echo "&nbsp;";
		echo $widgets->createSelectField("Seperators", $seperators, $default=$seperators_default, $description=getBadgerTranslation2('UserSettingsAdmin','seperators_description'), $mandatory=true);
		
		echo "<br/><br/><br/>";
				
		echo $widgets->createLabel("MaximumLoginAttempts", getBadgerTranslation2('UserSettingsAdmin','maximum_login_attempts_name'), true);
		echo "&nbsp;";
		echo $widgets->createField("MaximumLoginAttempts", 10, $us->getProperty('badgerMaxLoginAttempts'), getBadgerTranslation2('UserSettingsAdmin','maximum_login_attempts_description'), true);
		
		echo "<br/><br/>";
		
		echo $widgets->createLabel("LockOutTime", getBadgerTranslation2('UserSettingsAdmin','lock_out_time_name'), true);
		echo "&nbsp;";
		echo $widgets->createField("LockOutTime", 10, $us->getProperty('badgerLockOutTime'), getBadgerTranslation2('UserSettingsAdmin','lock_out_time_description'), true);
		
		echo "<br/><br/>";
		echo $widgets->createButton("SubmitUserSettings", getBadgerTranslation2('UserSettingsAdmin','submit_button'), "submit", "Widgets/table_save.gif");
		
		?>
		
	</form>

<br/><br/>
<?php

// If Validation for User Settings had returned
// a bad result, print the error messages
if(isset($validation_user_settings) && $validation_user_settings != true){
	print("<div class=\"USAError\">".$validation_user_settings_errors."</div><br/><br/>");
};

if(isset($validation_user_settings) && $validation_user_settings == true){
	print(getBadgerTranslation2('UserSettingsAdmin','user_settings_change_commited')."<br/><br/>");
};

// Print Form for change of password 
?>
	
	<form name="ChangePassword" method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<?php	
		
		echo "<div class=\"USAHeading\">".getBadgerTranslation2('UserSettingsAdmin','change_password_heading')."</div><br/>";
		
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
		
		echo $widgets->createButton("SubmitChangePassword", getBadgerTranslation2('UserSettingsAdmin','submit_button'), "submit", "Widgets/table_save.gif");
		//echo "<input name=\"SubmitChangePassword\" value=\"".getBadgerTranslation2('UserSettingsAdmin','submit_button')."\" type=\"submit\">";

echo "</form>";

//--

if(isset($validation_change_password ) && $validation_change_password == true){
	$us->setProperty('badgerPassword',md5($_POST['NewPassword']));
	echo getBadgerTranslation2('UserSettingsAdmin','password_change_commited')."<br/>";
};

if(isset($validation_change_password ) && $validation_change_password != true){
	echo $validation_change_password_errors;
};

//--

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>
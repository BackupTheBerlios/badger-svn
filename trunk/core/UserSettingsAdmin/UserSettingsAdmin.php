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
$widgets->addNavigationHead();
echo $tpl->getHeader(getBadgerTranslation2('UserSettingsAdmin','site_name')); //write header */
echo $widgets->getNavigationBody();
echo $widgets->addToolTipLayer();

//$us->setProperty('badgerPassword',md5("badger"));

// Was the form for change of User Settings sent?
if( isset( $_POST['SubmitUserSettings'] ) ){
	// Validate submitted values
	// Is yet to be implemented
	// So let´s just say that all is well for now

	$validation_user_settings = true;
	$validation_user_settings_errors = "";
	
	
	// is something written in the change password fields?
	if (
		(
			(isset($_POST['OldPassword']) && $_POST['OldPassword']=="")
			&&
			(isset($_POST['NewPassword']) && $_POST['NewPassword']=="")
			&&
			(isset($_POST['NewPasswordConfirm']) && $_POST['NewPasswordConfirm']=="")
		)
	)
	{
		$change_password = false;
	}
		else
		{
		$change_password = true;
		
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
	
		//if($validation_change_password == true){
		//	$us->setProperty('badgerPassword',md5($_POST['NewPassword']));
		//};
	};
	
	
	if((
		isset($validation_user_settings) && $validation_user_settings == true
		&&
		$change_password == true
		&&
		isset($validation_change_password )	&&	$validation_change_password == true
	)||(
		isset($validation_user_settings) && $validation_user_settings == true
		&&
		$change_password == false
	)){
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
		if($change_password == true){
			$us->setProperty('badgerPassword',md5($_POST['NewPassword']));
		};
	};
	
}else{
	$change_password = false;
};

// Print form for change of User Settings.

		$USFormLabel = getBadgerTranslation2('UserSettingsAdmin','user_settings_heading');
		
		//$templates = array();
		
		$templatesString = "\$templates = array(";
		$first = true;
		
		if($handle = opendir(BADGER_ROOT . '/tpl')){
			while($file = readdir($handle)) {
				if($file != "." && $file != ".." && $file != ".svn") {
					if($first == true) {
						$templatesString .= "\"" . $file . "\"" . "=>" . "\"" . $file . "\"";
						$first = false;
						}
					else {
						$templatesString .= ",\"" . $file . "\"" . "=>" . "\"". $file . "\"";
					};
				};
			};
		};
		
		$templatesString .= ");";
		
		eval ($templatesString);
		
		
		$TemplateLabel = $widgets->createLabel("Template", getBadgerTranslation2('UserSettingsAdmin','template_name'), true);
		$TemplateField = $widgets->createSelectField("Template", $templates, $default=$us->getProperty('badgerTemplate'), $description=getBadgerTranslation2('UserSettingsAdmin','template_description'), true);
		
		$langs = $tr->getLangs();
		$LanguageLabel = $widgets->createLabel("Language", getBadgerTranslation2('UserSettingsAdmin','language_name'), true);

		$LanguageField = $widgets->createSelectField("Language", $langs, $default=$us->getProperty('badgerLanguage'), $description=getBadgerTranslation2('UserSettingsAdmin','language_description'), $mandatory=true);
		
		$date_formats = array(
			"dd.mm.yyyy" => getBadgerTranslation2('DateFormats','dd.mm.yyyy'),
			"dd/mm/yyyy" => getBadgerTranslation2('DateFormats','dd/mm/yyyy'),
			"dd-mm-yyyy" => getBadgerTranslation2('DateFormats','dd-mm-yyyy'),
			"yyyy-mm-dd" => getBadgerTranslation2('DateFormats','yyyy-mm-dd'),
			"yyyy/mm/dd" => getBadgerTranslation2('DateFormats','yyyy/mm/dd')
		);
		
		$DateFormatLabel = $widgets->createLabel("DateFormat", getBadgerTranslation2('UserSettingsAdmin','date_format_name'), true);
		$DateFormatField = $widgets->createSelectField("DateFormat", $date_formats, $default=$us->getProperty('badgerDateFormat'), $description=getBadgerTranslation2('UserSettingsAdmin','date_format_description'), $mandatory=true);
		
		$seperators = array(
			".," => "12.345,67",
			",." => "12,345.67"
		);
		
		if($us->getProperty('badgerDecimalSeperator') == ","){
			$seperators_default = ".,";
		}else{
			$seperators_default = ",.";
		};
		
		$SeperatorsLabel = $widgets->createLabel("Seperators", getBadgerTranslation2('UserSettingsAdmin','seperators_name'), true);
		$SeperatorsField = $widgets->createSelectField("Seperators", $seperators, $default=$seperators_default, $description=getBadgerTranslation2('UserSettingsAdmin','seperators_description'), $mandatory=true);
				
		$MaxLoginLabel = $widgets->createLabel("MaximumLoginAttempts", getBadgerTranslation2('UserSettingsAdmin','maximum_login_attempts_name'), true);
		$MaxLoginField = $widgets->createField("MaximumLoginAttempts", 10, $us->getProperty('badgerMaxLoginAttempts'), getBadgerTranslation2('UserSettingsAdmin','maximum_login_attempts_description'), true);
		
		
		$LockOutTimeLabel = $widgets->createLabel("LockOutTime", getBadgerTranslation2('UserSettingsAdmin','lock_out_time_name'), true);
		$LockOutTimeField = $widgets->createField("LockOutTime", 10, $us->getProperty('badgerLockOutTime'), getBadgerTranslation2('UserSettingsAdmin','lock_out_time_description'), true);
		

		$StartPageLabel = $widgets->createLabel("StartPageLabel", getBadgerTranslation2('UserSettingsAdmin','start_page_name'), true);
		$StartPageField = $widgets->createField("StartPageField", 20, $us->getProperty('badgerStartPage'), getBadgerTranslation2('UserSettingsAdmin','start_page_description'), true);
		
		$SessionTimeLabel = $widgets->createLabel("SessionTimeLabel", getBadgerTranslation2('UserSettingsAdmin','session_time_name'), true);
		$SessionTimeField = $widgets->createField("SessionTimeField", 10, $us->getProperty('badgerSessionTime'), getBadgerTranslation2('UserSettingsAdmin','session_time_description'), true);
		
// Print Form for change of password 
		
		$PWFormLabel = getBadgerTranslation2('UserSettingsAdmin','change_password_heading');
		
		$OldPwLabel = $widgets->createLabel("OldPassword", getBadgerTranslation2('UserSettingsAdmin','old_password_name'), true);
		
		$OldPwField = $widgets->createField("OldPassword", 20, "", getBadgerTranslation2('UserSettingsAdmin','old_password_description'), true);
		
		$NewPwLabel = $widgets->createLabel("NewPassword", getBadgerTranslation2('UserSettingsAdmin','new_password_name'), true);
		$NewPwField = $widgets->createField("NewPassword", 20, "", getBadgerTranslation2('UserSettingsAdmin','new_password_description'), true);
		
		$ConfPwLabel = $widgets->createLabel("NewPasswordConfirm", getBadgerTranslation2('UserSettingsAdmin','new_password_confirm_name'), true);
		
		$ConfPwField = $widgets->createField("NewPasswordConfirm", 20, "", getBadgerTranslation2('UserSettingsAdmin','new_password_confirm_description'), true);
		
		$btnSubmit = $widgets->createButton("SubmitUserSettings", getBadgerTranslation2('UserSettingsAdmin','submit_button'), "submit", "Widgets/table_save.gif");
		
		eval("echo \"".$tpl->getTemplate("UserSettingsAdmin/UserSettingsAdmin")."\";");
		
		//$rows = "";
		//for
		//eval("\$rows .=  \"".$tpl->getTemplate("UserSettingsAdmin/PWChange")."\";");
		//for end
		//echo $rows;
		
		//echo "<input name=\"SubmitChangePassword\" value=\"".getBadgerTranslation2('UserSettingsAdmin','submit_button')."\" type=\"submit\">";

//--


// If Validation for User Settings had returned
// a bad result, print the error messages
if(isset($validation_user_settings) && $validation_user_settings != true){
	print("<div class=\"USAError\">".$validation_user_settings_errors."</div><br/><br/>");
};

//--


if((
		isset($validation_user_settings) && $validation_user_settings == true
		&&
		$change_password == true
		&&
		isset($validation_change_password )	&&	$validation_change_password == true
	)||(
		isset($validation_user_settings) && $validation_user_settings == true
		&&
		$change_password == false
	)
){
	if($change_password == true){
		print(getBadgerTranslation2('UserSettingsAdmin','user_settings_change_commited')."<br/><br/>");
		echo getBadgerTranslation2('UserSettingsAdmin','password_change_commited')."<br/>";
	};
};

if($change_password == true && isset($validation_change_password ) && $validation_change_password != true){
	echo $validation_change_password_errors;
};

//--

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>
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
$widgets->addNavigationHead();

//$us->setProperty('badgerPassword',md5("badger"));

// Was the form for change of User Settings sent?
if( isset( $_POST['SubmitUserSettings'] ) ){
	// Validate submitted values
	// Is yet to be implemented
	// So lets just say that all is well for now

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
	} else {
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
		$us->setProperty('badgerStartPage', $_POST['StartPageField']);
		$us->setProperty('badgerSessionTime', $_POST['SessionTimeField']);

		if($_POST['Seperators'] == ".,"){
			$us->setProperty('badgerDecimalSeparator',",");
			$us->setProperty('badgerThousandSeparator',".");
		}else{
			$us->setProperty('badgerDecimalSeparator',".");
			$us->setProperty('badgerThousandSeparator',",");
		};
		if($change_password == true){
			$us->setProperty('badgerPassword',md5($_POST['NewPassword']));
		};
		
		if (isset($_POST['autoExpandPlannedTransactionsField']) && $_POST['autoExpandPlannedTransactionsField']) {
			$us->setProperty('autoExpandPlannedTransactions', true);
		} else {
			$us->setProperty('autoExpandPlannedTransactions', false);
		}
	};
	
} else {
	$change_password = false;
};

$pageHeading = getBadgerTranslation2('UserSettingsAdmin', 'title');

echo $tpl->getHeader($pageHeading);
echo $widgets->addToolTipLayer();

// Print form for change of User Settings.

$USFormLabel = getBadgerTranslation2('UserSettingsAdmin','user_settings_heading');
$FsHeading = getBadgerTranslation2('UserSettingsAdmin', 'fs_heading');
//$templates = array();

$templatesString = "\$templates = array(";
$first = true;

//directory listing of the /tpl/ - folder
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
$TemplateField = $widgets->createSelectField("Template", $templates, $default=$us->getProperty('badgerTemplate'), $description=getBadgerTranslation2('UserSettingsAdmin','template_description'), true, 'style="width: 10em;"');

$langs = $tr->getLangs();
$LanguageLabel = $widgets->createLabel("Language", getBadgerTranslation2('UserSettingsAdmin','language_name'), true);

$LanguageField = $widgets->createSelectField("Language", $langs, $default=$us->getProperty('badgerLanguage'), $description=getBadgerTranslation2('UserSettingsAdmin','language_description'), $mandatory=true, 'style="width: 10em;"');

$date_formats = array(
	"dd.mm.yyyy" => getBadgerTranslation2('DateFormats','dd.mm.yyyy'),
	"dd/mm/yyyy" => getBadgerTranslation2('DateFormats','dd/mm/yyyy'),
	"dd-mm-yyyy" => getBadgerTranslation2('DateFormats','dd-mm-yyyy'),
	"yyyy-mm-dd" => getBadgerTranslation2('DateFormats','yyyy-mm-dd'),
	"yyyy/mm/dd" => getBadgerTranslation2('DateFormats','yyyy/mm/dd')
);

$DateFormatLabel = $widgets->createLabel("DateFormat", getBadgerTranslation2('UserSettingsAdmin','date_format_name'), true);
$DateFormatField = $widgets->createSelectField("DateFormat", $date_formats, $default=$us->getProperty('badgerDateFormat'), $description=getBadgerTranslation2('UserSettingsAdmin','date_format_description'), $mandatory=true, 'style="width: 10em;"');

$seperators = array(
	".," => "12.345,67",
	",." => "12,345.67"
);

if($us->getProperty('badgerDecimalSeparator') == ","){
	$seperators_default = ".,";
}else{
	$seperators_default = ",.";
};

$SeperatorsLabel = $widgets->createLabel("Seperators", getBadgerTranslation2('UserSettingsAdmin','seperators_name'), true);
$SeperatorsField = $widgets->createSelectField("Seperators", $seperators, $default=$seperators_default, $description=getBadgerTranslation2('UserSettingsAdmin','seperators_description'), $mandatory=true, 'style="width: 10em;"');
		
$MaxLoginLabel = $widgets->createLabel("MaximumLoginAttempts", getBadgerTranslation2('UserSettingsAdmin','maximum_login_attempts_name'), true);
$MaxLoginField = $widgets->createField("MaximumLoginAttempts", 0, $us->getProperty('badgerMaxLoginAttempts'), getBadgerTranslation2('UserSettingsAdmin','maximum_login_attempts_description'), true, 'text', 'style="width: 10em;"');


$LockOutTimeLabel = $widgets->createLabel("LockOutTime", getBadgerTranslation2('UserSettingsAdmin','lock_out_time_name'), true);
$LockOutTimeField = $widgets->createField("LockOutTime", 0, $us->getProperty('badgerLockOutTime'), getBadgerTranslation2('UserSettingsAdmin','lock_out_time_description'), true, 'text', 'style="width: 10em;"');


$StartPageLabel = $widgets->createLabel("StartPageLabel", getBadgerTranslation2('UserSettingsAdmin','start_page_name'), true);
$StartPageField = $widgets->createField("StartPageField", 0, $us->getProperty('badgerStartPage'), getBadgerTranslation2('UserSettingsAdmin','start_page_description'), true, 'text', 'style="width: 10em;"');

$SessionTimeLabel = $widgets->createLabel("SessionTimeLabel", getBadgerTranslation2('UserSettingsAdmin','session_time_name'), true);
$SessionTimeField = $widgets->createField("SessionTimeField", 0, $us->getProperty('badgerSessionTime'), getBadgerTranslation2('UserSettingsAdmin','session_time_description'), true, 'text', 'style="width: 10em;"');

$autoExpandPlannedTransactionsLabel = $widgets->createLabel('autoExpandPlannedTransactionsLabel', getBadgerTranslation2('UserSettingsAdmin', 'autoExpandPlannedTransactionsName'), true);
$autoExpandPlannedTransactionsField = $widgets->createField('autoExpandPlannedTransactionsField', 0, 1, getBadgerTranslation2('UserSettingsAdmin','autoExpandPlannedTransactionsDescription'), true, 'checkbox', $us->getProperty('autoExpandPlannedTransactions') ? 'checked="checked"' : '');

// Print Form for change of password 

$PWFormLabel = getBadgerTranslation2('UserSettingsAdmin','change_password_heading');

$OldPwLabel = $widgets->createLabel("OldPassword", getBadgerTranslation2('UserSettingsAdmin','old_password_name'), false);
$OldPwField = $widgets->createField("OldPassword", 20, "", getBadgerTranslation2('UserSettingsAdmin','old_password_description'), false, 'password');

$NewPwLabel = $widgets->createLabel("NewPassword", getBadgerTranslation2('UserSettingsAdmin','new_password_name'), false);
$NewPwField = $widgets->createField("NewPassword", 20, "", getBadgerTranslation2('UserSettingsAdmin','new_password_description'), false, 'password');

$ConfPwLabel = $widgets->createLabel("NewPasswordConfirm", getBadgerTranslation2('UserSettingsAdmin','new_password_confirm_name'), false);
$ConfPwField = $widgets->createField("NewPasswordConfirm", 20, "", getBadgerTranslation2('UserSettingsAdmin','new_password_confirm_description'), false, 'password');

$btnSubmit = $widgets->createButton("SubmitUserSettings", getBadgerTranslation2('UserSettingsAdmin','submit_button'), "submit", "Widgets/accept.gif", "accesskey='s'");

// Begin of Feedback

$Feedback = "<br/>";

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
		$Feedback .= getBadgerTranslation2('UserSettingsAdmin','password_change_commited')."<br/>";
	};
$Feedback .= getBadgerTranslation2('UserSettingsAdmin','user_settings_change_commited')."<br/><br/>";
};

if($change_password == true && isset($validation_change_password ) && $validation_change_password != true){
	$Feedback .= $validation_change_password_errors;
};

// If Validation for User Settings had returned
// a bad result, print the error messages
if(isset($validation_user_settings) && $validation_user_settings != true){
	$Feedback .= "<div class=\"USAError\">".$validation_user_settings_errors."</div><br/><br/>";
};

// End of Feedback

eval("echo \"".$tpl->getTemplate("UserSettingsAdmin/UserSettingsAdmin")."\";");
//--


//--

eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");
?>
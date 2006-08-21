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
define ('BADGER_ROOT', '../..');

require_once BADGER_ROOT . '/includes/fileHeaderFrontEnd.inc.php';
require_once BADGER_ROOT . '/modules/importExport/exportLogic.php';
require_once BADGER_ROOT . '/core/urlTools.php';

if (isset($_GET['mode'])) {
	$action = $_GET['mode'];
} else {
	$action = 'displayProcedure';
}

switch ($action) {
	case 'backupDatabase':
		backupDatabase();
		break;
	
	case 'update':
		update();
		break;
	
	case 'displayProcedure':
	default:
		displayProcedure();
		break;
}

function displayProcedure() {
	global $tpl;
	$widgets = new WidgetEngine($tpl); 
	
	$widgets->addNavigationHead();
	
	$procedureTitle = getUpdateTranslation('updateProcedure', 'pageTitle');
	echo $tpl->getHeader($procedureTitle);
	
	$legend = getUpdateTranslation('updateProcedure', 'legend');
	$updateInformation = getUpdateTranslation('updateProcedure', 'updateInformation');
	$dbVersionText = getUpdateTranslation('updateProcedure', 'dbVersionText');
	$dbVersion = getBadgerDbVersion();
	$fileVersionText = getUpdateTranslation('updateProcedure', 'fileVersionText');
	$fileVersion = BADGER_VERSION;
	$stepDescription = getUpdateTranslation('updateProcedure', 'stepDescription');
	$step1PreLink = getUpdateTranslation('updateProcedure', 'step1PreLink');
	$step1LinkTarget = BADGER_ROOT . '/core/update/update.php?mode=backupDatabase';
	$step1LinkText = getUpdateTranslation('updateProcedure', 'step1LinkText');
	$step1PostLink = getUpdateTranslation('updateProcedure', 'step1PostLink');
	$step2PreLink = getUpdateTranslation('updateProcedure', 'step2PreLink');
	$step2LinkTarget = BADGER_ROOT . '/core/update/update.php?mode=update';
	$step2LinkText = getUpdateTranslation('updateProcedure', 'step2LinkText');
	$step2PostLink = getUpdateTranslation('updateProcedure', 'step2PostLink');
	
	eval('echo "' . $tpl->getTemplate('update/procedure') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function backupDatabase() {
	sendSqlDump();
	
	exit;
}

function update() {
	global $tpl, $us;
	
	$versionHistory = array (
		array (
			'version' => '1.0 beta',
			'function' => 'update1_0betaTo1_0beta2',
		),
		array (
			'version' => '1.0 beta 2',
			'function' => false
		)
	);

	$widgets = new WidgetEngine($tpl); 
	
	$widgets->addNavigationHead();
	
	$updateTitle = getUpdateTranslation('updateUpdate', 'pageTitle');
	echo $tpl->getHeader($updateTitle);

	$currentDbVersion = getBadgerDbVersion();

	for ($dbVersionIndex = 0; $dbVersionIndex < count($versionHistory); $dbVersionIndex++) {
		if ($versionHistory[$dbVersionIndex]['version'] == $currentDbVersion) {
			break;
		}
	}
	
	$numNeededSteps = count($versionHistory) - $dbVersionIndex - 1;
	
	$dbVersion = $currentDbVersion;
	$fileVersion = BADGER_VERSION;
	
	$betweenVersions = '';
	for ($i = $dbVersionIndex + 1; $i < count($versionHistory) - 1; $i++) {
		$currentVersion = $versionHistory[$i];
		eval('$betweenVersions .= "' . $tpl->getTemplate('update/betweenVersionsLine') . '";');
	}

	$betweenVersionsText = getUpdateTranslation('updateUpdate', 'betweenVersionsText');
	
	if ($betweenVersions !== '') {
		eval('$betweenVersionsBlock = "' . $tpl->getTemplate('update/betweenVersionsBlock') . '";');
	} else {
		$betweenVersionsBlock = '';
	}

	$updateLog = '';
	
	$preCurrentText = getUpdateTranslation('updateUpdate', 'preCurrentText');
	$postCurrentText = getUpdateTranslation('updateUpdate', 'postCurrentText');
	$postNextText = getUpdateTranslation('updateUpdate', 'postNextText');

	$logEntryHeader = getUpdateTranslation('updateUpdate', 'logEntryHeader');

	for ($currentVersionIndex = $dbVersionIndex; $currentVersionIndex < count($versionHistory) - 1; $currentVersionIndex++) {
		$currentVersion = $versionHistory[$currentVersionIndex]['version'];
		$nextVersion = $versionHistory[$currentVersionIndex + 1]['version'];

		eval('$updateLog .= "' . $tpl->getTemplate('update/updateStepHeader') . '";');
		
		$logEntry = $versionHistory[$currentVersionIndex]['function']();
		
		eval('$updateLog .= "' . $tpl->getTemplate('update/updateStepEntry') . '";');
	}

	$updateInformation = getUpdateTranslation('updateUpdate', 'updateInformation');
	$errorInformation = getUpdateTranslation('updateUpdate', 'errorInformation');
	$dbVersionText = getUpdateTranslation('updateProcedure', 'dbVersionText');
	$fileVersionText = getUpdateTranslation('updateProcedure', 'fileVersionText');
	$updateFinished = getUpdateTranslation('updateUpdate', 'updateFinished');
	
	$goToStartPagePreLink = getUpdateTranslation('updateUpdate', 'goToStartPagePreLink');
	$goToStartPageLinkText = getUpdateTranslation('updateUpdate', 'goToStartPageLinkText');
	$goToStartPagePostLink = getUpdateTranslation('updateUpdate', 'goToStartPagePostLink');
	
	$startPageURL = getAbsoluteStartPage();

	eval('echo "' . $tpl->getTemplate('update/update') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function update1_0betaTo1_0beta2() {
	$log = '';
	
/*
	$log .= "&rarr; Deleting duplicate i18n entries.\n";
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'error_confirm_failed' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'error_empty_password' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'error_old_password_not_correct' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'new_password_name' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'old_password_description' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'old_password_name' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'password_change_commited' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'seperators_description' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'seperators_name' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'session_time_description' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'session_time_name' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'site_name' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'start_page_description' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'start_page_name' AND LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'submit_button' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'template_description' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'template_name' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'user_settings_change_commited' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'user_settings_heading' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'UserSettingsAdmin' AND `id` = 'login_button' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'Navigation' AND `id` = 'Help' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'forecast' AND `id` = 'plannedTransactionsLabel' LIMIT 1");
	$log .= doQuery("DELETE FROM `i18n` WHERE `page_id` = 'forecast' AND `id` = 'plannedTransactionsToolTip' LIMIT 1");
*/
	$log .= "&rarr; Adding primary key to i18n.\n";
	$log .= doQuery("ALTER IGNORE TABLE `i18n` ADD PRIMARY KEY ( `page_id` , `id` ( 255 ) )", array(-1));
	
	$log .= "&rarr; Adding primary key to langs.\n";
	$log .= doQuery("ALTER TABLE `langs` ADD PRIMARY KEY ( `id` )", array(-1));

	$log .= "&rarr; Removing old sessions.\n";
	$log .= doQuery("TRUNCATE TABLE session_master");
	$log .= "&rarr; Adding primary key to session_master.\n";
	$log .= doQuery("ALTER TABLE `session_master` ADD PRIMARY KEY ( `sid` )", array(-1));
	
	$log .= "&rarr; Removing old session data.\n";
	$log .= doQuery("TRUNCATE TABLE session_global");
	$log .= "&rarr; Adding primary key to session_global.\n";
	$log .= doQuery("ALTER TABLE `session_global` ADD PRIMARY KEY ( `sid` , `variable` );", array(-1));	

	$log .= "&rarr; Creating references from transferred recurring transactions to recurring transactions.\n";
	$log .= doQuery("UPDATE `finished_transaction` f SET `planned_transaction_id` = (SELECT planned_transaction_id FROM planned_transaction p WHERE f.category_id <=> p.category_id AND f.account_id <=> p.account_id AND f.title <=> p.title AND f.transaction_partner <=> p.transaction_partner AND f.amount <=> p.amount LIMIT 1)");

	$log .= "&rarr; Creating new account id sequence table.\n";
	$log .= doQuery("CREATE TABLE IF NOT EXISTS `account_ids_seq` (
			`id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	$log .= "&rarr; Deleting old account id sequence.\n";
	$log .= doQuery("TRUNCATE TABLE account_ids_seq");
	$log .= "&rarr; Inserting max id to account sequence table.\n";
	$log .= doQuery("INSERT INTO account_ids_seq (id) VALUES ((SELECT MAX(account_id) FROM account) + 1)");

	$log .= "&rarr; Creating new category id sequence table.\n";
	$log .= doQuery("CREATE TABLE IF NOT EXISTS `category_ids_seq` (
			`id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	$log .= "&rarr; Deleting old category id sequence.\n";
	$log .= doQuery("TRUNCATE TABLE category_ids_seq");
	$log .= "&rarr; Inserting max id to category sequence table.\n";
	$log .= doQuery("INSERT INTO category_ids_seq (id) VALUES ((SELECT MAX(category_id) FROM category) + 1)");

	$log .= "&rarr; Creating new currency id sequence table.\n";
	$log .= doQuery("CREATE TABLE IF NOT EXISTS `currency_ids_seq` (
			`id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	$log .= "&rarr; Deleting old currency id sequence.\n";
	$log .= doQuery("TRUNCATE TABLE currency_ids_seq");
	$log .= "&rarr; Inserting max id to currency sequence table.\n";
	$log .= doQuery("INSERT INTO currency_ids_seq (id) VALUES ((SELECT MAX(currency_id) FROM currency) + 1)");

	$log .= "&rarr; Creating new finished transaction id sequence table.\n";
	$log .= doQuery("CREATE TABLE IF NOT EXISTS `finished_transaction_ids_seq` (
			`id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	$log .= "&rarr; Deleting old finished transaction id sequence.\n";
	$log .= doQuery("TRUNCATE TABLE finished_transaction_ids_seq");
	$log .= "&rarr; Inserting max id to finished transaction sequence table.\n";
	$log .= doQuery("INSERT INTO finished_transaction_ids_seq (id) VALUES ((SELECT MAX(finished_transaction_id) FROM finished_transaction) + 1)");

	$log .= "&rarr; Creating new navigation id sequence table.\n";
	$log .= doQuery("CREATE TABLE IF NOT EXISTS `navi_ids_seq` (
			`id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	$log .= "&rarr; Deleting old navigation id sequence.\n";
	$log .= doQuery("TRUNCATE TABLE navi_ids_seq");
	$log .= "&rarr; Inserting max id to navigation sequence table.\n";
	$log .= doQuery("INSERT INTO navi_ids_seq (id) VALUES ((SELECT MAX(navi_id) FROM navi) + 1)");

	$log .= "&rarr; Creating new planned transaction id sequence table.\n";
	$log .= doQuery("CREATE TABLE IF NOT EXISTS `planned_transaction_ids_seq` (
			`id` int(10) unsigned NOT NULL auto_increment PRIMARY KEY
		) ENGINE=MyISAM DEFAULT CHARSET=utf8");
	$log .= "&rarr; Deleting old planned transaction id sequence.\n";
	$log .= doQuery("TRUNCATE TABLE planned_transaction_ids_seq");
	$log .= "&rarr; Inserting max id to planned transaction sequence table.\n";
	$log .= doQuery("INSERT INTO planned_transaction_ids_seq (id) VALUES ((SELECT MAX(planned_transaction_id) FROM planned_transaction) + 1)");

	$log .= "&rarr; Dropping old account id sequence table.\n";
	$log .= doQuery("DROP TABLE IF EXISTS accountids_seq");
	$log .= doQuery("DROP TABLE IF EXISTS accountIds_seq");

	$log .= "&rarr; Dropping old category id sequence table.\n";
	$log .= doQuery("DROP TABLE IF EXISTS categoryids_seq");
	$log .= doQuery("DROP TABLE IF EXISTS categoryIds_seq");

	$log .= "&rarr; Dropping old currency id sequence table.\n";
	$log .= doQuery("DROP TABLE IF EXISTS currencyids_seq");
	$log .= doQuery("DROP TABLE IF EXISTS currencyIds_seq");

	$log .= "&rarr; Dropping old finished transaction id sequence table.\n";
	$log .= doQuery("DROP TABLE IF EXISTS finishedtransactionids_seq");
	$log .= doQuery("DROP TABLE IF EXISTS finishedTransactionIds_seq");

	$log .= "&rarr; Dropping old navigation id sequence table.\n";
	$log .= doQuery("DROP TABLE IF EXISTS naviids_seq");
	$log .= doQuery("DROP TABLE IF EXISTS naviIds_seq");

	$log .= "&rarr; Dropping old planned transaction id sequence table.\n";
	$log .= doQuery("DROP TABLE IF EXISTS plannedtransactionids_seq");
	$log .= doQuery("DROP TABLE IF EXISTS plannedTransactionIds_seq");

	$log .= "&rarr; Dropping CSV parser table not used anymore.\n";
	$log .= doQuery("DROP TABLE IF EXISTS csv_parser\n");
	
	$log .= "&rarr; Adding new translation entries.\n";
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'futureCalcSpanLabel', en = 'Planning horizon (months)', de = 'Planungszeitraum in Monaten'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'futureCalcSpanDescription', en = 'Please enter how far into the future you would like to be able to plan. With usability in mind, recurring transactions will only be displayed as far into the future as you enter here. ', de = 'Geben Sie hier ein, wie weit Sie in die Zukunft planen m&ouml;chten. Wiedekehrende Transaktionen werden der &Uuml;bersichtlichkeit wegen nur so weit in die Zukunft dargestellt, wie Sie hier eingeben.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics', id = 'trendTotal', en = 'Total', de = 'Gesamt'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountAccount', id = 'pageTitlePropNew', en = 'New Account', de = 'Konto erstellen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'badger_login', id = 'sessionTimeout', en = 'Your session timed out. You have been logged out for security reasons.', de = 'Ihre Sitzung ist abgelaufen. Sie wurden aus Sicherheitsgr&uuml;nden ausgeloggt.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'step1PostLink', en = '', de = ''");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'step2PreLink', en = 'Please click the following link to start the database update.', de = 'Bitte klicken Sie auf folgenden Link, um die Datenbank-Aktualisierung zu beginnen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'step1PreLink', en = 'Please click the following link and save the file to your computer.', de = 'Bitte klicken Sie auf folgenden Link und speichern Sie die Datei auf Ihrem Computer.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'step1LinkText', en = 'Save backup', de = 'Sicherungskopie speichern'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'fileVersionText', en = 'File version:', de = 'Datei-Version:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'stepDescription', en = 'The update consists of two simple steps. First, a backup of the database is saved to your computer. This preserves your data in the rare case anything goes wrong. Second, the database is updated.', de = 'Die Aktualisierung besteht aus zwei einfachen Schritten. Zuerst wird eine Sicherheitskopie der Datenbank auf Ihrem Computer gespeichert. Dadurch bleiben Ihre Daten auch im unwahrscheinlichen Fall eines Fehlschlags erhalten. Anschlie&szlig;end wird die Datenbank aktualisiert.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'dbVersionText', en = 'Database version:', de = 'Datenbank-Version:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'legend', en = 'Steps to Update', de = 'Schritte zur Aktualisierung'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'updateInformation', en = 'BADGER finance detected an update of its files. This page updates the database. All your data will be preserved.', de = 'BADGER finance hat eine Aktualisierung seiner Dateien festgestellt. Diese Seite aktualisiert die Datenbank. Ihre Daten bleiben vollst&auml;ndig erhalten.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'pageTitle', en = 'Update BADGER finance', de = 'BADGER finance aktualisieren'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'step2LinkText', en = 'Update database', de = 'Datenbank aktualisieren'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateProcedure', id = 'step2PostLink', en = '', de = ''");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'pageTitle', en = 'Updating BADGER finance', de = 'BADGER finance wird aktualisiert'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'betweenVersionsText', en = 'Versions in between:', de = 'Dazwischenliegende Versionen:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'preCurrentText', en = 'Update from', de = 'Aktualisierung von'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'postCurrentText', en = 'to', de = 'auf'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'postNextText', en = '', de = ''");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'logEntryHeader', en = 'Information from the update:', de = 'Informationen der Aktualisierung:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'updateInformation', en = 'BADGER finance is now performing the update. It is performed step-by-step, one step for each version.', de = 'Die Aktualisierung wird nun durchgef&uuml;hrt. Dies findet Schritt f&uuml;r Schritt statt, einen Schritt f&uuml;r jede Version.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'errorInformation', en = 'Please read the output of the process. If it encounters any severe errors they are written in red. In this case, please send the whole output to the BADGER development team (see help for contact info).', de = 'Bitte lesen sie die Ausgabe dieses Prozesses. Die einfachen Informationen sind auf Englisch gehalten. Falls der Prozess irgend welche schweren Fehler meldet, sind diese rot eingef&auml;rbt. Bitte schicken Sie in diesem Fall die gesamte Ausgabe an das BADGER Entwicklungsteam (siehe Hilfe f&uuml;r Kontaktinformationen).'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'updateFinished', en = 'The update has finished.', de = 'Die Aktualisierung ist beendet.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'severeError', en = 'The update encountered a severe error. Please send the whole output to the BADGER finance development team.', de = 'Die Aktualisierung stie&szlig; auf einen schweren Fehler. Bitte schicken Sie die gesamte Ausgabe an das BADGER finance development team.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'goToStartPagePreLink', en = 'Please ', de = 'Bitte '");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'goToStartPageLinkText', en = 'go to start page', de = 'zur Startseite gehen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'updateUpdate', id = 'goToStartPagePostLink', en = ' to continue.', de = ' um fortzusetzen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'goToStartPagePreLink', en = 'Please ', de = 'Bitte '");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'goToStartPageLinkText', en = 'go to start page', de = 'zur Startseite gehen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'goToStartPagePostLink', en = ' to continue.', de = ' um fortzusetzen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'newerVersion', en = 'Your backup file was from a previous version of BADGER finance. A database update will occur.', de = 'Ihre Sicherheitskopie war von einer vorherigen Version von BADGER finance. Es wird eine Datenbank-Aktualisierung stattfinden.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'DateFormats', id = 'mm/dd/yy', en = 'mm/dd/yy', de = 'mm/tt/jj'");
	
	$log .= "&rarr; Updating old translation entries.\n";
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'session_time_name', en = 'Session time (min):', de = 'Sessionlänge (min):'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'askImportVersionInfo', en = 'If you upload a backup created with a previous BADGER finance version an update to the current database layout will occur after importing. All your data will be preserved.', de = 'Falls Sie eine von einer vorherigen BADGER-finance-Version erstellten Sicherheitskopie hochladen, wird im Anschluss an den Import eine Datenbank-Aktualisierung auf die neueste Version stattfinden. All Ihre Daten bleiben erhalten.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'insertSuccessful', en = 'Data successfully saved. Please use the password from the backup file to log in.', de = 'Die Daten wurden erfolgreich importiert. Bitte benutzen Sie das Passwort aus der Sicherheitskopie zum einloggen.'");

	$log .= "&rarr; Updating demo account menu links.\n";
	$log .= doQuery("UPDATE user_settings SET prop_value = 's:2:\"35\";' WHERE prop_key = 'accountNaviId_3'");
	$log .= doQuery("UPDATE user_settings SET prop_value = 's:2:\"34\";' WHERE prop_key = 'accountNaviId_4'");
	
	$log .= "&rarr; Increasing security of session timeout.\n";
	$log .= doQuery("UPDATE user_settings SET prop_value = 's:2:\"30\";' WHERE prop_key = 'badgerSessionTime' AND prop_value = 's:4:\"9999\";'");

	$log .= "&rarr; Updating database version to 1.0 beta 2.\n";
	$log .= doQuery("REPLACE user_settings SET prop_key = 'badgerDbVersion', prop_value = 's:10:\"1.0 beta 2\";'");

	$log .= "\n&rarr;&rarr; Update to version 1.0 beta 2 finished. &larr;&larr;\n\n";

	return $log;
}

function doQuery($sql, $acceptableResults = array()) {
	global $badgerDb, $tpl;

	$severeError = getUpdateTranslation('updateUpdate', 'severeError');

	$log = "SQL: $sql\n";
	
	$result = $badgerDb->query($sql);

	if (PEAR::isError($result)) {
		$log .= 'Query resulted in error. Error code: ' . $result->getCode() . ' Error message: ' . $result->getMessage() . ' Native error message: ' . $result->getUserInfo() . "\n"; 

		if (array_search($result->getCode(), $acceptableResults) === false) {
			eval('$log .= "' . $tpl->getTemplate('update/severeError') . '";');
		} else {
			$log .= "This error is not severe.\n";
		}
	} else {
		$log .= "Query succeeded. " . $badgerDb->affectedRows() . " rows affected.\n";
	}
	
	$log .= "\n";
	
	return $log;
}

function getUpdateTranslation($pageId, $id) {
	global $us;
	
	static $transTbl = array (
		'updateProcedure' => array (
			'en' => array (
				'pageTitle' => 'Update BADGER finance',
				'legend' => 'Steps to Update',
				'updateInformation' => 'BADGER finance detected an update of its files. This page updates the database. All your data will be preserved.',
				'dbVersionText' => 'Database version:',
				'fileVersionText' => 'File version:',
				'stepDescription' => 'The update consists of two simple steps. First, a backup of the database is saved to your computer. This preserves your data in the rare case anything goes wrong. Second, the database is updated.',
				'step1PreLink' => 'Please click the following link and save the file to your computer.',
				'step1LinkText' => 'Save backup',
				'step1PostLink' => '',
				'step2PreLink' => 'Please click the following link to start the database update.',
				'step2LinkText' => 'Update database',
				'step2PostLink' => ''
			),
			'de' => array (
				'pageTitle' => 'BADGER finance aktualisieren',
				'legend' => 'Schritte zur Aktualisierung',
				'updateInformation' => 'BADGER finance hat eine Aktualisierung seiner Dateien festgestellt. Diese Seite aktualisiert die Datenbank. Ihre Daten bleiben vollständig erhalten.',
				'dbVersionText' => 'Datenbank-Version:',
				'fileVersionText' => 'Datei-Version:',
				'stepDescription' => 'Die Aktualisierung besteht aus zwei einfachen Schritten. Zuerst wird eine Sicherheitskopie der Datenbank auf Ihrem Computer gespeichert. Dadurch bleiben Ihre Daten auch im unwahrscheinlichen Fall eines Fehlschlags erhalten. Anschließend wird die Datenbank aktualisiert.',
				'step1PreLink' => 'Bitte klicken Sie auf folgenden Link und speichern Sie die Datei auf Ihrem Computer.',
				'step1LinkText' => 'Sicherungskopie speichern',
				'step1PostLink' => '',
				'step2PreLink' => 'Bitte klicken Sie auf folgenden Link, um die Datenbank-Aktualisierung zu beginnen.',
				'step2LinkText' => 'Datenbank aktualisieren',
				'step2PostLink' => ''
			)
		),
		'updateUpdate' => array (
			'en' => array (
				'pageTitle' => 'Updating BADGER finance',
				'betweenVersionsText' => 'Versions in between:',
				'preCurrentText' => 'Update from',
				'postCurrentText' => 'to',
				'postNextText' => '',
				'logEntryHeader' => 'Information from the update:',
				'updateInformation' => 'BADGER finance is now performing the update. It is performed step-by-step, one step for each version.',
				'errorInformation' => 'Please read the output of the process. If it encounters any severe errors they are written in red. In this case, please send the whole output to the BADGER development team (see help for contact info).',
				'updateFinished' => 'The update has finished.',
				'severeError' => 'The update encountered a severe error. Please send the whole output to the BADGER finance development team.',
				'goToStartPagePreLink' => 'Please ',
				'goToStartPageLinkText' => 'go to start page',
				'goToStartPagePostLink' => ' to continue.'
			),
			'de' => array (
				'pageTitle' => 'BADGER finance wird aktualisiert',
				'betweenVersionsText' => 'Dazwischenliegende Versionen:',
				'preCurrentText' => 'Aktualisierung von',
				'postCurrentText' => 'auf',
				'postNextText' => '',
				'logEntryHeader' => 'Informationen der Aktualisierung:',
				'updateInformation' => 'Die Aktualisierung wird nun durchgeführt. Dies findet Schritt für Schritt statt, einen Schritt für jede Version.',
				'errorInformation' => 'Bitte lesen sie die Ausgabe dieses Prozesses. Die einfachen Informationen sind auf Englisch gehalten. Falls der Prozess irgend welche schweren Fehler meldet, sind diese rot eingefärbt. Bitte schicken Sie in diesem Fall die gesamte Ausgabe an das BADGER Entwicklungsteam (siehe Hilfe für Kontaktinformationen).',
				'updateFinished' => 'Die Aktualisierung ist beendet.',
				'severeError' => 'Die Aktualisierung stieß auf einen schweren Fehler. Bitte schicken Sie die gesamte Ausgabe an das BADGER finance development team.',
				'goToStartPagePreLink' => 'Bitte ',
				'goToStartPageLinkText' => 'zur Startseite gehen',
				'goToStartPagePostLink' => ' um fortzusetzen.'
			)
		)
	);

	$trans = getBadgerTranslation2($pageId, $id);
	
	if (PEAR::isError($trans) || $trans === '') {
		$trans = $transTbl[$pageId][$us->getProperty('badgerLanguage')][$id];
	}
	
	return $trans;
}

require_once BADGER_ROOT . '/includes/fileFooter.php';
?>
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

if (isset($_GET['mode'])) {
	$action = getGPC($_GET, 'mode');
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
			'function' => 'update1_0betaTo1_0beta2'
		),
		array (
			'version' => '1.0 beta 2',
			'function' => 'update1_0beta2To1_0beta3'
		),
		array (
			'version' => '1.0 beta 3',
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

	$startPageURL = BADGER_ROOT . '/' . $us->getProperty('badgerStartPage');

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
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics', id = 'showButton', en = 'Show', de = 'Anzeigen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGrid', id = 'open', en = 'Open', de = 'Öffnen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'Navigation', id = 'releaseNotes', en = 'Release Notes', de = 'Versionsgeschichte (englisch)'");
	$log .= doQuery("REPLACE i18n SET page_id = 'welcome', id = 'pageTitle', en = 'Your accounts', de = 'Ihre Konten'");

	$log .= "&rarr; Updating old translation entries.\n";
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'mandatory_change_password_heading', en = 'You are currently using the BADGER standard password.<br />\r\nPlease change it.<br />\r\nSie können die Sprache von BADGER unter dem Menüpunkt System / Preferences unter Language ändern.', de = 'Sie verwenden momentan das BADGER Standardpasswort.<br />\r\nBitte ändern Sie es.<br />\r\nYou can change the language of BADGER at menu System / Einstellungen, field Sprache.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'session_time_name', en = 'Session time (min):', de = 'Sessionlänge (min):'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'askImportVersionInfo', en = 'If you upload a backup created with a previous BADGER finance version an update to the current database layout will occur after importing. All your data will be preserved.', de = 'Falls Sie eine von einer vorherigen BADGER-finance-Version erstellten Sicherheitskopie hochladen, wird im Anschluss an den Import eine Datenbank-Aktualisierung auf die neueste Version stattfinden. All Ihre Daten bleiben erhalten.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importExport', id = 'insertSuccessful', en = 'Data successfully saved. Please use the password from the backup file to log in.', de = 'Die Daten wurden erfolgreich importiert. Bitte benutzen Sie das Passwort aus der Sicherheitskopie zum einloggen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'periodicalToolTip', en = 'This setting is used for automatic pocket money calculation. When calculating your pocket money from the past (i.e. your regular money spending habits), the BADGER will ignore all transactions marked &quot;periodical&quot; because it assumes that you have those already covered in the future recurring transactions. An example would be your rent. For the future rent, you have entered a recurring transactions. Past rent payments are flagged &quot;periodical transactions&quot; and not used for pocket money calculation.', de = 'Diese Wert wird bei der automatischen Taschengeldberechnung benutzt. Wenn der BADGER das Taschengeld der Vergangenheit (also Ihr Ausgabeverhalten) berechnet, ignoriert er periodische Transaktionen, da angenommen wird, dass diese über wiederkehrende Transaktionen in der Zukunft bereits erfasst sind. Ein Beispiel hierfür ist die Miete: Für die Zukunft wird die Miete über eine wiederkehrende Transaktion abgebildet, muss also nicht im Taschengeld berücksichtigt werden. In der Vergangenheit sind die Mietzahlungen periodische Transaktionen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'ExceptionalToolTip', en = 'This setting is used for automatic pocket money calculation. When calculating your pocket money from the past (i.e. your regular money spending habits), the BADGER will ignore all transactions marked &quot;exceptional&quot; because they do not resemble your usual spending habits. Examples would be a surprise car repair job, a new tv (unless you buy new tvs every month) or a holiday.', de = 'Diese Wert wird bei der automatischen Taschengeldberechnung benutzt. Wenn der BADGER das Taschengeld der Vergangenheit (also Ihr Ausgabeverhalten) berechnet, ignoriert er außergewöhnliche Transaktionen. Beispiele hierfür sind eine große Autoreparatur, ein neuer Fernseher (wenn man nicht jeden Monat einen neuen kauft) oder ein Urlaub.'");


	$log .= "&rarr; Inserting new menu entry for release notes.\n";
	$log .= doQuery("SELECT @max_navi_id := max(navi_id) FROM navi;");
	$log .= doQuery("INSERT INTO navi(navi_id, parent_id, menu_order, item_type, item_name, tooltip, icon_url, command) VALUES (@max_navi_id + 1, 28, 10, 'i', 'releaseNotes', '', 'information.gif', 'javascript:showReleaseNotes();')");
	$log .= "&rarr; Updating max id to navigation sequence table.\n";
	$log .= doQuery("UPDATE navi_ids_seq SET id = ((SELECT MAX(navi_id) FROM navi) + 1)");


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

function update1_0beta2To1_0beta3() {
	global $badgerDb;
	
	$log = '';

	$log .= "&rarr; Adding page settings table.\n";
	$log .= doQuery(
		"CREATE TABLE IF NOT EXISTS `page_settings` (
		`page_name` VARCHAR(255) NOT NULL,
		`setting_name` VARCHAR(255) NOT NULL,
		`setting` TEXT NULL,
		PRIMARY KEY (`page_name`, `setting_name`)
		)", array(-1)
	);

	$log .= "&rarr; Adding new columns to account table.\n";
	$log .= doQuery(
		"ALTER TABLE `account` ADD `last_calc_date` DATE NOT NULL DEFAULT '1000-01-01',
		ADD `csv_parser` VARCHAR( 100 ) NULL,
		ADD `delete_old_planned_transactions` BOOL NULL", array(-1)
	);

	$log .= "&rarr; Adding new columns to category table.\n";
	$log .= doQuery(
		"ALTER TABLE `category` ADD `keywords` TEXT NULL,
		ADD `expense` BOOL NULL", array(-1)
	);


	$log .= "&rarr; Adding new datagrid handler.\n";
	$log .= doQuery("REPLACE datagrid_handler SET handler_name = 'MultipleAccounts', file_path = '/modules/statistics2/MultipleAccounts.class.php', class_name = 'MultipleAccounts'");

	$log .= "&rarr; Adding new columns to finished transaction table.\n";
	$log .= doQuery(
		"ALTER TABLE `finished_transaction` ADD `transferal_transaction_id` INT NULL,
		ADD `transferal_source` BOOL NULL", array(-1)
	);

	$log .= "&rarr; Adding new columns to planned transaction table.\n";
	$log .= doQuery(
		"ALTER TABLE `planned_transaction` ADD `transferal_transaction_id` INT NULL,
		ADD `transferal_source` BOOL NULL", array(-1)
	);

	$log .= "&rarr; Deleting unused translation entries.\n";
	$log .= doQuery("DELETE FROM i18n WHERE page_id = 'accountCategory' AND id = 'pageTitle'");

	$log .= "&rarr; Adding new translation entries.\n";
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCategory', id = 'pageTitleEdit', en = 'Edit Category', de = 'Kategorie bearbeiten'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGrid', id = 'filterLegend', en = 'Filter', de = 'Filter'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGrid', id = 'setFilter', en = 'Set Filter', de = 'Filtern'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGrid', id = 'resetFilter', en = 'Reset', de = 'Reset'");
	$log .= doQuery("REPLACE i18n SET page_id = 'common', id = 'gpcFieldUndefined', en = 'GET/POST/COOKIE field undefined', de = 'GET/POST/COOKIE-Feld nicht definiert'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCategory', id = 'pageTitleNew', en = 'Create new Catagory', de = 'Neue Kategorie erstellen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'DataGridHandler', id = 'illegalFieldSelected', en = 'The following field is not known to this DataGridHandler:', de = 'Das folgende Feld ist diesem DataGridHandler nicht bekannt:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'MultipleAccounts', id = 'invalidFieldName', en = 'An unknown field was used with MultipleAccounts.', de = 'Es wurde ein unbekanntes Feld mit MultipleAccounts verwendet.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountAccount', id = 'deleteOldPlannedTransactions', en = 'Auto-insert recurring transactions:', de = 'Wiederkehrende Transaktionen automatisch eintragen:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountAccount', id = 'csvParser', en = 'CSV parser:', de = 'CSV-Parser:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountAccount', id = 'deleteOldPlannedTransactionsDescription', en = 'If this option is checked, every occuring instance of a recurring transaction is automatically inserted as an single transaction. Uncheck this if you import your transactions from a CSV file on a regular basis.', de = 'Wenn diese Option ausgewählt wurde, werden eintretende Instanzen einer wiederkehrenden Transaktion automatisch als einmalige Transaktionen eingetragen. Wählen Sie die Option nicht aus, wenn Sie Ihre Transaktionen regelmäßig aus einer CSV-Datei importieren.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'range', en = 'Apply to', de = 'Anwenden auf'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'rangeAll', en = 'all', de = 'alle'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'rangeThis', en = 'this', de = 'diese'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'rangePrevious', en = 'this and previous', de = 'diese und vorherige'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'rangeFollowing', en = 'this and following', de = 'diese und folgende'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'rangeUnit', en = 'instances', de = 'Ausprägungen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'plannedTransaction', id = 'afterTitle', en = 'after', de = 'nach'");
	$log .= doQuery("REPLACE i18n SET page_id = 'plannedTransaction', id = 'beforeTitle', en = 'before', de = 'vor'");
	$log .= doQuery("REPLACE i18n SET page_id = 'AccountManager', id = 'UnknownFinishedTransactionId', en = 'An unknown single transaction id was used.', de = 'Es wurde eine unbekannte ID einer einmaligen Transaktion verwendet.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'AccountManager', id = 'UnknownPlannedTransactionId', en = 'An unknown recurring transaction id was used.', de = 'Es wurde eine unbekannte ID einer wiederkehrenden Transaktion verwendet.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'transferalEnabled', en = 'Add transferal transaction', de = 'Überweisungstransaktion hinzufügen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'transferalAccount', en = 'Target account', de = 'Zielkonto'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'transferalAmount', en = 'Amount on target Account', de = 'Betrag auf Zielkonto'");
	$log .= doQuery("REPLACE i18n SET page_id = 'Account', id = 'FinishedTransferalSourceTransaction', en = 'Source of single transferal transaction', de = 'Quelle einer Einmaligen Überweisungstransaktion'");
	$log .= doQuery("REPLACE i18n SET page_id = 'Account', id = 'FinishedTransferalTargetTransaction', en = 'Target of single transferal transaction', de = 'Ziel einer Einmaligen Überweisungstransaktion'");
	$log .= doQuery("REPLACE i18n SET page_id = 'Account', id = 'PlannedTransferalSourceTransaction', en = 'Source of recurring transferal transaction', de = 'Quelle einer Wiederkehrenden Überweisungstransaktion'");
	$log .= doQuery("REPLACE i18n SET page_id = 'Account', id = 'PlannedTransferalTargetTransaction', en = 'Target of recurring transferal transaction', de = 'Ziel einer Wiederkehrenden Überweisungstransaktion'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCommon', id = 'includeSubCategories', en = '(including sub-categories)', de = '(Unterkategorien eingeschlossen)'");
	$log .= doQuery("REPLACE i18n SET page_id = 'widgetEngine', id = 'noImage', en = 'An image file cannot be found in the current theme or the Standard theme.', de = 'Eine Bilddatei kann weder im aktuellen noch im Standardtheme gefunden werden.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'NavigationFromDB', id = 'noIcon', en = 'An navigation icon cannot be found in the current theme or the Standard theme.', de = 'Ein Navigationsicon kann weder im aktuellen noch im Standardtheme gefunden werden.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCategory', id = 'keywordsLabel', en = 'Keywords', de = 'Schlüsselwörter'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCategory', id = 'keywordsDescription', en = 'If an imported transaction contains one of these keywords, this category will be pre-selected for this transaction. Use one line per keyword.', de = 'Wenn eine importierte Transaktion eines dieser Schlüsselwörter enthält, wird diese Kategorie vor-ausgewählt. Geben Sie pro Schlüsselwort eine neue Zeile ein.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'matchingDateDeltaLabel', en = 'Max. difference in days:', de = 'Max. Differenz in Tagen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'matchingDateDeltaDescription', en = 'Only transactions that differ at most this amount of days from the imported transaction are considered for comparison.', de = 'Nur Transaktionen, die maximal diese Anzahl an Tagen von der importierten Transaktion abweichen, werden zum Vergleich herangezogen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'matchingAmountDeltaLabel', en = 'Max. difference of amount (%)', de = 'Max. Abweichung des Betrags (%)'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'matchingAmountDeltaDescription', en = 'Only transactions that differ at most this percentage in amount from the imported transaction are considered for comparison.', de = 'Nur Transaktionen, deren Betrag maximal diesen Prozentsatz von der importierten Transaktion abweichen, werden zum Vergleich herangezogen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'matchingTextSimilarityLabel', en = 'Min. text similarity (%)', de = 'Mind. Textähnlichkeit (%)'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'matchingTextSimilarityDescription', en = 'Only transactions that are similar to the imported transaction by this percentage are considered for comparison.', de = 'Nur Transaktionen, die mindestens diesen Prozentsatz an Ähnlichkeit zur importierten Transaktion aufweisen, werden zum Vergleich herangezogen.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'UserSettingsAdmin', id = 'matchingHeading', en = 'CSV Import Matching', de = 'Abgleich beim CSV-Import'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'matchingHeader', en = 'Similar Transactions', de = 'Ähnliche Transaktionen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'matchingToolTip', en = 'If you choose a transaction here, it will be replaced by the imported data.', de = 'Wenn Sie hier eine Transaktion auswählen, wird sie durch die importierten Daten ersetzt.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'dontMatchTransaction', en = '&lt;Import as new&gt;', de = '&lt;Neu importieren&gt;'");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'descriptionFieldImportedPartner', en = 'Imported transaction partner: ', de = 'Importierter Transaktionspartner: '");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'descriptionFieldOrigValutaDate', en = 'Original valuta date: ', de = 'Original-Buchungsdatum: '");
	$log .= doQuery("REPLACE i18n SET page_id = 'importCsv', id = 'descriptionFieldOrigAmount', en = 'Original amount: ', de = 'Original-Betrag: '");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountOverview', id = 'colBalance', en = 'Balance', de = 'Kontostand'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'colAccountName', en = 'Account', de = 'Konto'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'pageTitle', en = 'Advanced Statistics', de = 'Erweiterte Statistik'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'titleFilter', en = 'Title is ', de = 'Titel ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'descriptionFilter', en = 'Description is ', de = 'Beschreibung ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'valutaDateFilter', en = 'Valuta date is ', de = 'Buchungsdatum ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'valutaDateBetweenFilter', en = 'Valuta date is between ', de = 'Buchungsdatum ist zwischen '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'valutaDateBetweenFilterConj', en = ' and ', de = ' und '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'valutaDateBetweenFilterInclusive', en = ' (both inclusive)', de = ' (beide inklusive)'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'valutaDateAgoFilter', en = 'Valuta within the last ', de = 'Buchungsdatum innerhalb der letzten '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'valutaDateAgoFilterDaysAgo', en = ' days', de = ' Tage'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'amountFilter', en = 'Amount is ', de = 'Betrag ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outsideCapitalFilter', en = 'Source is ', de = 'Quelle ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outsideCapitalFilterOutside', en = 'outside capital', de = 'Fremdkapital'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outsideCapitalFilterInside', en = 'inside capital', de = 'Eigenkapital'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'transactionPartnerFilter', en = 'Transaction partner is ', de = 'Transaktionspartner ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'categoryFilter', en = 'Category ', de = 'Kategorie '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'categoryFilterIs', en = 'is', de = 'ist'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'categoryFilterIsNot', en = 'is not', de = 'ist nicht'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'exceptionalFilter', en = 'Transaction is ', de = 'Transaktion ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'exceptionalFilterExceptional', en = 'exceptional', de = 'außergewöhnlich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'exceptionalFilterNotExceptional', en = 'not exceptional', de = 'nicht außergewöhnlich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'periodicalFilter', en = 'Transaction is ', de = 'Transaktion ist '");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'periodicalFilterPeriodical', en = 'periodical', de = 'regelämäßig'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'periodicalFilterNotPeriodical', en = 'not periodical', de = 'unregelmäßig'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersUnselected', en = 'Please choose a filter', de = 'Bitte wählen Sie einen Filter'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersTitle', en = 'Title', de = 'Titel'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersDescription', en = 'Description', de = 'Beschreibung'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersValutaDate', en = 'Valuta date', de = 'Buchungsdatum'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersValutaDateBetween', en = 'Valuta date between', de = 'Buchungsdatum zwischen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersValutaDateAgo', en = 'Valuta date last days', de = 'Buchungsdatum vergangene Tage'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersAmount', en = 'Amount', de = 'Betrag'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersOutsideCapital', en = 'Outside capital', de = 'Fremdkapital'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersTransactionPartner', en = 'Transaction partner', de = 'Transaktionspartner'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersCategory', en = 'Category', de = 'Kategorie'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersExceptional', en = 'Exceptional', de = 'Außergewöhnlich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersPeriodical', en = 'Periodical', de = 'Regelmäßig'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'availableFiltersDelete', en = '&lt;Delete Filter&gt;', de = '&lt;Filter löschen&gt;'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'filterCaption', en = 'Filters', de = 'Filter'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'twistieCaptionInput', en = 'Input Values', de = 'Eingabewerte'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTrendStartValue', en = 'Start Value', de = 'Startwert'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTrendStartValueZero', en = '0 (zero)', de = '0 (null)'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTrendStartValueBalance', en = 'Balance', de = 'Kontostand'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTrendTickLabels', en = 'Tick labels', de = 'Tickmarken'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTrendTickLabelsShow', en = 'Show', de = 'Anzeigen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTrendTickLabelsHide', en = 'Hide', de = 'Verbergen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionCategoryType', en = 'Category Type', de = 'Kategorietyp'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionCategoryTypeInput', en = 'Input', de = 'Einnahmen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionCategoryTypeOutput', en = 'Output', de = 'Ausgaben'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionCategorySubCategories', en = 'Sub-Categories', de = 'Unterkategorien'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionCategorySubCategoriesSummarize', en = 'Summarize sub-categories', de = 'Unterkategorien zusammenfassen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionCategorySubCategoriesNoSummarize', en = 'Do not summarize sub-categories', de = 'Unterkategorien einzeln aufführen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTimespanType', en = 'Type', de = 'Typ'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTimespanTypeWeek', en = 'Week', de = 'Woche'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTimespanTypeMonth', en = 'Month', de = 'Monat'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTimespanTypeQuarter', en = 'Quarter', de = 'Quartal'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionTimespanTypeYear', en = 'Year', de = 'Jahr'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionGraphType', en = 'Graph Type', de = 'Graphtyp'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionGraphTypeTrend', en = 'Trend', de = 'Verlauf'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionGraphTypeCategory', en = 'Category', de = 'Kategorie'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'outputSelectionGraphTypeTimespan', en = 'Timespan', de = 'Zeitvergleich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'twistieCaptionOutputSelection', en = 'Output Selection', de = 'Ausgabeauswahl'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'analyzeButton', en = 'Analyse', de = 'Analysieren'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'twistieCaptionGraph', en = 'Graph', de = 'Graph'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'twistieCaptionOutput', en = 'Output', de = 'Ausgabe'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'addFilterButton', en = 'Add Filter', de = 'Filter hinzufügen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2Graph', id = 'noMatchingTransactions', en = 'No transactions match your criteria.', de = 'Keine Transaktionen entsprechen Ihren Kriterien.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'beginsWith', en = 'begins with', de = 'fängt an mit'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'endsWith', en = 'ends with', de = 'hört auf mit'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'contains', en = 'contains', de = 'enthält'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'dateEqualTo', en = 'equal to', de = 'gleich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'dateBefore', en = 'before', de = 'vor'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'dateBeforeEqual', en = 'before or equal to', de = 'vor oder gleich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'dateAfter', en = 'after', de = 'nach'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'dateAfterEqual', en = 'after or equal to', de = 'nach oder gleich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'dataGridFilter', id = 'dateNotEqual', en = 'not equal to', de = 'ungleich'");
	$log .= doQuery("REPLACE i18n SET page_id = 'Navigation', id = 'Statistics2', en = 'Advanced Statistics', de = 'Erweiterte Statistik'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountAccount', id = 'csvNoParser', en = '&lt;No parser&gt;', de = '&lt;Kein Parser&gt;'");
	$log .= doQuery("REPLACE i18n SET page_id = 'PageSettings', id = 'SQLError', en = 'An SQL error occured attempting to fetch the PageSettings data from the database.', de = 'Beim Abrufen der PageSettings-Daten aus der Datenbank trat ein SQL-Fehler auf.'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'pageSettingSave', en = 'Save Settings', de = 'Einstellungen speichern'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'pageSettingDelete', en = 'Delete Setting', de = 'Einstellung löschen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'pageSettingsTwistieTitle', en = 'Settings', de = 'Einstellungen'");
	$log .= doQuery("REPLACE i18n SET page_id = 'statistics2', id = 'pageSettingNewNamePrompt', en = 'Please enter the name for the setting:', de = 'Bitte geben Sie den Namen für die Einstellung ein:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCategory', id = 'expenseRowLabel', en = 'Standard direction:', de = 'Standardgeldfluss:'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCategory', id = 'expenseIncome', en = 'Income', de = 'Einnahme'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountCategory', id = 'expenseExpense', en = 'Expense', de = 'Ausgabe'");
	$log .= doQuery("REPLACE i18n SET page_id = 'accountTransaction', id = 'categoryExpenseWarning', en = 'The selected category is marked as expense, but your amount is positive.', de = 'Die ausgewählte Kategorie ist als Ausgabe markiert, jedoch ist Ihr Betrag positiv.'");

	$log .= "&rarr; Changing translation entries.\n";
	$log .= doQuery("REPLACE i18n SET page_id = 'CategoryManager', id = 'no_parent', en = '&lt;No parent category&gt;', de = '&lt;Keine Elternkategorie&gt;'");

	$sql = "SELECT count(navi_id) FROM navi WHERE item_name = 'Statistics2'";
	$result =& $badgerDb->query($sql);
	$arr = array();
	$result->fetchInto($arr, DB_FETCHMODE_ORDERED);
	if ($arr[0] == 0) {
		$log .= "&rarr; Inserting new menu entry for advanced statistics.\n";
		$log .= doQuery("SELECT @max_navi_id := max(navi_id) FROM navi;");
		$log .= doQuery("INSERT INTO navi(navi_id, parent_id, menu_order, item_type, item_name, tooltip, icon_url, command) VALUES (@max_navi_id + 1, 30, 5, 'i', 'Statistics2', '', 'statistics.gif', '{BADGER_ROOT}/modules/statistics2/statistics2.php')");
		$log .= "&rarr; Updating max id to navigation sequence table.\n";
		$log .= doQuery("UPDATE navi_ids_seq SET id = ((SELECT MAX(navi_id) FROM navi) + 1)");
	
		$log .= "&rarr; Updating menu order of forecast.\n";
		$log .= doQuery("UPDATE navi SET menu_order = 6 WHERE item_name = 'Forecast'");
	}

	$log .= "&rarr; Updating database version to 1.0 beta 3.\n";
	$log .= doQuery("REPLACE user_settings SET prop_key = 'badgerDbVersion', prop_value = 's:10:\"1.0 beta 3\";'");

	$log .= "\n&rarr;&rarr; Update to version 1.0 beta 3 finished. &larr;&larr;\n\n";

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
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

	$us->setProperty('badgerDbVersion', BADGER_VERSION);

	$updateInformation = getUpdateTranslation('updateUpdate', 'updateInformation');
	$errorInformation = getUpdateTranslation('updateUpdate', 'errorInformation');
	$dbVersionText = getUpdateTranslation('updateProcedure', 'dbVersionText');
	$fileVersionText = getUpdateTranslation('updateProcedure', 'fileVersionText');
	$updateFinished = getUpdateTranslation('updateUpdate', 'updateFinished');
	
	eval('echo "' . $tpl->getTemplate('update/update') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function update1_0betaTo1_0beta2() {
	$log = 'No content implemented yet';
	
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
				'severeError' => 'The update encountered a severe error. Please send the whole output to the BADGER finance development team.'
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
				'severeError' => 'Die Aktualisierung stieß auf einen schweren Fehler. Bitte schicken Sie die gesamte Ausgabe an das BADGER finance development team.'
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
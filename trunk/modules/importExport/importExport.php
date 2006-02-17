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

define ('BADGER_VERSION_TAG', '-- BADGER_VERSION = ' . BADGER_VERSION);

if (isset($_GET['mode'])) {
	$mode = $_GET['mode'];
} else {
	$mode = 'ask';
}

switch ($mode) {
	case 'ask':
	default:
		printAskExport();
		break;
	case 'export':
		printAskExport();
		break;
	case 'import':
		printAskInsert();
		break;			
	case 'dump':
		sendSqlDump();
		break;
	
	case 'askInsert':
		printAskInsert();
		break;
	
	case 'insert':
		printInsert();
		break;
}

function printAskExport() {
	global $tpl;
	$widgets = new WidgetEngine($tpl); 
	
	$widgets->addNavigationHead();
	
	$legend = getBadgerTranslation2('askExport','legend');
	$askTitle = getBadgerTranslation2('importExport', 'askTitle');
	echo $tpl->getHeader($askTitle);
	
	$askExportTitle = getBadgerTranslation2('importExport', 'askExportTitle');
	$askExportText = getBadgerTranslation2('importExport', 'askExportText');
	$askExportLink = BADGER_ROOT . '/modules/importExport/importExport.php?mode=dump';
	$askExportAction = getBadgerTranslation2('importExport', 'askExportAction');
	
	$askExportButton = $widgets->createButton('downloadSQL', $askExportAction, "location.href = '$askExportLink';", "Widgets/accept.gif");
	
	eval(' echo "' . $tpl->getTemplate('importExport/ask') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function printAskInsert() {
	global $tpl;
	$widgets = new WidgetEngine($tpl); 
	
	$tpl->addJavaScript("js/acceptTerms.js");
	$tpl->addJavaScript("js/prototype.js");
	$widgets->addNavigationHead();

	$legend = getBadgerTranslation2('askInsert','legend');
	$askInsertTitle = getBadgerTranslation2('importExport', 'askInsertTitle');
	echo $tpl->getHeader($askInsertTitle);

	$askInsertAction = BADGER_ROOT . '/modules/importExport/importExport.php?mode=insert';
	$askImportWarning = getBadgerTranslation2('importExport', 'askImportWarning');

	$askImportFileUpload = $widgets->createField('sqlDump', null, null, '', true, 'file');
	$askImportFileUploadLabel = $widgets->createLabel('sqlDump', getBadgerTranslation2('importExport', 'askImportFile'));

	$askImportVersionInfo = getBadgerTranslation2('importExport', 'askImportVersionInfo');
	$askImportCurrentVersionInfo = getBadgerTranslation2('importExport', 'askImportCurrentVersionInfo');
	$versionInfo = BADGER_VERSION;

	$confirmUploadField = $widgets->createField('confirmUpload', null, 'yes', null, false, 'checkbox', 'onClick="agreesubmit()"');
	$confirmUploadLabel = $widgets->createLabel('confirmUpload', getBadgerTranslation2('importExport', 'askImportYes'));

	$askImportSubmit = $widgets->createButton("submit", getBadgerTranslation2('importExport', 'askImportSubmitButton'), "submit", "Widgets/accept.gif", 'disabled="disabled"');	

	eval(' echo "' . $tpl->getTemplate('importExport/askInsert') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function printInsert() {
	global $tpl;
	$widgets = new WidgetEngine($tpl); 
	
	$widgets->addNavigationHead();

	$insertTitle = getBadgerTranslation2('importExport', 'insertTitle'); 
	echo $tpl->getHeader($insertTitle);
	
	if (!isset($_POST['confirmUpload']) || $_POST['confirmUpload'] !== 'yes') {
		$insertMsg = getBadgerTranslation2('importExport', 'insertNoInsert');
	} else if (!is_uploaded_file($_FILES['sqlDump']['tmp_name'])) {
		$insertMsg = getBadgerTranslation2('importExport', 'insertNoFile');
	} else {
		applySqlDump();
		$insertMsg = getBadgerTranslation2('importExport', 'insertSuccessful');
	}
	
	eval(' echo "' . $tpl->getTemplate('importExport/insert') . '";');
	eval('echo "' . $tpl->getTemplate('badgerFooter') . '";');
}

function applySqlDump() {
	global $badgerDb;
	
	if (!isset($_FILES['sqlDump'])) {
		throw new BadgerException('importExport', 'noSqlDumpProvided');
	}
	
	$sqlDump = fopen($_FILES['sqlDump']['tmp_name'], 'r');
	
	if (!$sqlDump) {
		throw new BadgerException('importExport', 'errorOpeningSqlDump');
	}
	
	$version = fgets($sqlDump);
	
	if (trim($version) !== BADGER_VERSION_TAG) {
		throw new BadgerException('importExport', 'incompatibleBadgerVersion');
	}
	
	while (!feof($sqlDump)) {
		$sql = trim(fgets($sqlDump));
		
		if ($sql === '') {
			continue;
		}
		
		$dbResult =& $badgerDb->query($sql);
		
		if (PEAR::isError($dbResult)) {
			throw new BadgerException('importExport', 'SQLError', $dbResult->getMessage() . $sql);
		}
	}
}
		

function sendSqlDump() {
	$result =  BADGER_VERSION_TAG . "\n";
	
	$result .= getDbDump();
	
	$now = new Date();
	
	header ('Content-Type: text/sql');
	header('Content-Disposition: attachment; filename="BADGER-' . BADGER_VERSION . '-DatabaseBackup-' . $now->getDate() . '.sql"');
	header('Content-Length: ' . strlen($result));
	
	echo $result;
} 

function getDbDump() {
	$result = ''; 
	$tableList = array (
		'account',
		'accountIds_seq',
		'account_property',
		'category',
		'categoryIds_seq',
		'csv_parser',
		'currency',
		'currencyIds_seq',
		'datagrid_handler',
		'finishedTransactionIds_seq',
		'finished_transaction',
		'i18n',
		'langs',
		'navi',
		'naviIds_seq',
		'plannedTransactionIds_seq',
		'planned_transaction',
		'session_global',
		'session_master',
		'user_settings'
	);

	foreach ($tableList as $currentTable) {
		$result .= makeEmptyStatement($currentTable);
	}
	
	foreach ($tableList as $currentTable) {
		$result .= dumpTable($currentTable);
	}
	
	return $result;
}

function makeEmptyStatement($tableName) {
	return "DELETE FROM $tableName;\n";
}

function dumpTable($tableName) {
	global $badgerDb;

	$sql = "SELECT * FROM $tableName";
	
	$dbResult =& $badgerDb->query($sql);
	
	if (PEAR::isError($dbResult)) {
		throw new BadgerException('importExport', 'SQLError', $dbResult->getMessage() . ' ' . $sql);
	}
	
	$row = false;
	$result = '';
	
	while ($dbResult->fetchInto($row, DB_FETCHMODE_ASSOC)) {
		$result .= "INSERT INTO $tableName (";
	
		$columns = array_keys($row);
		$first = true;
		foreach ($columns as $currentColumn) {
			if (!$first) {
				$result .= ', ';
			} else {
				$first = false;
			}
			$result .= $currentColumn;
		}
		
		$result .= ') VALUES (';
		
		$first = true;
		foreach ($row as $currentValue) {
			if (!$first) {
				$result .= ', ';
			} else {
				$first = false;
			}
			$result .= $badgerDb->quoteSmart($currentValue);
		}
		
		$result .= ");\n";
	}
	
	return $result;
}

require_once BADGER_ROOT . '/includes/fileFooter.php';
?>	
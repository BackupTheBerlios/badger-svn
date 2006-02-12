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
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addNavigationHead();
echo $tpl->getHeader("Charts");
echo $widgets->getNavigationBody();
//include charts.php to access the InsertChart function
require_once(BADGER_ROOT . "/includes/charts/charts.php");

if (!isset($_POST['sendData'])){	
	//field for selecting end date of forecasting
	$endDateField = $widgets->addDateField("endDate");
	//get accounts from db & field to select the account for forecsatung
		$am = new AccountManager($badgerDb);
		$account = array();
			    	while ($currentAccount = $am->getNextAccount()) {
			    		$account[$currentAccount->getId()] = $currentAccount->getTitle();	
		}
	$accountField = $widgets->createSelectField("selectedAccount", $account, "", getBadgerTranslation2("forecast", "toolTipAccountSelect"));
	//field to select saving target, default is 0
	$savingTargetField = $widgets->createField("savingTarget", 5, 0);
	//field to insert pocketmoney1
	$pocketMoney1Field = $widgets->createField("pocketmoney1", 5);
	//field to insert pocketmoney2
	$pocketMoney2Field = $widgets->createField("pocketmoney2", 5);
	$lowerLimitBox = "<input type=\"checkbox\" name=\"lowerLimitBox\" value=\"select\" />";
	$upperLimitBox = "<input type=\"checkbox\" name=\"upperLimitBox\" value=\"select\" />";
	$plannedTransactionsBox = "<input type=\"checkbox\" name=\"plannedTransactionsBox\" value=\"select\" />";
	$savingTargetBox = "<input type=\"checkbox\" name=\"savingTargetBox\" value=\"select\" />";
	$pocketMoney1Box = "<input type=\"checkbox\" name=\"pocketMoney1Box\" value=\"select\" />";
	$pocketMoney2Box = "<input type=\"checkbox\" name=\"pocketMoney2Box\" value=\"select\" />";
	
	$sendButton = $widgets->createButton("sendData", getBadgerTranslation2("forecast", "sendData"), "submit", "Widgets/table_save.gif");
	eval("echo \"".$tpl->getTemplate("forecast/forecastSettings")."\";");
}


if (isset($_POST['sendData'])){
	# validate if date is in future
	if (isset ($_POST["lowerLimitBox"])){
		$showLowerLimit = 1;
	}else {
		$showLowerLimit = 0;
	}
	if (isset ($_POST["upperLimitBox"])){
		$showUpperLimit = 1;
	}else {
		$showUpperLimit = 0;
	}
	if (isset ($_POST["plannedTransactionsBox"])){
		$showPlannedTransactions = 1;
	}else {
		$showPlannedTransactions = 0;
	}
	if (isset ($_POST["savingTargetBox"])){
		$showSavingTarget = 1;
	}else {
		$showSavingTarget = 0;
	}
	if (isset ($_POST["pocketMoney1Box"])){
		$showPocketMoney1 = 1;
	}else {
		$showPocketMoney1 = 0;
	}
	if (isset ($_POST["pocketMoney2Box"])){
		$showPocketMoney2 = 1;
	}else {
		$showPocketMoney2 = 0;
	}
		
	$selectedDate = new Date($_POST["endDate"], true);
	$today = new Date();
	if ($today->compare($today, $selectedDate)!=1){
		
		$savingTarget = ($_POST["savingTarget"]);		
		$endDate = $selectedDate->getDate();
		$account = ($_POST["selectedAccount"]);
		$pocketMoney1 = ($_POST["pocketmoney1"]);			
		$pocketMoney2 = ($_POST["pocketmoney2"]);
		
		$insertChart = InsertChart ( BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/forecast/forecastChart.php?endDate=$endDate&account=$account&savingTarget=$savingTarget&pocketMoney1=$pocketMoney1&pocketMoney2=$pocketMoney2&showLowerLimit=$showLowerLimit&showUpperLimit=$showUpperLimit&showPlannedTransactions=$showPlannedTransactions&showSavingTarget=$showSavingTarget&showPocketMoney1=$showPocketMoney1&showPocketMoney2=$showPocketMoney2", 800, 400, "ECE9D8", true);
		eval("echo \"".$tpl->getTemplate("forecast/forecastChart")."\";");

	} else { //if ($today->compare($today, $selectedDate)!=1){
		echo "Datum muss in der Zukunft liegen, für Vergangenheitsdaten nutzten sie Statistiken, zurück im Browser drücken";
	}
} //if (isset($_POST['sendData'])){
eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");

?>

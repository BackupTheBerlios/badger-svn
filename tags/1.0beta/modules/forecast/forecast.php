<?php
/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://www.badger-finance.org 
*
**/
define("BADGER_ROOT", "../..");
require_once(BADGER_ROOT . "/includes/fileHeaderFrontEnd.inc.php");
require_once BADGER_ROOT . '/modules/account/AccountManager.class.php';
require_once BADGER_ROOT . '/modules/account/accountCommon.php';

$pageHeading = getBadgerTranslation2('forecast','title');
$widgets = new WidgetEngine($tpl); 
$widgets->addToolTipJS();
$widgets->addCalendarJS();
$widgets->addJSValMessages();
$tpl->addJavaScript("js/prototype.js");

$widgets->addNavigationHead();
echo $tpl->getHeader($pageHeading);
echo $widgets->addToolTipLayer();

//include charts.php to access the InsertChart function
require_once(BADGER_ROOT . "/includes/charts/charts.php");
//the button to calculate the pocket money from finished transactions was pressed, show the settings formula with the inputs made & the new pocket money 2
if (isset ($_POST['writePocketMoney'])){
	//help funktions for automatical calculation of pocket money from the finished transactions
	$standardStartDate = new Date();
	$standardStartDate->subtractSeconds(60*60*24*180);
	$calculatePocketMoneyStartDateField = $widgets->addDateField("startDate", $_POST["startDate"]);
	$writeCalcuatedPocketMoneyButton = $widgets->createButton("writePocketMoney", getBadgerTranslation2("forecast", "calculatedPocketMoneyButton"), "submit", "Widgets/accept.gif");
	$calculatedPocketMoneyLabel = getBadgerTranslation2("forecast", "calculatedPocketMoneyLabel"). ":";
	$writeCalculatedToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "calculatedPocketMoneyToolTip"));
	//field for selecting end date of forecasting
	$legendSetting = getBadgerTranslation2("forecast", "legendSetting");
	$legendGraphs = getBadgerTranslation2("forecast", "legendGraphs");
	$endDateLabel =  getBadgerTranslation2("forecast", "endDateField"). ":";
	$endDateField = $widgets->addDateField("endDate",$_POST["endDate"]);
	$endDateToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "endDateToolTip"));
	//get accounts from db & field to select the account for forecsatung
		$am = new AccountManager($badgerDb);
		$account = array();
			while ($currentAccount = $am->getNextAccount()) {
				$account[$currentAccount->getId()] = $currentAccount->getTitle();	
		}
	
	$accountLabel =  $widgets->createLabel("selectedAccount", getBadgerTranslation2("forecast", "accountField").":", true);
	$accountField = $widgets->createSelectField("selectedAccount", $account, $_POST["selectedAccount"], getBadgerTranslation2("forecast", "accountToolTip"), true, 'style="width: 10em;"');
	//field to select saving target, default is 0
	$savingTargetLabel =  $widgets->createLabel("savingTarget", getBadgerTranslation2("forecast", "savingTargetField").":", true);
	$savingTargetField = $widgets->createField("savingTarget", 5, $_POST["savingTarget"], getBadgerTranslation2("forecast", "savingTargetToolTip"), true, "text", 'style="width: 10em;"');
	//field to insert pocketmoney1
	$pocketMoney1Label =  $widgets->createLabel("pocketmoney1", getBadgerTranslation2("forecast", "pocketMoney1Field").":", true);
		
	$startSpendingDate = new Date ($_POST["startDate"],true);
	$spendingMoney = getSpendingMoney($_POST["selectedAccount"], $startSpendingDate);
	$spendingMoney->mul(-1);
	$calculatedPocketMoney = $spendingMoney->getFormatted();

	$pocketMoney1Field = $widgets->createField("pocketmoney1", 5, $_POST["pocketmoney1"], getBadgerTranslation2("forecast", "pocketMoney1ToolTip"), true, "text", 'style="width: 10em;"');
	//field to insert pocketmoney2
	$pocketMoney2Label =  $widgets->createLabel("pocketmoney2", getBadgerTranslation2("forecast", "pocketMoney2Field").":", true);
	$pocketMoney2Field = $widgets->createField("pocketmoney2", 5, $calculatedPocketMoney, getBadgerTranslation2("forecast", "pocketMoney2ToolTip"), true, "text", 'style="width: 10em;"');
	
	//checkbox for lower limit graph
	$lowerLimitLabel =  getBadgerTranslation2("forecast", "lowerLimitLabel").":";
	if (isset ($_POST["lowerLimitBox"])){
		$lowerLimitBox = "<input type=\"checkbox\" name=\"lowerLimitBox\" value=\"select\" checked=\"checked\"/>";
	}else{
		$lowerLimitBox = "<input type=\"checkbox\" name=\"lowerLimitBox\" value=\"select\" />";
	}
	$lowerLimitToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "lowerLimitToolTip"));
	//checkbox for upper limit graph
	$upperLimitLabel =  getBadgerTranslation2("forecast", "upperLimitLabel").":";
	if (isset ($_POST["upperLimitBox"])){
	$upperLimitBox = "<input type=\"checkbox\" name=\"upperLimitBox\" value=\"select\" checked=\"checked\"/>";
	}else{
		$upperLimitBox = "<input type=\"checkbox\" name=\"upperLimitBox\" value=\"select\" />";
	}
	$upperLimitToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "upperLimitToolTip"));
	//checkbox for planned transactions graph
	$plannedTransactionsLabel =  getBadgerTranslation2("forecast", "plannedTransactionsLabel").":";
	if (isset ($_POST["plannedTransactionsBox"])){
		$plannedTransactionsBox = "<input type=\"checkbox\" name=\"plannedTransactionsBox\" value=\"select\" checked=\"checked\"/>";
	}else{
		$plannedTransactionsBox = "<input type=\"checkbox\" name=\"plannedTransactionsBox\" value=\"select\" />";
	}
	$plannedTransactionsToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "plannedTransactionsToolTip"));
	//checkbox for saving target graph
	$savingTargetLabel1 =  getBadgerTranslation2("forecast", "savingTargetLabel").":";
	if (isset ($_POST["savingTargetBox"])){
		$savingTargetBox = "<input type=\"checkbox\" name=\"savingTargetBox\" value=\"select\" checked=\"checked\"/>";
	}else{
		$savingTargetBox = "<input type=\"checkbox\" name=\"savingTargetBox\" value=\"select\" />";
	}
	$savingTargetToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "showSavingTargetToolTip"));
	//checkbox for pocket money1 graph
	$pocketMoney1Label1 =  getBadgerTranslation2("forecast", "pocketMoney1Label").":";
	if (isset ($_POST["pocketMoney1Box"])){
		$pocketMoney1Box = "<input type=\"checkbox\" name=\"pocketMoney1Box\" value=\"select\" checked=\"checked\"/>";
	}else{
		$pocketMoney1Box = "<input type=\"checkbox\" name=\"pocketMoney1Box\" value=\"select\" />";
	}
	$pocketMoney1ToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "showPocketMoney1ToolTip"));
	//checkbox for pocket money1 graph
	$pocketMoney2Label1 =  getBadgerTranslation2("forecast", "pocketMoney2Label").":";
	if (isset ($_POST["pocketMoney2Box"])){
		$pocketMoney2Box = "<input type=\"checkbox\" name=\"pocketMoney2Box\" value=\"select\" checked=\"checked\"/>";
	}else{	
		$pocketMoney2Box = "<input type=\"checkbox\" name=\"pocketMoney2Box\" value=\"select\" />";
	}
	$pocketMoneyTool2Tip = $widgets->addToolTip(getBadgerTranslation2("forecast", "showPocketMoney2ToolTip"));
	//Create Chart Button
	$tooLongTimeSpanWarning = getBadgerTranslation2("forecast", "performanceWarning");
	$sendButton = $widgets->createButton("sendData", getBadgerTranslation2("forecast", "sendData"), "submit", "Widgets/accept.gif");
	eval("echo \"".$tpl->getTemplate("forecast/forecastSettings")."\";");
}
//Settings formular
if (!isset($_POST['writePocketMoney'])){	
	if (!isset($_POST['sendData'])){	
	//help funktions for automatical calculation of pocket money from the finished transactions
	$standardStartDate = new Date();
	$standardStartDate->subtractSeconds(60*60*24*180);
	$calculatePocketMoneyStartDateField = $widgets->addDateField("startDate", $standardStartDate->getFormatted());
	$writeCalcuatedPocketMoneyButton = $widgets->createButton("writePocketMoney", getBadgerTranslation2("forecast", "calculatedPocketMoneyButton"), "submit", "Widgets/accept.gif");
	$calculatedPocketMoneyLabel = getBadgerTranslation2("forecast", "calculatedPocketMoneyLabel"). ":";
	$writeCalculatedToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "calculatedPocketMoneyToolTip"));
	
	//field for selecting end date of forecasting
	$legendSetting = getBadgerTranslation2("forecast", "legendSetting");
	$legendGraphs = getBadgerTranslation2("forecast", "legendGraphs");
	$endDateLabel =  getBadgerTranslation2("forecast", "endDateField"). ":";
	$standardEndDate = new Date();
	$standardEndDate->addSeconds(60*60*24*180);
	$endDateField = $widgets->addDateField("endDate",$standardEndDate->getFormatted());
	$endDateToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "endDateToolTip"));
	//get accounts from db & field to select the account for forecsatung
		$am = new AccountManager($badgerDb);
		$account = array();
			    	while ($currentAccount = $am->getNextAccount()) {
			    		$account[$currentAccount->getId()] = $currentAccount->getTitle();	
		}
	//Drop down to select account		
	$accountLabel =  $widgets->createLabel("selectedAccount", getBadgerTranslation2("forecast", "accountField").":", true);
	$accountField = $widgets->createSelectField("selectedAccount", $account, $us->getProperty('forecastStandardAccount'), getBadgerTranslation2("forecast", "accountToolTip"), true, 'style="width: 10em;"');
	//field to select saving target, default is 0
	$savingTargetLabel =  $widgets->createLabel("savingTarget", getBadgerTranslation2("forecast", "savingTargetField").":", true);
	$savingTargetField = $widgets->createField("savingTarget", 5, 0, getBadgerTranslation2("forecast", "savingTargetToolTip"), true, "text", 'style="width: 10em;"');
	//field to insert pocketmoney1
	$pocketMoney1Label =  $widgets->createLabel("pocketmoney1", getBadgerTranslation2("forecast", "pocketMoney1Field").":", true);
	$pocketMoney1Field = $widgets->createField("pocketmoney1", 5, 0, getBadgerTranslation2("forecast", "pocketMoney1ToolTip"), true, "text", 'style="width: 10em;"');
	//field to insert pocketmoney2
	$pocketMoney2Label =  $widgets->createLabel("pocketmoney2", getBadgerTranslation2("forecast", "pocketMoney2Field").":", true);
	$pocketMoney2Field = $widgets->createField("pocketmoney2", 5, 0, getBadgerTranslation2("forecast", "pocketMoney2ToolTip"), true, "text", 'style="width: 10em;"');
	//checkbox for lower limit graph
	$lowerLimitLabel =  getBadgerTranslation2("forecast", "lowerLimitLabel").":";
	$lowerLimitBox = "<input type=\"checkbox\" name=\"lowerLimitBox\" value=\"select\" checked=\"checked\"/>";
	$lowerLimitToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "lowerLimitToolTip"));
	//checkbox for upper limit graph
	$upperLimitLabel =  getBadgerTranslation2("forecast", "upperLimitLabel").":";
	$upperLimitBox = "<input type=\"checkbox\" name=\"upperLimitBox\" value=\"select\" checked=\"checked\"/>";
	$upperLimitToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "upperLimitToolTip"));
	//checkbox for planned transactions graph
	$plannedTransactionsLabel =  getBadgerTranslation2("forecast", "plannedTransactionsLabel").":";
	$plannedTransactionsBox = "<input type=\"checkbox\" name=\"plannedTransactionsBox\" value=\"select\" checked=\"checked\"/>";
	$plannedTransactionsToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "plannedTransactionsToolTip"));
	//checkbox for saving target graph
	$savingTargetLabel1 =  getBadgerTranslation2("forecast", "savingTargetLabel").":";
	$savingTargetBox = "<input type=\"checkbox\" name=\"savingTargetBox\" value=\"select\" checked=\"checked\"/>";
	$savingTargetToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "showSavingTargetToolTip"));
	//checkbox for pocket money1 graph
	$pocketMoney1Label1 =  getBadgerTranslation2("forecast", "pocketMoney1Label").":";
	$pocketMoney1Box = "<input type=\"checkbox\" name=\"pocketMoney1Box\" value=\"select\" checked=\"checked\"/>";
	$pocketMoney1ToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "showPocketMoney1ToolTip"));
	//checkbox for pocket money1 graph
	$pocketMoney2Label1 =  getBadgerTranslation2("forecast", "pocketMoney2Label").":";
	$pocketMoney2Box = "<input type=\"checkbox\" name=\"pocketMoney2Box\" value=\"select\" checked=\"checked\"/>";
	$pocketMoneyTool2Tip = $widgets->addToolTip(getBadgerTranslation2("forecast", "showPocketMoney2ToolTip"));
	//Create Chart Button
	$tooLongTimeSpanWarning = getBadgerTranslation2("forecast", "performanceWarning");
	$sendButton = $widgets->createButton("sendData", getBadgerTranslation2("forecast", "sendData"), "submit", "Widgets/accept.gif");
	eval("echo \"".$tpl->getTemplate("forecast/forecastSettings")."\";");
	}
}
//show chart
if (isset($_POST['sendData'])){
	# validate if date is in future
	$selectedDate = new Date($_POST["endDate"], true);
	$today = new Date();
	$noFutureDates = NULL;
	$noLowerLimit = NULL;
	$noUpperLimit = NULL;
	$noGraphChosen = NULL;
	$insertChart = NULL;
	//to avoid a date in the past or the same date as today
	if ($today->compare($today, $selectedDate)!=1){
		$selectedSavingTarget = new Amount ($_POST["savingTarget"], true);
		$savingTarget = $selectedSavingTarget->get();	
		$endDate = $selectedDate->getDate();
		$account = ($_POST["selectedAccount"]);
		//save selected account as standard account
		$us->setProperty('forecastStandardAccount',$account);
		
		$selectedPocketMoney1 = new Amount ($_POST["pocketmoney1"], true);
		$pocketMoney1 = $selectedPocketMoney1->get();		
		$selectedPocketMoney2 = new Amount ($_POST["pocketmoney2"], true);
		$pocketMoney2 = $selectedPocketMoney2->get();	
		$dailyPocketMoneyLabel = NULL;
		$dailyPocketMoneyValue = NULL;
		$dailyPocketMoneyToolTip = NULL;
		$balancedEndDate2 = NULL;
		$balancedEndDateLabel2 = NULL;
		$printedPocketMoney2EndValue = NULL;
		$balancedEndDate1 = NULL;
		$balancedEndDateLabel1 = NULL;
		$printedPocketMoney1EndValue = NULL;

		$amTransfer = new AccountManager($badgerDb);
		$accountTransfer = $amTransfer->getAccountById($account);
		transferFormerFinishedTransactions($accountTransfer);
			
		$am1 = new AccountManager($badgerDb);
		$currentAccount1 = $am1->getAccountById($account);
		//if no graph was chosen
		if (!isset ($_POST["lowerLimitBox"]) && !isset ($_POST["upperLimitBox"])&& !isset ($_POST["plannedTransactionsBox"]) && !isset ($_POST["savingTargetBox"]) && !isset ($_POST["pocketMoney1Box"]) &&!isset ($_POST["pocketMoney2Box"])){
			$noGraphChosen = getBadgerTranslation2("forecast", "noGraphchosen");
		} else {
			if (isset ($_POST["lowerLimitBox"])){
				if (!is_null($currentAccount1->getLowerLimit()->get())){
					$showLowerLimit = 1;
				} else {
					$showLowerLimit = 0;
					$noLowerLimit = getBadgerTranslation2("forecast", "noLowerLimit") . "<br />";
				}
			}else {
				$showLowerLimit = 0;
			}
			if (isset ($_POST["upperLimitBox"])){
				if (!is_null($currentAccount1->getUpperLimit()->get())){
					$showUpperLimit = 1;
				} else {
					$showUpperLimit = 0;
					$noUpperLimit = getBadgerTranslation2("forecast", "noUpperLimit") . "<br />";
				}
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
			//create the chart
			$insertChart = InsertChart ( BADGER_ROOT . "/includes/charts/charts.swf", BADGER_ROOT . "/includes/charts/charts_library", BADGER_ROOT . "/modules/forecast/forecastChart.php?endDate=$endDate&account=$account&savingTarget=$savingTarget&pocketMoney1=$pocketMoney1&pocketMoney2=$pocketMoney2&showLowerLimit=$showLowerLimit&showUpperLimit=$showUpperLimit&showPlannedTransactions=$showPlannedTransactions&showSavingTarget=$showSavingTarget&showPocketMoney1=$showPocketMoney1&showPocketMoney2=$showPocketMoney2", 800, 400, "ECE9D8", true);
			
			$am = new AccountManager($badgerDb);
			$totals = array();
			$currentAccount = $am->getAccountById($account);
			$startDate = new Date ();
			$currentBalances = getDailyAmount($currentAccount, $startDate, $selectedDate);
			foreach ($currentBalances as $balanceKey => $balanceVal) {
				if (isset($totals[$balanceKey])) {
					$totals[$balanceKey]->add($balanceVal);
				} else {
					$totals[$balanceKey] = $balanceVal;
				}
			}
			//calculate spending money, if saving target should be reached
			$countDay = count($totals)-1; //get numbers of days between today & endDate
			$laststanding = new Amount($totals[$selectedDate->getDate()]);
			$endDateBalance = $laststanding; //get balance of end date
			$freeMoney = new Amount($endDateBalance->sub($savingTarget)); //endDateBalance - saving target = free money to spend
			$dailyPocketMoney = new Amount ($freeMoney->div($countDay)); //calculate daily pocket money = free money / count of Days
			if ($showSavingTarget==1){
				$dailyPocketMoneyLabel = getBadgerTranslation2("forecast", "dailyPocketMoneyLabel").":";
				$dailyPocketMoneyValue = $dailyPocketMoney->getFormatted();
				$dailyPocketMoneyToolTip = $widgets->addToolTip(getBadgerTranslation2("forecast", "dailyPocketMoneyToolTip")) . "<br />";
			}
			$day = 0;
			$pocketMoney1EndValue = "";
			$pocketMoney2EndValue = "";
			foreach($totals as $key => $val) {
				$tmp = new Date($key);
				
				$PocketMoney1Loop = new Amount ($pocketMoney1);
				$val2 = new Amount ($val->get());
				if ($showPocketMoney1 ==1){
					$pocketMoney1EndValue = $val2->sub($PocketMoney1Loop->mul($day))->get();
				}
				$PocketMoney2Loop = new Amount ($pocketMoney2);
				$val3 = new Amount ($val->get());
				if ($showPocketMoney2 == 1){
					$pocketMoney2EndValue = $val3->sub($PocketMoney2Loop->mul($day))->get();
				}
				$day++;
			} //foreach($totals as $key => $val) {
			if ($pocketMoney1EndValue){
				$balancedEndDateLabel1 = getBadgerTranslation2("forecast", "printedPocketMoney1Label"). ": " . $pocketMoney1 . ")".":";
				$printedPocketMoney1EndValue = new Amount ($pocketMoney1EndValue);
				$balancedEndDate1 = $printedPocketMoney1EndValue->getFormatted() ."<br />";
			}
			if ($pocketMoney2EndValue){
				$balancedEndDateLabel2 = getBadgerTranslation2("forecast", "printedPocketMoney2Label"). ": " . $pocketMoney2 . ")".":";
				$printedPocketMoney2EndValue = new Amount ($pocketMoney2EndValue);
				$balancedEndDate2 = $printedPocketMoney2EndValue->getFormatted() .  "<br />";
			}
		}
	} else { //if ($today->compare($today, $selectedDate)!=1){
		$noFutureDates = getBadgerTranslation2("forecast", "onlyFutureDates") . "<br />";
	}
	eval("echo \"".$tpl->getTemplate("forecast/forecastChart")."\";");
} //if (isset($_POST['sendData'])){
eval("echo \"".$tpl->getTemplate("badgerFooter")."\";");

?>

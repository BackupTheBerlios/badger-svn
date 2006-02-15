<h1>$pageHeading</h1>
<form action="" method="post" enctype="multipart/form-data" name="mainform" id="Selection" onSubmit="return validateCompleteForm(this, 'error');">
	<fieldset style="width: 31em">
		<legend>$legendSetting</legend>
		<table>
			<tr> 
				<td><b>$endDateLabel</b> </td>
				<td>$endDateField $endDateToolTip </td>
			</tr>
			<tr> 
			 	<td>$accountLabel </td>
			 	<td> $accountField </td>
			<tr>
			<tr> 
				<td>$savingTargetLabel</td>
				<td>$savingTargetField</td>
			</tr>
			<tr> 
				<td>$pocketMoney1Label </td>
				<td> $pocketMoney1Field </td>
			</tr>
			<tr> 
				<td>$pocketMoney2Label </td>
				<td>$pocketMoney2Field </td>
			</tr>
			<tr> 
				<td><b>$calculatedPocketMoneyLabel </b></td>
				<td>$calculatePocketMoneyStartDateField <br /> $writeCalcuatedPocketMoneyButton $writeCalculatedToolTip</td>
			</tr>
		</table>
	</fieldset>
<br /><br />
	<fieldset style="width: 31em">
		<legend>$legendGraphs</legend>
		<table>
			<tr> 
				<td>$lowerLimitLabel </td>
				<td>$lowerLimitBox $lowerLimitToolTip </td>
			</tr>
			<tr> 
			 	<td>$upperLimitLabel</td>
			 	<td> $upperLimitBox $upperLimitToolTip </td>
			<tr>
			<tr> 
				<td>$plannedTransactionsLabel</td>
				<td>$plannedTransactionsBox $plannedTransactionsToolTip</td>
			</tr>
			<tr> 
				<td>$savingTargetLabel1</td>
				<td> $savingTargetBox $savingTargetToolTip </td>
			</tr>
			<tr> 
				<td>$pocketMoney1Label1</td>
				<td>$pocketMoney1Box $pocketMoney1ToolTip</td>
			</tr>
			<tr> 
				<td>$pocketMoney2Label1</td>
				<td>$pocketMoney2Box $pocketMoneyTool2Tip</td>
			</tr>
		</table>
	</fieldset>
<br />
$tooLongTimeSpanWarning <br />
$sendButton <br />
</form>
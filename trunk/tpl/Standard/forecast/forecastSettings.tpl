<h1>$pageHeading</h1>
<form action="" method="post" enctype="multipart/form-data" name="mainform" id="Selection" onSubmit="return validateCompleteForm(this, 'error');">
	<fieldset>
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
	<fieldset>
		<legend>$legendGraphs</legend>
		<table>
			<tr> 
				<td><b>$lowerLimitLabel</b> </td>
				<td>$lowerLimitBox $lowerLimitToolTip </td>
			</tr>
			<tr> 
			 	<td><b>$upperLimitLabel</b></td>
			 	<td> $upperLimitBox $upperLimitToolTip </td>
			<tr>
			<tr> 
				<td><b>$plannedTransactionsLabel</b></td>
				<td>$plannedTransactionsBox $plannedTransactionsToolTip</td>
			</tr>
			<tr> 
				<td><b>$savingTargetLabel1</b> </td>
				<td> $savingTargetBox $savingTargetToolTip </td>
			</tr>
			<tr> 
				<td><b>$pocketMoney1Label1</b> </td>
				<td>$pocketMoney1Box $pocketMoney1ToolTip</td>
			</tr>
			<tr> 
				<td><b>$pocketMoney2Label1</b> </td>
				<td>$pocketMoney2Box $pocketMoneyTool2Tip</td>
			</tr>
		</table>
	</fieldset>
<br />
$sendButton <br />
</form>
<h1>$pageHeading</h1>
<form action="" method="post" accept-charset="ISO-8859-1" enctype="multipart/form-data" name="mainform" id="Selection" onSubmit="return validateCompleteForm(this, 'error');">
	<table>
	<tr>
	<td  valign="top">
	<fieldset style="width: 32em; height: 15em;">
		<legend>$legendSetting</legend>
		<table>
			<tr> 
				<td><b>$endDateLabel</b></td>
				<td style="white-space: nowrap;">$endDateField $endDateToolTip</td>
			</tr>
			<tr> 
			 	<td>$accountLabel</td>
			 	<td style="white-space: nowrap;">$accountField</td>
			<tr>
			<tr> 
				<td>$savingTargetLabel</td>
				<td style="white-space: nowrap;">$savingTargetField</td>
			</tr>
			<tr> 
				<td>$pocketMoney1Label</td>
				<td style="white-space: nowrap;">$pocketMoney1Field</td>
			</tr>
			<tr> 
				<td>$pocketMoney2Label</td>
				<td style="white-space: nowrap;">$pocketMoney2Field</td>
			</tr>
			<tr> 
				<td><b>$calculatedPocketMoneyLabel</b></td>
				<td style="white-space: nowrap;">$calculatePocketMoneyStartDateField<br />$writeCalcuatedPocketMoneyButton $writeCalculatedToolTip</td>
			</tr>
		</table>
	</fieldset>
	</td>
	<td valign="top">
	<fieldset style="width: 20em; height: 15em;">
		<legend>$legendGraphs</legend>
		<table>
			<tr> 
				<td>$lowerLimitLabel </td>
				<td style="white-space: nowrap;">$lowerLimitBox $lowerLimitToolTip</td>
			</tr>
			<tr> 
			 	<td>$upperLimitLabel</td>
			 	<td style="white-space: nowrap;">$upperLimitBox $upperLimitToolTip</td>
			<tr>
			<tr> 
				<td>$plannedTransactionsLabel</td>
				<td style="white-space: nowrap;">$plannedTransactionsBox $plannedTransactionsToolTip</td>
			</tr>
			<tr> 
				<td>$savingTargetLabel1</td>
				<td style="white-space: nowrap;">$savingTargetBox $savingTargetToolTip</td>
			</tr>
			<tr> 
				<td>$pocketMoney1Label1</td>
				<td style="white-space: nowrap;">$pocketMoney1Box $pocketMoney1ToolTip</td>
			</tr>
			<tr> 
				<td>$pocketMoney2Label1</td>
				<td style="white-space: nowrap;">$pocketMoney2Box $pocketMoneyTool2Tip</td>
			</tr>
		</table>
	</fieldset>
	</td>
	</tr>
	<tr>
		<td colspan="2">
			<fieldset style="width: 54em">
				$tooLongTimeSpanWarning
			</fieldset>
		</td>
	</tr>
	</table>
$sendButton <br />
</form>
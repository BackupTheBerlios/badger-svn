<h1>$pageTitle</h1>

<div id="filter">
	<form action="javascript:return false;" name="mainform" id="mainform">
		<fieldset>
			<legend>$legendFilter</legend>
			<table>
				<tr>
					<td>$titleLabel</td>
					<td>$titleFilterOperator</td>
					<td>$titleField</td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td>$valutaDateLabel</td>
					<td>$valutaDateFilterOperator</td>
					<td>$valutaDateField</td>
					<td>$btnFilterOkay</td>
				</tr>
				<tr>
					<td>$categoryLabel</td>
					<td>&nbsp;</td>
					<td>$categoryField</td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td>$amountLabel</td>
					<td>$amountFilterOperator</td>
					<td>$amountField</td>
					<td>$btnFilterReset</td>
				</tr>
			</table>
		</fieldset>
	</form>
</div>
<br>
$btnNewFinished&nbsp;$btnNewPlanned&nbsp;$btnEdit&nbsp;$btnDelete&nbsp;$btnShowPlannedTransactions&nbsp;
$dgHtml

<fieldset class="dataGridLegend">
	<legend>$legend</legend>
	<table>
		<tr>
			<td>$finishedTransactionImage $finishedTransactionText</td>
			<td>$finishedTransferalSourceTransactionImage $finishedTransferalSourceTransactionText</td>
			<td>$finishedTransferalTargetTransactionImage $finishedTransferalTargetTransactionText</td>
		</tr>
		<tr>
			<td>$plannedTransactionImage $plannedTransactionText</td>
			<td>$plannedTransferalSourceTransactionImage $plannedTransferalSourceTransactionText</td>
			<td>$plannedTransferalTargetTransactionImage $plannedTransferalTargetTransactionText</td>
		</tr>
	</table>
</fieldset>
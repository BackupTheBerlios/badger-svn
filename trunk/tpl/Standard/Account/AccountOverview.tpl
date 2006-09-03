<h1>$pageTitle</h1>

<div id="filter">
	<form action="javascript:return false;" name="mainform">
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
				</tr>
				<tr>
					<td>$categoryLabel</td>
					<td>&nbsp;</td>
					<td>$categoryField</td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td>$amountLabel</td>
					<td>$amountFilterOperator</td>
					<td>$amountField</td>
				</tr>
				<tr>
					<td></td>
					<td></td>
					<td></td>
					<td>&nbsp;&nbsp;&nbsp;</td>
					<td colspan="3" align="right">$btnFilterOkay&nbsp;$btnFilterReset</td>				
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
	<p>$finishedTransactionImage $finishedTransactionText</p>
	<p>$plannedTransactionImage $plannedTransactionText</p>
</fieldset>
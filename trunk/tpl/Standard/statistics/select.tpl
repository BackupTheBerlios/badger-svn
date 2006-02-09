<h1>$selectTitle</h1>

<table><tr>
	<td style="vertical-align: top;">
		<form method="post" action="$selectFormAction" id="selectForm" name="mainform">
		
			<table cellspacing="0" cellpadding="0"><tr>
				<td style="vertical-align: top; height: 50%;">
					<fieldset>
						<legend>Typ</legend>
						$trendRadio $trendLabel<br />
						$categoryRadio $categoryLabel
					</fieldset>
					<fieldset>
						<legend>Kategorie-Art</legend>
						$inputRadio $inputLabel<br />
						$outputRadio $outputLabel
					</fieldset>
				</td>
		
				<td style="vertical-align: top; height: 50%;">
					<fieldset style="white-space: nowrap; height: 100%;">
						<legend>Zeitraum</legend>
						<table>
							<tr>
								<td style="text-align: right;">$yearInput</td>
								<td style="text-align: right;">Von: $startDateField</td>
							</tr>
							<tr>
								<td style="text-align: right;">$monthSelect</td>
								<td style="text-align: right;">bis: $endDateField</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
			<tr>		
				<td colspan="2" style="height: 50%;">
					<fieldset style="height: 8em;">			
						<legend>Kategorien zusammenfassen</legend>
						$summarizeRadio $summarizeLabel<br />
						$distinguishRadio $distinguishLabel
					</fieldset>
				</td>
			</tr></table>
		</form>
	</td>
	<td>
		<fieldset>
			<legend>Konten</legend>
			$accountSelect
		</fieldset>
	</td>
</tr></table>
</div>
<p style="clear: both;">$submitButton</p>
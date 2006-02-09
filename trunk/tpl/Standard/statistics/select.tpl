<h1>$selectTitle</h1>

<form method="post" action="$selectFormAction" id="selectForm" name="mainform">
		
	<fieldset style="position: absolute; left: 1em; top: 10em; width: 7em; height: 4em;">
		<legend>Typ</legend>
		$trendRadio $trendLabel<br />
		$categoryRadio $categoryLabel
	</fieldset>
	<fieldset style="position: absolute; left: 1em; top: 16em; width: 7em; height: 4em;">
		<legend>Kategorie-Art</legend>
		$inputRadio $inputLabel<br />
		$outputRadio $outputLabel
	</fieldset>
	<fieldset style="position: absolute; left: 10em; top: 10em; width: 22em; height: 10em;">
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
	<fieldset style="position: absolute; left: 1em; top: 22em; width: 31em; height: 10em;">
		<legend>Kategorien zusammenfassen</legend>
		$summarizeRadio $summarizeLabel<br />
		$distinguishRadio $distinguishLabel
	</fieldset>
</form>
<fieldset style="position: absolute; left: 34em; top: 10em; width: 410px; height: 22em;">
	<legend>Konten</legend>
	$accountSelect
</fieldset>
<p style="margin-top: 26em;">$submitButton</p>
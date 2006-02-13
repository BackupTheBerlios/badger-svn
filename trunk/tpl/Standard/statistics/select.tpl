<h1>$selectTitle</h1>

<form method="post" action="$selectFormAction" id="selectForm" name="mainform">
		
	<fieldset style="position: absolute; left: 1em; top: 8em; width: 7em; height: 4em;">
		<legend>Typ</legend>
		$trendRadio $trendLabel<br />
		$categoryRadio $categoryLabel
	</fieldset>
	<fieldset style="position: absolute; left: 1em; top: 14em; width: 7em; height: 4em;">
		<legend>Kategorie-Art</legend>
		$inputRadio $inputLabel<br />
		$outputRadio $outputLabel
	</fieldset>
	<fieldset style="position: absolute; left: 10em; top: 8em; width: 22em; height: 10em;">
		<legend>Zeitraum</legend>
		<p>$monthSelect $yearInput</p>
		<p>Von: $startDateField bis: $endDateField</p>
	</fieldset>
	<fieldset style="position: absolute; left: 1em; top: 20em; width: 31em; height: 4em;">
		<legend>Kategorien zusammenfassen</legend>
		$summarizeRadio $summarizeLabel<br />
		$distinguishRadio $distinguishLabel
	</fieldset>
	
	$accountField
	$dateFormatField
	$errorMsgAccountMissingField
	$errorMsgStartBeforeEndField
	$errorMsgEndInFutureField
</form>
<fieldset style="position: absolute; left: 34em; top: 8em; width: 410px; height: 16em;">
	<legend>Konten</legend>
	$accountSelect
	<p>Achtung: Bei der gleichzeitigen Betrachtung mehrerer Konten mit unterschiedlichen Währungen findet keine Umrechnung statt!</p>
</fieldset>
<p style="margin-top: 19em;">$submitButton</p>

<div id="flashContainer" class="flashContainer"></div>
<div class="flashClear"></div>
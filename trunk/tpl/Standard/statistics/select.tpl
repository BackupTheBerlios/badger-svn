<h1>$selectTitle</h1>

<form method="post" action="$selectFormAction" id="selectForm" name="mainform">

	<h2>Typ</h2>
	<p>
		$trendRadio $trendLabel<br />
		$categoryRadio $categoryLabel
	</p>

	<h2>Zeitraum</h2>
	<p>Von: $startDateField bis: $endDateField</p>
	
	$accountField
</form>

<h2>Konten</h2>
<p>
	$accountSelect
</p>

<p>$submitButton</p>
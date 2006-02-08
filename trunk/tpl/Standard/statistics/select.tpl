<h1>$selectTitle</h1>

<form method="post" action="$selectFormAction" id="selectForm">

	<h2>Typ</h2>
	<p>
		$trendRadio $trendLabel<br />
		$categoryRadio $categoryLabel
	</p>

	<h2>Konten</h2>
	<p>
		$accountSelect
		$accountField
	</p>
	
	<h2>Zeitraum</h2>
	<p>Von: $startDateField bis: $endDateField</p>
	
	<p>$submitButton</p>
</form>
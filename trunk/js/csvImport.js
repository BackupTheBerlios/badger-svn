function updateParser() {
	var currentAccountId = $F("accountSelect");
	
	if (currentAccountId && accountParsers[currentAccountId.toString()]) {
		var parserField = $("parserSelect");
		parserField.value = accountParsers[currentAccountId];
	}
}
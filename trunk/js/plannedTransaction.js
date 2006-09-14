function checkBeginEndDate() {
	var beginDate = $("beginDate");
	var endDate =$("endDate");

	var val = $F("range");
	
	if (val == "all") {
		beginDate.disabled = false;
		endDate.disabled = false;
	} else {
		beginDate.disabled = true;
		endDate.disabled = true;
	}
}
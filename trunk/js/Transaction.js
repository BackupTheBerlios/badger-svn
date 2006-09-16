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

function toggleTransferal() {
	var showTransferalData = $F("transferalEnabled");
	var displayType = showTransferalData ? "table-row" : "none";
	
	$("transferalAccountRow").style.display = displayType;
	$("transferalAmountRow").style.display = displayType;
}

function updateTransferalAmount() {
	var currentAmount = $F("amount");
	var currentTransferalAmount = $F("transferalAmount");
	var negativeCurrentAmount;
	
	if (currentAmount.replace(/ /g, "").substr(0, 1) == "-") {
		negativeCurrentAmount = currentAmount.replace(/^ *-/, "");
	} else {
		negativeCurrentAmount = "-" + currentAmount.replace(/^ */, "");
	}
	if (previousAmount == currentTransferalAmount) {
		$("transferalAmount").value = negativeCurrentAmount;
		adjustInputNumberClass($("transferalAmount"));
	}
	
	previousAmount = negativeCurrentAmount;
}

function adjustInputNumberClass(elm) {
	var val = elm.value;
	
	if (val.replace(/ /g, "").substr(0, 1) == "-") {
		elm.className = "inputNumberMinus";
	} else {
		elm.className = "inputNumber"
	}
}
	
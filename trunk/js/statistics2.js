var currentFilterId = 0;

function addFilterX() {
	var filterLine = $("filterLineEmpty").innerHTML;
	filterLine = filterLine.replace(/__FILTER_ID__/g, currentFilterId);
	var newDiv = document.createElement("div");
	newDiv.id = "wholeFilterLine" + currentFilterId;
	newDiv.style.clear = "both";
	newDiv.innerHTML = filterLine;
	$("filterContent").appendChild(newDiv);
	currentFilterId++;
}

function setFilterContent(id) {
	var selectedFilter = $F("filterSelect" + id);
	
	if (selectedFilter != 'delete') {
		var filterLine = $(selectedFilter + "Empty").innerHTML;
		filterLine = filterLine.replace(/__FILTER_ID__/g, id);
		$("filterContent" + id).innerHTML = filterLine;
	} else {
		var filterLine = $("wholeFilterLine" + id);
		filterLine.parentNode.removeChild(filterLine);
	}
}

function applyFilterX() {
	dgDeleteAllFilter();
	
	for (var currentId = 0; currentId < currentFilterId; currentId++) {
		if ($("filterSelect" + currentId)) {
			var currentFilterType = $F("filterSelect" + currentId);
			switch (currentFilterType) {
				case "title":
				case "description":
				case "valutaDate":
				case "amount":
				case "transactionPartner":
					if ($F(currentFilterType + currentId) != "") {
						dgAddFilter(currentFilterType, $F(currentFilterType + "Operator" + currentId), $F(currentFilterType + currentId));
					}
					break;
				
				case "valutaDateBetween":
					if ($F("valutaDateStart" + currentId) != "" && $F("valutaDateEnd" + currentId) != "") {
						dgAddFilter("valutaDate", "ge", $F("valutaDateStart" + currentId));
						dgAddFilter("valutaDate", "le", $F("valutaDateEnd" + currentId));
					}
					break;
				
				case "valutaDateAgo":
					if (parseInt($F("valutaDateAgo" + currentId)) > 0) {
						var dateFormat = $F("dateFormat");
						var now = new Date();
						var ago = new Date(now.getTime() - parseInt($F("valutaDateAgo" + currentId)) * 24 * 60 * 60 * 1000);
						var agoString = dateFormat;
						var day = "" + (ago.getDay() < 10 ? "0" : "") + ago.getDay();
						var month = "" + (ago.getMonth() + 1 < 10 ? "0" : "") + (ago.getMonth() + 1);
						var year = "" + ago.getFullYear();
						agoString = agoString.replace(/dd/, day);
						agoString = agoString.replace(/mm/, month);
						agoString = agoString.replace(/yyyy/, year);
						agoString = agoString.replace(/yy/, year.substr(2, 2));
						dgAddFilter("valutaDate", "ge", agoString);
					}
					break;
				
				case "category":
					if (parseInt($F("categoryId" + currentId)) > 0) {
						dgAddFilter("categoryId", "eq", parseInt($F("categoryId" + currentId)));
					}
					break;
				
				case "outsideCapital":
				case "exceptional":
				case "periodical":
					var valTrue = $F(currentFilterType + currentId);
					var valFalse = $F(currentFilterType + currentId + "_0"); 

					var val = null;

					if (valTrue == "1") {
						val = 1;
					}
					if (valFalse == "0") {
						val = 0;
					}

					if (val !== null) {
						dgAddFilter(currentFilterType, "eq", val);
					}
					break;
			} //switch
		} //if filterSelect
	} //for currentId
	
	loadData();
	saveDataGridParameter();
}
				
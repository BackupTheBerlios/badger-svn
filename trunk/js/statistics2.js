var currentFilterId = 0;
var currentFilterX;
var baseFilterX;
var graphAjax;

function addFilterLineX() {
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
	emptyFilterX();
	
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
						addFilterX(currentFilterType, $F(currentFilterType + "Operator" + currentId), $F(currentFilterType + currentId));
					}
					break;
				
				case "valutaDateBetween":
					if ($F("valutaDateStart" + currentId) != "" && $F("valutaDateEnd" + currentId) != "") {
						addFilterX("valutaDate", "ge", $F("valutaDateStart" + currentId));
						addFilterX("valutaDate", "le", $F("valutaDateEnd" + currentId));
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
						addFilterX("valutaDate", "ge", agoString);
					}
					break;
				
				case "category":
					if ($F("categoryOp" + currentId) || $F("categoryOp" + currentId + "_0")) {
						var operator;
						
						if ($F("categoryOp" + currentId) == "eq") {
							operator = "eq";
						} else {
							operator = "ne";
						}
						
						if (parseInt($F("categoryId" + currentId)) != 0) {
							var field;
							var id;
							if ($F("categoryId" + currentId).substr(0, 1) == '-') {
								field = "parentCategoryId";
								id = parseInt($F("categoryId" + currentId)) * -1;
							} else {
								field = "categoryId";
								id = parseInt($F("categoryId" + currentId));
							}
							addFilterX(field, operator, id);
						}
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
						addFilterX(currentFilterType, "eq", val);
					}
					break;
			} //switch
		} //if filterSelect
	} //for currentId
	
	showGraph();
	saveBaseFilter();
	setDGResultAccounts(getSelectedAccountIds());
	updateDGResult();
}

function emptyFilterX() {
	currentFilterX = new Array();
}

function addFilterX(field, operator, value) {
	currentFilterX.push({
		"field" : field,
		"operator" : operator,
		"value" : value
	});
}

function saveBaseFilter() {
	baseFilterX = currentFilterX.concat();
}

function resetBaseFilter() {
	currentFilterX = baseFilterX.concat();
}

function getSelectedAccountIds() {
	var accountIdArr = $("dataGridStatistics2Accounts").obj.getAllIds();
	var accountIds = "";
	for (i = 0; i < accountIdArr.length; i++) {
		accountIds += accountIdArr[i] + ",";
	}
	accountIds = accountIds.substr(0, accountIds.length - 1);
	
	return accountIds;
}

function setDGResultAccounts(accountIds) {
	$("dataGridStatistics2Result").obj.sourceXML = "../../core/XML/getDataGridXML.php?q=MultipleAccounts&qp=" + accountIds;
}

function updateDGResult() {
$("dataGridStatistics2Result").obj.deleteAllFilter();

	for (var i = 0; i < currentFilterX.length; i++) {
		$("dataGridStatistics2Result").obj.addFilter(currentFilterX[i]["field"], currentFilterX[i]["operator"], currentFilterX[i]["value"]);
	}
	
	$("dataGridStatistics2Result").obj.loadData();
}

function serializeParameterX() {
	var result = "";
	
	for (var i = 0; i < currentFilterX.length; i++) {
		result += "&fk" + i + "=" + encodeURI(currentFilterX[i]["field"])
			+ "&fo" + i + "=" + encodeURI(currentFilterX[i]["operator"])
			+ "&fv" + i + "=" + encodeURI(currentFilterX[i]["value"]);
	}
	
	result = result.substr(1);
	
	return result;
}

function showGraph() {
	if ($F("outputSelectionType") == "Trend") {
		showTrendGraph();
	} else if ($F("outputSelectionType_0") == "Category") {
		showCategoryGraph();
	} else {
		showTimespanGraph();
	}
}

function showTrendGraph() {
	var start;
	var ticks;
	
	if ($F("outputSelectionTrendStart") == 0) {
		start = "0";
	} else {
		start = "b";
	}
	
	if ($F("outputSelectionTrendTicks") == "s") {
		ticks = "s";
	} else {
		ticks = "h";
	}
	
	loadGraph("trend.php?accounts=" + getSelectedAccountIds() + "&start=" + start + "&ticks=" + ticks + "&" + serializeParameterX());
}

function showCategoryGraph() {
	var type;
	var summarize;

	if ($F("outputSelectionCategoryType") == "i") {
		type = "i";
		addFilterX("amount", "ge", 0);
	} else {
		type = "o";
		addFilterX("amount", "le", 0);
	}
	
	if ($F("outputSelectionCategorySummarize") == "t") {
		summarize = "t";
	} else {
		summarize = "f";
	}
	
	loadGraph("category.php?accounts=" + getSelectedAccountIds() + "&type=" + type + "&summarize=" + summarize + "&" + serializeParameterX());
}

function showTimespanGraph() {
	var type;
	var summarize;
	
	if ($F("outputSelectionTimespanType") == "w") {
		type = "w";
	} else if ($F("outputSelectionTimespanType_0") == "m") {
		type = "m";
	} else if ($F("outputSelectionTimespanType_1") == "q") {
		type = "q";
	} else {
		type = "y";
	}
	
	if ($F("outputSelectionTimespanSummarize") == "t") {
		summarize = "t";
	} else {
		summarize = "f";
	}
	
	loadGraph("timespan.php?accounts=" + getSelectedAccountIds() + "&type=" + type + "&summarize=" + summarize + "&" + serializeParameterX());
}

function loadGraph(url) {
	if (graphAjax) {
		//How to do that?
		//graphAjax.stop();
	}
	
	graphAjax = new Ajax.Request(
		url,
		{
			onComplete: displayGraph
		}
	);
}

function displayGraph(request) {
	var graphArea = $("graphContent");
	
	graphArea.innerHTML = request.responseText;
}

function updateOutputSelection() {
	var sourceName;
	if ($F("outputSelectionType") == "Trend") {
		sourceName = "outputSelectionTrend";
	} else if ($F("outputSelectionType_0") == "Category") {
		sourceName = "outputSelectionCategory";
	} else {
		sourceName = "outputSelectionTimespan";
	}
	var source = $(sourceName).innerHTML;
	
	var target = $("outputSelectionContent");
	target.innerHTML = source.replace(/__ACTIVE_OS__/g, "");
}

function reachThroughTrend(date, accountIds) {
	setDGResultAccounts(accountIds);
	resetBaseFilter();
	addFilterX("valutaDate", "eq", date);
	updateDGResult();
}

function reachThroughCategory(categoryId) {
	var field;

	if ($F("outputSelectionCategorySummarize") == "t") {
		field = "parentCategoryId";
	} else {
		field = "categoryId";
	}
	resetBaseFilter();
	addFilterX(field, "eq", categoryId);
	updateDGResult();
}

function reachThroughTimespan(begin, end, categoryId) {
	var field;
	if ($F("outputSelectionTimespanSummarize") == "t") {
		field = "parentCategoryId";
	} else {
		field = "categoryId";
	}
	resetBaseFilter();
	addFilterX(field, "eq", categoryId);
	addFilterX("valutaDate", "ge", begin);
	addFilterX("valutaDate", "le", end);
	updateDGResult();
}
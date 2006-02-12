function submitSelect() {
	url = "../../modules/statistics/statistics.php"

	form = document.getElementById("selectForm");
	
	url += "?mode=" + (form.elements["mode"][0].checked ? "trendData" : "categoryData");
		
		
	accountIds = '';
	first = true;
	accountArray = dgGetAllIds();
	
	for (i = 0; i < accountArray.length; i++) {
		if (!first) {
			accountIds += ";";
		} else {
			first = false;
		}
		
		accountIds += accountArray[i];
	}

	url += "&accounts=" + 	accountIds;
	
	url += "&startDate=" + parseDate(form.startDate.value, "dd.mm.yyyy");
	
	url += "&endDate=" + parseDate(form.endDate.value, "dd.mm.yyyy");
	
	url += "&type=" + (form.elements["type"][0].checked ? "i" : "o");
	
	url += "&summarize=" + (form.elements["summarize"][0].checked ? "t" : "f");

	//alert(url);

	writeFlash(encodeURIComponent(url));
	
	return;

}

function writeFlash(url) {
	container = document.getElementById('flashContainer');
	
	objectStart = "<object classid='clsid:D27CDB6E-AE6D-11cf-96B8-444553540000' codebase='http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0' width='750' height='500'>";
	objectEnd = "</object>";
	paramMovie = "<param name='movie' value='../../includes/charts/charts.swf?library_path=..%2F..%2Fincludes%2Fcharts%2Fcharts_library&php_source=" + url + "' />";
	paramQuality = "<param name='quality' value='high' />";
	paramBgcolor = "<param name='bgcolor' value='#99cc00' />";
	paramWmode = "<param name='wmode' value='transparent' />";
	embed = "<embed src='../../includes/charts/charts.swf?library_path=..%2F..%2Fincludes%2Fcharts%2Fcharts_library&php_source=" + url + "' width='750' height='500' quality='high' pluginspage='http://www.macromedia.com/go/getflashplayer' type='application/x-shockwave-flash' bgcolor='#99cc00' wmode='transparent' />";
	
	container.innerHTML = objectStart + paramMovie + paramQuality + paramBgcolor + paramWmode + embed + objectEnd;
}

function parseDate (date, format) {
	dateFormat = format;
	formatChar = ' ';
	aFormat = dateFormat.split(formatChar);
	if (aFormat.length < 3) {
		formatChar = '/';
		aFormat = dateFormat.split(formatChar);
		if (aFormat.length < 3) {
			formatChar = '.';
			aFormat = dateFormat.split(formatChar);
			if (aFormat.length < 3) {
				formatChar = '-';
				aFormat = dateFormat.split(formatChar);
				if (aFormat.length < 3) {
					formatChar = '';					// invalid date format

				}
			}
		}
	}

	tokensChanged = 0;
	if (formatChar != "") {
		aData =	date.split(formatChar);			// use user's date

		for (i=0; i<3; i++) {
			if ((aFormat[i] == "d") || (aFormat[i] == "dd")) {
				dateSelected = parseInt(aData[i], 10);
				tokensChanged++;
			} else if ((aFormat[i] == "m") || (aFormat[i] == "mm")) {
				monthSelected = parseInt(aData[i], 10);
				tokensChanged++;
			} else if (aFormat[i] == "yyyy") {
				yearSelected = parseInt(aData[i], 10);
				tokensChanged++;
			} else if (aFormat[i] == "mmm") {
				for (j=0; j<12; j++) {
					if (aData[i] == monthName[j]) {
						monthSelected=j;
						tokensChanged++;
					}
				}
			} else if (aFormat[i] == "mmmm") {
				for (j=0; j<12; j++) {
					if (aData[i] == monthName2[j]) {
						monthSelected = j;
						tokensChanged++;
					}
				}
			}
		}
	}
	
	return yearSelected + "-" + (monthSelected < 10 ? "0" : "") + monthSelected + "-" + (dateSelected < 10 ? "0" : "") + dateSelected;
}
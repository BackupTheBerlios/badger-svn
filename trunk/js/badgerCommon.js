function openBadgerPrintView() {
	var newWindow;
	
	winUrl = document.location.href;
	if(winUrl.indexOf("?")==-1) {
		winUrl = winUrl + "?print";
	} else {
		winUrl = winUrl + "&print";
	}
	winName = "";
	winpars = 'height=550,width=800,top=0,left=0,scrollbars=yes,resizable=no';
	newWindow = window.open(winUrl, winName, winpars);
	
	newWindow.window.focus();
}
function openBadgerPrintPDF(badgerRoot) {
	targetUrl = document.location.href;
	if(targetUrl.indexOf("?")==-1) {
		targetUrl = targetUrl + "?print";
	} else {
		targetUrl = targetUrl + "&print";
	}
	PDFUrl = badgerRoot + "/includes/html2pdf/html2ps.php?URL=" + targetUrl + 
			"pixels=800&scalepoints=1&renderimages=1&renderlinks=1&" +
			"renderfields=1&media=A4&cssmedia=Print&leftmargin=30&" +
			"rightmargin=15&topmargin=15&bottommargin=15&" +
			"encoding=&method=fpdf&pdfversion=1.3&output=1&" +
			"badgerFileName=" + document.title + ".pdf";
	document.location.href = PDFUrl;
}
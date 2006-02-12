function openBadgerPrintView() {
	var newWindow;
	
	winUrl = document.location.href;
	if(winUrl.indexOf("?")==-1) {
		winUrl = winUrl + "?print";
	} else {
		winUrl = winUrl + "&print";
	}
	winName = "";
	winpars = 'height=550,width=700,top=0,left=0,scrollbars=yes,resizable=no';
	newWindow = window.open(winUrl, winName, winpars);
	
	newWindow.window.focus();
}
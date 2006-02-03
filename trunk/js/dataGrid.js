// XMLHTTPRequest
function loadData(url) {
	xmlHttp = new XMLHttpRequest();
	xmlHttp.open("POST", url, 0);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send(null);
	
	return xmlHttp.responseXML;
}



var objRowActive;
var mouseEventsDisabled;
mouseEventsDisabled = false;

// Row Handling
function activateRow(objRow) {
	if (objRow.className == "dgRow") objRow.className = "dgRowActive";
	if (objRow.className == "dgRowSelected") objRow.className = "dgRowSelectedActive";
	$("check" + objRow.id).focus();
}
function deactivateRow(objRow) {
	if (objRow.className == "dgRowActive") objRow.className = "dgRow";
	if (objRow.className == "dgRowSelectedActive") objRow.className = "dgRowSelected";
}

function selectRow(objRow) {
	if (objRow.className == "dgRow") objRow.className = "dgRowSelected";
	if (objRow.className == "dgRowActive") objRow.className = "dgRowSelectedActive";
	$("check"+objRow.id).checked = "checked";
	$("check"+objRow.id).focus();
}
function deselectRow(objRow) {
	if (objRow.className == "dgRowSelected") objRow.className = "dgRow";
	if (objRow.className == "dgRowSelectedActive") objRow.className = "dgRowActive";
	$("check"+objRow.id).checked = "";
	$("check"+objRow.id).focus();
}

function enableMouseEvents() {
	mouseEventsDisabled = false;
}

//Mouse-Events of the Rows
Behaviour.register( {
	'tr.dgRow' : function(element){
		element.onmouseover = function(){
			if (!mouseEventsDisabled) {
				if(objRowActive) deactivateRow(objRowActive);
				objRowActive = this;
				activateRow(this);
			}
		}
		element.onmouseout = function(){
			if (!mouseEventsDisabled) deactivateRow(this);
		}
		element.onclick = function(){
			if(this.className=="dgRowSelected" || this.className=="dgRowSelectedActive") {
				deselectRow(this);
			} else {
				selectRow(this);
			}
		}
		element.ondblclick = function(){
			alert("Editieren von ID: " + this.id);
		}
	}	
});

//Key-Events of the Rows
function dgKeyProcess(event) {
	if (!event) event=window.event;
	
	//alert(event.keyCode);
	if (event.keyCode == Event.KEY_DOWN) {
		Event.stop(event);
		
		//when dataGrid scrolls down, disable mouse events
		mouseEventsDisabled = true;
		window.setTimeout("enableMouseEvents()", 10);
		
		if (objRowActive) {
			objNextRow = objRowActive.nextSibling;
			if (objNextRow) {
				if(objNextRow.tagName!="TR") objNextRow = objNextRow.nextSibling; //only FF, difference in the DOM
				deactivateRow(objRowActive);
				activateRow(objNextRow);
				objRowActive = objNextRow;
			}
		} else {
			objRowActive = $("dgData").getElementsByTagName("tr")[0];
			activateRow(objRowActive);
		}
		
	}
	if (event.keyCode == Event.KEY_UP) {
		Event.stop(event);
		
		//when dataGrid scrolls down, disable mouse events
		mouseEventsDisabled = true;
		window.setTimeout("enableMouseEvents()", 10);
		
		if (objRowActive) {
			objNextRow = objRowActive.previousSibling;
			if (objNextRow) {
				if(objNextRow.tagName!="TR") objNextRow = objNextRow.previousSibling; //only FF, difference in the DOM
				deactivateRow(objRowActive);
				activateRow(objNextRow);
				objRowActive = objNextRow;
			}
		}
	}
	if (event.keyCode == Event.KEY_RETURN) {
		alert("Editieren");
	}
	if (event.keyCode == Event.KEY_DELETE) {
		confirm("ToDo: Wollen Sie die Datens?tze wirklich l?schen?")
	}

}
Event.observe(document, 'keypress', dgKeyProcess, false)
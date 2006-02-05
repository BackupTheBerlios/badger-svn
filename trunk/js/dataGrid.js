// XMLHTTPRequest
xmlHttp = new XMLHttpRequest();
function loadData(url) {
	//xmlHttp = new XMLHttpRequest();
	xmlHttp.onreadystatechange=xmlHttpChange;
	xmlHttp.open("POST", url, 1);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send(null);
	
	//return xmlHttp.responseXML;
	
}

function xmlHttpChange() {
	// if xmlhttp shows "loaded"
	if (xmlHttp.readyState==4) {
		// if "OK"
		if (xmlHttp.status==200) {
			dgInit();
		} else {
			alert("Problem retrieving XML data")
	    }
	}
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
var behaviour =  {
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
			dgEdit(this.id)
		}
	}	
};

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
		dgEdit(objRowActive.id);
	}
	if (event.keyCode == Event.KEY_DELETE) {
		dgDelete();
	}

}

function dgDelete() {
	choise = confirm(dgDeleteMsg);
	if (choise) {
		//get all selected row and delete them aysnc + update table
		dgData = document.getElementById("dgData");
		
		checkbox = Form.getInputs("dgForm","checkbox");
		//checkif enabled
		for (i=0; i<checkbox.length; i++) {
			if ($F(checkbox[i]) == "on") {
				// if background delete id okay then 
				alert("ToDo: BackgroundDelete " + dgDeleteAction + " ID:" + checkbox[i].parentNode.parentNode.id);
				//alert(checkbox[i].id);
				dgData.removeChild(checkbox[i].parentNode.parentNode);
				dgCount = document.getElementById("dgCount").innerHTML;
				dgCount--;
				document.getElementById("dgCount").innerHTML = dgCount;
			};
		} 
		
	}
}
function dgNew() {
	alert(dgEditAction);
}

function dgEdit(id) {
	alert("Action: "+ dgEditAction + " ID: " + id);
}

//delete all rows form the grid
function emptyDataGrid() {
	dgDataGrid = document.getElementById("dgData");
	dgRows = dgDataGrid.getElementsByTagName("tr");

	toBeDeleted = new Array();
	for (id=0; id<dgRows.length; id++) {
		toBeDeleted[id] = dgRows[id].id;
	}
	for (id=0; id<toBeDeleted.length; id++) {
		dgDataGrid.removeChild(document.getElementById(toBeDeleted[id]));
	}
}
Event.observe(document, 'keypress', dgKeyProcess, false)
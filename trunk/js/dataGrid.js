var objRowActive;
var mouseEventsDisabled;
var mouseEventsDisabled = false;
var urlParameter = new Object;

// Create a new XMLHttpRequest object
var xmlHttp = false;
/*@cc_on @*/
/*@if (@_jscript_version >= 5)
try {
  xmlHttp = new ActiveXObject("Msxml2.XMLHTTP");
} catch (e) {
  try {
    xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");
  } catch (e2) {
    xmlHttp = false;
  }
}
@end @*/
if (!xmlHttp && typeof XMLHttpRequest != 'undefined') {
  xmlHttp = new XMLHttpRequest();
}

// send a request to the server, define callback-function
// ToDo: if existing, cancel old request
function loadData(url) {
	xmlHttp.onreadystatechange=handleResponse;
	xmlHttp.open("POST", url, 1);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send(null);
	messageLayer('show', dgLoadingMessage);
}
function deleteData(url) {
	//xmlHttp.onreadystatechange=handleResponse;
	xmlHttp.open("POST", url, 1);
	xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	xmlHttp.send(null);
}

// callback-function
// process to server request
function handleResponse() {
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

function dgInit() {
	xmlDoc = xmlHttp.responseXML;
	//alert(xmlHttp.responseText);
	xmlColumns = xmlDoc.getElementsByTagName("column");
	xmlRows = xmlDoc.getElementsByTagName("row");
	
	dgData = $("dgData"); //.getElementsByTagName("tbody")[0];
	dgRows = dgData.getElementsByTagName("tr");
	if (dgRows.length>0) {emptyDataGrid()};
	
	var columns = new Array();
	for (j=0; j<xmlColumns.length; j++) {
		//alert(xmlColumns[j].textContent);
		columns[xmlColumns[j].textContent] = j; 
	}	
	
	for (j=0; j<xmlRows.length; j++) {
		cells = xmlRows[j].getElementsByTagName("cell");
		rowID = cells[0].textContent;
		
		newRow = document.createElement("tr");
		newRow.className = "dgRow";
		newRow.id=rowID;
		
		//add checkbox to first cell
		checkTD = document.createElement("td");
		checkTD.width="20";
		checkBox = document.createElement("input");
		checkBox.id = "check" + rowID;
		checkBox.name = "check" + rowID;
		checkBox.type = "checkbox";
		checkTD.appendChild(checkBox);
		newRow.appendChild(checkTD);
		
		//insert cell values
		// dgColumnOrder[0] -> 'balance' : name of the column
		// columns['balance'] -> '1' : first column
		// cells[1].textContent -> '899.23' : value
		for (i=0; i<dgColumnOrder.length; i++) {
			cell = document.createElement("td");
			cell.width = dgHeaderSize[i];
			cell.align = dgCellAlign[i];
			//alert("dgCO: " + dgColumnOrder[i]);
			//alert("columns[]=" + columns[dgColumnOrder[i]]);
			//alert("cells[]=" + cells[columns[dgColumnOrder[i]]].textContent);
			cell.innerHTML = cells[columns[dgColumnOrder[i]]].textContent;
			newRow.appendChild(cell);
		}		
		//insert empty cell as last one (only display purposes)
		lastTD = document.createElement("td");
		lastTD.innerHTML = "&nbsp;";
		newRow.appendChild(lastTD);
		
		//add complete row to the grid
		dgData.appendChild(newRow);
	}
	//refresh JS-behaviours of the rows
	Behaviour.apply();
	//refresh row count
	$("dgCount").innerHTML = xmlRows.length;
	messageLayer('hide');
}




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
	},
	'td.dgColumn' : function(element){
		element.onclick = function(){
			id = this.id.replace("dgColumn","");
			addNewSortOrder(id);
			loadData(dgSourceXML + serializeParameter());
		}
	},
	'#dgSelector' : function(element){
		element.onclick = function(){
			checkbox = Form.getInputs("dgForm","checkbox");
			if($F(this)=="on") {
				//select all checkboxes		
				for (i=0; i<checkbox.length; i++) {
					if(checkbox[i].id!="dgSelector") {
						selectRow(checkbox[i].parentNode.parentNode);
					}
				}
			} else {
				//deselect all checkboxes	
				for (i=0; i<checkbox.length; i++) {
					if(checkbox[i].id!="dgSelector") {
						deselectRow(checkbox[i].parentNode.parentNode);
					}
				}
			} //if($F(this)=="on")
			this.focus();
		} //element.onclick 
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
				if(objNextRow) {
					deactivateRow(objRowActive);
					activateRow(objNextRow);
					objRowActive = objNextRow;
				}
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

// delete all selected rows
//  - delete row in GUI
//  - send a background delete request to the server
function dgDelete() {
	if(dgDeleteAction) {
		dgData = $("dgData");	
		checkbox = Form.getInputs("dgForm","checkbox");
		
		allSelectedIds = dgGetAllIds();
		//count selected checkboxes
		
		//asks use, if he is sure
		choise = confirm(dgDeleteMsg +"("+allSelectedIds.length+")");
		if (choise) {
			//background delete -> ToDo: check result of deletion
			deleteData(dgDeleteAction + allSelectedIds);
			//delete rows from dataGrid
			for (i=0; i<allSelectedIds.length; i++) {
				Element.remove($(allSelectedIds[i]));
				dgCount = $("dgCount").innerHTML;
				dgCount--;
				$("dgCount").innerHTML = dgCount;
			}
		} //if (choise)
	}
}

// call site to add a new record
function dgNew(addParam) {
	if(dgNewAction) {
		document.location.href = dgNewAction + addParam;
	}
}

// edit record with ID in a special page
function dgEdit(id) {
	if(dgEditAction) {
		document.location.href = dgEditAction + id;
	}
}

function dgGetAllIds() {
	checkbox = Form.getInputs("dgForm","checkbox");
	allIDs = new Array;
	for (i=0; i<checkbox.length; i++) {
		if(checkbox[i].id.indexOf("check") != -1 ) {
			if ($F(checkbox[i]) == "on") allIDs.push(checkbox[i].parentNode.parentNode.id);
		}
	}
	return allIDs;
}

//delete all rows form the grid
function emptyDataGrid() {
	dgDataGrid = $("dgData");
	dgRows = dgDataGrid.getElementsByTagName("tr");

	toBeDeleted = new Array();
	for (id=0; id<dgRows.length; id++) {
		toBeDeleted[id] = dgRows[id].id;
	}
	for (id=0; id<toBeDeleted.length; id++) {
		dgDataGrid.removeChild($(toBeDeleted[id]));
	}
}

//change sort order and hide/show sort images
var activeColumn;
function addNewSortOrder(column) {
	if(activeColumn) changeColumnSortImage(activeColumn, "empty");
	if(column==urlParameter["ok0"]) {
		//click on the same column:  asc -> desc, desc -> asc
		if (urlParameter["od0"]=="a") {
			urlParameter["od0"]="d";
			changeColumnSortImage(column, "desc");
		} else {
			urlParameter["od0"]="a";
			changeColumnSortImage(column, "asc");
		}
	} else {
		urlParameter["ok2"] = urlParameter["ok1"];
		urlParameter["od2"] = urlParameter["od1"];
		urlParameter["ok1"] = urlParameter["ok0"];
		urlParameter["od1"] = urlParameter["od0"];
		urlParameter["ok0"] = column;
		urlParameter["od0"] = "a";
		changeColumnSortImage(column, "asc");
	}
	activeColumn = column;
}

//change the image for sorting direction
function changeColumnSortImage(id, newstatus) {
	switch(newstatus) {
		case 'empty':
			$("dgImg"+id).src = dgTplPath + "dropEmpty.png";
			break;
		case 'asc':
			$("dgImg"+id).src = dgTplPath + "dropDown.png";
			break;
		case 'desc':
			$("dgImg"+id).src = dgTplPath + "dropUp.png";
			break;
	}	
}

//convert array to string
function serializeParameter() {
	var urlParameterString ="";
	for (var parameter in urlParameter)
		if(parameter!="extend" && urlParameter[parameter]!=undefined) { 
	    	urlParameterString = urlParameterString+"&"+parameter+"="+urlParameter[parameter];
	    }
	return urlParameterString;
}

function messageLayer(action, message) {
	switch(action) {
		case 'show':
			divMessage = $("dgMessage");
			divMessage.style.display = "block";
			divMessage.innerHTML = message;
			break;
		case 'hide':
			divMessage = $("dgMessage");
			divMessage.style.display = "none";
			break;
	}
}


// add event to the document
Event.observe(document, 'keypress', dgKeyProcess, false)


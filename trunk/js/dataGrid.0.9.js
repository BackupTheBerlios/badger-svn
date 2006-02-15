var objRowActive;
var mouseEventsDisabled = false;
var urlParameter = new Object;

// send a request to the server, define callback-function
// ToDo: if existing, cancel old request
function loadData(url) {
	var myAjax = new Ajax.Request(
		url, {
			method: 'post',
			onComplete: dgInit
		}); 
	messageLayer('show', dgLoadingMessage);
}
function deleteData(url) {
	alert(url);
	var myAjax = new Ajax.Request(
		url, {
			method: 'get'
			});
}

function dgInit(originalRequest) {
	xmlDoc = originalRequest.responseXML;
	//alert(xmlHttp.responseText);
	xmlColumns = xmlDoc.getElementsByTagName("column");
	xmlRows = xmlDoc.getElementsByTagName("row");
	
	//delete old table body if exists
	if($("dgTableData").getElementsByTagName("tbody")[0]) {
		Element.remove($("dgTableData").getElementsByTagName("tbody")[0])	
	}
	//create new table body
	dgTableDataBody = document.createElement("tbody");
	dgData = $("dgTableData").appendChild(dgTableDataBody);
	
	var columns = new Array();
	for (j=0; j<xmlColumns.length; j++) {
		if(xmlColumns[j].textContent) {
			columnName = xmlColumns[j].textContent; //FF
		} else {
			columnName = xmlColumns[j].text; //IE
		};
		columns[columnName] = j; 
	}	
	
	for (j=0; j<xmlRows.length; j++) {
		xmlCells = xmlRows[j].getElementsByTagName("cell");
		if(xmlCells[0].textContent) {
			rowID = xmlCells[0].textContent; //FF
		} else {
			rowID = xmlCells[0].text; //IE
		}
		
		newRow = document.createElement("tr");
		newRow.className = "dgRow";
		newRow.id=rowID;
		
		//add checkbox to first cell
		checkTD = document.createElement("td");
		checkTD.style.width = "25px";
		checkBox = document.createElement("input");
		checkBox.id = "check" + rowID;
		checkBox.name = "check" + rowID;
		checkBox.type = "checkbox";
		checkTD.appendChild(checkBox);
		checkTD.innerHTML = checkTD.innerHTML + "&nbsp;";
		newRow.appendChild(checkTD);
		
		//insert cell values
		// dgColumnOrder[0] -> 'balance' : name of the column
		// columns['balance'] -> '1' : first column
		// cells[1].text{Content} -> '899.23' : value
		for (i=0; i<dgColumnOrder.length; i++) {
			cell = document.createElement("td");
			cell.style.width = dgHeaderSize[i] + "px";
			cell.align = dgCellAlign[i];
			xmlElement = xmlCells[columns[dgColumnOrder[i]]];
			if (xmlElement.textContent) {
				cell.innerHTML = xmlElement.textContent + "&nbsp;"; // FF
			} else {
				cell.innerHTML = xmlElement.text + "&nbsp;"; //IE
			}
			newRow.appendChild(cell);
		}		
		//insert empty cell as last one (only display purposes)
		lastTD = document.createElement("td");
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
			objRowActive = $("dgTableData").getElementsByTagName("tr")[0];
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
		dgData = $("dgTableData");	
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
	if(!addParam) addParam = "";
	if(dgNewAction) {
		document.location.href = dgNewAction + "&" + addParam;
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
			$("dgImg"+id).src = dgTplPath + "dropEmpty.gif";
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
			divMessage.style.display = "inline";
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


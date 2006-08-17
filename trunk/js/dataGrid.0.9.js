/*
* ____          _____   _____ ______ _____  
*|  _ \   /\   |  __ \ / ____|  ____|  __ \ 
*| |_) | /  \  | |  | | |  __| |__  | |__) |
*|  _ < / /\ \ | |  | | | |_ |  __| |  _  / 
*| |_) / ____ \| |__| | |__| | |____| | \ \ 
*|____/_/    \_\_____/ \_____|______|_|  \_\
* Open Source Financial Management
* Visit http://badger.berlios.org 
*
**/
/**
 * dataGrid.js
 * 
 * @author Sepp
 * @author togro82
 * @version $LastChangedRevision$
 *
**/

var objRowActive;
var strSortingColumnActive;
var arrSelectedRows = new Array();
var mouseEventsDisabled = false;
var objURLParameter = new Object;

// retrieve data from server, define callback-function
function loadData(strUrl) {
	arrSelectedRows = dgGetAllIds();
	var myAjax = new Ajax.Request(
		strUrl, {
			method: 'post',
			onComplete: dgInsertData,
			onFailure: dgError
		}); 
	messageLayer('show', '<span class="dgMessageHint"> '+dgLoadingMessage+' </span>');
}

// delete data
function deleteData(strUrl) {
	var myAjax = new Ajax.Request(
		strUrl, {
			method: 'get',
			onComplete: dgDeleteResponse,
			onFailure: dgError
			});
}

//displays the message from backend-object
function dgDeleteResponse(objXHR) {

	if (objXHR.responseText=="") {
		
		switch (dgDeleteRefreshType) {
		case 'refreshDataGrid': 
			//refresh complete dataGrid				
			loadData(dgSourceXML + serializeParameter());
			break;
		case 'refreshPage': 
			//refresh complete page	
			window.setTimeout("refreshPage()", 10);
			break;
		default: 
			// no refresh, delete rows in frontend
			allSelectedIds = dgGetAllIds();		    
			for (i=0; i<allSelectedIds.length; i++) {
				Element.remove($(allSelectedIds[i]));
				dgCount = $("dgCount").innerHTML;
				dgCount--;
				$("dgCount").innerHTML = dgCount;
			}
		} //switch	
	} else {
		messageLayer('show', '<span class="dgMessageError"> '+objXHR.responseText+' </span>');
	}
}

//XHR Error
function dgError() {
	messageLayer('show', '<span class="dgMessageError"> XHR Error </span>');
}

function dgInsertData(objXHR) {
	objXmlDoc = objXHR.responseXML;
	//alert(objXHR.responseText);
	xmlColumns = objXmlDoc.getElementsByTagName("column");
	xmlRows = objXmlDoc.getElementsByTagName("row");
	
	//delete old table body if exists
	if($("dgTableData").getElementsByTagName("tbody")[0]) {
		Element.remove($("dgTableData").getElementsByTagName("tbody")[0])	
	}
	//create new table body
	dgTableDataBody = document.createElement("tbody");
	dgData = $("dgTableData").appendChild(dgTableDataBody);

	//column assignment
	//e.g. columnPosition['title'] is the first column in the xml-file;
	var columnPosition = new Array();
	for (intPosition=0; intPosition<xmlColumns.length; intPosition++) {
		if(xmlColumns[intPosition].textContent) columnName = xmlColumns[intPosition].textContent; //FF
		if(xmlColumns[intPosition].text) columnName = xmlColumns[intPosition].text; //IE
		if(xmlColumns[intPosition].innerHTML) columnName = xmlColumns[intPosition].innerHTML; //Opera
		columnPosition[columnName] = intPosition;		
	}
	
	for (j=0; j<xmlRows.length; j++) {
		xmlCells = xmlRows[j].getElementsByTagName("cell");
		
		//first cell of a row, is always a unique ID
		if(xmlCells[0].textContent) rowID = xmlCells[0].textContent; //FF
		if(xmlCells[0].text) rowID = xmlCells[0].text; //IE
		if(xmlCells[0].innerHTML) rowID = xmlCells[0].innerHTML; //Opera
		
		//define a new row
		newRow = document.createElement("tr");
		newRow.className = "dgRow";
		newRow.id=rowID;
		
		//add checkbox as the first cell
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
		// columnPosition['balance'] -> '1' : first column
		// cells[1].text{Content} -> '899.23' : value
		for (i=0; i<dgColumnOrder.length; i++) {
			cell = document.createElement("td");
			cell.style.width = dgHeaderSize[i] + "px";
			cell.align = dgCellAlign[i];
			xmlElement = xmlCells[columnPosition[dgColumnOrder[i]]];
			if (xmlElement.textContent) cell.innerHTML = xmlElement.textContent + "&nbsp;"; // FF
			if (xmlElement.text) cell.innerHTML = xmlElement.text + "&nbsp;"; //IE
			if (xmlElement.innerHTML) { //Opera
				//Incredibly ugly hack to show images in Opera
				text = xmlElement.innerHTML;

				if (text.substr(0, 7) == "&lt;img" && text.substr(text.length - 4, 4) == "&gt;")  {
			 		text = "<" + text.substr(4, text.length - 8) + ">";
					//Why do we have to call replace twice?
			 		text = text.replace('&quot;', '\"').replace('&quot;', '\"');
			 	}
			 	
			 	cell.innerHTML = text;
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

	//activate previous selected rows (after resorting)
	for (i=0; i<arrSelectedRows.length; i++) {
		if($(arrSelectedRows[i])) {
			selectRow($(arrSelectedRows[i]));
		}
	}
	
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

//
function enableMouseEvents() {
	mouseEventsDisabled = false;
}

function refreshPage () {
	location.href = location.href;
}


//Mouse-Events
var behaviour =  {
	//Mouse-Events of the rows (selecting, activating)
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
			dgEdit(this.id);
		}
	},
	//Mouse-Events of the columns (sorting)
	'td.dgColumn' : function(element){
		element.onclick = function(){
			id = this.id.replace("dgColumn","");
			addNewSortOrder(id);
			loadData(dgSourceXML + serializeParameter());
		}
	},
	// checkbox in the dataGrid-Header, for (de-)selecting all
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
	
	//KEY_DOWN
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
	//KEY_UP
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
	//KEY_RETURN
	if (event.keyCode == Event.KEY_RETURN) {
		dgEdit();
	}
	//KEY_DELETE
	if (event.keyCode == Event.KEY_DELETE) {
		dgDelete();
	}
	//KEY_SPACE (only for opera)
	if (event.keyCode == 32) {
		if(objRowActive.className=="dgRowSelected" || objRowActive.className=="dgRowSelectedActive") {
			deselectRow(objRowActive);
		} else {
			selectRow(objRowActive);
		}	
	}
}

// delete all selected rows
//  - delete row in GUI
//  - send a background delete request to the server
function dgDelete() {
	if(dgDeleteAction) {		
		dgData = $("dgTableData");	
		checkbox = Form.getInputs("dgForm","checkbox");
		
		allSelectedIds = dgGetAllIds();	// get selected row ids
		
		//asks use, if he is sure
		choise = confirm(dgDeleteMsg +"("+allSelectedIds.length+")");
		if (choise) {
			// delete data in background
			deleteData(dgDeleteAction + allSelectedIds);			
		} //if (choise)
	} //if (dgDeleteAction)
}

// call site to add a new record
function dgNew(addParam) {
	if(!addParam) addParam = "";
	if(dgNewAction) {
		document.location.href = dgNewAction + "&" + addParam;
	}
}

// call site to  edit record with ID in a special page
function dgEdit(id) {
	if(dgEditAction) {
		if(!id) id = dgGetFirstId(); //if called by button, get first ID
		if(id) {
			document.location.href = dgEditAction + id;
		} else {
			alert (dgNoRowSelectedMsg);
		}
	}
}

// get all ids from selected rows -> array
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
// get all ids from selected rows -> array
function dgGetFirstId() {
	checkbox = Form.getInputs("dgForm","checkbox");

	for (i=0; i<checkbox.length; i++) {
		if(checkbox[i].id.indexOf("check") != -1 ) {
			if ($F(checkbox[i]) == "on") return checkbox[i].parentNode.parentNode.id;
		}
	}
}


//change sort order and hide/show sort images
function addNewSortOrder(strColumn, strDirection) {
	if(strSortingColumnActive) changeColumnSortImage(strSortingColumnActive, "empty");
	if(strColumn==objURLParameter["ok0"]) {
		//click on the same column:  asc -> desc, desc -> asc
		if (objURLParameter["od0"]=="a") {
			objURLParameter["od0"]="d";
			changeColumnSortImage(strColumn, "desc");
		} else {
			objURLParameter["od0"]="a";
			changeColumnSortImage(strColumn, "asc");
		}
	} else {
		objURLParameter["ok2"] = objURLParameter["ok1"];
		objURLParameter["od2"] = objURLParameter["od1"];
		objURLParameter["ok1"] = objURLParameter["ok0"];
		objURLParameter["od1"] = objURLParameter["od0"];
		objURLParameter["ok0"] = strColumn;
		if(strDirection!="desc") {
			objURLParameter["od0"] = "a";
			changeColumnSortImage(strColumn, "asc");
		} else {
			objURLParameter["od0"] = "d";
			changeColumnSortImage(strColumn, "desc");
		}
	}
	strSortingColumnActive = strColumn;
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
	var strURLParameter = "";
	
	for (var parameter in objURLParameter)
		if(parameter!="extend" && objURLParameter[parameter]!=undefined) { 
	    	strURLParameter = strURLParameter + "&" + parameter + "=" + objURLParameter[parameter];
	    }
	return strURLParameter;
}

//display a message in the dataGrid footer
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

//preselect an entry
function dgPreselectId(id) {
	arrSelectedRows.push(id);
}
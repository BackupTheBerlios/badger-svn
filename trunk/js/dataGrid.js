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
var arrURLParameter = new Array();

// initalize
function initDataGrid(strParameter) {
	// if there are some stored values in the usersettings
	if(strParameter) {
		deserializeParameter(strParameter);
		initSortOrder();
		initFilterFields();
	}
	loadData();
}

// retrieve data from server, define callback-function
function loadData() {
	// Base Grid Url + Parameter
	strUrl = dgSourceXML;

	// get selected rows, so that we can restore selection after reloading
	arrSelectedRows = dgGetAllIds();
	
	// load data
	var myAjax = new Ajax.Request(
		strUrl, {
			method: 'post',
			parameters: serializeParameter() + "&sf=" + dgColumnOrder,
			onComplete: dgInsertData,
			onFailure: dgError
		}); 

	// show loading message, image
	messageLayer('show', '<span class="dgMessageHint"> '+dgLoadingMessage+' </span>');
	$('dgDivScroll').className ="dgDivScrollLoading";
	// hide old data
	$('dgTableData').style.visibility = "hidden";
	
	// filter image in footer
	if( arrURLParameter["fn"]>"0" && arrURLParameter["fn"]!=undefined) {
		$('dgFilterStatus').style.visibility = "visible";
	} else {
		$('dgFilterStatus').style.visibility = "hidden";
	}	

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

// displays the message from backend-object
function dgDeleteResponse(objXHR) {
	if (objXHR.responseText=="") {
		
		switch (dgDeleteRefreshType) {
		case 'refreshDataGrid': 
			//refresh complete dataGrid				
			loadData();
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

// fill the datagrid with values
function dgInsertData(objXHR) {
	objXmlDoc = objXHR.responseXML;
	//alert(objXHR.responseText);
	
	if(objXmlDoc) {
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
			if(xmlCells[0].textContent) rowID = URLDecode(xmlCells[0].textContent); //FF
			if(xmlCells[0].text) rowID = URLDecode(xmlCells[0].text); //IE
			if(xmlCells[0].innerHTML) rowID = URLDecode(xmlCells[0].innerHTML); //Opera
			
			// add separator
			if (xmlCells[0].getAttribute("marker")) {
				addSeparatorRow(dgData);
			}
			
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
				// get cell className
				cell.className = xmlElement.getAttribute("class");
				// get cell inner content
				if (xmlElement.textContent) cell.innerHTML = xmlElement.textContent; // FF
				if (xmlElement.text) cell.innerHTML = xmlElement.text; //IE
				if (xmlElement.innerHTML) cell.innerHTML = xmlElement.innerHTML; //Opera
				
				// add image
				if (xmlElement.getAttribute("img")) {
					cell.innerHTML = "<img src='"+badgerRoot+"/"+xmlElement.getAttribute("img")+"' title='"+xmlElement.getAttribute("title")+"' />&nbsp;";
				}
				// decode content
				cell.innerHTML = URLDecode(cell.innerHTML) + "&nbsp;";				
				// add cell
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
		// refresh row count
		$("dgCount").innerHTML = xmlRows.length;
		
		// hide loading message
		messageLayer('hide');
		
		// display processed data
		$('dgTableData').style.visibility = "visible";
	} else {
		$("dgCount").innerHTML = "0";
		messageLayer('show', '<span class="dgMessageError"> '+objXHR.responseText+' </span>');
		dgDeleteAllFilter();
	}
	// hide loading image
	$('dgDivScroll').className = "";
}

function addSeparatorRow(dgData) {
	newRow = document.createElement("tr");
	newRow.id = "separator";
	newRow.className = "dgRowSeparator";	

	checkTD = document.createElement("td");
	checkTD.style.width = "25px";
	checkTD.style.height = "5px";
	newRow.appendChild(checkTD);	

	for (i=0; i<dgColumnOrder.length; i++) {
		cell = document.createElement("td");
		cell.style.width = dgHeaderSize[i] + "px";
		cell.style.height = "5px"; //overwrite css style
		newRow.appendChild(cell);						
	}
	dgData.appendChild(newRow);
	
	lastTD = document.createElement("td");
	lastTD.style.height = "5px";
	newRow.appendChild(lastTD);
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
			loadData();
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


// change sort order and hide/show sort images
function addNewSortOrder(strSortColumn, strDirection) {
	// reset old sorting image
	if(strSortingColumnActive) changeColumnSortImage(strSortingColumnActive, "empty");
		
	if(strSortColumn==arrURLParameter["ok0"]) {
		// click on the same column:  change sort direction
		if (arrURLParameter["od0"]=="a") {
			// asc -> desc
			arrURLParameter["od0"]="d";
			changeColumnSortImage(strSortColumn, "d");
		} else {
			// desc -> asc
			arrURLParameter["od0"]="a";
			changeColumnSortImage(strSortColumn, "a");
		}
	} else {
		// click on a different column
		arrURLParameter["ok2"] = arrURLParameter["ok1"];
		arrURLParameter["od2"] = arrURLParameter["od1"];
		arrURLParameter["ok1"] = arrURLParameter["ok0"];
		arrURLParameter["od1"] = arrURLParameter["od0"];
		arrURLParameter["ok0"] = strSortColumn;
		if(strDirection!="d") {
			arrURLParameter["od0"] = "a";
			changeColumnSortImage(strSortColumn, "a");
		} else {
			arrURLParameter["od0"] = "d";
			changeColumnSortImage(strSortColumn, "d");
		}
	}
	strSortingColumnActive = strSortColumn;
	
	saveDataGridParameter();		
}

function initSortOrder() {
	if(arrURLParameter["ok0"]!=undefined && arrURLParameter["ok0"]!="") {
		strSortingColumnActive = arrURLParameter["ok0"];
		if(arrURLParameter["od0"]=="a") {
			changeColumnSortImage(arrURLParameter["ok0"], "a");
		} else {
			changeColumnSortImage(arrURLParameter["ok0"], "d");
		}
	}
}

//change the image for sorting direction
function changeColumnSortImage(id, newstatus) {
	switch(newstatus) {
		case 'empty':
			$("dgImg"+id).src = dgTplPath + "dropEmpty.gif";
			break;
		case 'a':
			$("dgImg"+id).src = dgTplPath + "dropDown.png";
			break;
		case 'd':
			$("dgImg"+id).src = dgTplPath + "dropUp.png";
			break;
	}	
}

function serializeParameter() {
	var strURLParameter = $H(arrURLParameter).toQueryString();
	// reinitialize array without undefinded parameters
	arrURLParameter = new Array();
	deserializeParameter(strURLParameter);
	strURLParameter = $H(arrURLParameter).toQueryString();
	return strURLParameter;
}

function deserializeParameter(strParameter) {
	lines = strParameter.split("&");

	for(i=0; i<lines.length;i++) {
		parpair = lines[i].split("=");
		if(parpair[0]!=undefined && parpair[1]!="undefined" && parpair[1]!="" ) {
			arrURLParameter[parpair[0]] = parpair[1];
		}
	}
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

function saveDataGridParameter() {	
	var strUrl = badgerRoot+"/core/widgets/DataGridSaveParameter.php";
	var strParameter = serializeParameter();
	
	if( strParameter.indexOf("id="+dgUniqueId)==-1 ) {
		strParameter = "id="+dgUniqueId+"&"+strParameter;
	}	
	
	var myAjax = new Ajax.Request(
	strUrl, {
		method: 'post',
		parameters: strParameter
	}); 
}

function dgSetFilterFields(arrayOfFields) {
	dgDeleteAllFilter();
	if(arrayOfFields){
		for (i=0; i<arrayOfFields.length; i++) {
			if( $(arrayOfFields[i]) ) {
				if( $F(arrayOfFields[i]) != "" && $F(arrayOfFields[i])!="NULL" ) {
					strKey = arrayOfFields[i];
					strValue = $F(arrayOfFields[i]);
					if( $(arrayOfFields[i]+"Filter") ) {
						strOperator = $F(arrayOfFields[i]+"Filter");
					} else {					
						strOperator = "eq";
					}
					dgAddFilter(strKey, strOperator, strValue);
				}
			}		
		}
	}
	loadData();
	saveDataGridParameter();
}

function dgResetFilter(arrayOfFields) {
	dgDeleteAllFilter();
	saveDataGridParameter();	
	loadData();
	initFilterFields(arrayOfFields);	
}

function dgAddFilter(strKey, strOperator, strValue) {	
	arrURLParameter["fk"+arrURLParameter["fn"]] = strKey;
	arrURLParameter["fo"+arrURLParameter["fn"]] = strOperator;
	arrURLParameter["fv"+arrURLParameter["fn"]] = strValue;
	arrURLParameter["fn"]++;
}

function dgDeleteAllFilter() {
	for (i=0; i<arrURLParameter["fn"]; i++) {
		arrURLParameter["fk"+i] = undefined;
		arrURLParameter["fo"+i] = undefined;
		arrURLParameter["fv"+i] = undefined;
	}
	arrURLParameter.compact();
	arrURLParameter["fn"] = 0;
}

function initFilterFields(arrayOfFields) {
	for (i=0; i<arrURLParameter["fn"]; i++) {
		if($(arrURLParameter["fk"+i])) $(arrURLParameter["fk"+i]).value = arrURLParameter["fv"+i];
		if($(arrURLParameter["fk"+i]+"Filter")) {
			$(arrURLParameter["fk"+i]+"Filter").value = arrURLParameter["fo"+i];
		}
	}
	if(arrayOfFields) {
		for (i=0; i<arrayOfFields.length; i++) {
			if( $(arrayOfFields[i]) ) {
				$(arrayOfFields[i]).value = "";
			}
		}
	}
}

function URLDecode(strEncodeString) {
  // Create a regular expression to search all +s in the string
  var lsRegExp = /\+/g;
  // Return the decoded string
  return unescape(strEncodeString.replace(lsRegExp, " "));
}
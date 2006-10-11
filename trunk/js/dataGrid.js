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

DataGrid = Class.create();

DataGrid.prototype = {
	initialize: function(arrParameters) {
		//variables
		this.uniqueId = arrParameters.uniqueId;
		this.htmlDiv = arrParameters.htmlDiv;
		this.sourceXML = arrParameters.sourceXML;
		this.headerName = arrParameters.headerName;
		this.columnOrder = arrParameters.columnOrder;
		this.headerSize = arrParameters.headerSize;
		this.cellAlign = arrParameters.cellAlign;
		this.noRowSelectedMsg = arrParameters.noRowSelectedMsg;
		this.deleteMsg = arrParameters.deleteMsg;
		this.deleteRefreshType = arrParameters.deleteRefreshType;
		this.deleteAction = arrParameters.deleteAction;
		this.editAction = arrParameters.editAction;
		this.newAction = arrParameters.newAction;
		this.tplPath = arrParameters.tplPath;
		this.loadingMessage = arrParameters.loadingMessage;
	
		this.mouseEventsDisabled = false;
		
		this.objRowActive;
		this.strSortingColumnActive = "";
		this.arrSelectedRows = new Array();	
		this.arrURLParameter = new Array();
		
		// if there are some stored values in the usersettings
		if(arrParameters.parameter) {
			//TODO: Load Parameter via AJAX
			this.deserializeParameter(arrParameters.parameter);
			this.initSortOrder();
			this.initFilterFields();
		}
		this.loadData();
	},
	
	// retrieve data from server, define callback-function
	loadData: function() {
		// get selected rows, so that we can restore selection after reloading
		this.arrSelectedRows = this.getAllIds();
		
		// load data
		this.myAjax = new Ajax.Request(
			this.sourceXML, {
				method: 'post',
				parameters: this.serializeParameter() + "&sf=" + this.columnOrder,
				onComplete: this.insertData.bind(this),
				onFailure: this.handleError.bind(this)
			}); 
	
		// show loading message, image, hide old data
		this.showMessageLayer('<span class="dgMessageHint"> '+this.loadingMessage+' </span>');
		$('dgDivScroll'+this.uniqueId).className = "dgDivScrollLoading";
		$('dgTableData'+this.uniqueId).style.visibility = "hidden"; 

		// filter image in footer
		if( this.arrURLParameter["fn"]>"0" && this.arrURLParameter["fn"]!=undefined) {
			$('dgFilterStatus'+this.uniqueId).style.visibility = "visible"; //filter active
		} else {
			$('dgFilterStatus'+this.uniqueId).style.visibility = "hidden"; //filter inactive
		}	
	},
	// displays the message from backend-object
	handleDeleteResponse: function(objXHR) {
		var allSelectedIds;
		if (objXHR.responseText=="") {
			switch (this.deleteRefreshType) {
			case 'refreshDataGrid': 
				//refresh whole dataGrid				
				this.loadData();
				break;
			case 'refreshPage': 
				//refresh whole page	
				window.setTimeout("this.refreshPage()", 10);
				break;
			default: 
				// no refresh, delete rows in frontend
				allSelectedIds = this.getAllIds();		    
				for (i=0; i<allSelectedIds.length; i++) {
					Element.remove($(allSelectedIds[i]));
					NumberOfRows = $("dgCount"+this.uniqueId).innerHTML;
					NumberOfRows--;
					$("dgCount"+this.uniqueId).innerHTML = NumberOfRows;
				}
			} //switch	
		} else {
			this.showMessageLayer('<span class="dgMessageError"> '+objXHR.responseText+' </span>');
		}
	},
	//XHR Error
	handleError: function() {
		this.showMessageLayer('<span class="dgMessageError"> XHR Error </span>');
	},
	
	// fill the datagrid with values
	insertData: function(objXHR) {
		//alert(this)
		//alert(objXHR)
		objXmlDoc = objXHR.responseXML;
		strID = this.uniqueId;
		
		if(objXmlDoc) {
			
			xmlColumns = objXmlDoc.getElementsByTagName("column");
			xmlRows = objXmlDoc.getElementsByTagName("row");

			//delete old table body if exists
			if($("dgTableData"+strID).getElementsByTagName("tbody")[0]) {
				Element.remove($("dgTableData"+strID).getElementsByTagName("tbody")[0])	
			}
			//create new table body
			dgTableDataBody = document.createElement("tbody");
			dgData = $("dgTableData"+strID).appendChild(dgTableDataBody);
			
			//column assignment
			//e.g. columnPosition['title'] is the first column in the xml-file;
			var columnPosition = new Array();
			//alert("xmlColumns.length: " + xmlColumns.length);
			for (intPosition=0; intPosition<xmlColumns.length; intPosition++) {
				if(xmlColumns[intPosition].textContent) columnName = xmlColumns[intPosition].textContent; //FF
				if(xmlColumns[intPosition].text) columnName = xmlColumns[intPosition].text; //IE
				if(xmlColumns[intPosition].innerHTML) columnName = xmlColumns[intPosition].innerHTML; //Opera
				columnPosition[columnName] = intPosition;		
			}
			
			//alert("xmlRows.length: " + xmlRows.length);
			for (j=0; j<xmlRows.length; j++) {				
				xmlCells = xmlRows[j].getElementsByTagName("cell");				
				
				//first cell of a row, is always a unique ID
				if(xmlCells[0].textContent) rowID =	URLDecode(xmlCells[0].textContent); //FF
				if(xmlCells[0].text) rowID = URLDecode(xmlCells[0].text); //IE
				if(xmlCells[0].innerHTML) rowID = URLDecode(xmlCells[0].innerHTML); //Opera
				
				// add separator
				if (xmlCells[0].getAttribute("marker")) {
					this.addSeparatorRow(this, dgData);
				}
				
				//define a new row
				newRow = document.createElement("tr");
				newRow.className = "dgRow";
				newRow.id = strID+rowID;
				newRow.rowId = rowID;
				
				//add checkbox as the first cell
				checkTD = document.createElement("td");
				checkTD.style.width = "25px";
				checkBox = document.createElement("input");
				checkBox.id = "check"+strID+rowID;
				checkBox.name = "check"+strID+rowID;
				checkBox.type = "checkbox";
				checkTD.appendChild(checkBox);
				checkTD.innerHTML = checkTD.innerHTML + "&nbsp;";
				newRow.appendChild(checkTD);

				//insert cell values
				// dgColumnOrder[0] -> 'balance' : name of the column
				// columnPosition['balance'] -> '1' : first column
				// cells[1].text{Content} -> '899.23' : value				
				for (i=0; i<this.columnOrder.length; i++) {
					cell = document.createElement("td");
					cell.style.width = this.headerSize[i] + "px";
					cell.align = this.cellAlign[i];

					xmlElement = xmlCells[columnPosition[this.columnOrder[i]]];
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
			for (i=0; i<this.arrSelectedRows.length; i++) {
				if($(this.arrSelectedRows[i])) {
					this.selectRow($(strID+this.arrSelectedRows[i]));
				}
			}		
			// refresh row count
			$("dgCount"+strID).innerHTML = xmlRows.length;
			
			// hide loading message
			this.hideMessageLayer();
			
			// display processed data
			$('dgTableData'+strID).style.visibility = "visible";
		} else {
			//alert(strID)
			$("dgCount"+strID).innerHTML = "0";
			this.showMessageLayer('<span class="dgMessageError"> '+objXHR.responseText+' </span>');
			this.deleteAllFilter();
		}

		// hide loading image
		$('dgDivScroll'+strID).className = "dgDivScroll";
	},
	
	addSeparatorRow: function(dataGrid, dgData) {		
		newRow = document.createElement("tr");
		newRow.id = this.dataGrid.uniqueId+"separator";
		newRow.className = "dgRowSeparator";	
	
		checkTD = document.createElement("td");
		checkTD.style.width = "25px";
		checkTD.style.height = "5px";
		newRow.appendChild(checkTD);	

		for (i=0; i<dataGrid.columnOrder.length; i++) {
			cell = document.createElement("td");
			cell.style.width = dataGrid.headerSize[i] + "px";
			cell.style.height = "5px"; //overwrite css style
			newRow.appendChild(cell);						
		}
		dgData.appendChild(newRow);
		
		lastTD = document.createElement("td");
		lastTD.style.height = "5px";
		newRow.appendChild(lastTD);
	},

	// Row Handling
	activateRow: function (objRow) {
		if (objRow.className == "dgRow") objRow.className = "dgRowActive";
		if (objRow.className == "dgRowSelected") objRow.className = "dgRowSelectedActive";
		$("check"+objRow.id).focus();
	},
	deactivateRow: function (objRow) {
		if (objRow.className == "dgRowActive") objRow.className = "dgRow";
		if (objRow.className == "dgRowSelectedActive") objRow.className = "dgRowSelected";
	},
	selectRow: function (objRow) {
		if (objRow.className == "dgRow") objRow.className = "dgRowSelected";
		if (objRow.className == "dgRowActive") objRow.className = "dgRowSelectedActive";
		$("check"+objRow.id).checked = "checked";
		$("check"+objRow.id).focus();
	},
	deselectRow: function (objRow) {
		if (objRow.className == "dgRowSelected") objRow.className = "dgRow";
		if (objRow.className == "dgRowSelectedActive") objRow.className = "dgRowActive";
		$("check"+objRow.id).checked = "";
		$("check"+objRow.id).focus();
	},
	
	enableMouseEvents: function () {
		this.mouseEventsDisabled = false;
	},	
	refreshPage: function  () {
		location.href = location.href;
	},

	//TODO: PROBLEM
	//Key-Events of the Rows
	KeyEvents: function (event) {
		if (!event) event=window.event;
		
		//KEY_DOWN
		if (event.keyCode == Event.KEY_DOWN) {
			Event.stop(event);
			
			//when dataGrid scrolls down, disable mouse events
			dataGrid.mouseEventsDisabled = true;
			window.setTimeout("dataGrid.enableMouseEvents()", 10);
			
			if (dataGrid.objRowActive) {
				objNextRow = dataGrid.objRowActive.nextSibling;
				if (objNextRow) {
					if(objNextRow.tagName!="TR") objNextRow = objNextRow.nextSibling; //only FF, difference in the DOM
					dataGrid.deactivateRow(dataGrid.objRowActive);
					dataGrid.activateRow(objNextRow);
					dataGrid.objRowActive = objNextRow;
				}
			} else {
				dataGrid.objRowActive = $("dgTableData"+dataGrid.uniqueId).getElementsByTagName("tr")[0];
				dataGrid.activateRow(dataGrid.objRowActive);
			}			
		}
		
		//KEY_UP
		if (event.keyCode == Event.KEY_UP) {
			Event.stop(event);
			
			//when dataGrid scrolls down, disable mouse events
			dataGrid.mouseEventsDisabled = true;
			window.setTimeout("dataGrid.enableMouseEvents()", 10);
			
			if (dataGrid.objRowActive) {
				objNextRow = dataGrid.objRowActive.previousSibling;
				if (objNextRow) {
					if(objNextRow.tagName!="TR") objNextRow = objNextRow.previousSibling; //only FF, difference in the DOM
					if(objNextRow) {
						dataGrid.deactivateRow(dataGrid.objRowActive);
						dataGrid.activateRow(objNextRow);
						dataGrid.objRowActive = objNextRow;
					}
				}
			}
		}
		//KEY_RETURN
		if (event.keyCode == Event.KEY_RETURN) {
			dataGrid.callEditEvent();
		}
		//KEY_DELETE
		if (event.keyCode == Event.KEY_DELETE) {
			dataGrid.callDeleteEvent();
		}
		//KEY_SPACE (only for opera)
		if (event.keyCode == 32) {
			if(dataGrid.objRowActive.className=="dgRowSelected" || dataGrid.objRowActive.className=="dgRowSelectedActive") {
				dataGrid.deselectRow(dataGrid.objRowActive);
			} else {
				dataGrid.selectRow(dataGrid.objRowActive);
			}	
		}
	},
	
	// delete all selected rows
	//  - delete row in GUI
	//  - send a background delete request to the server
	deleteRows: function () {
		if(this.deleteAction) {		
			dgData = $("dgTableData"+this.uniqueId);	
			checkbox = Form.getInputs("dgForm"+this.uniqueId,"checkbox");
			
			allSelectedIds = this.getAllIds();	// get selected row ids
			
			//asks use, if he is sure
			choise = confirm(this.deleteMsg +"("+allSelectedIds.length+")");
			if (choise) {
				// delete data in background
				this.callDeleteEvent(this.deleteAction + allSelectedIds);			
			} //if (choise)
		} //if (dgDeleteAction)
	},

	// call site to add a new record
	callNewEvent: function (addParam) {
		if(!addParam) addParam = "";
		if(this.newAction) {
			document.location.href = this.newAction + "&" + addParam;
		}
	},

	// delete data
	callDeleteEvent: function(strUrl) {
		var myAjax = new Ajax.Request(
			strUrl, {
				method: 'get',
				onComplete: this.handleDeleteResponse.bind(this),
				onFailure: this.handleError.bind(this)
				});
	},
	
	// call site to  edit record with ID in a special page
	callEditEvent: function (id) {
		if(this.editAction) {
			if(!id) id = this.getFirstId(); //if called by button, get first ID
			if(id) {
				document.location.href = this.editAction + id;
			} else {
				alert (this.noRowSelectedMsg);
			}
		}
	},
	
	// get all ids from selected rows -> array
	getAllIds: function () {
		checkbox = Form.getInputs("dgForm"+this.uniqueId,"checkbox");
		var allIDs = new Array;
		for (i=0; i<checkbox.length; i++) {
			if(checkbox[i].id.indexOf("check") != -1 ) {
				if ($F(checkbox[i]) == "on") allIDs.push(checkbox[i].parentNode.parentNode.rowId);
			}
		}
		return allIDs;
	},
	
	// get all ids from selected rows -> array
	getFirstId: function () {
		checkbox = Form.getInputs("dgForm"+this.uniqueId, "checkbox");
	
		for (i=0; i<checkbox.length; i++) {
			if(checkbox[i].id.indexOf("check") != -1 ) {
				if ($F(checkbox[i]) == "on") return checkbox[i].parentNode.parentNode.rowId;
			}
		}
	},
		
	// change sort order and hide/show sort images
	addNewSortOrder: function (strSortColumn, strDirection) {
		// reset old sorting image
		if(this.strSortingColumnActive) this.changeColumnSortImage(this.strSortingColumnActive, "empty");
			
		if(strSortColumn==this.arrURLParameter["ok0"]) {
			// click on the same column:  change sort direction
			if (this.arrURLParameter["od0"]=="a") {
				// asc -> desc
				this.arrURLParameter["od0"]="d";
				this.changeColumnSortImage(strSortColumn, "d");
			} else {
				// desc -> asc
				this.arrURLParameter["od0"]="a";
				this.changeColumnSortImage(strSortColumn, "a");
			}
		} else {
			// click on a different column
			this.arrURLParameter["ok2"] = this.arrURLParameter["ok1"];
			this.arrURLParameter["od2"] = this.arrURLParameter["od1"];
			this.arrURLParameter["ok1"] = this.arrURLParameter["ok0"];
			this.arrURLParameter["od1"] = this.arrURLParameter["od0"];
			this.arrURLParameter["ok0"] = strSortColumn;
			if(strDirection!="d") {
				this.arrURLParameter["od0"] = "a";
				this.changeColumnSortImage(strSortColumn, "a");
			} else {
				this.arrURLParameter["od0"] = "d";
				this.changeColumnSortImage(strSortColumn, "d");
			}
		}
		this.strSortingColumnActive = strSortColumn;
		
		this.saveDataGridParameter();		
	},
	
	initSortOrder: function () {
		if(this.arrURLParameter["ok0"]!=undefined && this.arrURLParameter["ok0"]!="") {
			this.strSortingColumnActive = this.arrURLParameter["ok0"];
			if(this.arrURLParameter["od0"]=="a") {
				this.changeColumnSortImage(this.arrURLParameter["ok0"], "a");
			} else {
				this.changeColumnSortImage(this.arrURLParameter["ok0"], "d");
			}
		}
	},
	
	//change the image for sorting direction
	changeColumnSortImage: function (id, newstatus) {
		switch(newstatus) {
			case 'empty':
				$("dgImg"+this.uniqueId+id).src = this.tplPath + "dropEmpty.gif";
				break;
			case 'a':
				$("dgImg"+this.uniqueId+id).src = this.tplPath + "dropDown.png";
				break;
			case 'd':
				$("dgImg"+this.uniqueId+id).src = this.tplPath + "dropUp.png";
				break;
		}	
	},
	
	serializeParameter: function () {
		var strURLParameter = $H(this.arrURLParameter).toQueryString();
		// reinitialize array without undefinded parameters
		this.arrURLParameter = new Array();
		this.deserializeParameter(strURLParameter);
		strURLParameter = $H(this.arrURLParameter).toQueryString();
		return strURLParameter;
	},
	
	deserializeParameter: function (strParameter) {
		lines = strParameter.split("&");
	
		for(i=0; i<lines.length;i++) {
			parpair = lines[i].split("=");
			if(parpair[0]!=undefined && parpair[1]!="undefined" && parpair[1]!="" ) {
				this.arrURLParameter[parpair[0]] = parpair[1];
			}
		}
	},
	
	//display a message in the dataGrid footer
	showMessageLayer: function (strMessage) {
		divMessage = $("dgMessage"+this.uniqueId);
		divMessage.style.display = "inline";
		divMessage.innerHTML = strMessage;
	},
	hideMessageLayer: function() {
		divMessage = $("dgMessage"+this.uniqueId);
		divMessage.style.display = "none";
	},
	
	//preselect an entry
	preselectId: function (id) {
		arrSelectedRows.push(id);
	},
	
	saveDataGridParameter: function () {	
		var strUrl = badgerRoot+"/core/widgets/DataGridSaveParameter.php";
		var strParameter = this.serializeParameter();
		
		if( strParameter.indexOf("id="+this.uniqueId)==-1 ) {
			strParameter = "id="+this.uniqueId+"&"+strParameter;
		}	
		
		var myAjax = new Ajax.Request(
		strUrl, {
			method: 'post',
			parameters: strParameter
		}); 
	},
	
	setFilterFields: function (arrayOfFields) {
		this.deleteAllFilter();
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
						this.addFilter(strKey, strOperator, strValue);
					}
				}		
			}
		}
		this.loadData();
		this.saveDataGridParameter();
	},
	
	resetFilter: function (arrayOfFields) {
		this.deleteAllFilter();
		this.saveDataGridParameter();	
		this.loadData();
		this.initFilterFields(arrayOfFields);	
	},
	
	addFilter: function (strKey, strOperator, strValue) {	
		this.arrURLParameter["fk"+this.arrURLParameter["fn"]] = strKey;
		this.arrURLParameter["fo"+this.arrURLParameter["fn"]] = strOperator;
		this.arrURLParameter["fv"+this.arrURLParameter["fn"]] = strValue;
		this.arrURLParameter["fn"]++;
	},
	
	deleteAllFilter: function () {
		for (i=0; i<this.arrURLParameter["fn"]; i++) {
			this.arrURLParameter["fk"+i] = undefined;
			this.arrURLParameter["fo"+i] = undefined;
			this.arrURLParameter["fv"+i] = undefined;
		}
		this.arrURLParameter.compact();
		this.arrURLParameter["fn"] = 0;
	},
	
	initFilterFields: function (arrayOfFields) {
		for (i=0; i<this.arrURLParameter["fn"]; i++) {
			if($(this.arrURLParameter["fk"+i])) $(this.arrURLParameter["fk"+i]).value = this.arrURLParameter["fv"+i];
			if($(this.arrURLParameter["fk"+i]+"Filter")) {
				$(this.arrURLParameter["fk"+i]+"Filter").value = this.arrURLParameter["fo"+i];
			}
		}
		if(arrayOfFields) {
			for (i=0; i<arrayOfFields.length; i++) {
				if( $(arrayOfFields[i]) ) {
					$(arrayOfFields[i]).value = "";
				}
			}
		}
	},

	//TODO: PROBLEM
	//Mouse-Events
	behaviour:  {
		//Mouse-Events of the rows (selecting, activating)
		'tr.dgRow' : function(element){
			element.onmouseover = function(){
				dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				if (!dataGrid.mouseEventsDisabled) {
					if(dataGrid.objRowActive) dataGrid.deactivateRow(dataGrid.objRowActive);
					dataGrid.objRowActive = this;
					dataGrid.activateRow(this);
				}
			}
			element.onmouseout = function(){
				if (!dataGrid.mouseEventsDisabled) dataGrid.deactivateRow(this);
			}
			element.onclick = function(){
				dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				if(this.className=="dgRowSelected" || this.className=="dgRowSelectedActive") {
					dataGrid.deselectRow(this);
				} else {
					dataGrid.selectRow(this);
				}
			}
			element.ondblclick = function(){
				dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				dataGrid.callEditEvent(this.rowId);
			}
		},
		//Mouse-Events of the columns (sorting)
		'td.dgColumn' : function(element){
			element.onclick = function(){
				dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				id = this.id.replace("dgColumn"+dataGrid.uniqueId,"");
				dataGrid.addNewSortOrder(id);
				dataGrid.loadData();
			}
		},
		// checkbox in the dataGrid-Header, for (de-)selecting all
		'#dgSelector' : function(element){
			element.onclick = function(){
				dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				checkbox = Form.getInputs("dgForm"+dataGrid.uniqueId,"checkbox");
				if($F(this)=="on") {
					//select all checkboxes		
					for (i=0; i<checkbox.length; i++) {
						if(checkbox[i].id!="dgSelector"+dataGrid.uniqueId) {
							dataGrid.selectRow(checkbox[i].parentNode.parentNode);
						}
					}
				} else {
					//deselect all checkboxes	
					for (i=0; i<checkbox.length; i++) {
						if(checkbox[i].id!="dgSelector"+dataGrid.uniqueId) {
							dataGrid.deselectRow(checkbox[i].parentNode.parentNode);
						}
					}
				} //if($F(this)=="on")
				this.focus();
			} //element.onclick 
		}
	}

}

function URLDecode(strEncodeString) {
	// Create a regular expression to search all +s in the string
	var lsRegExp = /\+/g;
	// Return the decoded string	  
	return unescape(strEncodeString.replace(lsRegExp, " "));
}
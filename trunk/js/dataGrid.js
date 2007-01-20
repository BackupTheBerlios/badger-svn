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

pageSettings = new PageSettings();

DataGrid = Class.create();
DataGrid.SortOrder = Class.create();
DataGrid.Filter = Class.create();

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
		this.discardSelectedRows = arrParameters.discardSelectedRows;
		this.loadingMessage = arrParameters.loadingMessage;
		
	
		this.mouseEventsDisabled = false;
		
		this.objRowActive;
		this.arrSelectedRows = new Array();	
		this.sortParameter = new Object();
		
		//initialize Sort and Filter Parameter
		this.sortOrder = new DataGrid.SortOrder(this);		
		this.filter = new DataGrid.Filter(this);
		this.filter.initFilterFields();
		
		this.loadData();
		
		//when we save selected rows at page refresh, then we should restore it here
		if (!this.discardSelectedRows) {
			this.restoreSelectedRows();
		}		
	},
	
	// retrieve data from server, define callback-function
	loadData: function() {
		// load data
		if (this.myAjaxLoad) {
			this.myAjaxLoad.transport.abort();
		};
		this.myAjaxLoad = new Ajax.Request(
			this.sourceXML, {
				method: 'post',
				parameters: "&sf=" + this.columnOrder + "&" +this.sortOrder.toQueryString() + "&" +this.filter.toQueryString(),
				onComplete: this.insertData.bind(this),
				onFailure: this.handleError.bind(this)
			}); 
	
		// show loading message, image, hide old data
		this.showMessageLayer('<span class="dgMessageHint"> '+this.loadingMessage+' </span>');
		$('dgDivScroll'+this.uniqueId).className = "dgDivScrollLoading";
		$('dgTableData'+this.uniqueId).style.visibility = "hidden"; 

		// filter image in footer
		if( this.filter.getNumberOfActiveFilters() >0 ) {
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
				allSelectedIds = this.getAllSelectedIds();		    
				for (i=0; i<allSelectedIds.length; i++) {
					Element.remove($(this.uniqueId + allSelectedIds[i]));
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
		objXmlDoc = objXHR.responseXML;
		
		if(objXmlDoc) {			
			xmlColumns = objXmlDoc.getElementsByTagName("column");
			xmlRows = objXmlDoc.getElementsByTagName("row");

			//delete old table body if exists
			if($("dgTableData"+this.uniqueId).getElementsByTagName("tbody")[0]) {
				Element.remove($("dgTableData"+this.uniqueId).getElementsByTagName("tbody")[0])	
			}
			//create new table body
			tableDataBody = document.createElement("tbody");
			$("dgTableData"+this.uniqueId).appendChild(tableDataBody);
			
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
				//alert(j + "/"+ xmlRows.length);			
				xmlCells = xmlRows[j].getElementsByTagName("cell");				
				
				//first cell of a row, is always a unique ID
				if(xmlCells[0].textContent) rowID =	URLDecode(xmlCells[0].textContent); //FF
				if(xmlCells[0].text) rowID = URLDecode(xmlCells[0].text); //IE
				if(xmlCells[0].innerHTML) rowID = URLDecode(xmlCells[0].innerHTML); //Opera
				
				// add separator
				if (xmlCells[0].getAttribute("marker")) {
					this.addSeparatorRow(this, tableDataBody);
				}
				
				//define a new row
				newRow = document.createElement("tr");
				newRow.className = "dgRow";
				newRow.id = this.uniqueId+rowID;
				newRow.rowId = rowID;
				
				//add checkbox as the first cell
				checkTD = document.createElement("td");
				checkTD.style.width = "25px";
				checkBox = document.createElement("input");
				checkBox.id = "check"+this.uniqueId+rowID;
				checkBox.name = "check"+this.uniqueId+rowID;
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
				lastTD.innerHTML = "&nbsp;"; //filling dummy cell
					
				//add complete row to the grid
				tableDataBody.appendChild(newRow);
			}
			//refresh JS-behaviours of the rows
			Behaviour.apply();

			//activate previous selected rows 
			for (i=0; i<this.arrSelectedRows.length; i++) {
				if($(this.uniqueId + this.arrSelectedRows[i])) {
					$(this.uniqueId + this.arrSelectedRows[i]).className = "dgRowSelected";
					$("check"+$(this.uniqueId + this.arrSelectedRows[i]).id).checked = "checked";
				}
			}
			
			// refresh row count
			$("dgCount"+this.uniqueId).innerHTML = xmlRows.length;
			
			// hide loading message
			this.hideMessageLayer();
			
			// display processed data
			$('dgTableData'+this.uniqueId).style.visibility = "visible";
		} else { //if(objXmlDoc)
			$("dgCount"+this.uniqueId).innerHTML = "0";
			this.showMessageLayer('<span class="dgMessageError"> '+objXHR.responseText+' </span>');
			//this.filter.reset();
		} // if(objXmlDoc)

		// hide loading image
		$('dgDivScroll'+this.uniqueId).className = "dgDivScroll";		
	},
	
	addSeparatorRow: function(dataGrid, tableDataBody) {
		var newRow = document.createElement("tr");
		newRow.id = dataGrid.uniqueId+"separator";
		newRow.className = "dgRowSeparator";	
	
		var firstCell = document.createElement("td");
		firstCell.style.width = "25px";
		firstCell.style.height = "5px";
		newRow.appendChild(firstCell);

		for (i=0; i<dataGrid.columnOrder.length; i++) {
			var cell = document.createElement("td");
			cell.style.width = dataGrid.headerSize[i] + "px";
			cell.style.height = "5px"; //overwrite css style
			newRow.appendChild(cell);						
		}
		tableDataBody.appendChild(newRow);
		
		var lastCell = document.createElement("td");
		lastCell.style.height = "5px";
		newRow.appendChild(lastCell);
	},
	gotoToday: function () {
		var separatorRow = $(this.uniqueId+"separator");
		var numberOfOffsetRows = 4;
		
		if (separatorRow) {
			var rowToFocus = separatorRow;
			for(i=0; i<numberOfOffsetRows;i++) {
				if(rowToFocus.nextSibling) {
					rowToFocus = rowToFocus.nextSibling;
				}
			}
			rowToFocus.firstChild.childNodes[0].focus();
		} else {
			// focus last checkbox
			var tableDataBody = $("dgTableData"+this.uniqueId);
			var tableRows = tableDataBody.getElementsByTagName("tr");
			
			if (tableRows) {
				if (tableRows.length > 4) {
					var lastRow = tableRows[tableRows.length-1];
					lastRow.firstChild.childNodes[0].focus();
				}				
			}
		}
	},
	// Row Handling
	//Activation -> Highlight, when mouse over
	activateRow: function (objRow) {
		if (objRow.className == "dgRow") objRow.className = "dgRowActive";
		if (objRow.className == "dgRowSelected") objRow.className = "dgRowSelectedActive";
		$("check"+objRow.id).focus();
	},
	deactivateRow: function (objRow) {
		if (objRow.className == "dgRowActive") objRow.className = "dgRow";
		if (objRow.className == "dgRowSelectedActive") objRow.className = "dgRowSelected";
	},
	//Selection -> enable checkbox
	selectRow: function (objRow) {
		this.arrSelectedRows.push(objRow.rowId);
		//save selected rows
		if (!this.discardSelectedRows) {
			this.saveSelectedRows();
		}
		if (objRow.className == "dgRow") objRow.className = "dgRowSelected";
		if (objRow.className == "dgRowActive") objRow.className = "dgRowSelectedActive";
		$("check"+objRow.id).checked = "checked";
		$("check"+objRow.id).focus();

	},
	deselectRow: function (objRow) {
		//remove row id from array
		position = this.arrSelectedRows.indexOf(objRow.rowId);
		if(position>=0) {
			this.arrSelectedRows[position] = null;
			this.arrSelectedRows = this.arrSelectedRows.compact();
			//save selected rows
			if (!this.discardSelectedRows) {
				this.saveSelectedRows();
			}
		}
		
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
	//  - send a background delete request to the server
	callDeleteEvent: function () {
		if(this.deleteAction) {		
			// get selected row ids
			allSelectedIds = this.getAllSelectedIds();	
			
			//asks use, if he is sure
			choise = confirm(this.deleteMsg +"("+allSelectedIds.length+")");
			if (choise) {
				// delete data in background
				this.deleteTheseRows(this.deleteAction + allSelectedIds);			
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
	deleteTheseRows: function(strUrl) {
		var myAjaxDelete = new Ajax.Request(
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
	
	saveSelectedRows: function(event) {
		pageSettings.setSettingSer("DataGrid"+this.uniqueId, "arrSelectedRows", this.arrSelectedRows);
	},
	restoreSelectedRows: function() {
		eval("this.restoreSelectedRowsCallback(" + pageSettings.getSettingSync("DataGrid"+this.uniqueId, "arrSelectedRows") + ")");		
	},
	restoreSelectedRowsCallback: function (objResult) {
		if(objResult) {
			this.arrSelectedRows = objResult;
		}
	},
			
	deselectAllRows: function () {
		allCheckboxes = Form.getInputs("dgForm"+this.uniqueId, "checkbox");
		for (i=0; i<allCheckboxes.length; i++) {
			if(allCheckboxes[i].id!="dgSelector"+this.uniqueId) {
				this.deselectRow(allCheckboxes[i].parentNode.parentNode);
			}
		}		
	},
	
	// get all ids from selected rows -> array
	getAllSelectedIds: function () {
		var selectedIDs = new Array;
		
		var allCheckboxes = Form.getInputs("dgForm"+this.uniqueId,"checkbox");		
		for (i=0; i < allCheckboxes.length; i++) {
			if(allCheckboxes[i].id.indexOf("check") != -1 ) {
				if ($F(allCheckboxes[i]) == "on") selectedIDs.push(allCheckboxes[i].parentNode.parentNode.rowId);
			}
		}
		return selectedIDs;
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

	//display a message in the dataGrid footer
	showMessageLayer: function (strMessage) {
		var divMessage = $("dgMessage"+this.uniqueId);
		divMessage.style.display = "inline";
		divMessage.innerHTML = strMessage;
	},
	hideMessageLayer: function() {
		var divMessage = $("dgMessage"+this.uniqueId);
		divMessage.style.display = "none";
	},
	
	//preselect an entry
	preselectId: function (id) {
		this.arrSelectedRows.push(id);
		
		var row = $(this.uniqueId + id);
		if (row) {
			this.selectRow(row);
		}
	},
	getFirstColumnName: function () {
		return this.columnOrder[0];		
	},

	//Mouse-Events
	behaviour:  {
		//Mouse-Events of the rows (selecting, activating)
		'tr.dgRow' : function(element){
			element.onmouseover = function(){
				var dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
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
				var dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				if(this.className=="dgRowSelected" || this.className=="dgRowSelectedActive") {
					dataGrid.deselectRow(this);
				} else {
					dataGrid.selectRow(this);
				}
			}
			element.ondblclick = function(){
				var dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				dataGrid.callEditEvent(this.rowId);
			}
		},
		//Mouse-Events of the columns (sorting)
		'td.dgColumn' : function(element){
			element.onclick = function(){
				var dataGrid = this.parentNode.parentNode.parentNode.parentNode.obj;
				id = this.id.replace("dgColumn"+dataGrid.uniqueId,"");
				dataGrid.sortOrder.addNewSortOrder(id);
				dataGrid.loadData();
			}
		},
		
		// checkbox in the dataGrid-Header, for (de-)selecting all
		'input.dgSelector' : function(element){
			element.onclick = function(){
				var dataGrid = this.parentNode.parentNode.parentNode.parentNode.parentNode.obj;
				
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

DataGrid.SortOrder.prototype= {
	/*
	 * ok[0-2]: order key
	 * od[0-2]: order direction
	 * a: ascending
	 * d: descending
	 */
	sortOrder: new Object(),
	parent: new Object(),
	
	initialize: function(objDataGrid) {
		// remember handle to data grid object
		this.parent = objDataGrid;	
		
		// load data from page settings
		this.load();
		
		// initialise sort order
		if(this.sortOrder.ok0!=undefined && this.sortOrder.ok0!="") {
			// set sort order
			this.addNewSortOrder( this.sortOrder.ok0, this.sortOrder.od0);
		} else {
			// delete object
			this.sortOrder = new Object();
			// set default sorting, first column ascending
			this.addNewSortOrder( this.parent.getFirstColumnName(), "a" );
		}
		
		//return sort object
		return this;
	},
	toQueryString: function() {		
		var cleanedSortOrder = new Object();
		
		//clean up object, remove undefined attributes
		for (i in this.sortOrder) {
			if ( this.sortOrder[i] != undefined && i != "toJSONString") {
				cleanedSortOrder[i] = this.sortOrder[i];
			}
		}
		//Object to QueryString
		return $H(cleanedSortOrder).toQueryString();

	},
	
	addNewSortOrder: function(sortColumn, sortDirection) {
		// reset old sorting image
		if(this.activeSortColumn) this.changeColumnSortImage(this.activeSortColumn, "empty");
			
		if(sortColumn==this.sortOrder.ok0 & sortDirection==undefined) {
			// click on the same column:  change sort direction
			// no directions is specified when called by column click
			if (this.sortOrder.od0=="a") {
				// asc -> desc
				this.sortOrder.od0="d";
				this.changeColumnSortImage(sortColumn, "d");
			} else {
				// desc -> asc
				this.sortOrder.od0="a";
				this.changeColumnSortImage(sortColumn, "a");
			}
		} else {
			// click on a different column
			// or initialisation of sorting
			this.sortOrder.ok2 = this.sortOrder.ok1;
			this.sortOrder.od2 = this.sortOrder.od1;
			this.sortOrder.ok1 = this.sortOrder.ok0;
			this.sortOrder.od1 = this.sortOrder.od0;
			this.sortOrder.ok0 = sortColumn;
			if(sortDirection!="d") {
				this.sortOrder.od0 = "a";
				this.changeColumnSortImage(sortColumn, "a");
			} else {
				this.sortOrder.od0 = "d";
				this.changeColumnSortImage(sortColumn, "d");
			}
		}
		this.activeSortColumn = sortColumn;
		this.save();
	},
	save: function() {
		pageSettings.setSettingSer("DataGrid"+this.parent.uniqueId, "SortOrder", this.sortOrder);
	},
	load: function() {
		eval("this.processResult(" + pageSettings.getSettingSync("DataGrid"+this.parent.uniqueId, "SortOrder") + ")");
	},
	processResult: function(objResult) {
		this.sortOrder = objResult;
	},
	
	//change the image for sorting direction
	changeColumnSortImage: function (columnId, newSortOrder) {
		switch(newSortOrder) {
			case 'empty':
				$("dgImg"+this.parent.uniqueId+columnId).src = this.parent.tplPath + "dropEmpty.gif";
				break;
			case 'a':
				$("dgImg"+this.parent.uniqueId+columnId).src = this.parent.tplPath + "dropDown.png";
				break;
			case 'd':
				$("dgImg"+this.parent.uniqueId+columnId).src = this.parent.tplPath + "dropUp.png";
				break;
		}	
	}	
}


DataGrid.Filter.prototype = {
	activeFilter: new Object(),
	parent: new Object(),
	
	initialize: function(objDataGrid) {
		// remember handle to data grid object
		this.parent = objDataGrid;	
		
		this.activeFilter.arrCriterias = new Array();
		this.activeFilter.arrCriterias.length = 0;
		
		// load data from page settings
		this.load();
		
		//return filter object
		return this;
	},
	reset: function() {		
		this.activeFilter = new Object()
	},
	save: function(strFilterName) {
		if (strFilterName) {
			strFilterName += "Filter";
			//save a specific filter
			//???????
			pageSettings.setSettingSer("DataGrid"+this.parent.uniqueId, strFilterName, this.activeFilter);
		} else {
			//save last used filter for this grid
			pageSettings.setSettingSer("DataGrid"+this.parent.uniqueId, "FilterActive", this.activeFilter);
		}
	},
	
	load: function(strFilterName) {
		if (strFilterName) {
			strFilterName += "Filter";
			//load a specific filter
			eval("this.processResult(" + pageSettings.getSettingSync("DataGrid"+this.parent.uniqueId, strFilterName) + ")");
			
		} else {
			//load last used filter for this grid
			eval("this.processResult(" + pageSettings.getSettingSync("DataGrid"+this.parent.uniqueId, "FilterActive") + ")");
		}
		
	},
	processResult: function(objResult) {
		this.activeFilter = objResult;
	},
	
	addFilterCriteria: function(field, operator, value) {
		if (!this.activeFilter.arrCriterias) {
			this.activeFilter.arrCriterias = new Array();
			this.activeFilter.arrCriterias.length = 0;
		}
		//alert("numberOfFilterCriterias:"+this.activeFilter.arrCriterias.length)

		this.activeFilter.arrCriterias.push({
			"field" : field,
			"operator" : operator,
			"value" : value
		});
		//alert("numberOfFilterCriterias:"+this.activeFilter.arrCriterias.length)		
	},
	
	setFilterFields: function (arrayOfFields) {
		this.reset();
		
		if(arrayOfFields){
			for (i=0; i<arrayOfFields.length; i++) {
				if( $(arrayOfFields[i]) ) {
					if( $F(arrayOfFields[i]) != "" && $F(arrayOfFields[i])!="NULL" ) {
						strField = arrayOfFields[i];
						strValue = $F(arrayOfFields[i]);
						if( $(arrayOfFields[i]+"Filter") ) {
							strOperator = $F(arrayOfFields[i]+"Filter");
						} else {					
							strOperator = "eq";
						}
						if (strField == "categoryId" & strValue.substr(0, 1) == '-') {
							strField = "parentCategoryId";
							strValue = strValue * -1;
						}
						//alert(strField +":"+ strOperator +":"+ strValue);
						this.addFilterCriteria(strField, strOperator, strValue);
					}
				}		
			}
		}		
		this.save();
		this.parent.loadData();
	},
	resetFilterFields: function (arrayOfFields) {
		this.reset();		
		this.save();
		this.parent.loadData();
		
		//reset values in form fields
		if(arrayOfFields) {
			for (i=0; i<arrayOfFields.length; i++) {
				if( $(arrayOfFields[i]) ) {
					$(arrayOfFields[i]).value = "";
				}
			}
		}
	},
	initFilterFields: function () {
		if (this.activeFilter.arrCriterias) {
			var arrCriterias = this.activeFilter.arrCriterias
			for (i=0; i<arrCriterias.length; i++) {
				strField = arrCriterias[i].field;
				strValue = arrCriterias[i].value;
				if(strField=="parentCategoryId") {
					strField = "categoryId";
					strValue = strValue * -1;
				}
				$(strField).value = strValue;
				if ( $(strField+"Filter") ) {
					$(strField+"Filter").value = arrCriterias[i].operator;
				}				
			}
		}
	},
	getNumberOfActiveFilters: function() {
		if (this.activeFilter.arrCriterias) {
			return this.activeFilter.arrCriterias.length;
		} else return 0;
	},
	toQueryString: function() {		
		var arrResult = new Array();
		
		//build querystring
		if(this.activeFilter && this.activeFilter.arrCriterias) {
			for (i=0; i<this.activeFilter.arrCriterias.length; i++) {
				//alert("i: " + i);
				//alert($H(this.activeFilter.arrCriterias[i]).toQueryString())
				if ( this.activeFilter.arrCriterias[i] != undefined && i != "toJSONString") {
					arrResult["fk"+i] = this.activeFilter.arrCriterias[i].field;
					arrResult["fo"+i] = this.activeFilter.arrCriterias[i].operator;
					arrResult["fv"+i] = this.activeFilter.arrCriterias[i].value;
					//alert(this.activeFilter.arrCriterias[i].field)
				}
			}
			//Object to QueryString
			return $H(arrResult).toQueryString();
		} else return "";		
	}		
}

function URLDecode(strEncodeString) {
	// Create a regular expression to search all +s in the string
	var lsRegExp = /\+/g;
	// Return the decoded string	  
	return unescape(strEncodeString.replace(lsRegExp, " "));
}
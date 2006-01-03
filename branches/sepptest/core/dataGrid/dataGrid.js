//DATAGRID
var objRowActive;
var mouseEventsDisabled;
mouseEventsDisabled = false;

function activateRow(objRow) {
	if (objRow.className == "dgRow") objRow.className = "dgRowActive";
	if (objRow.className == "dgRowSelected") objRow.className = "dgRowSelectedActive";
	document.getElementById("check" + objRow.id).focus();
}
function deactivateRow(objRow) {
	if (objRow.className == "dgRowActive") objRow.className = "dgRow";
	if (objRow.className == "dgRowSelectedActive") objRow.className = "dgRowSelected";
}

function selectRow(objRow) {
	if (objRow.className == "dgRow") objRow.className = "dgRowSelected";
	if (objRow.className == "dgRowActive") objRow.className = "dgRowSelectedActive";
	document.getElementById("check"+objRow.id).checked = "checked";
	document.getElementById("check"+objRow.id).focus();
}
function deselectRow(objRow) {
	if (objRow.className == "dgRowSelected") objRow.className = "dgRow";
	if (objRow.className == "dgRowSelectedActive") objRow.className = "dgRowActive";
	document.getElementById("check"+objRow.id).checked = "";
	document.getElementById("check"+objRow.id).focus();
}

var highlight = {
	'tr.dgRow' : function(element){
		element.onmouseover = function(){
			if (!mouseEventsDisabled) {
				if(objRowActive) {
					deactivateRow(objRowActive);
				}
				objRowActive = this;
				document.getElementById("check" + this.id).focus();
				activateRow(this);
			}
		}
		element.onmouseout = function(){
			if (!mouseEventsDisabled) {
				deactivateRow(this);
			}
		}
		element.onclick = function(){
			if(this.className=="dgRowSelected" || this.className=="dgRowSelectedActive") {
				deselectRow(this);
			} else {
				selectRow(this);
			}
		}
	}	
	
};
Behaviour.register(highlight);

function enableMouseEvents() {
	mouseEventsDisabled = false;
	//alert("test");
}

function dgKeyProcess(event) {
	if (!event) event=window.event;
	
	if (event.keyCode == Event.KEY_DOWN) {
		mouseEventsDisabled = true;
		window.setTimeout("enableMouseEvents()", 10);
		Event.stop(event);
		if (objRowActive) {
			objNextRow = objRowActive.nextSibling.nextSibling;
			if (objNextRow) {
				deactivateRow(objRowActive);
				activateRow(objNextRow);
				objRowActive = objNextRow;
			}
		} else {
			objRowActive = document.getElementById("dgData").getElementsByTagName("tr")[0];
			activateRow(objRowActive);
		}
		Behaviour.register(disabled);
		
	}
	if (event.keyCode == Event.KEY_UP) {
		mouseEventsDisabled = true;
		window.setTimeout("enableMouseEvents()", 10);
		Event.stop(event);
			if (objRowActive) {
				objNextRow = objRowActive.previousSibling.previousSibling;
				if (objNextRow) {
					deactivateRow(objRowActive);
					activateRow(objNextRow);
					objRowActive = objNextRow;
				}
			}
	}
	if (event.keyCode == Event.KEY_RETURN) {
		
	}
	if (event.keyCode == Event.KEY_DELETE) {
		confirm("Wollen Sie die Datens?tze wirklich l?schen?")
	}

}
Event.observe(window, 'keypress', dgKeyProcess, false)
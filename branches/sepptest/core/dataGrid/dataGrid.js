//DATAGRID

var highlight = {
	'tr.dgRow' : function(element){
		element.onmouseover = function(){
			if (this.className != "dgRowSelected"){
				this.className = "dgRowActive";
			}
			cells = this.getElementsByTagName("TD");
			for (i = 0; i < cells.length;i++) {
				if (i==0) {
					cells[i].style.borderLeft = "1px dashed #000000";
				}
				cells[i].style.borderTop = "1px dashed #000000";
				cells[i].style.borderBottom = "1px dashed #000000";
			} 
			cells[i-1].style.borderRight = "1px dashed #000000";
		}
		element.onmouseout = function(){
			if (this.className != "dgRowSelected"){
				this.className = "dgRow";
			}
			cells = this.getElementsByTagName("TD");
			for (i = 0; i < cells.length;i++) {
				cells[i].style.borderLeft = "";
				cells[i].style.borderRight = "";
				cells[i].style.borderTop = "";
				cells[i].style.borderBottom = "1px solid #ececec";
			} 
		}
		element.onclick = function(){
			if(this.className != "dgRowSelected"){
				this.className = "dgRowSelected";
				document.getElementById("check"+this.id).checked="checked";
			} else {
				this.className = "dgRowActive";
				document.getElementById("check"+this.id).checked="";	
			}
		}

	}	
	
};

Behaviour.register(highlight);
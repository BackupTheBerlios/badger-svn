function validateUpperLimit(id) {
	var returnValue;
	var lowerLimit = $("lowerLimit").value;
	var upperLimit = $(id).value;
	
	if(upperLimit!="" &&  upperLimit < lowerLimit) {
		labelLower = getFieldLabel("lowerLimit");
		labelUpper = getFieldLabel(id);
		alert(labelLower +" > "+ labelUpper + ": " + lowerLimit +" > "+upperLimit);
		return false;
	} else {
		return true;
	}
	
	
}

function getFieldLabel(id) {
	var strFieldName;

	label = $("label" + id);
	if(label.textContent) strFieldName = label.textContent; //FF
	if(label.text) strFieldName = label.text; //IE
	if(label.innerText) strFieldName = label.innerText; //Opera
		
	strFieldName = strFieldName.replace( ":", "" );
	
	return strFieldName;
}
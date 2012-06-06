function SetDefaultValue(inID, inValue) {
	$(inID).addEvent("focus", function() {
		if($(inID).value == inValue) {
			$(inID).value = "";
		}
	});
	$(inID).addEvent("blur", function() {
		if($(inID).value == "") {
			$(inID).value = inValue;
		}
	});
}
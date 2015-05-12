// Set global change detection variable and array
var IsChangeGlobal = false;
var ChangeArrayGlobal = new Array();

function GetChange(ParseID) {
	// Set flag to determine if change has occured
	var IsChange = false;

	// Loop through the array of id's to check
	for ( var Index = 0; Index < ChangeArray.length; Index++ ) {
		// Get the element checking
		var ElementToCheck = document.getElementById(ChangeArray[Index] + ParseID);

		// Check the element for change
		switch ( ElementToCheck.type ) {
			case "checkbox":
				if ( ElementToCheck.checked != ElementToCheck.defaultChecked ) {
					IsChange = true;
				}
				break;
			case "select":
				if ( !ElementToCheck.options[ElementToCheck.selectedIndex].defaultSelected ) {
					IsChange = true;
				}
				break;
			default:
				if ( ElementToCheck.defaultValue != ElementToCheck.value ) {
					IsChange = true;
				}
		}
	}

	// Show/hide the save changes button
	if ( IsChange ) {
		DisplayStyle = "inline";
		if ( typeof ChangeArrayGlobal[ParseID] == "undefined" ) {
			ChangeArrayGlobal[ParseID] = true;
			IsChangeGlobal = true;
		}
	} else {
		DisplayStyle = "none";
		if ( typeof ChangeArrayGlobal[ParseID] != "undefined" ) {
			delete ChangeArrayGlobal[ParseID];
			if ( ChangeArrayGlobal.length == 0 ) {
				IsChangeGlobal = false;
			}
		}
	}
	document.getElementById("ChangeCell" + ParseID).style.display = DisplayStyle;
}

window.onbeforeunload = function (e) {
	if ( IsChangeGlobal ) {
		var message = "There are unsaved changes on this page.";
		var e = e || window.event;
		// Most browsers
		if (e) {
			e.returnValue = message;
		}

		// Safari
		return message;
	}
}

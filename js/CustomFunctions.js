// Set global change detection variable and array
var IsChangeGlobal = false;
var ChangeArrayGlobal = new Object();

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
			case "select-one":
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

	// Set the array entry
	if ( IsChange && typeof ChangeArrayGlobal[ParseID] == "undefined" ) {
		ChangeArrayGlobal[ParseID] = true;
	} else if ( !IsChange && typeof ChangeArrayGlobal[ParseID] != "undefined" ) {
		delete ChangeArrayGlobal[ParseID];
	}
	
	// Set the global change variable
	if ( Object.keys(ChangeArrayGlobal).length == 0 ) {
		IsChangeGlobal = false;
	} else {
		IsChangeGlobal = true;
	}
}

window.onbeforeunload = function (e) {
	if ( IsChangeGlobal ) {
		var message = "There are unsaved changes on this page. The following items have been changed:\n\n";
		var e = e || window.event;
		var key;

		for ( key in ChangeArrayGlobal ) {
			if ( document.getElementById(NameID + key).type == "text" ) {
				message = message + document.getElementById(NameID + key).value + "\n";
			} else {
				message = message + document.getElementById(NameID + key).innerText + "\n";
			}
		}

		// Most browsers
		if (e) {
			e.returnValue = message;
		}

		// Safari
		return message;
	}
}

function PostToPage(PostString,PageName,ElementID) {
	// Function to send a post string to a page and alert the user during the process
	// @param PostString - the string to send to the page
	// @param PageName - the page to send the post to
	// @param ElementID - the Element to put the user message in

	// Check for an ElementID and put a message there if there is one
	ElementID = ElementID || -1;
	if ( ElementID != -1 ) {
		// Notify the user that the post is being executed
		document.getElementById(ElementID).innerHTML = "Processing request...";
	
		// Make the user message section visible if it isn't already
		if ( document.getElementById(ElementID).style.display == "none" ) {
			document.getElementById(ElementID).style.display = "inline";
		}
	}
	
	// Encode post string
	PostString = encodeURI(PostString);
	
	// Set up XMLHttp request
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	// Create the function that handles the response
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
			// Handle successful response
			response = xmlhttp.responseText;
			if ( response == 'true' ) {
				// Reload page if success
				location.reload();
			} else {
				if ( ElementID != -1 ) {
					// Show error if error
					document.getElementById(ElementID).innerHTML = response;
				} else {
					// Create alert window if there isn't an ElementID to work with
					window.alert(response);
				}
			}
		} else if ( xmlhttp.readyState == 4 ) {
			// Handle unsuccessful response
			if ( ElementID != -1 ) {
				// Put an error message in the ElementID
				document.getElementById(ElementID).innerHTML = "Error Submitting Data: Status code " + xmlhttp.status;
			} else {
				// Alert window if no ElementID
				window.alert("Error Submitting Data: Status code " + xmlhttp.status);
			}
		}
	}

	// Open the connection to the page
	xmlhttp.open("POST",PageName,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

	// Send the PostString
	xmlhttp.send(PostString);
}

function CreatePage(PageName,PageTitle) {
	// Function to load a new page without reloading all the JS, etc
	// @param PageName - the name of the page to load from

	// Replace the header, URL, and title
	document.getElementById("PageTitle").innerHTML = PageTitle;
	window.history.pushState("Object",PageTitle,PageName);

	// Name of the main div to replace
	MainBodyName = "MainBody";
	
	// Create a loading icon in the page
	document.getElementById(MainBodyName).innerHTML = "Loading...";

	// Post to the new page
	// Set up XMLHttp request
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}

	// Create the post string
	PostString = "LoadPage=1";

	// Create the function that handles the response
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
			// Handle successful response
			response = xmlhttp.responseText;
			document.getElementById(MainBodyName).innerHTML = response;
		} else if ( xmlhttp.readyState == 4 ) {
			// Handle unsuccessful response
			document.getElementById(MainBodyName).innerHTML = "Error Submitting Data: Status code " + xmlhttp.status;
		}
	}

	// Open the connection to the page
	xmlhttp.open("POST",PageName,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

	// Send the PostString
	xmlhttp.send(PostString);
}

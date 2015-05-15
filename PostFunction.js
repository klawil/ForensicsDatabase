function PostToPage(PostString,PageName,ElementID) {
	// Function to send a post string to a page and alert the user during the process
	// @param PostString - the string to send to the page
	// @param PageName - the page to send the post to
	// @param ElementID - the Element to put the user message in

	// Notify the user that the post is being executed
	document.getElementById(ElementID).innerHTML = "Processing request...";

	// Make the user message section visible if it isn't already
	if ( document.getElementById(ElementID).style.display == "none" ) {
		document.getElementById(ElementID).style.display = "inline";
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
				// Show error if error
				document.getElementById(ElementID).innerHTML = response;
			}
		} else if ( xmlhttp.readyState == 4 ) {
			// Handle unsuccessful response
			document.getElementById(ElementID).innerHTML = "Error Submitting Data: Status code " + xmlhttp.status;
		}
	}

	// Open the connection to the page
	xmlhttp.open("POST",PageName,true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");

	// Send the PostString
	xmlhttp.send(PostString);
}

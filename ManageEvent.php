<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Event Management';
require_once 'restrictedpage.inc';

$DoQuery = false;

// Handle event deletion
if ( isset($_POST['delete']) ) {
	// Set EventID
	if ( !isset($_POST['EventID']) ) {
		echo 'Event ID is required';
		return 0;
	}
	$EventData['EventID'] = MySQLEscape($_POST['EventID'],$DBConn);
	
	// Validate ID
	$CheckArray = [['variable' => 'EventID','IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'EventID');},'Error' => 'Event ID is invalid']];
	$Validation = ValidateArray($EventData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Create query string
	$EventString = 'delete from Events where EventID="' . $EventData['EventID'] . '";';
	
	// Tell to execute query
	$DoQuery = true;
}

// Create array of POST values
if ( !$DoQuery && (isset($_POST['EventID']) || isset($_POST['EventName']) ) ) {
	// Names of variables to put in array
	$Variables = ['EventID','EventName','Partner','EventAbbr'];
	
	// Set all variables
	foreach ( $Variables as $Name ) {
		if ( isset($_POST[$Name]) ) {
			if ( $_POST[$Name] == '' ) {
				echo $Name . ' is required';
				return 0;
			}
			// URL Decode and MySQL escape
			$EventData[$Name] = MySQLEscape(urldecode($_POST[$Name]),$DBConn);
		}
	}
	
	// Create testing array
	$CheckArray = [['variable' => 'EventID','IsSet' => 0, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'EventID');},'Error' => 'The event ID is invalid'],
	['variable' => 'EventName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,50);},'Error' => 'The event name is too long (must be less than 50 characters)'],
	['variable' => 'Partner','IsSet' => 1, 'Validate' => function($var,$DBConn){return IsBool($var);},'Error' => 'Partner requirement is invalid'],
	['variable' => 'EventAbbr','IsSet' => 1, 'Validate' => function($var){return IsLength($var,10);},'Error' => 'Event Abbreviation is too long (must be less than 10 characters)']];
	
	// Validate variables
	$Validation = ValidateArray($EventData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Check for duplicate entry
	if ( !isset($_POST['EventID']) ) {
		$DupQuery = MySQLQuery($DBConn,'select EventID from Events where EventName="' . $EventData['EventName'] . '" or EventAbbr="' . $EventData['EventAbbr'] . '";');
		if ( !$DupQuery['Result'] ) {
			echo $DupQuery['Query'];
			return 0;
		}
		if ( mysqli_num_rows($DupQuery['Query']) != 0 ) {
			echo 'An event with that name and/or abbreviation already exists';
			return 0;
		}
	}
}

// Handle update
if ( !$DoQuery && isset($_POST['EventID']) ) {
	// Pull out EventID
	$EventID = $EventData['EventID'];
	unset($EventData['EventID']);
	
	// Create query string
	$EventString = '';
	foreach ( $EventData as $Name => $Value ) {
		if ( $EventString == '' ) {
			$EventString = 'update Events set ' . $Name . '="' . $Value . '"';
		} else {
			$EventString = $EventString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$EventString = $EventString . ' where EventID="' . $EventID . '";';
	
	// Set flag to execute query
	$DoQuery = true;
}

// Handle creation
if ( !$DoQuery && isset($_POST['EventName']) ) {
	// Create query string
	$EventString = '';
	foreach ( $EventData as $Name => $Value ) {
		if ( $EventString == '' ) {
			$EventString = 'insert into Events set ' . $Name . '="' . $Value . '"';
		} else {
			$EventString = $EventString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$EventString = $EventString . ';';
	
	// Set flag to execute query
	$DoQuery = true;
}

// Execute query
if ( $DoQuery ) {
	$EventQuery = MySQLQuery($DBConn,$EventString);
	if ( !$EventQuery['Result'] ) {
		echo $EventQuery['Query'];
		return 0;
	}
	echo 'true';
	return 0;
}

// Create query of all Events
$EventQuery = MySQLQuery($DBConn,'select EventName, EventID, Partner, EventAbbr from Events order by Events.EventName;');
if ( !$EventQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $EventQuery['Query'];
	require_once 'ErrorPage.inc';
}

// Create header
require_once 'header.inc';
?>
<h3>Update, create, and delete events</h3>
<div id="PostMessage" class="alert"></div>
<table class="Table">
<tr><th>Event Name</th><th>Abbreviation</th><th>Partner</th></tr>
<tr>
	<td><span title="Name of the event"><input type="text" id="EventName" autofocus="autofocus"></span></td>
	<td><span title="Abbreviation of the event"><input type="text" id="EventAbbr"></span></td>
	<td><span title="Is this a partner event?"><input type="checkbox" id="Partner"></span></td>
	<td><span title="Create this event"><input type="button" value="Create Event" onclick="SubmitEvent()"></span></td>
</tr>
<?php
// Set up loop
$NumRows = mysqli_num_rows($EventQuery['Query']);
$CurrentRow = 1;

// Loop through the query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$EventData = mysqli_fetch_assoc($EventQuery['Query']);
	
	// Set EventID
	$EventID = $EventData['EventID'];
	
	// Return data as HTML table?>
<tr>
	<td><span title="Name of the event"><input type="text" id="EventName<?php echo $EventID; ?>" value="<?php echo $EventData['EventName']; ?>"></span></td>
	<td><span title="Abbreviation of the event"><input type="text" id="EventAbbr<?php echo $EventID; ?>" value="<?php echo $EventData['EventAbbr']; ?>"></span></td>
	<td><span title="Is this a partner event?"><input type="checkbox" id="Partner<?php echo $EventID; ?>"<?php if ( $EventData['Partner'] == 1 ) { echo ' checked'; }?>></span></td>
	<td><span title="Delete this event"><input type="button" onclick="DeleteEvent(<?php echo $EventID; ?>)" value="Delete Event"></span></td>
	<td><span title="Save changes to this event"><input type="button" value="Save Changes" onclick="SubmitEvent(<?php echo $EventID; ?>)"></span></td>
</tr>
<?php
	$CurrentRow++;
}
?>
</table>
<script>
function PostToPage(PostString) {
	// Alert user
	document.getElementById("PostMessage").innerHTML = "Processing request...";
	document.getElementById("PostMessage").style.display = "inline";
	
	// Encode string
	PostString = encodeURI(PostString);
	
	// Set up post
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","ManageEvent.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(PostString);
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
			// Handle successful response
			response = xmlhttp.responseText;
			if ( response == 'true' ) {
				// Reload page if success
				location.reload();
			} else {
				// Show error if error
				document.getElementById("PostMessage").style.display = "none";
				window.alert(response);
			}
		} else if ( xmlhttp.readyState == 4 ) {
			// Handle unsuccessful response
			document.getElementById("PostMessage").style.display = "none";
			window.alert("Error: Status code " + xmlhttp.status);
		}
	}
}
function SubmitEvent(EventID) {
	// Set default EventID
	EventID = EventID || -1;
	
	// Declare PostString
	PostString = "";
	
	// Set Element Names
	EventNameElement = "EventName";
	EventAbbrElement = "EventAbbr";
	PartnerElement = "Partner";
	if ( EventID != -1 ) {
		PostString = "EventID=" + EventID + "&";
		EventNameElement = EventNameElement + EventID
		EventAbbrElement = EventAbbrElement + EventID
		PartnerElement = PartnerElement + EventID
	}
	
	// Get PostString data
	EventName = encodeURIComponent(document.getElementById(EventNameElement).value);
	EventAbbr = encodeURIComponent(document.getElementById(EventAbbrElement).value);
	Partner = 0;
	if ( document.getElementById(PartnerElement).checked ) {
		Partner = 1;
	}
	
	// Create PostString
	PostString = PostString + "EventName=" + EventName + "&EventAbbr=" + EventAbbr + "&Partner=" + Partner;
	
	// Execute Post
	PostToPage(PostString);
}
function DeleteEvent(EventID) {
	// Check if they are certain
	if ( !window.confirm("DANGER DANGER!!\nThis will PERMANENTLY erase this event.\n\nFOREVER\n\nDo you still want to do this?") ) {
		return 0;
	}
	
	// Create PostString
	PostString = "delete=1&EventID=" + EventID;
	
	// Execute Post
	PostToPage(PostString);
}
</script>
</body>
</html>

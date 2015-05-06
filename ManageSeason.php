<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Season Management';
require_once 'restrictedpage.inc';

$DoQuery = false;

// Handle season deletion
if ( isset($_POST['delete']) ) {
	// Set SeasonID
	if ( !isset($_POST['SeasonID']) ) {
		echo 'Season ID is required';
		return 0;
	}
	$SeasonData['SeasonID'] = MySQLEscape($_POST['SeasonID'],$DBConn);
	
	// Check Array
	$CheckArray = [['variable' => 'SeasonID','IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'SeasonID');},'Error' => 'Season ID is invalid']];
	
	// Validate variables
	$Validation = ValidateArray($SeasonData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Create query string
	$SeasonString = 'delete from Seasons where SeasonId="' . $SeasonData['SeasonID'] . '";';
	
	// Tell to execute query
	$DoQuery = true;
}

// Create array of values if POST data
if ( !$DoQuery && (isset($_POST['StartYear']) || isset($_POST['SeasonID'])) ) {
	// Names of variables to put in array
	$Variables = ['StartYear','SeasonName','SeasonID'];
	
	// Set all variables
	foreach ( $Variables as $Name ) {
		if ( isset($_POST[$Name]) ) {
			// URL Decode and MySQL escape
			$SeasonData[$Name] = MySQLEscape(urldecode($_POST[$Name]),$DBConn);
		}
	}
	
	// Create testing array
	$CheckArray = [['variable' => 'StartYear','IsSet' => 1, 'Validate' => function($var){return IsInt($var);},'Error' => 'The year must be an integer'],
	['variable' => 'SeasonName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,150);},'Error' => 'Season Name is too long'],
	['variable' => 'SeasonID','IsSet' => 0, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'SeasonID');},'Error' => 'Season ID is invalid']];
	
	// Validate variables
	$Validation = ValidateArray($SeasonData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
}

// Handle update
if ( !$DoQuery && isset($_POST['SeasonID']) ) {
	// Pull out SeasonID
	$SeasonID = $SeasonData['SeasonID'];
	unset($SeasonData['SeasonID']);
	
	// Create query string
	$SeasonString = '';
	foreach ( $SeasonData as $Name => $Value ) {
		if ( $SeasonString == '' ) {
			$SeasonString = 'update Seasons set ' . $Name . '="' . $Value . '"';
		} else {
			$SeasonString = $SeasonString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$SeasonString = $SeasonString . ' where SeasonID="' . $SeasonID . '";';
	
	// Tell to execute query
	$DoQuery = true;
}

// Handle creation
if ( !$DoQuery && isset($_POST['StartYear']) ) {
	// Create query string
	$SeasonString = '';
	foreach ( $SeasonData as $Name => $Value ) {
		if ( $SeasonString == '' ) {
			$SeasonString = 'insert into Seasons set ' . $Name . '="' . $Value . '"';
		} else {
			$SeasonString = $SeasonString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$SeasonString = $SeasonString . ';';
	
	// Tell to execute query
	$DoQuery = true;
}

if ( $DoQuery ) {
	// Execute query
	$SeasonQuery = MySQLQuery($DBConn,$SeasonString);
	if ( !$SeasonQuery['Result'] ) {
		echo $SeasonQuery['Query'];
		return 0;
	}
	echo 'true';
	return 0;
}

// Create query of all seasons
$SeasonQuery = MySQLQuery($DBConn,'select SeasonName, StartYear, SeasonID from Seasons order by StartYear;');
if ( !$SeasonQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $SeasonQuery['Query'];
	require_once 'ErrorPage.inc';
}

// Create header
require_once 'header.inc';
?>
<h3>Update, create, and delete seasons</h3>
<table class="Table">
<tr><th>Season Name</th><th>Start Year</th></tr>
<?php
// Set up loop variables
$NumRows = mysqli_num_rows($SeasonQuery['Query']);
$CurrentRow = 1;

// Loop through all query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$SeasonData = mysqli_fetch_assoc($SeasonQuery['Query']);
	
	// Add a row to the html table
	echo '<tr><td><input type="text" id="SeasonName' . $SeasonData['SeasonID'] . '" value="' . $SeasonData['SeasonName'] . '" onchange="ShowHideButton(' . $SeasonData['SeasonID'] . ')"></td><td><input type="number" id="StartYear' . $SeasonData['SeasonID'] . '" value="' . $SeasonData['StartYear'] . '" onchange="ShowHideButton(' . $SeasonData['SeasonID'] . ')"></td><td><input type="button" value="Delete Season" onclick="DeleteSeason(' . $SeasonData['SeasonID'] . ')"></td><td class="ChangeCell" id="' . $SeasonData['SeasonID'] . '"><input type="button" value="Save Changes" onclick="SubmitSeason(' . $SeasonData['SeasonID'] . ')"></td></tr>';
	$CurrentRow++;
}
?>
<tr><td><input type="text" id="SeasonName" onchange="ShowHideButton()"></td><td><input type="number" id="StartYear" onchange="ShowHideButton()"></td><td class="ChangeCell" id="NewSeason"><input type="button" value="Save Changes" onclick="SubmitSeason()"></td></tr>
</table>
<script>
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}
function ShowHideButton(SeasonID) {
	// Set default SeasonID
	SeasonID = SeasonID || -1;
		
	// Set Element Names
	StartYearElement = "StartYear";
	SeasonNameElement = "SeasonName";
	ButtonElement = "NewSeason";
	if ( SeasonID != -1 ) {
		StartYearElement = StartYearElement + SeasonID;
		SeasonNameElement = SeasonNameElement + SeasonID;
		ButtonElement = SeasonID;
	}
	
	// Get start year
	StartYear = document.getElementById(StartYearElement).value;
	StartYear = pad(StartYear,4);
	document.getElementById(StartYearElement).value = StartYear;
	
	// Get Season name
	SeasonName = document.getElementById(SeasonNameElement).value;
	
	// Show save button if the value is changed
	if ( document.getElementById(StartYearElement).value != document.getElementById(StartYearElement).defaultValue ) {
		// Start Year is changed
		document.getElementById(ButtonElement).style.display = "inline";
	} else if ( document.getElementById(SeasonNameElement).value != document.getElementById(SeasonNameElement).defaultValue ) {
		// Season name is changed
		document.getElementById(ButtonElement).style.display = "inline";
	} else {
		document.getElementById(ButtonElementName).style.display = "none";
	}
}
function PostToPage(PostString) {
	// Encode string
	PostString = encodeURI(PostString);
	
	// Set up post
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","ManageSeason.php",true);
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
				window.alert(response);
			}
		} else if ( xmlhttp.readyState == 4 ) {
			// Handle unsuccessful response
			window.alert("Error: Status code " + xmlhttp.status);
		}
	}
}
function SubmitSeason(SeasonID) {
	// Set default SeasonID
	SeasonID = SeasonID || -1;
	
	// Declare PostString
	PostString = "";
	
	// Set Element Names
	StartYearElement = "StartYear";
	SeasonNameElement = "SeasonName";
	if ( SeasonID != -1 ) {
		StartYearElement = StartYearElement + SeasonID;
		SeasonNameElement = SeasonNameElement + SeasonID;
		PostString = "SeasonID=" + SeasonID + "&";
	}
	
	// Get start year and add to PostString
	StartYear = document.getElementById(StartYearElement).value;
	StartYear = pad(StartYear,4);
	PostString = PostString + "StartYear=" + StartYear + "&";
	
	// Get Season name and add to PostString
	SeasonName = document.getElementById(SeasonNameElement).value;
	SeasonName = encodeURIComponent(SeasonName);
	PostString = PostString + "SeasonName=" + SeasonName;
	
	// Execute Post
	PostToPage(PostString);
}
function DeleteSeason(SeasonID) {
	// Create PostString
	PostString = "delete=1&SeasonID=" + SeasonID;
	
	// Execute Post
	PostToPage(PostString);
}
</script>
</body>
</html>

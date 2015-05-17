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
			if ( $_POST[$Name] == '' ) {
				echo $Name . ' is required';
				return 0;
			}
			// URL Decode and MySQL escape
			$SeasonData[$Name] = MySQLEscape(urldecode($_POST[$Name]),$DBConn);
		}
	}
	
	// Create testing array
	$CheckArray = [['variable' => 'StartYear','IsSet' => 1, 'Validate' => function($var){return IsInt($var);},'Error' => 'The year must be an integer'],
	['variable' => 'SeasonName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,150);},'Error' => 'Season Name is too long (must be less than 150 characters)'],
	['variable' => 'SeasonID','IsSet' => 0, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'SeasonID');},'Error' => 'Season ID is invalid']];
	
	// Validate variables
	$Validation = ValidateArray($SeasonData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Check for duplicate entry
	if ( !isset($_POST['SeasonID']) ) {
		$DupQuery = MySQLQuery($DBConn,'select SeasonID from Seasons where SeasonName="' . $SeasonData['SeasonName'] . '";');
		if ( !$DupQuery['Result'] ) {
			echo $DupQuery['Query'];
			return 0;
		}
		if ( mysqli_num_rows($DupQuery['Query']) != 0 ) {
			echo 'A season with that name already exists';
			return 0;
		}
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
$SeasonQuery = MySQLQuery($DBConn,'select SeasonName, StartYear, SeasonID from Seasons order by StartYear desc;');
if ( !$SeasonQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $SeasonQuery['Query'];
	require_once 'ErrorPage.inc';
}

// Create header
require_once 'header.inc';
?>
<div class="container">
<h3>Update, create, and delete seasons</h3>
<div id="PostMessage" class="alert"></div>
<table class="Table">
<tr><th>Season Name</th><th>Start Year</th><th></th><th></th></tr>
<tr>
	<td><span title="Name to assign to the season (i.e. 2014-2015)"><input type="text" id="SeasonName" autofocus="autofocus"></span></td>
	<td><span title="First year of the season (i.e. 2014)"><input type="number" id="StartYear"></span></td>
	<td><span title="Create this season"><input type="button" value="Create Season" onclick="SubmitSeason()"></span></td>
	<td></td>
</tr>
<?php
// Set up loop variables
$NumRows = mysqli_num_rows($SeasonQuery['Query']);
$CurrentRow = 1;

// Loop through all query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$SeasonData = mysqli_fetch_assoc($SeasonQuery['Query']);
	
	// Add a row to the html table?>
<tr>
	<td><span title="Name to assign to the season (i.e. 2014-2015)"><input type="text" id="SeasonName<?php echo $SeasonData['SeasonID']; ?>" value="<?php echo $SeasonData['SeasonName']; ?>" onchange="GetChange(<?php echo $SeasonData['SeasonID']; ?>)"></span></td>
	<td><span title="First year of the season (i.e. 2014)"><input type="number" id="StartYear<?php echo $SeasonData['SeasonID']; ?>" value="<?php echo $SeasonData['StartYear']; ?>" onchange="GetChange(<?php echo $SeasonData['SeasonID']; ?>)"></span></td>
	<td><span title="Delete this season"><input type="button" value="Delete Season" onclick="DeleteSeason(<?php echo $SeasonData['SeasonID']; ?>)" onchange="GetChange(<?php echo $SeasonData['SeasonID']; ?>)"></span></td>
	<td><span title="Save changes to the season"><input type="button" value="Save Changes" onclick="SubmitSeason(<?php echo $SeasonData['SeasonID']; ?>)" onchange="GetChange(<?php echo $SeasonData['SeasonID']; ?>)"></span></td>
</tr>
<?php
	$CurrentRow++;
}
?>
</table>
</div>
<script>
// Create array to check for changes
var ChangeArray = ["SeasonName","StartYear"];

// Name of the row name event
var NameID = "SeasonName";

function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
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
	PostString = PostString + "StartYear=" + StartYear + "&";
	
	// Get Season name and add to PostString
	SeasonName = document.getElementById(SeasonNameElement).value;
	SeasonName = encodeURIComponent(SeasonName);
	PostString = PostString + "SeasonName=" + SeasonName;
	
	// Execute Post
	PostToPage(PostString,"ManageSeason.php","PostMessage");
}

function DeleteSeason(SeasonID) {
	// Check if they are certain
	if ( !window.confirm("DANGER DANGER!!\nThis will PERMANENTLY erase this season.\n\nFOREVER\n\nDo you still want to do this?") ) {
		return 0;
	}
	
	// Create PostString
	PostString = "delete=1&SeasonID=" + SeasonID;
	
	// Execute Post
	PostToPage(PostString,"ManageSeason.php","PostMessage");
}

function GetChange(SeasonID) {
	// See if there is a change and if so show the save changes button
	
	// Create array to guide checking
	var ChangeArray = ["SeasonName","StartYear"];
	
	// Initialize variable to check for changes
	var ischange = false;

	// Loop through the change array and find a change
	for ( var Index = 0; Index < ChangeArray.length; Index++ ) {
		// Set the information needed
		var ElementID = ChangeArray[Index] + SeasonID;
		var Element = document.getElementById(ElementID);
		switch ( Element.type ) {
			case "checkbox":
				if ( Element.checked != Element.defaultChecked ) {
					ischange = true;
				}
				break;
			case "select":
				if ( !Element.options[Element.selectedIndex].defaultSelected ) {
					ischange = true;
				}
				break;
			default:
				if ( Element.defaultValue != Element.value ) {
					ischange = true;
				}
		}
	}
	
	// If there is change, show the button, otherwise hide it
	if ( ischange ) {
		var displaytype = "inline";
	} else {
		var displaytype = "none";
	}
	document.getElementById("ChangeCell" + SeasonID).style.display = displaytype;
}
</script>
</body>
</html>

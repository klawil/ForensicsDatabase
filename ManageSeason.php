<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Season Management';
require_once 'restrictedpage.inc';

// Handle update
if ( isset($_POST['SeasonID']) ) {

}

// Handle creation
if ( isset($_POST['StartYear']) ) {
	echo 'true';
	return 0;
}

// Create query of all seasons
$SeasonQuery = MySQLQuery($DBConn,'select * from  Seasons order by Seasons.StartYear;');
if ( !$SeasonQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $SeasonQuery['Query'];
	require_once 'ErrorPage.inc';
}

// Create header
require_once 'header.inc';
?>
<h3>Update, create, and delete seasons</h3>
<table class="Table">
<tr><th>Season Name</th><th>Start Year</th><th></th></tr>
<?php
// Set up loop variables
$NumRows = mysqli_num_rows($SeasonQuery['Query']);
$CurrentRow = 1;

// Loop through all query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$SeasonData = mysqli_fetch_assoc($SeasonQuery['Query']);
	
	// Add a row to the html table
	echo '<tr><td><input type="text" id="SeasonName' . $SeasonData['SeasonID'] . '" value="' . $SeasonData['SeasonName'] . '" onchange="ShowHideButton(' . $SeasonData['SeasonID'] . ')"></td><td><input type="number" id="StartYear' . $SeasonData['SeasonID'] . '" value="' . $SeasonData['StartYear'] . '" onchange="ShowHideButton(' . $SeasonData['SeasonID'] . ')"></td><td class="ChangeCell" id="' . $SeasonData['SeasonID'] . '"><input type="button" value="Save Changes" onclick="SubmitSeason(' . $SeasonData['SeasonID'] . ')"></td></tr>';
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
	StartYear = document.getElementById(StartElementName).value;
	StartYear = pad(StartYear,4);
	PostString = PostString + "StartYear=" + StartYear + "&";
	
	// Get Season name and add to PostString
	SeasonName = document.getElementById(SeasonNameElement).value;
	SeasonName = encodeURIComponent(SeasonName);
	PostString = PostString + "SeasonName=" + SeasonName;
	
	// Encode PostString
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
</script>
</body>
</html>

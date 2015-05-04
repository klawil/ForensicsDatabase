<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Season Management';

// Handle update
if ( isset($_POST['YID']) ) {

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
<tr><th>Start Year</th><th>End Year</th><th></th></tr>
<?php
// Set up loop variables
$NumRows = mysqli_num_rows($SeasonQuery['Query']);
$CurrentRow = 1;

// Loop through all query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$SeasonData = mysqli_fetch_assoc($SeasonQuery['Query']);
	
	// Add a row to the html table
	echo '<tr><td><input type="number" id="StartYear' . $SeasonData['YID'] . '" value="' . $SeasonData['StartYear'] . '" onchange="ShowHideButton(' . $SeasonData['YID'] . ')"></td><td id="EndYear' . $SeasonData['YID'] . '">' . $SeasonData['EndYear'] . '</td><td class="ChangeCell" id="' . $SeasonData['YID'] . '"><input type="button" value="Save Changes" onclick="SubmitSeason(' . $SeasonData['YID'] . ')"></td></tr>';
	$CurrentRow++;
}
// LName FName NoviceYear SID
?>
<tr><td><input type="number" id="StartYear" onchange="ShowHideButton()"></td><td id="EndYear"></td><td class="ChangeCell" id="NewSeason"><input type="button" value="Save Changes" onclick="SubmitSeason()"></td></tr>
</table>
<script>
function pad (str, max) {
  str = str.toString();
  return str.length < max ? pad("0" + str, max) : str;
}
function ShowHideButton(YID) {
	// Set default YID
	YID = YID || -1;
		
	// Set Element Names
	StartElementName = "StartYear";
	EndElementName = "EndYear";
	ButtonElementName = "NewSeason";
	if ( YID != -1 ) {
		StartElementName = StartElementName + YID;
		EndElementName = EndElementName + YID;
		ButtonElementName = YID;
	}
	
	// Get start year and set end year
	StartYear = document.getElementById(StartElementName).value;
	StartYear = pad(StartYear,4);
	document.getElementById(StartElementName).value = StartYear;
	document.getElementById(EndElementName).innerHTML = pad(parseInt(StartYear) + 1,4);
	
	// Show save button if the value is changed
	if ( document.getElementById(StartElementName).value != document.getElementById(StartElementName).defaultValue ) {
		document.getElementById(ButtonElementName).style.display = "inline";
	} else {
		document.getElementById(ButtonElementName).style.display = "none";
	}
}
function SubmitSeason(YID) {
	// Set default YID
	YID = YID || -1;
	
	// Declare PostString
	PostString = "";
	
	// Set Element name
	StartElementName = "StartYear";
	if ( YID != -1 ) {
		StartElementName = StartElementName + YID;
		PostString = "YID=" + YID + "&";
	}
	
	// Get start year and add to PostString
	StartYear = document.getElementById(StartElementName).value;
	StartYear = pad(StartYear,4);
	PostString = PostString + "StartYear=" + StartYear;
	
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

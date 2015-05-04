<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Season Management';

// Create query of all seasons
$SeasonQuery = MySQLQuery($DBConn,'select * from  Seasons order by Seasons.StartYear;');
if ( !$SeasonQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $SeasonQuery['Query'];
	require_once 'ErrorPage.inc';
}

// Create header
require_once 'header.inc';

if ( isset($_POST['YID']) ) {
	// Handle if a season is getting updated
}
?>
<h3>Update, create, and delete students</h3>
<table class="Table">
<tr><th>First Name</th><th>Last Name</th><th>Novice Season</th><th></th></tr>
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
function ShowHideButton(YID) {
	YID = YID || -1;
	
	// Element of the start year
	StartElementName = "StartYear";
	if ( YID != -1 ) {
		StartElementName = StartElementName + YID;
	}
	
	// Element of the button cell
	ButtonElementName = "NewSeason";
	if ( YID != -1 ) {
		ButtonElementName = YID;
	}
	
	// Show save button if the value is changed
	if ( document.getElementById(StartElementName).value != document.getElementById(StartElementName).defaultValue ) {
		document.getElementById(ButtonElementName).style.display = "inline";
	} else {
		document.getElementById(ButtonElementName).style.display = "none";
	}
}
function SubmitSeason(YID) {
	YID = YID || -1;
}
</script>
</body>
</html>

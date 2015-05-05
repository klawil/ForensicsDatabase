<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Season Management';
require_once 'restrictedpage.inc';

$DoQuery = false;

// Create query of all Students
$StudentQuery = MySQLQuery($DBConn,'select LName, FName, StudentID, NoviceYear from Students, Seasons where Students.NoviceYear = Seasons.SeasonID order by Seasons.StartYear, LName, FName;');
if ( !$StudentQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $StudentQuery['Query'];
	require_once 'ErrorPage.inc';
}

// Create header
require_once 'header.inc';
?>
<h3>Update, create, and delete students</h3>
<table class="Table">
<tr><th>Last Name</th><th>First Name</th><th>Novice Season</th></tr>
<?php
// Set up loop
$NumRows = mysqli_num_rows($StudentQuery['Query']);
$CurrentRow = 1;

// Loop through the query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$StudentData = mysqli_fetch_assoc($StudentQuery['Query']);
	
	// Echo data as HTML table?>
<tr>
	<td><input type="text" id="LName<?php echo $StudentData['StudentID']; ?>" value="<?php echo $StudentData['LName']; ?>"></td>
	<td><input type="text" id="FName<?php echo $StudentData['StudentID']; ?>" value="<?php echo $StudentData['FName']; ?>"></td>
	<td><?php echo CreateSeasonList($DBConn,NULL,NULL,'NoviceSeason' . $StudentData['StudentID'],$StudentData['NoviceYear']?></td>
	<td><input type="button" value="Delete Student" onclick="DeleteStudent(<?php echo $StudentData['StudentID']; ?>)"></td>
	<td class="ChangeCell" id="<?php echo $StudentData['StudentID']; ?>"><input type="button" value="Save Changes" onclick="SubmitStudent(<?php echo $StudentData['StudentID']; ?>)"></td>
</tr>
<?php
	$CurrentRow++;
}
?>
<tr>
	<td><input type="text" id="FName"></td>
	<td><input type="text" id="LName"></td>
	<td><?php echo CreateSeasonList($DBConn,NULL,NULL,'NoviceSeason'); ?></td>
	<td><input type="button" value="Create Student" onclick="SubmitStudent()"></td></tr>
</table>
<script>
function ShowHideButton(StudentID) {
	// See if StudentID is set
	StudentID = StudentID || -1;
	if ( StudentID == -1 ) {
		window.alert("Student ID is missing");
	}
	
	// Set Element Names
	FNameElement = "FName" + StudentID;
	LNameElement = "LName" + StudentID;
	NoviceSeasonElement = "NoviceSeason" + StudentID;
	ButtonElement = StudentID;
	
	// Get variables
	FName = document.getElementById(FNameElement).value;
	LName = document.getElementById(LNameElement).value;
	NoviceSeason = document.getElementById(NoviceSeasonElement).value;
	
	// Check for changes
	if ( FName != document.getElementById(FNameElement).defaultValue ) {
		DisplayType = "inline";
	} else if ( LName != document.getElementById(LNameElement).defaultValue ) {
		DisplayType = "inline";
	} else if ( document.getElementById(NoviceSeasonElement).options[document.getElementById(NoviceSeasonElement).selectedIndex].defaultSelected ) {
		window.alert("Test");
		DisplayType = "inline";
	} else {
		DisplayType = "none";
	}
	
	// Show/hide button accordingly
	document.getElementById(ButtonElement).style.display = DisplayType;
}
</script>
</body>
</html>

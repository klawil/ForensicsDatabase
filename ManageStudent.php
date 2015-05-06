<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Season Management';
require_once 'restrictedpage.inc';

$DoQuery = false;

// Handle student deletion
if ( isset($_POST['delete']) ) {
	// Set StudentID
	if ( !isset($_POST['StudentID']) ) {
		echo 'Student ID is required';
	}
	$StudentData['StudentID'] = MySQLEscape($_POST['StudentID'],$DBConn);
	
	// Validate ID
	$CheckArray = [['variable' => 'StudentID','IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'StudentID');},'Error' => 'Student ID is invalid']];
	$Validation = ValidateArray($StudentData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Create query string
	$StudentString = 'delete from Students where StudentID="' . $StudentData['StudentID'] . '";';
	
	// Tell to execute query
	$DoQuery = true;
}

// Create array of POST values
if ( !$DoQuery && (isset($_POST['StudentID']) || isset($_POST['LName']) ) ) {
	// Names of variables to put in array
	$Variables = ['FName','LName','NoviceYear'];
	
	// Set all variables
	foreach ( $Variables as $Name ) {
		if ( isset($_POST[$Name]) ) {
			// URL Decode and MySQL escape
			$StudentData[$Name] = MySQLEscape(urldecode($_POST[$Name]),$DBConn);
		}
	}
	
	// Create testing array
	$CheckArray = [['variable' => 'FName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,50);},'Error' => 'The first name is too long (must be less than 50 characters)'],
	['variable' => 'LName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,50);},'Error' => 'The last name is too long (must be less than 50 characters)'],
	['variable' => 'NoviceYear','IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'SeasonID');},'Error' => 'Season ID is invalid'],
	['variable' => 'StudentID','IsSet' => 0, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'StudentID');},'Error' => 'Student ID is invalid']];
	
	// Validate variables
	$Validation = ValidateArray($StudentData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
}

// Handle creation
if ( !$DoQuery && isset($_POST['LName']) ) {
	// Create query string
	$StudentString = '';
	foreach ( $StudentData as $Name => $Value ) {
		if ( $StudentString == '' ) {
			$StudentString = 'insert into Students set ' . $Name . '="' . $Value . '"';
		} else {
			$StudentString = $StudentString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$StudentString = $StudentString . ';';
	
	// Set flag to execute query
	$DoQuery = true;
}

// Execute query
if ( $DoQuery ) {
	$StudentQuery = MySQLQuery($DBConn,$StudentString);
	if ( !$StudentQuery['Result'] ) {
		echo $StudentQuery['Query'];
		return 0;
	}
	echo 'true';
	return 0;
}

// Create query of all Students
$StudentQuery = MySQLQuery($DBConn,'select LName, FName, StudentID, NoviceYear from Students, Seasons where Students.NoviceYear = Seasons.SeasonID order by Seasons.StartYear desc, LName, FName;');
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
	<td><input type="text" id="LName<?php echo $StudentData['StudentID']; ?>" value="<?php echo $StudentData['LName']; ?>" onchange="ShowHideButton(<?php echo $StudentData['StudentID']; ?>)"></td>
	<td><input type="text" id="FName<?php echo $StudentData['StudentID']; ?>" value="<?php echo $StudentData['FName']; ?>" onchange="ShowHideButton(<?php echo $StudentData['StudentID']; ?>)"></td>
	<td><?php echo CreateSeasonList($DBConn,NULL,$StudentData['NoviceYear'],'NoviceSeason' . $StudentData['StudentID'],'ShowHideButton(' . $StudentData['StudentID'] . ')'); ?></td>
	<td><input type="button" value="Delete Student" onclick="DeleteStudent(<?php echo $StudentData['StudentID']; ?>)"></td>
	<td class="ChangeCell" id="<?php echo $StudentData['StudentID']; ?>"><input type="button" value="Save Changes" onclick="SubmitStudent(<?php echo $StudentData['StudentID']; ?>)"></td>
</tr>
<?php
	$CurrentRow++;
}
?>
<tr>
	<td><input type="text" id="LName"></td>
	<td><input type="text" id="FName"></td>
	<td><?php echo CreateSeasonList($DBConn,NULL,NULL,'NoviceSeason'); ?></td>
	<td><input type="button" value="Create Student" onclick="SubmitStudent()"></td></tr>
</table>
<script>
function ShowHideButton(StudentID) {
	// See if StudentID is set
	StudentID = StudentID || "";
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
	} else if ( !document.getElementById(NoviceSeasonElement).options[document.getElementById(NoviceSeasonElement).selectedIndex].defaultSelected ) {
		DisplayType = "inline";
	} else {
		DisplayType = "none";
	}
	
	// Show/hide button accordingly
	document.getElementById(ButtonElement).style.display = DisplayType;
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
	xmlhttp.open("POST","ManageStudent.php",true);
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
function SubmitStudent(StudentID) {
	// Set default SeasonID
	StudentID = StudentID || -1;
	
	// Declare PostString
	PostString = "";
	
	// Set Element Names
	LastNameElement = "LName";
	FirstNameElement = "FName";
	NoviceSeasonElement = "NoviceSeason";
	if ( StudentID != -1 ) {
		LastNameElement = LastNameElement + StudentID;
		FirstNameElement = FirstNameElement + StudentID;
		NoviceSeasonElement = NoviceSeasonElement + StudentID;
		PostString = "StudentID=" + StudentID + "&";
	}
	
	// Get name and add to PostString
	FName = encodeURIComponent(document.getElementById(FirstNameElement).value);
	LName = encodeURIComponent(document.getElementById(LastNameElement).value);
	PostString = PostString + "FName=" + FName + "&LName=" + LName + "&";
	
	// Get Novice year and add to PostString
	NoviceYear = document.getElementById(NoviceSeasonElement).options[document.getElementById(NoviceSeasonElement).selectedIndex].value;
	PostString = PostString + "NoviceYear=" + NoviceYear;
	
	// Execute Post
	PostToPage(PostString);
}
</script>
</body>
</html>

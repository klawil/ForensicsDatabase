<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Student Management';
require_once 'restrictedpage.inc';

$DoQuery = false;

// Handle student deletion
if ( isset($_POST['delete']) ) {
	// Set StudentID
	if ( !isset($_POST['StudentID']) ) {
		echo 'Student ID is required';
		return 0;
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
	$Variables = ['FName','LName','NoviceYear','StudentID'];
	
	// Set all variables
	foreach ( $Variables as $Name ) {
		if ( isset($_POST[$Name]) ) {
			if ( $_POST[$Name] == '' ) {
				echo $Name . ' is required';
				return 0;
			}
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
	
	// Check for duplicate entry
	if ( !isset($_POST['StudentID']) ) {
		$DupQuery = MySQLQuery($DBConn,'select StudentID from Students where FName="' . $StudentData['FName'] . '" and LName="' . $StudentData['LName'] . '";');
		if ( !$DupQuery['Result'] ) {
			echo $DupQuery['Query'];
			return 0;
		}
		if ( mysqli_num_rows($DupQuery['Query']) != 0 ) {
			echo 'A student with that name already exists';
			return 0;
		}
	}
}

// Handle update
if ( !$DoQuery && isset($_POST['StudentID']) ) {
	// Pull out StudentID
	$StudentID = $StudentData['StudentID'];
	unset($StudentData['StudentID']);
	
	// Create query string
	$StudentString = '';
	foreach ( $StudentData as $Name => $Value ) {
		if ( $StudentString == '' ) {
			$StudentString = 'update Students set ' . $Name . '="' . $Value . '"';
		} else {
			$StudentString = $StudentString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$StudentString = $StudentString . ' where StudentID="' . $StudentID . '";';
	
	// Set flag to execute query
	$DoQuery = true;
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
<div id="PostMessage" class="alert"></div>
<table class="Table">
<tr><th>Last Name</th><th>First Name</th><th>Novice Season</th><th></th><th></th></tr>
<tr>
	<td><span title="Last Name of the student"><input type="text" id="LName" autofocus="autofocus"></span></td>
	<td><span title="First Name of the student"><input type="text" id="FName"></span></td>
	<td><span title="The novice season of the student"><?php echo CreateList($DBConn,'Seasons',NULL,NULL,'NoviceSeason'); ?></span></td>
	<td><span title="Create the student"><input type="button" value="Create Student" onclick="SubmitStudent()"></span></td>
	<td></td>
</tr>
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
	<td><span title="Last Name of the student"><input type="text" id="LName<?php echo $StudentData['StudentID']; ?>" value="<?php echo $StudentData['LName']; ?>" onchange="GetChange(<?php echo $StudentData['StudentID']; ?>)"></span></td>
	<td><span title="First Name of the student"><input type="text" id="FName<?php echo $StudentData['StudentID']; ?>" value="<?php echo $StudentData['FName']; ?>" onchange="GetChange(<?php echo $StudentData['StudentID']; ?>)"></span></td>
	<td><span title="The novice season of the student"><?php echo CreateList($DBConn,'Seasons',NULL,$StudentData['NoviceYear'],'NoviceSeason' . $StudentData['StudentID'],"GetChange(" . $StudentData['StudentID'] . ")"); ?></span></td>
	<td><span title="Delete the student"><input type="button" value="Delete Student" onclick="DeleteStudent(<?php echo $StudentData['StudentID']; ?>)"></span></td>
	<td><span title="Save changes to the student"><input type="button" value="Save Changes" onclick="SubmitStudent(<?php echo $StudentData['StudentID']; ?>)"></span></td>
</tr>
<?php
	$CurrentRow++;
}
?>
</table>
</div>
<script>
// Create array to check for change from
var ChangeArray = ["LName","FName","NoviceSeason"];

// The name of the item that has the name of the row in it
var NameID = "LName";

function DeleteStudent(StudentID) {
	// Check if they are certain
	if ( !window.confirm("DANGER DANGER!!\nThis will PERMANENTLY erase this student.\n\nFOREVER\n\nDo you still want to do this?") ) {
		return 0;
	}
	
	// Create PostString
	PostString = "delete=1&StudentID=" + StudentID;
	
	// Execute Post
	PostToPage(PostString,"ManageStudent.php","PostMessage");
}

function SubmitStudent(StudentID) {
	// Set default StudentID
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
	PostToPage(PostString,"ManageStudent.php","PostMessage");
}
</script>
<?php
if ( !isset($_POST['LoadPage']) ) {
?>
</div>
</body>
</html>
<?php
}
?>

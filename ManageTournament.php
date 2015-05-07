<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Tournament Management';
require_once 'restrictedpage.inc';

$DoQuery = false;

// Handle tournament deletion
if ( isset($_POST['delete']) ) {
	// Set TournamentID
	if ( !isset($_POST['TournamentID']) ) {
		echo 'Tournament ID is required';
		return 0;
	}
	$TournamentData['TournamentID'] = MySQLEscape($_POST['TournamentID'],$DBConn);
	
	// Validate ID
	$CheckArray = [['variable' => 'TournamentID','IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'TournamentID');},'Error' => 'Tournament ID is invalid']];
	$Validation = ValidateArray($TournamentData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Create query string
	$TournamentString = 'delete from Tournaments where TournamentID="' . $TournamentData['TournamentID'] . '";';
	
	// Tell to execute query
	$DoQuery = true;
}

// Create array of POST values
if ( !$DoQuery && (isset($_POST['TournamentID']) || isset($_POST['TournamentName'])) ) {
	// Names of variables to put in array
	$Variables = ['TournamentID', 'TournamentName', 'NumRounds', 'NumJudges', 'NumElimRounds', 'NumElimJudges', 'StartDate', 'EndDate', 'Season'];
	
	// Set all variables
	foreach ( $Variables as $Name ) {
		if ( isset($_POST[$Name]) ) {
			if ( $_POST[$Name] == '' ) {
				echo $Name . ' is required';
				return 0;
			}
			$TournamentData[$Name] = MySQLEscape(urldecode($_POST[$Name]), $DBConn);
		}
	}
	
	// Create testing array
	$CheckArray = [['variable' => 'TournamentID', 'IsSet' => 0, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'TournamentID');}, 'Error' => 'Invalid Tournament ID'],
	['variable' => 'TournamentName', 'IsSet' => 1, 'Validate' => function($var){return IsLength($var,50);}, 'Error' => 'Tournament Name is too long (must be less than 50 characters)'],
	['variable' => 'NumRounds', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Invalid number of rounds'],
	['variable' => 'NumJudges', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Invalid number of judges in each round'],
	['variable' => 'NumElimRounds', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Invalid number of elimination rounds'],
	['variable' => 'NumElimJudges', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Invalid number of judges in each elimination round'],
	['variable' => 'StartDate', 'IsSet' => 1, 'Validate' => function($var){return IsDate($var);}, 'Error' => 'Invalid start date'],
	['variable' => 'EndDate', 'IsSet' => 1, 'Validate' => function($var){return IsDate($var);}, 'Error' => 'Invalid end date'],
	['variable' => 'Season', 'IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'SeasonID');}, 'Error' => 'Invalid season ID']];
	
	// Validate variables
	$Validation = ValidateArray($TournamentData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Check for duplicate entry
	if ( !isset($_POST['TournamentID']) ) {
		$DupQuery = MySQLQuery($DBConn,'select TournamentID from Tournaments where TournamentName="' . $TournamentData['TournamentName'] . '" and StartDate="' . $TournamentData['StartDate'] . '" and EndDate="' . $TournamentData['EndDate'] . '";');
		if ( !$DupQuery['Result'] ) {
			echo $DupQuery['Query'];
			return 0;
		}
		if ( mysqli_num_rows($DupQuery['Query']) != 0 ) {
			echo 'That tournament already exists';
			return 0;
		}
	}
}

// Handle update
if ( !$DoQuery && isset($_POST['TournamentID']) ) {
	// Pull out TournamentID
	$TournamentID = $TournamentData['TournamentID'];
	unset($TournamentData['TournamentID']);
	
	// Create query string
	$TournamentString = '';
	foreach ( $TournamentData as $Name => $Value ) {
		if ( $TournamentString == '' ) {
			$TournamentString = 'update Tournaments set ' . $Name . '="' . $Value . '"';
		} else {
			$TournamentString = $TournamentString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$TournamentString = $TournamentString . ' where TournamentID="' . $TournamentID . '";';
	
	// Set flag to execute query
	$DoQuery = true;
}

// Handle creation
if ( !$DoQuery && isset($_POST['LName']) ) {
	// Create query string
	$TournamentString = '';
	foreach ( $TournamentData as $Name => $Value ) {
		if ( $TournamentString == '' ) {
			$TournamentString = 'insert into Tournaments set ' . $Name . '="' . $Value . '"';
		} else {
			$TournamentString = $TournamentString . ', ' . $Name . '="' . $Value . '"';
		}
	}
	$TournamentString = $TournamentString . ';';
	
	// Set flag to execute query
	$DoQuery = true;
}

// Execute Query
if ( $DoQuery ) {
	$TournamentQuery = MySQLQuery($DBConn,$TournamentString);
	if ( !$TournamentQuery['Result'] ) {
		echo $TournamentQuery['Query'];
		return 0;
	}
	echo 'true';
	return 0;
}

// Create a query of all tournaments
require_once 'header.inc';
?>
<h3>Update, create, and delete students</h3>
<table class="Table">
<tr><th></th><th colspan="2">Prelims</th><th colspan="2">Elims</th></tr>
<tr><th>Tournament Name</th><th>Rounds</th><th>Judges</th><th>Rounds</th><th>Judges</th><th>Start Date</th><th>End Date</th><th>Season</th></tr>
<tr>
	<td><span title="The name of the tournament"><input type="text" id="TournamentName" autofocus="autofocus"></span></td>
	<td><span title="The number of preliminary rounds at the tournament"><input type="number" id="NumRounds"></span></td>
	<td><span title="The maximum number of judges in a preliminary round"><input type="number" id="NumJudges"></span></td>
	<td><span title="The number of elim rounds at the tournament"><input type="number" id="NumElimRounds"></span></td>
	<td><span title="The maximum number of judges in an elimination round"><input type="number" id="NumElimJudges"></span></td>
	<td><span title="The first day of the tournament"><input type="date" id="StartDate"></span></td>
	<td><span title="The last day of the tournament"><input type="date" id="EndDate"></span></td>
	<td><span title="The season the tournament occured during"><?php echo CreateSeasonList($DBConn,NULL,NULL,'Season'); ?></span></td>
	<td><span title="Create a tournament with the specified parameters"><input type="button" value="Create Tournament" onclick="SubmitTournament()"></span></td>
</tr>
<?php
// Set up loop
$NumRows = mysqli_num_rows($TournamentQuery['Query']);
$CurrentRow = 1;

// Loop through the query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$TournamentData = mysqli_fetch_assoc($TournamentQuery['Query']);
	$TournamentID = $TournamentData['TournamentID'];
	
	// Create table row?>
<tr>
	<td><span title="The name of the tournament"><input type="text" id="TournamentName<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['TournamentName']; ?>" autofocus="autofocus"></span></td>
	<td><span title="The number of preliminary rounds at the tournament"><input type="number" id="NumRounds<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumRounds']; ?>"></span></td>
	<td><span title="The maximum number of judges in a preliminary round"><input type="number" id="NumJudges<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumJudges']; ?>"></span></td>
	<td><span title="The number of elim rounds at the tournament"><input type="number" id="NumElimRounds<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumElimRounds']; ?>"></span></td>
	<td><span title="The maximum number of judges in an elimination round"><input type="number" id="NumElimJudges<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumElimJudges']; ?>"></span></td>
	<td><span title="The first day of the tournament"><input type="date" id="StartDate<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['StartDate']; ?>"></span></td>
	<td><span title="The last day of the tournament"><input type="date" id="EndDate<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['EndDate']; ?>"></span></td>
	<td><span title="The season the tournament occured during"><?php echo CreateSeasonList($DBConn,NULL,$TournamentData['Season'],'Season' . $TournamentID); ?></span></td>
	<td><span title="Delete this tournament"><input type="button" value="Delete Tournament" onclick="DeleteTournament(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="Save changes to this tournament"><input type="button" value="Save Changes" onclick="SubmitTournament(<?php echo $TournamentID; ?>)"></span></td>
</tr>
<?php
	$CurrentRow++;
}
?>
</table>
<script>
function PostToPage(PostString) {
	// Encode string
	PostString = encodeURI(PostString);
	
	// Set up post
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","ManageTournament.php",true);
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
function DeleteTournament(TournamentID) {
	// Create PostString
	PostString = "delete=1&TournamentID=" + TournamentID;
	
	// Execute Post
	PostToPage(PostString);
}
function SubmitTournament(TournamentID) {
	// Set default TournamentID
	TournamentID = TournamentID || -1;
	
	// Declare PostString
	PostString = "";
	
	// Set Element Names
	TournamentNameElement = "TournamentName";
	NumRoundsElement = "NumRounds";
	NumJudgesElement = "NumJudges";
	NumElimRoundsElement = "NumElimRounds";
	NumElimJudgesElement = "NumElimJudges";
	StartDateElement = "StartDate";
	EndDateElement = "EndDate";
	SeasonElement = "Season";
	if ( TournamentID != -1 ) {
		TournamentNameElement = "TournamentName" + TournamentID;
		NumRoundsElement = "NumRounds" + TournamentID;
		NumJudgesElement = "NumJudges" + TournamentID;
		NumElimRoundsElement = "NumElimRounds" + TournamentID;
		NumElimJudgesElement = "NumElimJudges" + TournamentID;
		StartDateElement = "StartDate" + TournamentID;
		EndDateElement = "EndDate" + TournamentID;
		SeasonElement = "Season" + TournamentID;
	}
	
	// declare function to get data
	function GetData(Element) {
		return encodeURIComponent(document.getElementById(Element).value);
	}
	
	// Get data
	TournamentName = GetData(TournamentNameElement);
	NumRounds = GetData(NumRoundsElement);
	NumJudges = GetData(NumJudgesElement);
	NumElimRounds = GetData(NumElimRoundsElement);
	NumElimJudges = GetData(NumElimJudgesElement);
	StartDate = GetData(StartDateElement);
	EndDate = GetData(EndDateElement);
	Season = document.getElementById(SeasonElement).options[document.getElementById(SeasonElement).selectedIndex].value;
	
	// Create Post String
	PostString = "TournamentName=" + TournamentName + "&NumRounds=" + NumRounds + "&NumJudges=" + NumJudges + "&NumElimRounds=" + NumElimRounds + "&NumElimJudges=" + NumElimJudges + "&StartDate=" + StartDate + "&EndDate=" + EndDate + "&Season=" + Season;
	
	// Execute Post
	PostToPage(PostString);
}
</script>
</body>
</html>

<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Tournament Management';
require_once 'restrictedpage.inc';

$DoQuery = false;

// Handle tournament deletion
if ( isset($_POST['delete']) ) {
	// Set TournamentID
	if ( !isset($_POST['ID']) ) {
		echo 'Tournament ID is required';
		return 0;
	}
	$TournamentData['TournamentID'] = MySQLEscape($_POST['ID'],$DBConn);
	
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
if ( !$DoQuery && isset($_POST['TournamentName']) ) {
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
$TournamentQuery = MySQLQuery($DBConn,'select Tournaments.TournamentName, Tournaments.NumRounds, Tournaments.NumJudges, Tournaments.NumElimRounds, Tournaments.NumElimJudges, Tournaments.StartDate, Tournaments.EndDate, Tournaments.Season, Tournaments.TournamentID from Tournaments, Seasons where Tournaments.Season = Seasons.SeasonID order by Seasons.StartYear desc, Tournaments.StartDate desc, Tournaments.EndDate desc, Tournaments.TournamentName;');
if ( !$TournamentQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $TournamentQuery['Query'];
	require_once 'ErrorPage.inc';
}

// Create header
require_once 'header.inc';
?>
<style>
input[type=number] {
	width: 3em;
}
input[type=date] {
	width: 7em;
}
.unstyled::-webkit-inner-spin-button, .unstyled::-webkit-calendar-picker-indicator {
	display: none;
	-webkit-appearance: none;
}
</style>
<h3>Update, create, and delete tournaments</h3>
<div id="PostMessage" class="alert"></div>
<table class="Table">
<tr><th></th><th colspan="2" class="text-center">Prelims</th><th colspan="2" class="text-center">Elims</th><th></th><th></th><th></th><th></th><th></th></tr>
<tr><th>Tournament Name</th><th>Rounds</th><th>Judges</th><th>Rounds</th><th>Judges</th><th>Start Date</th><th>End Date</th><th>Season</th><th></th><th></th></tr>
<tr>
	<td><span title="The name of the tournament"><input type="text" id="TournamentName" autofocus="autofocus"></span></td>
	<td><span title="The number of preliminary rounds at the tournament"><input type="number" id="NumRounds"></span></td>
	<td><span title="The maximum number of judges in a preliminary round"><input type="number" id="NumJudges"></span></td>
	<td><span title="The number of elim rounds at the tournament"><input type="number" id="NumElimRounds"></span></td>
	<td><span title="The maximum number of judges in an elimination round"><input type="number" id="NumElimJudges"></span></td>
	<td><span title="The first day of the tournament"><input type="date" id="StartDate" class="unstyled"></span></td>
	<td><span title="The last day of the tournament"><input type="date" id="EndDate" class="unstyled"></span></td>
	<td><span title="The season the tournament occured during"><?php echo CreateList($DBConn,'Seasons',NULL,NULL,'Season'); ?></span></td>
	<td><span title="Create a tournament with the specified parameters"><input type="button" value="Create Tournament" onclick="SubmitChange()"></span></td>
	<td></td>
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
	<td><span title="The name of the tournament"><input type="text" id="TournamentName<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['TournamentName']; ?>" onchange="GetChange(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="The number of preliminary rounds at the tournament"><input type="number" id="NumRounds<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumRounds']; ?>" onchange="GetChange(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="The maximum number of judges in a preliminary round"><input type="number" id="NumJudges<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumJudges']; ?>" onchange="GetChange(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="The number of elim rounds at the tournament"><input type="number" id="NumElimRounds<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumElimRounds']; ?>" onchange="GetChange(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="The maximum number of judges in an elimination round"><input type="number" id="NumElimJudges<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['NumElimJudges']; ?>" onchange="GetChange(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="The first day of the tournament"><input type="date" class="unstyled" id="StartDate<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['StartDate']; ?>" onchange="GetChange(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="The last day of the tournament"><input type="date" class="unstyled" id="EndDate<?php echo $TournamentID; ?>" value="<?php echo $TournamentData['EndDate']; ?>" onchange="GetChange(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="The season the tournament occured during"><?php echo CreateList($DBConn,'Seasons',NULL,$TournamentData['Season'],'Season' . $TournamentID,"GetChange(" . $TournamentID .")"); ?></span></td>
	<td><span title="Delete this tournament"><input type="button" value="Delete Tournament" onclick="DeleteID(<?php echo $TournamentID; ?>)"></span></td>
	<td><span title="Save changes to this tournament"><input type="button" value="Save Changes" onclick="SubmitChange(<?php echo $TournamentID; ?>)"></span></td>
</tr>
<?php
	$CurrentRow++;
}
?>
</table>
</div>
<script>
// Create array to check for change from
var ChangeArray = ["TournamentName","NumRounds","NumJudges","NumElimRounds","NumElimJudges","StartDate","EndDate","Season"];

// The name of the item that has the name of the row in it
var NameID = "TournamentName";

// Create object to have the info pulled from
var StoreInfo = {TName: {Name: "TournamentName", ElementID: "TournamentName", IsID: false},
	NumRounds: {Name: "NumRounds", ElementID: "NumRounds", IsID: false},
	NumJudges: {Name: "NumJudges", ElementID: "NumJudges", IsID: false},
	NumElimRounds: {Name: "NumElimRounds", ElementID: "NumElimRounds", IsID: false},
	NumElimJudges: {Name: "NumElimJudges", ElementID: "NumElimJudges", IsID: false},
	StartDate: {Name: "StartDate", ElementID: "StartDate", IsID: false},
	EndDate: {Name: "EndDate", ElementID: "EndDate", IsID: false},
	SeasonID: {Name: "Season", ElementID: "Season", IsID: false},
	TournamentID: {Name: "TournamentID", IsID: true}};

// Page location
var PageLocation = "/ManageTournament.php";
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

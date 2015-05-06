<?php
function MySQLEscape($String,$DBConn = Null) {
	// Escapes a string to prevent SQLi
	// @param $String - the string to escape
	// @param $DBConn - connection to the MySQL database
	// @return escaped version of the string
	if ( !$DBConn ) {
		$DBConn = NULL;
	}
	if ( $DBConn == Null ) {
		$DBConn = mysqli_connect();
	}
	return mysqli_real_escape_string($DBConn,$String);
}
function MySQLQuery($DBConn,$QueryString) {
	// Executes a query
	// @param $DBConn - connection to the MySQL database
	// @param $QueryString - the query to execute
	// @return [Array<Result, Query>]
	//  Result - boolean indicating whether the query was successful
	//  Query - either the query object or the error string
	if ( !$DBConn ){
		$ReturnArray['Result'] = false;
		$ReturnArray['Query'] = 'Invalid DB Connection';
	} else {
		$Query = mysqli_query($DBConn,$QueryString);
		if ( !$Query ) {
			$ReturnArray['Result'] = false;
			$ReturnArray['Query'] = mysqli_error($DBConn);
		} else {
			$ReturnArray['Result'] = true;
			$ReturnArray['Query'] = $Query;
		}
	}
	return $ReturnArray;
}
function CreateTournamentList($DBConn, $IncludeAll = NULL, $DefaultTournamentID = NULL) {
	// Creates an html list of all the tournaments sorted by date
	// @param $DBConn - connection to MySQL database
	// @param $IncludeAll - Include an option that is "All Tournaments"
	// @param $DefaultTournamentID - the TournamentID to set as the default value
	// @return String - html code for tournament select
	
	// Get list of tournaments
	$TQuery = MySQLQuery($DBConn, 'select TournamentName, TournamentID from Tournaments order by Date desc, TournamentName;');
	if ( !$TQuery['Result'] ) {
		return $TQuery['Query'];
	}
	
	// Loop through all tournaments
	$NumRows = mysqli_num_rows($TQuery['Query']);
	$CurrentRow = 0;
	$TournamentString = '<select id="TournamentID" name="TournamentID">';
	if ( $IncludeAll == 1 ) {
		$TournamentString = $TournamentString . '<option value="-1">All Tournaments</option>';
	}
	while ( $CurrentRow < $NumRows ) {
		$Tournament = mysqli_fetch_assoc($TQuery['Query']);
		if ( $Tournament['TournamentID'] == $DefaultTournamentID ) {
			$TournamentString = $TournamentString . '<option selected="selected" value="' . $Tournament['TournamentID'] . '">' . $Tournament['TournamentName'] . '</option>';
		} else {
			$TournamentString = $TournamentString . '<option value="' . $Tournament['TournamentID'] . '">' . $Tournament['TournamentName'] . '</option>';
		}
		$CurrentRow++;
	}
	$TournamentString = $TournamentString . '</select>';
	return $TournamentString;
}
function CreateStudentList($DBConn, $IncludeAll = NULL, $DefaultStudentID = NULL, $SelectName = NULL, $OnChange = NULL, $FormName = NULL) {
	// Creates an html list of all the Students sorted by last name
	// @param $DBConn - connection to MySQL database
	// @param $IncludeAll - Include an option that is "All Students"
	// @param $DefaultStudentID - the StudentID to set as the default value
	// @param $SelectName - What to set the "id" and "name" attributes
	// @param $OnChange - What function to call when the value changes
	// @param $FormName - What to set the "form" attribute
	// @return String - html code for student select
	
	// Get student information
	$SQuery = MySQLQuery($DBConn, 'select FName, LName, StudentID from Students, Seasons where Students.NoviceYear = Seasons.SeasonID order by Seasons.StartYear desc, Students.LName, Students.FName;');
	if ( !$SQuery['Result'] ) {
		return $SQuery['Query'];
	}
	
	// Loop through all students
	$NumRows = mysqli_num_rows($SQuery['Query']);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$SelectName = 'StudentID';
	}
	$StudentString = '<select id="' . $SelectName . '" name="' . $SelectName . '"';
	if ( $FormName != NULL ) {
		$StudentString = $StudentString . ' form="' . $FormName . '"';
	}
	if ( $OnChange != NULL ) {
		$StudentString = $StudentString . ' onchange="' . $OnChange . '"';
	}
	$StudentString = $StudentString . '>';
	if ( $IncludeAll == 1 ) {
		$StudentString = $StudentString . '<option value="-1">All Students</option>';
	}
	while ( $CurrentRow < $NumRows ) {
		$Student = mysqli_fetch_assoc($SQuery['Query']);
		if ( $Student['StudentID'] == $DefaultStudentID ) {
			$StudentString = $StudentString . '<option selected="selected" value="' . $Student['StudentID'] . '">' . $Student['LName'] . ', ' . $Student['FName'] . '</option>';
		} else {
			$StudentString = $StudentString . '<option value="' . $Student['StudentID'] . '">' . $Student['LName'] . ', ' . $Student['FName'] . '</option>';
		}
		$CurrentRow++;
	}
	$StudentString = $StudentString . '</select>';
	return $StudentString;
}
function CreateEventList($DBConn, $IncludeAll = NULL, $DefaultEventID = NULL, $SelectName = NULL, $OnChange = NULL) {
	// Creates an html list of all the Events sorted by name
	// @param $DBConn - connection to MySQL database
	// @param $IncludeAll - Include an option that is "All Events"
	// @param $DefaultEventID - the EventID to set as the default value
	// @param $SelectName - What to set the "id" and "name" attributes
	// @param $OnChange - What function to call when the value changes
	// @return String - html code for event select
	
	// Get event information
	$EQuery = MySQLQuery($DBConn, 'select * from Events order by EventName;');
	if ( !$EQuery['Result'] ) {
		return $EQuery['Query'];
	}
	
	// Loop through all events
	$NumRows = mysqli_num_rows($EQuery['Query']);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$SelectName = 'EventID';
	}
	$EventString = '<select id="' . $SelectName . '" name="' . $SelectName . '"';
	if ( $OnChange != NULL ) {
		$EventString = $EventString . ' onchange="' . $OnChange . '"';
	}
	$EventString = $EventString . '>';
	if ( $IncludeAll == 1 ) {
		$EventString = $EventString . "<option value='-1'>All Events</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$Event = mysqli_fetch_assoc($EQuery['Query']);
		if ( $Event['EventID'] == $DefaultEventID ) {
			$EventString = $EventString . '<option selected="selected" value="' . $Event['EventID'] . '">' . $Event['EventName'] . '</option>';
		} else {
			$EventString = $EventString . '<option value="' . $Event['EventID'] . '">' . $Event['EventName'] . '</option>';
		}
		$CurrentRow++;
	}
	$EventString = $EventString . '</select>';
	return $EventString;
}
function CreateSeasonList($DBConn, $IncludeAll = NULL, $DefaultSeasonID = NULL, $SelectName = NULL, $OnChange = NULL) {
	// Creates an html list of all the Seasons sorted by year
	// @param $DBConn - connection to MySQL database
	// @param $IncludeAll - Include an option that is "All Seasons"
	// @param $DefaultSeasonID - the SeasonID to set as the default value
	// @param $SelectName - What to set the "id" and "name" attributes
	// @param $OnChange - What function to call when the value changes
	// @return String - html code for season select
	
	// Get event information
	$SQuery = MySQLQuery($DBConn, 'select * from Seasons order by StartYear desc;');
	if ( !$SQuery['Result'] ) {
		return $SQuery['Query'];
	}
	
	// Loop through all events
	$NumRows = mysqli_num_rows($SQuery['Query']);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$SelectName = 'SeasonID';
	}
	$SeasonString = '<select id="' . $SelectName . '" name="' . $SelectName . '"';
	if ( $OnChange != NULL ) {
		$SeasonString = $SeasonString . ' onchange="' . $OnChange . '"';
	}
	$SeasonString = $SeasonString . '>';
	if ( $IncludeAll == 1 ) {
		$SeasonString = $SeasonString . "<option value='-1'>All Seasons</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$Season = mysqli_fetch_assoc($SQuery['Query']);
		if ( $Season['SeasonID'] == $DefaultSeasonID ) {
			$SeasonString = $SeasonString . '<option selected="selected" value="' . $Season['SeasonID'] . '">' . $Season['SeasonName'] . '</option>';
		} else {
			$SeasonString = $SeasonString . '<option value="' . $Season['SeasonID'] . '">' . $Season['SeasonName'] . '</option>';
		}
		$CurrentRow++;
	}
	$SeasonString = $SeasonString . '</select>';
	return $SeasonString;
}

// Set up connection to MySQL database
$UN = 'forensics';
$PW = 'A15j89%%8JsTk991LexzQ#'; // Credentials to connect to MySQL
$HOST = 'localhost';
$GLOBALS['DBName'] = array_shift(explode('.',$_SERVER['HTTP_HOST'])); // DB Name - set to the subdomain
if ( $GLOBALS['DBName'] == 'www' ) { // if they use www redirect to the same site minus the www
	$HostName = explode('.',$_SERVER['HTTP_HOST']);
	array_shift($HostName);
	$HostName = implode('.',$HostName);
	header('Location: ' . $HostName);
}

// Get information about the subdomain they are using
$FDBConn = mysqli_connect($HOST,$UN,$PW,'Schools');
if ( !$FDBConn ) {
	$GLOBALS['ErrorMessage'] = 'Connection failed: ' . mysqli_connect_error();
	include 'ErrorPage.inc';
}
$Query = MySQLQuery($FDBConn,'select * from Subdomains where Subdomain="' . $GLOBALS['DBName'] . '";');
if ( ! $Query['Result'] ) {
	$GLOBALS['ErrorMessage'] = $Query['Query'];
	include 'ErrorPage.inc';
} elseif ( mysqli_num_rows($Query['Query']) == 0 ) {
	include '404.html';
	return 0;
} elseif ( mysqli_num_rows($Query['Query']) > 1 ) {
	$GLOBALS['ErrorMessage'] = 'Impossible error: dulicate primary keys in subdomain table';
	include 'ErrorPage.inc';
}
$GLOBALS['DBData'] = mysqli_fetch_assoc($Query['Query']);
$GLOBALS['DBName'] = $GLOBALS['DBData']['DBName'];

// *********************************************************************
// Future: Add ability to prevent people from seeing protected subdomain
// *********************************************************************

mysqli_close($FDBConn);
$DBConn = mysqli_connect($HOST,$UN,$PW,$GLOBALS['DBName']);
if ( ! $DBConn ) {
	$GLOBALS['ErrorMessage'] = 'Connection failed: ' . mysqli_connect_error();
	require_once 'ErrorPage.inc';
}
?>

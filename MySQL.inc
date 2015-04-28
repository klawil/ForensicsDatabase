<?php
function MySQLEscape($String,$DBConn = Null) {
	// Escapes a string to prevent SQLi
	// @param $String - the string to escape
	// @param $DBConn - connection to the MySQL database
	// @return escaped version of the string
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
	if ( !is_resource($DBConn) ){
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
function CreateTournamentList($DBConn, $IncludeAll = NULL, $DefaultTID = NULL) {
	// Creates an html list of all the tournaments sorted by date
	// $DBConn - connection to MySQL database
	// $IncludeAll - Include an option that is "All Tournaments"
	// $DefaultTID - the TID to set as the default value
	$TQuery = mysqli_query($DBConn, 'select TName, TID from Tournaments order by Date desc, TName;');
	if ( !$TQuery ) {
		return ReturnMySQLError($DBConn);
	}
	$NumRows = mysqli_num_rows($TQuery);
	$CurrentRow = 0;
	$TournamentString = '<select id="TID" name="TID">';
	if ( $IncludeAll == 1 ) {
		$TournamentString = $TournamentString . '<option value="-1">All Tournaments</option>';
	}
	while ( $CurrentRow < $NumRows ) {
		$Tournament = mysqli_fetch_assoc($TQuery);
		if ( $Tournament['TID'] == $DefaultTID ) {
			$TournamentString = $TournamentString . '<option selected="selected" value="' . $Tournament['TID'] . '">' . $Tournament['TName'] . '</option>';
		} else {
			$TournamentString = $TournamentString . '<option value="' . $Tournament['TID'] . '">' . $Tournament['TName'] . '</option>';
		}
		$CurrentRow++;
	}
	$TournamentString = $TournamentString . '</select>';
	return $TournamentString;
}
function CreateStudentList($DBConn, $IncludeAll = NULL, $DefaultSID = NULL, $SelectName = NULL, $OnChange = NULL, $FormName = NULL) {
	// Creates an html list of all the Students sorted by last name
	// $DBConn - connection to MySQL database
	// $IncludeAll - Include an option that is "All Tournaments"
	// $DefaultSID - the SID to set as the default value
	// $SelectName - What to set the "id" and "name" attributes
	// $OnChange - What function to call when the value changes
	// $FormName - What to set the "form" attribute
	$SQuery = mysqli_query($DBConn, 'select FName, LName, SID from Students order by LName, FName;');
	if ( !$SQuery ) {
		return ReturnMySQLError($DBConn);
	}
	$NumRows = mysqli_num_rows($SQuery);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$SelectName = 'SID';
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
		$results = mysqli_fetch_assoc($SQuery);
		if ( $results['SID'] == $DefaultSID ) {
			$StudentString = $StudentString . '<option selected="selected" value="' . $results['SID'] . '">' . $results['LName'] . ', ' . $results['FName'] . '</option>';
		} else {
			$StudentString = $StudentString . '<option value="' . $results['SID'] . '">' . $results['LName'] . ', ' . $results['FName'] . '</option>';
		}
		$CurrentRow++;
	}
	$StudentString = $StudentString . '</select>';
	return $StudentString;
}
function CreateEventList($DBConn, $IncludeAll = NULL, $DefaultEID = NULL, $SelectName = NULL, $OnChange = NULL) {
	// Creates an html list of all the Events sorted by name
	// $DBConn - connection to MySQL database
	// $IncludeAll - Include an option that is "All Tournaments"
	// $DefaultEID - the EID to set as the default value
	// $SelectName - What to set the "id" and "name" attributes
	// $OnChange - What function to call when the value changes
	$EQuery = mysqli_query($DBConn, 'select * from Events order by EName;');
	if ( !$EQuery ) {
		return ReturnMySQLError($DBConn);
	}
	$NumRows = mysqli_num_rows($EQuery);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$SelectName = 'EID';
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
		$results = mysqli_fetch_assoc($EQuery);
		if ( $results['EID'] == $DefaultEID ) {
			$EventString = $EventString . '<option selected="selected" value="' . $results['EID'] . '">' . $results['EName'] . '</option>';
		} else {
			$EventString = $EventString . '<option value="' . $results['EID'] . '">' . $results['EName'] . '</option>';
		}
		$CurrentRow++;
	}
	$EventString = $EventString . '</select>';
	return $EventString;
}
$UN = 'forensics';
$PW = 'A15j89%%8JsTk991LexzQ#'; // Credentials to connect to MySQL
$HOST = 'localhost';
$GLOBALS['DBName'] = array_shift(explode('.',$_SERVER['HTTP_HOST'])); // DB Name - set to the subdomain
if ( $GLOBALS['DBName'] == 'www' ) {
	$HostName = explode('.',$_SERVER['HTTP_HOST']);
	array_shift($HostName);
	$HostName = implode('.',$HostName);
	header('Location: ' . $HostName);
}
$FDBConn = mysqli_connect($HOST,$UN,$PW,'Forensics');
if ( ! $FDBConn ) {
	$GLOBALS['ErrorMessage'] = 'Connection failed: ' . mysqli_connect_error();
	include 'ErrorPage.inc';
}
$Query = MySQLQuery($FDBConn,"select * from Subdomains where Subdomain='" . $GLOBALS['DBName'] . "';");
if ( ! $Query['Result'] ) {
	$GLOBALS['ErrorMessage'] = $Query['Query'];
	include 'ErrorPage.inc';
} elseif ( mysqli_num_rows($Query['Query']) == 0 ) {
	include '404.html';
	return 0;
}
$Data = mysqli_fetch_assoc($Query);
mysqli_close($FDBConn);
$DBConn = mysqli_connect($HOST,$UN,$PW,$GLOBALS['DBName']);
if ( ! $DBConn ) {
	$GLOBALS['ErrorMessage'] = 'Connection failed: ' . mysqli_connect_error();
	include 'ErrorPage.inc';
}
?>

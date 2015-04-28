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
$UN = 'forensics';
$PW = 'A15j89%%8JsTk991LexzQ#'; // Credentials to connect to MySQL
$HOST = 'localhost';
$GLOBALS['DBName'] = array_shift((explode('.',$_SERVER['HTTP_HOST']))); // DB Name - set to the subdomain
if ( $GLOBALS['DBName'] == 'www' ) {
	header('Location: forensicsdb.com');
}
$FDBConn = mysqli_connect($HOST,$UN,$PW,'Forensics');
$Query = MySQLQuery($FDBConn,"select * from Subdomains where Subdomain='" . $GLOBALS['DBName'] . "';");
if ( ! $Query['Result'] ) {
	$GLOBALS['ErrorMessage'] = $Query['Query'];
	include 'ErrorPage.inc';
} elseif ( mysqli_num_rows($Query['Query']) == 0 ) {
	include '404.html';
	return 0;
}
?>

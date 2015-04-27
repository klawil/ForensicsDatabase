<?php
include 'MySQLAuth.php'; // File that connects to MySQL database
$GLOBALS['CookieName'] = 'forensics_db_auth_token'; // Name of the cookie used to make login persistent
$GLOBALS['SecretWord'] = 'ForensicsSECRET'; // MD5'd with username to cookie name
$GLOBALS['UserName'] = ''; // Stores the username
$GLOBALS['CanUserEdit'] = 0; // Stores the admin ability of the user
function InsertBallots($Ballots, $DBConn = NULL, $Insert = NULL) {
	// Inserts Ballots from an array
	// @param $Ballots: [RID: RID,
	//  Round: Round,
	//  Judge: Judge,
	//  ElimLevel: ElimLevel,
	//  Rank: Rank,
	//  Qual: Qual]
	// @param $DBConn - connection to MySQL database
	// @param $Insert - if set, insert/updates the ballots
	$ReturnValue = ''; // Value to return at the end
	$Queries = ''; // List of queries to execute at the end
	function IsInt($String){
		return (string)(int)$String == $String;
	}
	
	// Array of variables and values to check
	$CheckArray = [['variable' => 'RID', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'RID not valid'],
	['variable' => 'Round', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Round not valid'],
	['variable' => 'Judge', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Judge not valid'],
	['variable' => 'ElimLevel', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Elimination level not valid'],
	['variable' => 'Rank', 'IsSet' => 1, 'Validate' => function($var){return IsInt($var);}, 'Error' => 'Rank not valid'],
	['variable' => 'Qual', 'IsSet' => 0, 'Validate' => function($var){return is_numeric($var);}, 'Error' => 'Qual not valid']];
	
	// Loop through all ballots
	foreach ($Ballots as $key=>$Ballot) {
		// Check variables
		foreach ( $CheckArray as $Check) {
			if ( !isset($Ballot[$Check['variable']]) ) {
				if ( $Check['IsSet'] == 1 ) {
					$ReturnValue[$key] = $Check['variable'] . ' not set';
					continue 2;
				}
			} elseif ( !$Check['Validate']($Ballot[$Check['variable']]) ) {
				$ReturnValue[$key] = $Check['Error'];
				continue 2;
			}
		}
		
		// Insert data if desired
		if ( $Insert != NULL ) {
			if ( ! isset($Ballot['Qual']) ) {
				$Ballot['Qual'] = '';
			}
			
			// Check if ballot already exists
			$ExistsString = 'select * from Ballots where RID="' . MySQLEscape($DBConn,$Ballot['RID']) . '" and Round="' . MySQLEscape($DBConn,$Ballot['Round']) . '" and Judge="' . MySQLEscape($DBConn,$Ballot['Judge']) . '";';
			$ExistsQuery = MySQLQuery($DBConn,$ExistsString);
			if ( ! $ExistsQuery ) {
				$ReturnValue[$key] = $ExistsQuery;
				continue
			}
			if ( mysqli_num_rows($ExistsQuery) == 0 ) {
				// Update query if exists
				$QueryString = 'insert into Ballots set RID="' . MySQLEscape($DBConn,$Ballot['RID']) . '", Round="' . MySQLEscape($DBConn,$Ballot['Round']) . '", Judge="' . MySQLEscape($DBConn,$Ballot['Judge']) . '", Rank="' . MySQLEscape($DBConn,$Ballot['Rank']) . '", Qual="' . MySQLEscape($DBConn,$Ballot['Qual']) . '", ElimLevel="' . MySQLEscape($DBConn,$Ballot['ElimLevel']) . '";';
			} else {
				// Insert query if doesn't exist
				$QueryString = 'update Ballots set Rank="' . MySQLEscape($DBConn,$Ballot['Rank']) . '", Qual="' . MySQLEscape($DBConn,$Ballot['Qual']) . '", ElimLevel="' . MySQLEscape($DBConn,$Ballot['ElimLevel']) . '" where RID="' . MySQLEscape($DBConn,$Ballot['RID']) . '" and Round="' . MySQLEscape($DBConn,$Ballot['Round']) . '" and Judge="' . MySQLEscape($DBConn,$Ballot['Judge']) . '";';
			}
			$Query = MySQLQuery($DBConn,$QueryString);
			if ( $Query ) {
				$ReturnValue[$key] = true;
			} else {
				$ReturnValue[$key] = $Query;
			}
		} else {
			$ReturnValue[$key] = true;
		}
	}
	return $ReturnValue;
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
function CheckAuthorization() {
	// Checks if the user has cookie authorization
	if ( isset($_COOKIE[$GLOBALS['CookieName']]) ) { do {
		$Array = explode(',',$_COOKIE[$GLOBALS['CookieName']],2);
		$GLOBALS['UserName'] = $Array[0];
		$Cookie = $Array[1];
		$query = mysqli_query($GLOBALS['DBConn'], "select cookie, cookieExp, CanMod, concat(LName, ', ', FName) as Name from users where UName='" . $GLOBALS['UserName'] . "';");
		if ( !$query ) {
			break;
		}
		$Data = mysqli_fetch_assoc($query);
		if ( $Cookie != $Data['cookie'] ) {
			setcookie($GLOBALS['CookieName'], "", time() - 3600);
			$GLOBALS['UserName'] = "";
			break;
		} elseif ( time() > $Data['cookieExp'] ) {
			setcookie($GLOBALS['CookieName'], "", time() - 3600);
			$GLOBALS['UserName'] = "";
			break;
		}
		$GLOBALS['UserName'] = $Data['Name'];
		$GLOBALS['CanUserEdit'] = $Data['CanMod'];
	} while (false); }
	$myfile = fopen("/var/log/forensics/general.log","a");
	if ( $GLOBALS['UserName'] != "" ) {
		fwrite($myfile, "User " . $GLOBALS['UserName'] . " from ");
	}
	fwrite($myfile, "IP " . $_SERVER['REMOTE_ADDR'] . " accessed " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
	fclose($myfile);
}
function SetAuthCookie($UN) {
	// Sets an authoriztion cookie
	// $UN - the username to set the cookie for
	$ExpDate = time() + (86400 * 7);
	$MD5 = md5($UN . $GLOBALS['SecretWord'] . $ExpDate);
	$Cookie = $UN . "," . $MD5;
	$query = mysqli_query($GLOBALS['DBConn'], "update users set cookie='" . $MD5 . "', cookieExp='" . $ExpDate . "' where UName='" . $UN . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	setcookie($GLOBALS['CookieName'], $Cookie, $ExpDate, "/");
	return 1;
}
function WriteToLog($LogString = Null) {
	// Writes a log file
	// $LogString - String to use for the log file
	if ( $LogString == Null ) {
		$LogString = '';
		if ( $GLOBALS['UserName'] != '' ) {
			$LogString = 'User ' . $GLOBALS['UserName'] . ' from ';
		}
		$LogString = $LogString . 'IP ' . $_SERVER['REMOTE_ADDR'] . ' accessed ' . basename($_SERVER['PHP_SELF']);
	}
	$LogFile = fopen("/var/log/forensics/general.log","a");
	fwrite($LogFile,$LogString . ' on ' . date('Y-m-d') . ' at ' . date('H:i:s') . "\n");
}
function MySQLEscape($DBConn,$String) {
	return mysqli_real_escape_string($DBConn,$String);
}
function MySQLQuery($DBConn,$QueryString) {
	$Query = mysqli_query($DBConn,$QueryString);
	if ( !$Query ) {
		return mysqli_error($DBConn);
	} else {
		return $Query;
	}
}
function ReturnMySQLError($DBConn, $CustomText = NULL) {
	// Returns a MySQL error string
	// $DBConn - connection to MySQL database
	// $CustomText - Custom text to put in the error string
	if ( $CustomText == NULL ) {
		$CustomText = 'Error - MySQL error: ';
	}
	$CustomText = $CustomText . mysqli_error($DBConn) . '.';
	return $CustomText;
}
CheckAuthorization();
?>

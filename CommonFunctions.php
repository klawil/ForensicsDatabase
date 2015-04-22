<?php
$GLOBALS['DBName'] = 'kmc'; // Name of the database
include 'MySQLAuth.php'; // File that connects to MySQL database
$GLOBALS['CookieName'] = 'forensics_db_auth_token'; // Name of the cookie used to make login persistent
$GLOBALS['SecretWord'] = 'ForensicsSECRET'; // MD5'd with username to cookie name
$GLOBALS['UserName'] = ''; // Stores the username
$GLOBALS['CanUserEdit'] = 0; // Stores the admin ability of the user
function InsertBallots($Ballots, $DBConn = NULL, $Insert = NULL) {
	// Inserts Ballots from an array
	// $Ballots: [RID: RID,
	//  Round: Round,
	//  Judge: Judge,
	//  ElimLevel: ElimLevel,
	//  Rank: Rank,
	//  Qual: Qual]
	// $DBConn - connection to MySQL database
	// $Insert - if set, insert/updates the ballots
	$ReturnValue = 'true'; // Value to return at the end
	$Queries = ''; // List of queries to execute at the end
	foreach ($Ballots as $key=>$Ballot) {
		// Loop through all the ballots
		if ( ! isset($Ballot['RID']) ) {
			// Check if RID is set
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No RID for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No RID for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Round']) ) {
			// Check if a Round is set
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No round given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No round given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Judge']) ) {
			// Check if a Judge is set
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No judge given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No judge given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['RID'] == $Ballot['RID'] ) {
			// Check if RID is an int
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid RID for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid RID for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Round'] == $Ballot['Round'] ) {
			// Check if Round is an int
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid round given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid round given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Judge'] == $Ballot['Judge'] ) {
			// Check if Judge is an int
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid judge given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid judge given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['ElimLevel']) ) {
			// Check if an ElimLevel is set
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No level set for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No level set for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['ElimLevel'] == $Ballot['ElimLevel'] ) {
			// Check if ElimLevel is an int
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invald level for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invald level for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Rank']) ) {
			// Check if Rank is set
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No rank given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No rank given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Rank'] == $Ballot['Rank'] ) {
			// Check if Rank is an int
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid rank given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid rank given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( isset($Ballot['Qual']) && ! is_numeric($Ballot['Qual'])) {
			// Check if Qual is set and if so if Qual is numeric
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid quality points given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid quality points given for one of the ballots - '.$key;
			}
			continue;
		}
		if ( $Insert != NULL ) {
			// If insert is to be done
			if ( ! isset($Ballot['Qual']) ) {
				$Ballot['Qual'] = '';
			}
			if ( ! isset($Ballot['ElimLevel']) ) {
				$Ballot['ElimLevel'] = 0;
			}
			$ConnectVar = mysqli_connect();
			// Check if ballot already exists
			$ExistsQuery = mysqli_query($DBConn,'select * from Ballots where RID="'.mysqli_real_escape_string($ConnectVar,$Ballot['RID']).'" and Round="'.mysqli_real_escape_string($ConnectVar,$Ballot['Round']).'" and Judge="'.mysqli_real_escape_string($ConnectVar,$Ballot['Judge']).'";');
			if ( !$ExistsQuery ) {
				if ( $ReturnValue == 'true' ) {
					$ReturnValue = $key . ': ' . ReturnMySQLError($DBConn,'Exists Query Error: ');
				} else {
					$ReturnValue = $ReturnValue . '; ' . $key . ': ' . ReturnMySQLError($DBConn,'Exists Query Error: ');
				}
				continue;
			}
			if ( mysqli_num_rows($ExistsQuery) == 0 ) {
				// Update query if exists
				$QueryString = 'insert into Ballots set RID="'.mysqli_real_escape_string($ConnectVar,$Ballot['RID']).'", Round="'.mysqli_real_escape_string($ConnectVar,$Ballot['Round']).'", Judge="'.mysqli_real_escape_string($ConnectVar,$Ballot['Judge']).'", Rank="'.mysqli_real_escape_string($ConnectVar,$Ballot['Rank']).'", Qual="'.mysqli_real_escape_string($ConnectVar,$Ballot['Qual']).'", ElimLevel="'.mysqli_real_escape_string($ConnectVar,$Ballot['ElimLevel']).'";';
			} else {
				// Insert query if doesn't exist
				$QueryString = 'update Ballots set Rank="'.mysqli_real_escape_string($ConnectVar,$Ballot['Rank']).'", Qual="'.mysqli_real_escape_string($ConnectVar,$Ballot['Qual']).'", ElimLevel="'.mysqli_real_escape_string($ConnectVar,$Ballot['ElimLevel']).'" where RID="'.mysqli_real_escape_string($ConnectVar,$Ballot['RID']).'" and Round="'.mysqli_real_escape_string($ConnectVar,$Ballot['Round']).'" and Judge="'.mysqli_real_escape_string($ConnectVar,$Ballot['Judge']).'";';
			}
			$Queries[$key] = $QueryString; // Save queries
		}
	}
	if ( $ReturnValue == 'true' && $Insert != NULL ) {
		// Only perform queries if no errors and Insert is set
		foreach ( $Queries as $key=>$QueryString ) {
			// Loop through all queries
			$InsertQuery = mysqli_query($DBConn,$QueryString);
			if ( !$InsertQuery ) {
				if ( $ReturnValue == 'true' ) {
					$ReturnValue = $key . ': ' . ReturnMySQLError($DBConn,'Insert Query Error: ');
				} else {
					$ReturnValue = $ReturnValue . '; ' . $key . ': ' . ReturnMySQLError($DBConn,'Insert Query Error: ');
				}
			}
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
		$StudentString = $StudentString . '<option value='-1'>All Students</option>';
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
function ReturnMySQLError($DBConn, $CustomText = NULL) {
	// Returns a MySQL error string
	// $DBConn - connection to MySQL database
	// $CustomText - Custom text to put in the error string
	if ( $CustomText == NULL ) {
		$CustomText = 'Error - MySQL error: ';
	}
	$ReturnString = $ReturnString . mysqli_error($DBConn) . '.';
	return $ReturnString;
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
Authorize();
?>

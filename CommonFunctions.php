<?php
$GLOBALS['DBName'] = 'kmc'; //Name of the database
include 'MySQLAuth.php'; //File that connects to MySQL database
$GLOBALS['CookieName'] = 'forensics_db_auth_token'; //Name of the cookie used to make login persistent
$GLOBALS['SecretWord'] = 'ForensicsSECRET'; //MD5'd with username to cookie name
$GLOBALS['UserName'] = ''; //Stores the username
$GLOBALS['CanUserEdit'] = 0; //Stores the admin ability of the user
function InsertBallots($Ballots, $DBConn = NULL, $Insert = NULL) {
	$ReturnValue = 'true';
	$Queries = "";
	foreach ($Ballots as $key=>$Ballot) {
		if ( ! isset($Ballot['RID']) ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No RID for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No RID for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Round']) ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No round given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No round given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Judge']) ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No judge given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No judge given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['RID'] == $Ballot['RID'] ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid RID for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid RID for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Round'] == $Ballot['Round'] ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid round given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid round given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Judge'] == $Ballot['Judge'] ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid judge given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid judge given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['ElimLevel']) ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No level set for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No level set for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['ElimLevel'] == $Ballot['ElimLevel'] ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invald level for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invald level for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Rank']) ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - No rank given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No rank given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Rank'] == $Ballot['Rank'] ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid rank given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid rank given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( isset($Ballot['Qual']) && ! is_numeric($Ballot['Qual'])) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid quality points given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid quality points given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( isset($Ballot['ElimLevel']) && ! (string)(int)$Ballot['ElimLevel'] == $Ballot['ElimLevel'] ) {
			if ( $ReturnValue == 'true' ) {
				$ReturnValue = 'Error - Invalid elimination round level - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid elimination round level - '.$key;
			}
			continue;
		}
		if ( $Insert != NULL ) {
			if ( ! isset($Ballot['Qual']) ) {
				$Ballot['Qual'] = '';
			}
			if ( ! isset($Ballot['ElimLevel']) ) {
				$Ballot['ElimLevel'] = 0;
			}
			$ConnectVar = mysqli_connect();
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
				$QueryString = 'insert into Ballots set RID="'.mysqli_real_escape_string($ConnectVar,$Ballot['RID']).'", Round="'.mysqli_real_escape_string($ConnectVar,$Ballot['Round']).'", Judge="'.mysqli_real_escape_string($ConnectVar,$Ballot['Judge']).'", Rank="'.mysqli_real_escape_string($ConnectVar,$Ballot['Rank']).'", Qual="'.mysqli_real_escape_string($ConnectVar,$Ballot['Qual']).'", ElimLevel="'.mysqli_real_escape_string($ConnectVar,$Ballot['ElimLevel']).'";';
			} else {
				$QueryString = 'update Ballots set Rank="'.mysqli_real_escape_string($ConnectVar,$Ballot['Rank']).'", Qual="'.mysqli_real_escape_string($ConnectVar,$Ballot['Qual']).'", ElimLevel="'.mysqli_real_escape_string($ConnectVar,$Ballot['ElimLevel']).'" where RID="'.mysqli_real_escape_string($ConnectVar,$Ballot['RID']).'" and Round="'.mysqli_real_escape_string($ConnectVar,$Ballot['Round']).'" and Judge="'.mysqli_real_escape_string($ConnectVar,$Ballot['Judge']).'";';
			}
			$Queries[$key] = $QueryString;
		}
	}
	if ( $ReturnValue == 'true' && $Insert != NULL ) {
		foreach ( $Queries as $key=>$QueryString ) {
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
function CreateTournamentList($IncludeAll, $DBConn, $DefaultTID = NULL) {
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
function CreateStudentList($IncludeAll, $DBConn, $FormName = NULL, $DefaultSID = NULL, $SelectName = NULL, $OnChange = NULL) {
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
function CreateEventList($IncludeAll, $DBConn, $DefaultEID = NULL, $SelectName = NULL) {
	$EQuery = mysqli_query($DBConn, 'select * from Events order by EName;');
	if ( !$EQuery ) {
		return ReturnMySQLError($DBConn);
	}
	$NumRows = mysqli_num_rows($EQuery);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$SelectName = 'EID';
	}
	$EventString = '<select id="' . $SelectName . '" name="' . $SelectName . '">';
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
	if ( $CustomText != NULL ) {
		$ReturnString = $CustomText;
	} else {
		$ReturnString = 'Error - MySQL error: ';
	}
	$ReturnString = $ReturnString . mysqli_error($DBConn) . '.';
	return $ReturnString;
}
function WriteToLog($LogString = Null) {
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

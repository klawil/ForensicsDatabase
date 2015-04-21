<?php
include 'MySQLAuth.php';
$GLOBALS['CookieName'] = "forensics_db_auth_token";
$GLOBALS['SecretWord'] = "ForensicsSECRET";
$GLOBALS['UserName'] = "";
$GLOBALS['CanUserEdit'] = 0;
$GLOBALS['DBName'] = "kmc";
function InsertBallots($Ballots, $DBConn = NULL, $Insert = NULL) {
	$ReturnValue = "true";
	$Queries = "";
	foreach ($Ballots as $key=>$Ballot) {
		if ( ! isset($Ballot['RID']) ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - No RID for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No RID for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Round']) ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - No round given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No round given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Judge']) ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - No judge given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No judge given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['RID'] == $Ballot['RID'] ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - Invalid RID for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid RID for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Round'] == $Ballot['Round'] ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - Invalid round given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid round given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Judge'] == $Ballot['Judge'] ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - Invalid judge given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid judge given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['ElimLevel']) ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - No level set for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No level set for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['ElimLevel'] == $Ballot['ElimLevel'] ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - Invald level for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invald level for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! isset($Ballot['Rank']) ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - No rank given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - No rank given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( ! (string)(int)$Ballot['Rank'] == $Ballot['Rank'] ) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - Invalid rank given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid rank given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( isset($Ballot['Qual']) && ! is_numeric($Ballot['Qual'])) {
			if ( $ReturnValue == "true" ) {
				$ReturnValue = 'Error - Invalid quality points given for one of the ballots - '.$key;
			} else {
				$ReturnValue = $ReturnValue.';Error - Invalid quality points given for one of the ballots - '.$key;
			}
			continue;
		} elseif ( isset($Ballot['ElimLevel']) && ! (string)(int)$Ballot['ElimLevel'] == $Ballot['ElimLevel'] ) {
			if ( $ReturnValue == "true" ) {
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
				if ( $ReturnValue == "true" ) {
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
	if ( $ReturnValue == "true" && $Insert != NULL ) {
		foreach ( $Queries as $key=>$QueryString ) {
			$InsertQuery = mysqli_query($DBConn,$QueryString);
			if ( !$InsertQuery ) {
				if ( $ReturnValue == "true" ) {
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
	$TournamentString = '<select id="Tournament" name="TID">';
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
	$SQuery = mysqli_query($DBConn, "select FName, LName, SID from Students order by LName, FName;");
	if ( !$SQuery ) {
		return ReturnMySQLError($DBConn);
	}
	$NumRows = mysqli_num_rows($SQuery);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$Name = 'SID';
	} else {
		$Name = $SelectName;
	}
	$StudentString = '<select id="Student" name="' . $Name . '"';
	if ( $FormName != NULL ) {
		$StudentString = $StudentString . ' form="' . $FormName . '"';
	}
	if ( $OnChange != NULL ) {
		$StudentString = $StudentString . ' onchange="' . $OnChange . '"';
	}
	$StudentString = $StudentString . '>';
	if ( $IncludeAll == 1 ) {
		$StudentString = $StudentString . "<option value='-1'>All Students</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysqli_fetch_assoc($SQuery);
		if ( $results['SID'] == $DefaultSID ) {
			$StudentString = $StudentString . '<option selected="selected" value="' . $results['SID'] . '">' . $results['LName'] . ", " . $results['FName'] . "</option>";
		} else {
			$StudentString = $StudentString . '<option value="' . $results['SID'] . '">' . $results['LName'] . ", " . $results['FName'] . "</option>";
		}
		$CurrentRow++;
	}
	$StudentString = $StudentString . "</select>";
	return $StudentString;
}
function Events($IncludeAll, $DefaultEID = NULL, $SelectName = NULL) {
	$query = mysqli_query($GLOBALS['DBConn'], "select * from Events order by EName;");
	if ( !$query ) {
		return "Error - MySQL error: " . mysqli_error($DBConn) . ".";
	}
	$NumRows = mysqli_num_rows($query);
	$CurrentRow = 0;
	if ( $SelectName == NULL ) {
		$Name = "EID";
	} else {
		$Name = $SelectName;
	}
	$EventString = '<select id="Event" name="' . $Name . '">';
	if ( $IncludeAll == 1 ) {
		$EventString = $EventString . "<option value='-1'>All Events</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysqli_fetch_assoc($query);
		if ( $results['EID'] == $DefaultEID ) {
			$EventString = $EventString . '<option selected="selected" value="' . $results['EID'] . '">' . $results['EName'] . "</option>";
		} else {
			$EventString = $EventString . '<option value="' . $results['EID'] . '">' . $results['EName'] . "</option>";
		}
		$CurrentRow++;
	}
	$EventString = $EventString . "</select>";
	return $EventString;
}
function MakeHeader() {
	echo '<nav>
	<ul>
	<a href="."><li>Index</li></a>
	<a href="TournamentInfo.php"><li>Results</li></a>
	<li>Summary
		<ul>
		<a href="StudentSummary.php"><li>Student</li></a>
		<a href="TournamentSummary.php"><li>Tournament</li></a>
		<a href="TeamSummary.php"><li>Team</li></a>
		</ul>
	</li>
	';
	if ( $GLOBALS['CanUserEdit'] == 1 ) {
		echo '<li>Management
		<ul>
		<a href="NewStudent.php"><li>New Student</li></a>
		<a href="NewTournament.php"><li>New Tournament</li></a>
		<a href="TournamentUpdate.php"><li>Insert Data</li></a>
		</ul>
	</li>
	<a href="https://github.com/klawil/ForensicsDatabase" target="_blank"><li>The Project on Github</li></a>
	';
	echo '<a href="IssueReport.php"><li>Report an Issue</li></a>
	';
	}
	if ( $GLOBALS['UserName'] != "" ) {
		echo '<li id="login"><b onclick="ShowLogin();">' . $GLOBALS['UserName'] . '</b>
		<ul id="logoutwin">
		<li id="login_message" style="display: none;"></li>
		<li style="text-align: center;"><input type="button" value="Log Out" onclick="UserLogout();"></li>
		</ul>
	</li>
	</ul>
</nav>
<script>
function ShowLogin() {
	if ( document.getElementById("logoutwin").style.display != "block" ) {
		document.getElementById("logoutwin").style.display = "block";
	} else {
		document.getElementById("login_message").style.display = "none";
		document.getElementById("logoutwin").style.display = "none";
	}
}
function UserLogout() {
	PString = "Logout=1";
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","Login.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(PString);
	document.getElementById("login_message").style.display = "block";
	document.getElementById("login_message").innerHTML = "Logging Out...";
	xmlhttp.onreadystatechange=function() {
		response = xmlhttp.responseText;
		if ( response == "true" ) {
			location.reload();
		} else {
			document.getElementById("login_message").innerHTML = response;
		}
	}
}
</script>
';
	} else {
		echo '<li id="login"><b onclick="ShowLogin();">Login</b>
		<ul id="loginwin">
		<li id="login_message" style="display: none;"></li>
		<form id="LoginForm">
		<li style="text-align: right;">User: <input type="text" name="UName" id="UName"></li>
		<li style="text-align: right;">Password: <input type="password" name="PWord" id="PWord"></li>
		<li style="text-align: center;"><input type="button" value="Log In" onclick="LoginUser();"> <a href="NewUser.php"><input type="button" value="New User"></a></li>
		</form>
		</ul>
	</li>
	</ul>
</nav>
<script>
function ShowLogin() {
	if ( document.getElementById("loginwin").style.display != "block" ) {
		document.getElementById("loginwin").style.display = "block";
	} else {
		document.getElementById("login_message").style.display = "none";
		document.getElementById("loginwin").style.display = "none";
	}
}
function LoginUser() {
	PString = "UName=" + document.getElementById("UName").value + "&PWord=" + document.getElementById("PWord").value;
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","Login.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(PString);
	document.getElementById("login_message").style.display = "block";
	document.getElementById("login_message").innerHTML = "Checking Credentials...";
	xmlhttp.onreadystatechange = function() {
		response = xmlhttp.responseText;
		if ( response == "true" ) {
			location.reload();
		} else {
			document.getElementById("LoginForm").reset();
			document.getElementById("login_message").innerHTML = response;
		}
	}
}
</script>
';
	}
}
function Authorize() {
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
Authorize();
?>

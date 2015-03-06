<?php
$GLOBALS['CookieName'] = "forensics_db_auth_token";
$GLOBALS['SecretWord'] = "ForensicsSECRET";
$GLOBALS['UserName'] = "";
$GLOBALS['CanUserEdit'] = 0;
function Tournaments($IncludeAll) {
	$query = mysql_query("select TName, TID from Tournaments order by Date desc, TName;");
	if (( mysql_errno() )) {
		return "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$TournamentString = '<select id="Tournament" name="TID">';
	if ( $IncludeAll == 1 ) {
		$TournamentString = $TournamentString . "<option value='-1'>All Tournaments</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$TournamentString = $TournamentString . '<option value="' . $results['TID'] . '">' . $results['TName'] . "</option>";
		$CurrentRow++;
	}
	$TournamentString = $TournamentString . "</select>";
	return $TournamentString;
}
function Students($IncludeAll, $FormName = NULL) {
	$query = mysql_query("select FName, LName, SID from Students order by LName, FName;");
	if (( mysql_errno() )) {
		return "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	if ( $FormName == NULL ) {
		$StudentString = '<select id="Student" name="SID">';
	} else {
		$StudentString = '<select id="Student" name="SID" form="' . $FormName . '">';
	}
	if ( $IncludeAll == 1 ) {
		$StudentString = $StudentString . "<option value='-1'>All Students</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$StudentString = $StudentString . '<option value="' . $results['SID'] . '">' . $results['LName'] . ", " . $results['FName'] . "</option>";
		$CurrentRow++;
	}
	$StudentString = $StudentString . "</select>";
	return $StudentString;
}
function Events($IncludeAll) {
	$query = mysql_query("select * from Events order by EName;");
	if (( mysql_errno() )) {
		return "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$EventString = '<select id="Event" name="EID">';
	if ( $IncludeAll == 1 ) {
		$EventString = $EventString . "<option value='-1'>All Events</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$EventString = $EventString . '<option value="' . $results['EID'] . '">' . $results['EName'] . "</option>";
		$CurrentRow++;
	}
	$EventString = $EventString . "</select>";
	return $EventString;
}
function MakeHeader() {
	echo '<nav>
	<ul>
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
	';
	}
	if ( $GLOBALS['UserName'] != "" ) {
		echo '<li id="login" onclick="ShowLogin();"><b>' . $GLOBALS['UserName'] . '</b>
		<ul id="loginwin">
		<li style="text-align: center;"><input type="button" value="Log Out"></li>
		</ul>
	</li>';
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
	</li>';
	}
	echo '</ul>
</nav>
<script>
function ShowLogin() {
	if ( document.getElementById("loginwin").style.display != "block" ) {
		document.getElementById("loginwin").style.display = "block";
	} else {
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
	xmlhttp.open("POST","Login.php",false);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(PString);
	response = xmlhttp.responseText;
	if ( response == "true" ) {
		location.reload();
	} else {
		document.getElementById("login_message").style.display = "block";
		document.getElementById("LoginForm").reset();
		document.getElementById("login_message").innerHTML = response;
	}
}
</script>
';
}
function Authorize() {
	if ( isset($_COOKIE[$GLOBALS['CookieName']]) ) {
		$Array = explode(',',$_COOKIE[$GLOBALS['CookieName']],2);
		$GLOBALS['UserName'] = $Array[0];
		$Cookie = $Array[1];
		$query = mysql_query("select cookie, cookieExp, CanMod from users where UName='" . $GLOBALS['UserName'] . "';");
		if (( mysql_errno() )) {
			return 0;
		}
		$Data = mysql_fetch_assoc($query);
		if ( $Cookie != $Data['cookie'] ) {
			setcookie($GLOBALS['CookieName'], "", time() - 3600);
			$GLOBALS['UserName'] = "";
			return 0;
		} elseif ( time() > $Data['cookieExp'] ) {
			setcookie($GLOBALS['CookieName'], "", time() - 3600);
			$GLOBALS['UserName'] = "";
			return 0;
		}
		$GLOBALS['CanUserEdit'] = $Data['CanMod'];
	}
}
function SetAuthCookie($UN) {
	$ExpDate = time() + (86400 * 7);
	$MD5 = md5($UN . $GLOBALS['SecretWord'] . $ExpDate);
	$Cookie = $UN . "," . $MD5;
	mysql_query("update users set cookie='" . $MD5 . "', cookieExp='" . $ExpDate . "' where UName='" . $UN . "';");
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		return 0;
	}
	setcookie($GLOBALS['CookieName'], $Cookie, $ExpDate, "/");
	return 1;
}
Authorize();
?>

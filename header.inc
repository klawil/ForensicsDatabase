<html>
<head>
<title><?php echo $GLOBALS['PageName'];?> | ForensicsDB.com</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 850px)" href="MobileStyles.css" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h1><?php echo $GLOBALS['PageName'];?></h1>
<nav>
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
<?php
if ( isset($GLOBALS['CanUserEdit']) && $GLOBALS['CanUserEdit'] == 1 ) {
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
if ( isset($GLOBALS['UserName']) && $GLOBALS['UserName'] != "" ) {
	echo '<li id="login"><b onclick="ShowLogout();">' . $GLOBALS['UserName'] . '</b>
		<ul id="logoutwin">
		<li id="login_message" style="display: none;"></li>
		<li style="text-align: center;"><input type="button" value="Log Out" onclick="UserLogout();"></li>
		</ul>
	</li>
	</ul>
';
} else {
	echo '<li id="login"><b onclick="ShowLogin();">Login</b>
		<ul id="loginwin">
		<li id="login_message" style="display: none;"></li>
		<form id="LoginForm">
		<li style="text-align: right;">User: <input type="text" name="UserName" id="UserName"></li>
		<li style="text-align: right;">Password: <input type="password" name="PWord" id="PWord"></li>
		<li style="text-align: center;"><input type="button" value="Log In" onclick="LoginUser();"> <a href="NewUser.php"><input type="button" value="New User"></a></li>
		</form>
		</ul>
	</li>
	</ul>
';
}
?>
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
function ShowLogout() {
	if ( document.getElementById("logoutwin").style.display != "block" ) {
		document.getElementById("logoutwin").style.display = "block";
	} else {
		document.getElementById("login_message").style.display = "none";
		document.getElementById("logoutwin").style.display = "none";
	}
}
function LoginUser() {
	PString = "UName=" + document.getElementById("UserName").value + "&PWord=" + document.getElementById("PWord").value;
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
		if ( response == 'true' ) {
			location.reload();
		} else {
			document.getElementById("LoginForm").reset();
			document.getElementById("login_message").innerHTML = response;
		}
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
		if ( response == 'true' ) {
			location.reload();
		} else {
			document.getElementById("login_message").innerHTML = response;
		}
	}
}
</script>

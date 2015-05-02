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
	<ul class="TopMenu">
	<a href="/"><li class="TopMenuItem">Home</li></a>
	<a href="TournamentInfo.php"><li class="TopMenuItem">Results</li></a>
	<li class="TopMenuItem">Summaries
		<ul class="SubMenu">
		<a href="StudentSummary.php"><li class="SubMenuItem">Student</li></a>
		<a href="TournamentSummary.php"><li class="SubMenuItem">Tournament</li></a>
		<a href="TeamSummary.php"><li class="SubMenuItem">Team</li></a>
		</ul>
	</li>
<?php
if ( isset($GLOBALS['CanUserEdit']) && $GLOBALS['CanUserEdit'] == 1 ) {
	echo '	<li class="TopMenuItem">Management
		<ul class="SubMenu">
		<a href="InsertResult.php"><li class="SubMenuItem">Enter Results</li></a>
		<a href="EditResult.php"><li class="SubMenuItem">Edit Results</li></a>
		<a href="ManageStudent.php"><li class="SubMenuItem">Manage Students</li></a>
		<a href="ManageTournament.php"><li class="SubMenuItem">Manage Tournaments</li></a>
		<a href="ManageEvents.php"><li class="SubMenuItem">Manage Events</li></a>
		<a href="ManageUser.php"><li class="SubMenuItem">Manage Users</li></a>
		</ul>
	</li>
	<a href="https://github.com/klawil/ForensicsDatabase" target="_blank"><li class="TopMenuItem">The Project on Github</li></a>
	<a href="IssueReport.php"><li class="TopMenuItem">Report an Issue</li></a>
';
}
?>
	<li class="UserItem" onclick="ShowInOutForm();"><?php
if ( isset($GLOBALS['UserName']) && $GLOBALS['UserName'] != "" ) {
	echo $GLOBALS['UserData']['LName'] . ', ' . $GLOBALS['UserData']['LName'] . '
	<ul id="LogInOut">
		<li class="Alert" id="UserMessage"></li>
		<a href="#" onclick="UserLogout();"><li class="SubMenuItem">Log Out</li></a>
		<a href="ManageSelf.php"><li class="SubMenuItem">Manage Account</li></a>
';
} else {
	echo 'Login
		<ul id="LogInOut">
		<li class="Alert" id="UserMessage"></li>
		<form id="LoginForm">
		<li>
		<table class="LogInOutForm">
		<tr><td style="text-align: right;">User</td><td><input type="text" name="UserName" id="UserName"></td></tr>
		<tr><td style="text-align: right;">Password</td><td><input type="password" name="PWord" id="PWord"></td>
		<tr><td colspan="2" style="text-align: center;"><input type="button" value="Log In" onclick="LoginUser();"> <a href="NewUser.php"><input type="button" value="New User"></a></td></tr>
		</table>
		</li>
		</form>
';
}
?>
		</ul>
	</li>
</nav>
<script>
function ShowInOutForm() {
	if ( document.getElementById('LogInOut').style.display != 'block' ) {
		document.getElementById('LogInOut').style.display = 'block';
	} else {
		document.getElementById('LogInOut').style.display = 'none';
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

<html>
<head>
<title><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName'] . ' | ';}?> ForensicsDB.com</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 850px)" href="MobileStyles.css" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<h1><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName']; } else { echo 'ForensicsDB.com'; }?></h1>
<nav>
	<ul class="TopMenu">
	<a href="/" title="Return to the subdomain's home"><li class="TopMenuItem">Home</li></a>
	<a href="Results.php" title="Get specific results"><li class="TopMenuItem">Results</li></a>
	<li class="TopMenuItem">Summaries
		<ul class="SubMenu">
		<a href="StudentSummary.php" title="Summary of a student's results"><li class="SubMenuItem">Student</li></a>
		<a href="TournamentSummary.php" title="Summary of a tournament's results"><li class="SubMenuItem">Tournament</li></a>
		<a href="TeamSummary.php" title="Summary of the team's results"><li class="SubMenuItem">Team</li></a>
		</ul>
	</li>
<?php
if ( isset($GLOBALS['CanUserEdit']) && $GLOBALS['CanUserEdit'] == 1 ) {
?>
<li class="TopMenuItem">Management
		<ul class="SubMenu">
		<a href="ResultInsert.php" title="Enter new results"><li class="SubMenuItem">Enter Results</li></a>
		<a href="ResultEdit.php" title="Edit previous results"><li class="SubMenuItem">Edit Results</li></a>
		<a href="ManageSeason.php" title="Create, modify, and delete seasons"><li class="SubMenuItem">Manage Seasons</li></a>
		<a href="ManageStudent.php" title="Create, modify, and delete students"><li class="SubMenuItem">Manage Students</li></a>
		<a href="ManageEvent.php" title="Create, modify, and delete events"><li class="SubMenuItem">Manage Events</li></a>
		<a href="ManageTournament.php" title="Create, modify, and delete tournaments"><li class="SubMenuItem">Manage Tournaments</li></a>
		<a href="ManageUser.php" title="Create, modify, and delete users"><li class="SubMenuItem">Manage Users</li></a>
		</ul>
	</li>
	<a href="https://github.com/klawil/ForensicsDatabase" target="_blank" title="View the code that runs the site or officially report an issue"><li class="TopMenuItem">The Project on Github</li></a>
	<!--<a href="IssueReport.php"><li class="TopMenuItem">Report an Issue</li></a>-->
<?php
}
?>
	<li class="UserItem"><a onclick="ShowInOutForm();"><?php
if ( isset($GLOBALS['UserName']) && $GLOBALS['UserName'] != "" ) {
	echo $GLOBALS['UserData']['LName'] . ', ' . $GLOBALS['UserData']['FName']?></a>
	<ul id="LogInOut">
		<li class="Alert" id="UserMessage"></li>
		<a href="#" onclick="UserLogout();"><li class="SubMenuItem">Log Out</li></a>
		<!--<a href="ManageSelf.php"><li class="SubMenuItem">Manage Account</li></a>-->
<?php
} else {
	?>Login</a>
		<ul id="LogInOut">
		<li class="Alert" id="UserMessage"></li>
		<form id="LoginForm">
		<li>
		<table class="LogInOutForm">
		<tr><td style="text-align: right;">User</td><td><input type="text" name="UserName" id="UserName"></td></tr>
		<tr><td style="text-align: right;">Password</td><td><input type="password" name="PWord" id="PWord"></td>
		<tr><td colspan="2" style="text-align: center;"><input type="submit" value="Log In" onclick="LoginUser();"> <a href="NewUser.php"><input type="button" value="New User"></a></td></tr>
		</table>
		</li>
		</form>
<?php
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
	event.preventDefault();
	PString = "UName=" + document.getElementById("UserName").value + "&PWord=" + document.getElementById("PWord").value;
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","Login.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(PString);
	document.getElementById("UserMessage").style.display = "block";
	document.getElementById("UserMessage").innerHTML = "Checking Credentials...";
	xmlhttp.onreadystatechange = function() {
		response = xmlhttp.responseText;
		if ( response == 'true' ) {
			location.reload();
		} else {
			document.getElementById("LoginForm").reset();
			document.getElementById("UserMessage").innerHTML = response;
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
	document.getElementById("UserMessage").style.display = "block";
	document.getElementById("UserMessage").innerHTML = "Logging Out...";
	xmlhttp.onreadystatechange=function() {
		response = xmlhttp.responseText;
		if ( response == 'true' ) {
			location.reload();
		} else {
			document.getElementById("UserMessage").innerHTML = response;
		}
	}
}
</script>

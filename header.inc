<html>
<head>
<title><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName'] . ' | ';}?> ForensicsDB.com</title>
<!--<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 850px)" href="MobileStyles.css" />-->
<link rel="stylesheet" href="/ForensicsDatabase/css/bootstrap.css" />
<link rel="shortcut icon" type="image/png" href="/FavIcon.png" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" src="ChangeFunctions.js"></script>
</head>
<body>
<h1><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName']; } else { echo 'ForensicsDB.com'; }?></h1>
<nav class="navbar navbar-default">
	<div class="container-fluid">
		
		<!-- NavBar Header -->
		<div class="navbar-header">
			<button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#NavBarColapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="#">ForensicsDB</a>
		</div>

		<!-- NavBar Content -->
		<div class="collapse navbar-collapse" id="NavBarColapse">
			<ul class="nav navbar-nav">
				
				<!-- Main navigation -->
				<li><a href="/">Home</a></li>
				<li><a href="/Results.php">Results</a></li>

				<!-- Summaries -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Summaries<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="/SummaryStudent.php">Student Summary</a></li>
						<li><a href="/SummaryTournament.php">Tournament Summary</a></li>
						<li><a href="/SummaryTeam.php">Team Summary</a></li>
					</ul>
				</li>

				<!-- Management Menu -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Manage<span class="caret"></span></a><!-- Enter Results, Edit Results, Manage Seasons, Manage Students, Manage Events, Manage Tournaments, Manage Users -->
					<ul class="dropdown-menu" role="menu">
						<li><a href="/ResultEnter.php">Enter Results</a></li>
						<li><a href="/ResultEdit.php">Edit Results</a></li>
						<li><a href="/ManageSeason.php">Manage Seasons</a></li>
						<li><a href="/ManageStudent.php">Manage Students</a></li>
						<li><a href="/ManageEvent.php">Manage Events</a></li>
						<li><a href="/ManageTournament.php">Manage Tournaments</a></li>
						<li><a href="/ManageUser.php">Manage Users</a></li>
					</ul>
				</li>
				
				<!-- Github Link -->
				<li><a href="https://github.com/klawil/ForensicsDatabase/" target="_blank">The Project on GitHub</a></li>

				<!-- Login Window -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Manage<span class="caret"></span></a><!-- Enter Results, Edit Results, Manage Seasons, Manage Students, Manage Events, Manage Tournaments, Manage Users -->
					<ul class="dropdown-menu" role="menu">
						<li><a href="/ResultEnter.php">Enter Results</a></li>
						<li><a href="/ResultEdit.php">Edit Results</a></li>
						<li><a href="/ManageSeason.php">Manage Seasons</a></li>
						<li><a href="/ManageStudent.php">Manage Students</a></li>
						<li><a href="/ManageEvent.php">Manage Events</a></li>
						<li><a href="/ManageTournament.php">Manage Tournaments</a></li>
						<li><a href="/ManageUser.php">Manage Users</a></li>
					</ul>
				</li>

	<li class="UserItem"><a onclick="ShowInOutForm();"><?php
if ( isset($GLOBALS['UserName']) && $GLOBALS['UserName'] != "" ) {
	echo $GLOBALS['UserData']['LName'] . ', ' . $GLOBALS['UserData']['FName']?></a>
	<ul id="LogInOut">
		<li class="Alert" id="UserMessage"></li>
		<a href="#" onclick="UserLogout();"><li class="SubMenuItem LogoutButton">Log Out</li></a>
		<!--<a href="ManageSelf.php"><li class="SubMenuItem">Manage Account</li></a>-->
	Login</a>
		<ul id="LogInOut">
		<li class="Alert" id="UserMessage"></li>
		<form id="LoginForm">
		<li>
		<table class="LogInOutForm">
		<tr><td style="text-align: right;">User</td><td><input type="text" name="UserName" id="UserName"></td></tr>
		<tr><td style="text-align: right;">Password</td><td><input type="password" name="PWord" id="PWord"></td>
		<tr><td colspan="2" style="text-align: center;"><a href="#"><input type="submit" value="Log In" onclick="LoginUser();"></a> <a href="NewUser.php"><input type="button" value="New User"></a></td></tr>
		</table>
		</li>
		</form>
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

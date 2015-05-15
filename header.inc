<html>
<head>
<title><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName'] . ' | ';} ?> ForensicsDB.com</title>
<link rel="stylesheet" href="/css/bootstrap.css" />
<link rel="shortcut icon" type="image/png" href="/FavIcon.png" />
<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
<script src="/js/bootstrap.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" src="ChangeFunctions.js"></script>
</head>
<body>
<h1><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName']; } else { echo 'ForensicsDB.com'; } ?></h1>
<nav class="navbar navbar-inverse">
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

<?php if ( isset($GLOBALS['CanUserEdit']) && $GLOBALS['CanUserEdit'] == 1 ) { ?>
				<!-- Management Menu -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Manage<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a href="/ResultEnter.php">Enter Results</a></li>
						<li><a href="/ResultEdit.php">Edit Results</a></li>
						<li><a href="/ManageSeason.php">Manage Seasons</a></li>
						<li><a href="/ManageStudent.php">Manage Students</a></li>
						<li><a href="/ManageEvent.php">Manage Events</a></li>
						<li><a href="/ManageTournament.php">Manage Tournaments</a></li>
	<?php if ( $GLOBALS['UserData']['Email'] == $GLOBALS['DBData']['AdminEmail'] || $GLOBALS['UserName'] == 'admin' ) { ?>
						<li><a href="/ManageUser.php">Manage Users</a></li>
	<?php } ?>
					</ul>
				</li>
				
				<!-- Github Link -->
				<li><a href="https://github.com/klawil/ForensicsDatabase/" target="_blank">The Project on GitHub</a></li>
<?php } ?>
			</ul>

<?php if ( isset($GLOBALS['UserName']) && $GLOBALS['UserName'] != "" ) { ?>
			<!-- User Section -->
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown navbar-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false"><?php echo $GLOBALS['UserData']['LName'] . ', ' . $GLOBALS['UserData']['FName']; ?><span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li id="UserMessage"></li>
						<form class="navbar-form" role="login">
							<div class="form-group">
								<input type="text" id="UserName" class="form-control" placeholder="Username">
							</div>
							<div class="form-group">
								<input type="password" id="PWord" class="form-control" placeholder="Password">
							</div>
							<div class="text-center">
								<button type="submit" onclick="LoginUser()" class="btn btn-default">Login</button>
								<a role="button" class="btn btn-default" href="/NewUser.php">Create User</a>
							</div>
						</form>
					</ul>
				</li> <!-- End Login Window -->
			</ul> <!-- End Navbar Section for Login Window -->

<?php } else { ?>
			<!-- Login Window -->
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown navbar-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Login<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li id="UserMessage"></li>
						<form class="navbar-form" role="login">
							<div class="form-group">
								<input type="text" id="UserName" class="form-control" placeholder="Username">
							</div>
							<div class="form-group">
								<input type="password" id="PWord" class="form-control" placeholder="Password">
							</div>
							<div class="text-center">
								<button type="submit" onclick="LoginUser()" class="btn btn-default">Login</button>
								<a role="button" class="btn btn-default" href="/NewUser.php">Create User</a>
							</div>
						</form>
					</ul>
				</li> <!-- End Login Window -->
			</ul> <!-- End Navbar Section for Login Window -->
<?php } ?>
		</div>
	</div>
</nav> <!-- End Nav -->

<script>
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

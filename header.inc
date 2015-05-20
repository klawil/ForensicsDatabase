<?php
if ( !isset($_POST['LoadPage']) ) {
?>
<html>
<head>
<title><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName'] . ' | ';} ?> ForensicsDB.com</title>
<link rel="stylesheet" href="/css/bootstrap.css" />
<link rel="shortcut icon" type="image/png" href="/FavIcon.png" />
<script src="/js/jquery.min.js"></script>
<script src="/js/bootstrap.js"></script>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<script type="text/javascript" src="/js/CustomFunctions.js"></script>
</head>
<body>
<h1 style="padding-left: 10px;" id="PageTitle"><?php if ( isset($GLOBALS['PageName']) ) { echo $GLOBALS['PageName']; } else { echo 'ForensicsDB.com'; } ?></h1>
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
				<li><a onclick="CreatePage('/index.php','Home')">Home</a></li>
				<li><a onclick="CreatePage('/Results.php','Results')">Results</a></li>

				<!-- Summaries -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Summaries<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a onclick="CreatePage('/SummaryStudent.php','Student Summary')">Student Summary</a></li>
						<li><a onclick="CreatePage('/SummaryTournament.php','Tournament Summary')">Tournament Summary</a></li>
						<li><a onclick="CreatePage('/SummaryTeam.php','Team Summary')">Team Summary</a></li>
					</ul>
				</li>

<?php if ( isset($GLOBALS['CanUserEdit']) && $GLOBALS['CanUserEdit'] == 1 ) { ?>
				<!-- Management Menu -->
				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Manage<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<li><a onclick="CreatePage('/ResultEnter.php','Enter Results')">Enter Results</a></li>
						<li><a onclick="CreatePage('/ResultEdit.php','Edit Results')">Edit Results</a></li>
						<li><a onclick="CreatePage('/ManageSeason.php','Manage Seasons')">Manage Seasons</a></li>
						<li><a onclick="CreatePage('/ManageStudent.php','Manage Students')">Manage Students</a></li>
						<li><a onclick="CreatePage('/ManageEvent.php','Manage Events')">Manage Events</a></li>
						<li><a onclick="CreatePage('/ManageTournament.php','Manage Tournaments')">Manage Tournaments</a></li>
	<?php if ( $GLOBALS['UserData']['Email'] == $GLOBALS['DBData']['AdminEmail'] || $GLOBALS['UserName'] == 'admin' ) { ?>
						<li><a onclick="CreatePage('/ManageUser.php','Manage Users')">Manage Users</a></li>
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
						<p id="LoginMessage" class="navbar-text"></p>
						<li class="text-center"><a href="#" onclick="UserLogout()">Logout</a></li>
					</ul>
				</li>
			</ul>

<?php } else { ?>
			<!-- Login Window -->
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown navbar-right">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Login<span class="caret"></span></a>
					<ul class="dropdown-menu" role="menu">
						<p id="LoginMessage" class="navbar-text"></p>
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
	// Function to log in the user or display a message if the login fails
	event.preventDefault(); // prevent the form submission from redirecting the page

	// Create the post string of username and password
	PString = "UName=" + document.getElementById("UserName").value + "&PWord=" + document.getElementById("PWord").value;
	
	// Execute the post
	PostToPage(PString,"Login.php","LoginMessage");
}

function UserLogout() {
	// Logs the user out
	// No parameters

	// Set the post string to let the server know the user is logging out (username will be procured from the cookie)
	PString = "Logout=1";

	// Execute the post
	PostToPage(PString,"Login.php","LoginMessage");
}
</script>
<div class="container" id="MainBody">
<?php
}
?>

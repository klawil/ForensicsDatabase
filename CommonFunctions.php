<?php
function Tournaments($IncludeAll) {
	$query = mysql_query("select TName, TID from Tournaments order by Date desc, TName;");
	if (( mysql_errno() )) {
		return "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$TournamentString = '<select id="Tournament">';
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
		$StudentString = '<select id="Student">';
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
	$EventString = '<select id="Event">';
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
	echo '<style>
nav ul {
	background: #0000ff;
	padding: 0 20px;
	border-radius: 10px;
	list-style: none;
	position: relative;
	display: inline-table;
	width: calc(100% - 40px);
}
nav ul #login {
	float: right;
}
nav ul li ul {
	padding-top: 0px;
	padding-left: 0px;
	padding-right: 0px;
	padding-bottom: 0px;
	border-radius: 0px;
	width: auto;
}
nav ul li a {
	color: #FFFFFF;
	text-decoration: none;
}
nav ul li:hover {
	background: #000000;
}
nav ul li {
	float: left;
	padding: 5px;
	padding-right: 30px;
}
nav ul li ul li {
	float: none;
	padding: 5px;
	padding-right: 30px;
}
li ul {
	display: none;
	position: absolute;
	top: 28px;
}
li:hover ul {
	display: block;
	position: absolute;
}
</style>
<nav>
	<ul>
	<li><a href="TournamentInfo.php">Results</a></li>
	<li><a href="#">Summary</a>
		<ul>
		<li><a href="StudentSummary.php">Student</a></li>
		<li><a href="TournamentSummary.php">Tournament</a></li>
		<li><a href="TeamSummary.php">Team</a></li>
		</ul>
	</li>
	<li><a href="#">Management</a>
		<ul>
		<li><a href="NewStudent.php">New Student</a></li>
		<li><a href="NewTournament.php">New Tournament</a></li>
		<li><a href="TournamentUpdate.php">Insert Data</a></li>
		</ul>
	</li>
	<li id="login"><a href="Login.php"><b>Login</b></a></li>
	</ul>
</nav>';
}
?>

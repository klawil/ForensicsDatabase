<?php
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
	<li>Management
		<ul>
		<a href="NewStudent.php"><li>New Student</li></a>
		<a href="NewTournament.php"><li>New Tournament</li></a>
		<a href="TournamentUpdate.php"><li>Insert Data</li></a>
		</ul>
	</li>
	<li id="login"><a href="Login.php"><b>Login</b></a></li>
	</ul>
</nav>';
}
function Authorize() {
	$CookieName = 'forensics_db_auth_token';
	
}
?>

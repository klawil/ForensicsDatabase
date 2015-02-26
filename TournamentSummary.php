<?php
include "MySQLAuth.php";
$query = mysql_query("select TName, TID from Tournaments order by Date desc, TName;");
$NumRows = mysql_num_rows($query);
$CurrentRow = 0;
$TournamentString = '<select name="TID" form="TournamentInfo">';
if ( isset($_POST['IncludeAll']) ) {
	$TournamentString = $TournamentString . "<option value='-1'>All Tournaments</option>";
}
while ( $CurrentRow < $NumRows ) {
	$results = mysql_fetch_assoc($query);
	$TournamentString = $TournamentString . '<option value="' . $results['TID'] . '">' . $results['TName'] . "</option>";
	$CurrentRow++;
}
$TournamentString = $TournamentString . "</select>";
if ( isset($_POST['TID']) ) {
	$query = mysql_query("select TName, Date from Tournaments where TID='" . $_POST['TID'] . "';");
	if ( mysql_num_rows($query) != 1 ) {
		echo '<html>
<head><title>Tournament Information</title></head>
<body>
<h1>Choose a Tournament</h1>
<h3>Error - That tournament is invalid.</h3>
<h3>This page will return ranking contestants from that tournament.</h3>
<form id="TournamentInfo" action="TournamentSummary.php" method="post">
' . $TournamentString . '<br>
<input type="submit" value="Select">
</form>
</body>
</html>';
		return 0;
	}
	$Data = mysql_fetch_assoc($query);
	echo '<html>
<head><title>Tournament Information</title>
<style>
#tab {
	padding-left:1em;
}
</style>
</head>
<body>
<h1>Tournament: ' . $Data['TName'] . '</h1>
<h3>Date: ' . $Data['Date'] . '</h3>
<form id="TournamentInfo" action="TournamentSummary.php" method="post">
' . $TournamentString . '<br>
<input type="submit" value="Select">
</form>
';
	/*$query = mysql_query('select concat(LName, ", ", FName) as Name, place, EName from Events, Students, Results where TID="' . $_POST['TID'] . '" and Events.EID = Results.EID and Students.SID = Results.SID and place is not null and State = "1" order by place, Name;');
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$OldPlace = 0;
	echo '<h3>State Qualifiers</h4>';
	while ( $CurrentRow < $NumRows ) {
		
		$CurrentRow++;
	}*/
	echo '<i>Those marked with an asterisk (*) qualified for state</i><br>
';
	$query = mysql_query('select concat(LName, ", ", FName) as Name, place, EName, State from Events, Students, Results where TID="' . $_POST['TID'] . '" and Events.EID = Results.EID and Students.SID = Results.SID and place is not null order by place, Name;');
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$OldPlace = 0;
	while ( $CurrentRow < $NumRows ) {
		$Data = mysql_fetch_assoc($query);
		if ( $Data['place'] > $OldPlace ) {
			$OldPlace = $Data['place'];
			if ( $OldPlace > 3 ) {
				echo '<b><u>' . $OldPlace . 'th Place</b></u><br>
';
			} elseif ( $OldPlace == 3 ) {
				echo '<b><u>3rd Place</b></u><br>
';
			} elseif ( $OldPlace == 1 ) {
				echo '<b><u>1st Place</b></u><br>
';
			} elseif ( $OldPlace == 2 ) {
				echo '<b><u>2nd Place</b></u><br>
';
			}
		}
		if ( $Data['State'] == "1" ) {
			echo '<span id="tab"><b>*' . $Data['Name'] . '</b> - ' . $Data['EName'] . '</span><br>
';
		} else {
			echo '<span id="tab"><b>' . $Data['Name'] . '</b> - ' . $Data['EName'] . '</span><br>
';
		}
		$CurrentRow++;
	}
	echo '</body>
</html>';
} else {
	echo '<html>
<head><title>Tournament Information</title></head>
<body>
<h1>Choose a Tournament</h1>
<h3>This page will return ranking contestants from that tournament.</h3>
<form id="TournamentInfo" action="TournamentSummary.php" method="post">
' . $TournamentString . '<br>
<input type="submit" value="Select">
</form>
</body>
</html>';
}
?>

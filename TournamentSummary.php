<?php
include "MySQLAuth.php";
include "CommonFunctions.php";
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
' . Tournaments(0) . '<br>
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
<i>Those marked with an asterisk (*) qualified for state</i><br>
';
	$query = mysql_query('select SID2, concat(LName, ", ", FName) as Name, place, EName, State from Events, Students, Results where TID="' . $_POST['TID'] . '" and Events.EID = Results.EID and Students.SID = Results.SID and place is not null order by place, Name;');
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
		$Name = $Data['Name'];
		if ( $Data['SID2'] != NULL ) {
			$NameQuery = mysql_query("select concat(LName, ', ', FName) as Name from Students where SID='" . $Data['SID2'] . "';");
			$NameData = mysql_fetch_assoc($NameQuery);
			$Name = $Name . " and " . $NameData['Name'];
		}
		if ( $Data['State'] == "1" ) {
			echo '<span id="tab"><b>*' . $Name . '</b> - ' . $Data['EName'] . '</span><br>
';
		} else {
			echo '<span id="tab"><b>' . $Name . '</b> - ' . $Data['EName'] . '</span><br>
';
		}
		$CurrentRow++;
	}
	echo '</body>
</html>';
	return 0;
}
?>
<html>
<head><title>Tournament Information</title></head>
<body>
<h1>Tournament Summary</h1>
<?php MakeHeader(); ?>
<h3>This page will return ranking contestants from that tournament.</h3>
<form id="TournamentInfo" action="TournamentSummary.php" method="post">
<?php echo Tournaments(0); ?><br>
<input type="submit" value="Select">
</form>
</body>
</html>

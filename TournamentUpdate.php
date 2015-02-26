<?php
include "MySQLAuth.php";
if ( isset($_POST['RID']) ) {
	if ( ! isset($_POST['SID']) ) {
		echo "Error - No partner selected.";
		return 0;
	}
	$query = mysql_query("update Results set SID2='" . $_POST['SID'] . "' where RID='" . $_POST['RID'] . "';");
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		return 0;
	}
	echo "true";
	return 0;
}
$tbl = "Tournaments";
if (( ! isset($_POST['TID']) )) {
	echo "Error - No tournament ID specified";
	return 0;
}
$query = mysql_query("SELECT NumRounds, NumFinalsJudges from Tournaments where TID='" . $_POST['TID'] . "';");
$NumRows = mysql_num_rows($query);
if ( $NumRows == 0 ) {
	echo "Error - No tournament with that ID";
	return 0;
}
$data = mysql_fetch_assoc($query);
$NumRounds = $data['NumRounds'];
$NumJudges = $data['NumFinalsJudges'];
if ( ! isset($_POST['broke']) ) {
	echo "Error - No information on whether participant broke.";
	return 0;
} elseif ( $_POST['broke'] == "1" && ! isset($_POST['place']) ) {
	echo "Error - No place listed.";
	return 0;
}
if (( ! isset($_POST['qual']) )) {
	echo "Error - No 'State Qualifier' checkbox information.";
	return 0;
}
if (( ! isset($_POST['Event']) )) {
	echo "Error - No event information.";
	return 0;
}
if (( ! isset($_POST['Student']) )) {
	echo "Error - No student information.";
	return 0;
}
$PString = "";
if ( $_POST['broke'] == "1" ) {
	$PString = ", place='" . $_POST['place'] . "'";
}
$query = mysql_query("insert into Results set SID='" . $_POST['Student'] . "', EID='" . $_POST['Event'] . "', TID='" . $_POST['TID'] . "', broke='" . $_POST['broke'] . "', State='" . $_POST['qual'] . "'" . $PString . ";");
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	return 0;
}
$query = mysql_query("select last_insert_id() as RID;");
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	return 0;
}
$data = mysql_fetch_assoc($query);
$RID = $data['RID'];
if (( $_POST['broke'] == 1 )) {
	for ( $x = 1; $x <= $NumJudges; $x++ ) {
		if (( ! isset($_POST['J' . $x . 'R']) || ! isset($_POST['J' . $x . 'Q']) )) {
			echo "Error - Either quals or ranks for judge " . $x . " in finals are missing.";
			return 0;
		}
		$query = mysql_query("insert into Ballots set RID='" . $RID . "', Judge='" . $x . "', Rank='" . $_POST['J' . $x . 'R'] . "', Qual='" . $_POST['J' . $x . 'Q'] . "';");
		if (( mysql_errno() )) {
			echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . " on Judge " . $x . ".";
			return 0;
		}
	}
}
for ( $x = 1; $x <= $NumRounds; $x++ ) {
	if (( ! isset($_POST['R' . $x . 'R']) || ! isset($_POST['R' . $x . 'Q']) )) {
		echo "Error - Either quals or ranks for round " . $x . " are missing.";
		return 0;
	}
	$query = mysql_query("insert into Ballots set RID='" . $RID . "', Round='" . $x . "', Rank='" . $_POST['R' . $x . 'R'] . "', Qual='" . $_POST['R' . $x . 'Q'] . "';");
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . " on Round " . $x . ".";
		return 0;
	}
}
$query = mysql_query("select Partner from Events where EID='" . $_POST['Event'] . "';");
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	return 0;
}
$Data = mysql_fetch_assoc($query);
if ( $Data['Partner'] == "1" ) {
	$query = mysql_query("select FName, LName, SID from Students order by LName, FName;");
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$StudentString = '<select name="SID" form="SIDSelect" id="SID">';
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$StudentString = $StudentString . '<option value="' . $results['SID'] . '">' . $results['LName'] . ", " . $results['FName'] . "</option>";
		$CurrentRow++;
	}
	$StudentString = $StudentString . "</select>";
	echo 'Select the partner:<br>
<form id="SIDSelect" action="TournamentUpdate.php" method="post">
<input type="hidden" value="' . $RID . '" id="RID">
' . $StudentString . '<br>
<input type="button" value="Select" onclick="SubmitPartner();">
</form>
';
} else {
	echo "true";
}
?>

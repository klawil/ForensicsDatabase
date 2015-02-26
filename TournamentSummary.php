<?php
include "MySQLAuth.php";
if ( isset($_POST['TID']) ) {

} else {
	$query = mysql_query("select TName, TID from Tournaments order by Date desc, TName;");
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$TournamentString = '<select id="Tournament">';
	if ( isset($_POST['IncludeAll']) ) {
		$TournamentString = $TournamentString . "<option value='-1'>All Tournaments</option>";
	}
	while ( $CurrentRow < $NumRows ) {
		$results = mysql_fetch_assoc($query);
		$TournamentString = $TournamentString . '<option value="' . $results['TID'] . '">' . $results['TName'] . "</option>";
		$CurrentRow++;
	}
	$TournamentString = $TournamentString . "</select>";
	echo $TournamentString;
	echo '
';
?>

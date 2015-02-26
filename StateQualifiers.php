<?php
include "MySQLAuth.php";
echo '<html>
<head>
<title>State Qualifiers</title>
<style>
#tab {
	padding-left:1em;
}
</style>
</head>
<h1>State Qualifiers from KMC</h1>
';
$query = mysql_query("select distinct SID2, concat(LName, ', ', FName) as Name, EName from Results, Events, Students where State=1 and Results.EID = Events.EID and Students.SID = Results.SID order by EName, Name;");
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
	return 0;
}
$NumRows = mysql_num_rows($query);
$CurrentRow = 0;
$EName = "";
while ( $CurrentRow < $NumRows ) {
	$Data = mysql_fetch_assoc($query);
	if ( $Data['EName'] != $EName ) {
		$EName = $Data['EName'];
		echo '<b><i>' . $EName . '</b></i><br>
';
	}
	$Name = $Data['Name'];
	if ( $Data['SID2'] != NULL ) {
		$NameQuery = mysql_query("select concat(LName, ', ', FName) as Name from Students where SID='" . $Data['SID2'] . "';");
		$NameData = mysql_fetch_assoc($NameQuery);
		$Name = $Name . " and " . $NameData['Name'];
	}
	echo '<span id="tab"></span>' . $Name . '<br>
';
	$CurrentRow++;
}
?>

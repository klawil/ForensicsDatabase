<html>
<head>
<title>New Tournament</title>
</head>
<link rel="stylesheet" type="text/css" href="Styles.css">
<body>
<h1>New Tournament</h1>
<?php
include 'CommonFunctions.php';
MakeHeader();
if ( isset($_POST['TName']) ) {
	include "MySQLAuth.php";
	$tbl = "Tournaments";
	if ( strlen($_POST['TName']) > 30 ) {
		echo 'Name is too long. Must be 30 characters or less.
';
	} elseif ( strlen($_POST['TblName']) > 15 ) {
		echo 'Table Name is too long. Must be 15 characters or less.
';
	} else {
		$query = mysql_query("INSERT INTO Tournaments SET TName='" . $_POST['TName'] . "', Date='" . $_POST['Date'] . "', NumRounds='" . $_POST['NumRounds'] . "', NumFinalsJudges='" . $_POST['NumFinalsJudges'] . "';");
		if (( mysql_errno() )) {
			echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
			break;
		}
		echo $_POST['TName'] . " added succesfully.";
	}
}
?>
<form id="NewTournament" action="NewTournament.php" method="post">
<table>
    <tr><td>Name</td><td><input type="text" name="TName" onblur="CheckTName();"></td><td id="TNameWarn"></td></tr>
    <tr><td>Rounds</td><td><input type="number" name="NumRounds"></td></tr>
    <tr><td>Finals Judges</td><td><input type="number" name="NumFinalsJudges"></td></tr>
    <tr><td>Date</td><td><input type="date" name="Date"></td></tr>
</table>
<input type="submit" value="Submit">
</form>
<script>
function CheckTName(){
    var TName = document.forms["NewTournament"]["TName"].value;
    if ( TName.length > 30 ) {
        document.getElementById("TNameWarn").innerHTML = "Must be 30 characters or less";
    } else {
        document.getElementById("TNameWarn").innerHTML = "";
    }
}
</script>
</body>
</html>

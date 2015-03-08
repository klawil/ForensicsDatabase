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
if ( $GLOBALS['CanUserEdit'] != 1 ) {
	echo '<h2>Authentication Error: You do not have the permission to access this page</h2>
</body>
</html>';
	return 0;
}
if ( isset($_POST['TName']) ) {
	$tbl = "Tournaments";
	if ( strlen($_POST['TName']) > 50 ) {
		echo 'Name is too long. Must be 50 characters or less.
';
	} else {
		$query = mysqli_query($DBConn, "INSERT INTO Tournaments SET TName='" . $_POST['TName'] . "', Date='" . $_POST['Date'] . "', NumRounds='" . $_POST['NumRounds'] . "', NumFinalsJudges='" . $_POST['NumFinalsJudges'] . "';");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
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
    if ( TName.length > 50 ) {
        document.getElementById("TNameWarn").innerHTML = "Must be 50 characters or less";
    } else {
        document.getElementById("TNameWarn").innerHTML = "";
    }
}
</script>
</body>
</html>

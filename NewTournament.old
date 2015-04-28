<html>
<head>
<title>New Tournament | ForensicsDB.com</title>
</head>
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 800px)" href="MobileStyles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<body>
<h1>New Tournament</h1>
<?php
include 'CommonFunctions.php';
MakeHeader();
if ( $GLOBALS['CanUserEdit'] != 1 ) {
	$myfile = fopen("/var/log/forensics/general.log","a");
	fwrite($myfile,"IP " . $_SERVER['REMOTE_ADDR'] . " tried to access restricted page " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
	fclose($myfile);
	echo '<h2>Authentication Error: You do not have the permission to access this page</h2>
</body>
</html>';
	return 0;
}
if ( isset($_POST['TName']) ) { do{
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
		$myfile = fopen("/var/log/forensics/general.log","a");
		if ( $GLOBALS['UserName'] != "" ) {
			fwrite($myfile, "User " . $GLOBALS['UserName'] . " from ");
		}
		fwrite($myfile, "IP " . $_SERVER['REMOTE_ADDR'] . " accessed " . basename($_SERVER['PHP_SELF']) . " on " . date('Y-m-d') . " at " . date('H:i:s') . " (Created Tournament " . $_POST['TName'] . ")\n");
		fclose($myfile);
	}
} while (false);}
?>
<form id="NewTournament" action="NewTournament.php" method="post">
<table>
    <tr><td>Name</td><td><input type="text" name="TName" onblur="CheckTName();"></td><td id="TNameWarn"></td></tr>
    <tr><td>Prelim Rounds</td><td><input type="number" name="NumRounds"></td></tr>
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

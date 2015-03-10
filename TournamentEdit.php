<?php
include 'CommonFunctions.php';
if ( isset($_POST['TID']) ) {
	$WString = " where TID='" . $_POST['TID'] . "'";
	
}
?>
<html>
<head>
<title>Tournament Edit</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
</head>
<body>
<h1>Tournament Edit</h1>
<?php
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
$query = mysqli_query($DBConn "update Ballots set Rank=1 where RID=119 and Judge=1;");
if ( !$query ) {
	echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
	return 0;
}
?>
Select Tournament
<div id="Tourneys"><?php echo Tournaments(0); ?> <input type="button" value="Select" onclick="MakePage();"></div><form id="EntryID" action="TournamentUpdate.php"><div id="TourneyID"></div>
<script>
function MakePage() {
	TID = document.getElementById("Tournament").options[document.getElementById("Tournament").selectedIndex].value;
	window.alert(TID);
}
</script>
</body>
</html>

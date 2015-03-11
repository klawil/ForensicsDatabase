<html>
<head>
<title>KMC Forensics</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
</head>
<body>
<h1>KMC Forensics</h1>
<?php
$ErrorString = "";
include 'CommonFunctions.php';
MakeHeader();
$query = mysqli_query($DBConn, "select concat(LName, ', ', FName) as Name, EName, TName, place from Events, Results, Tournaments, Students where Results.EID = Events.EID and Results.broke = 1 and Results.TID = Tournaments.TID and Results.SID = Students.SID order by EName, Date;");
if ( !$query ) {
	$ErrorString =  "Error - MySQL error: " . mysqli_error($DBConn) . ".";
}
echo "<h2>Medals from Kapaun Mt. Carmel Catholic High School</h2>
";
if ( $ErrorString == "" && mysqli_num_rows($query) > 0 ) {
	$NumRows = mysqli_num_rows($query);
	$FirstThird = floor($NumRows/3);
	$SecondThird = floor(2*$NumRows/3);
	$Third = 1;
	$CurrentRow = 0;
	$Event = "";
	echo '<div class="main">
<div class="left">
';
	while ( $CurrentRow < $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		if ( $Data['EName'] != $Event ) {
			if ( $Third == 1 && $CurrentRow >= $FirstThird ) {
				echo '</div>
<div class="center">
';
				$Third = 2;
			} elseif ( $Third == 2 && $CurrentRow >= $SecondThird ) {
				echo '</div>
<div class="right">
';
				$Third = 3;
			}
			echo "<b>" . $Data['EName'] . "</b><br>
";
			$Event = $Data['EName'];
		}
		if ( $Data['place'] == 1 ) {
			$place = "1st";
		} elseif ( $Data['place'] == 2 ) {
			$place = "2nd";
		} elseif ( $Data['place'] == 3 ) {
			$place = "3rd";
		} else {
			$place = $Data['place'] . "th";
		}
		echo '<span id="tab"></span>' . $Data['Name'] . " - " . $Data['TName'] . " - " . $place . "<br>
";
		$CurrentRow++;
	}
	echo '</div>
</div>
';
} else {
	echo "No medals<br>" . $ErrorString . "
";
}
?>
</body>
</html>

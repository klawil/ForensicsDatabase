<?php
include "CommonFunctions.php";
$ErrorString = "";
$TName = "";
$ResultsString = "";
if ( isset($_POST['TID']) ) {
	$query = mysqli_query($DBConn, "select TName, Date from Tournaments where TID='" . $_POST['TID'] . "';");
	if ( !$query ) {
		$ErrorString = "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		break;
	}
	if ( mysqli_num_rows($query) != 1 ) {
		$ErrorString = "That tournament is invalid. TID is " . $_POST['TID'];
		break;
	}
	$Data = mysqli_fetch_assoc($query);
	$TName = $Data['TName'];
	$TDate = $Data['Date'];
	$query = mysqli_query($DBConn, 'select SID2, concat(LName, ", ", FName) as Name, place, EName, State from Events, Students, Results where TID="' . $_POST['TID'] . '" and Events.EID = Results.EID and Students.SID = Results.SID and place is not null order by place, Name;');
	$NumRows = mysqli_num_rows($query);
	$CurrentRow = 0;
	$OldPlace = 0;
	$ResultsString = '<i>Those marked with an asterisk (*) qualified for state</i><br>
';
	while ( $CurrentRow < $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		if ( $Data['place'] > $OldPlace ) {
			$OldPlace = $Data['place'];
			if ( $OldPlace > 3 ) {
				$ResultsString = $ResultsString . '<b><u>' . $OldPlace . 'th Place</b></u><br>
';
			} elseif ( $OldPlace == 3 ) {
				$ResultsString = $ResultsString . '<b><u>3rd Place</b></u><br>
';
			} elseif ( $OldPlace == 1 ) {
				$ResultsString = $ResultsString . '<b><u>1st Place</b></u><br>
';
			} elseif ( $OldPlace == 2 ) {
				$ResultsString = $ResultsString . '<b><u>2nd Place</b></u><br>
';
			}
		}
		$Name = $Data['Name'];
		if ( $Data['SID2'] != NULL ) {
			$NameQuery = mysqli_query($DBConn, "select concat(LName, ', ', FName) as Name from Students where SID='" . $Data['SID2'] . "';");
			$NameData = mysqli_fetch_assoc($NameQuery);
			$Name = $Name . " and " . $NameData['Name'];
		}
		if ( $Data['State'] == "1" ) {
			$ResultsString = $ResultsString . '<span id="tab"><b>*' . $Name . '</b> - ' . $Data['EName'] . '</span><br>
';
		} else {
			$ResultsString = $ResultsString . '<span id="tab"><b>' . $Name . '</b> - ' . $Data['EName'] . '</span><br>
';
		}
		$CurrentRow++;
	}
}
?>
<html>
<head><title>Tournament Information | ForensicsDB.com</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 800px)" href="MobileStyles.css" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
</head>
<body>
<?php
if ( $TName == "" ) {
	echo '<h1>Tournament Summary</h1>
';
} else {
	echo '<h1>' . $TName . '</h1>
';
}
MakeHeader();
if ( $TName != "" ) {
	echo '<h3>Date: ' . $TDate . '</h3>
';
} else {
	echo '<h3>This page will return ranking contestants from that tournament.</h3>
';
}
?>
<h3><?php echo $ErrorString; ?></h3>
<form id="TournamentInfo" action="TournamentSummary.php" method="post">
<?php
if ( isset($_POST['TID']) ) {
	echo Tournaments(0,$_POST['TID']);
} else {
	echo Tournaments(0);
}
?><br>
<input type="submit" value="Select">
</form>
<?php echo $ResultsString; ?>
</body>
</html>

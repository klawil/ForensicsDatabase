<?php
include "CommonFunctions.php";
if ( isset($_POST['SID']) ) {
	$ErrorString = "";
	$query = mysqli_query($DBConn, "select concat(LName, ', ', FName) as NameLF, concat(FName, ' ', LName) as NameFL, Year from Students where SID='" . $_POST['SID'] . "';");
	if ( !$query ) {
		$ErrorString =  "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		break;
	}
	$Data = mysqli_fetch_assoc($query);
	$NameLF = $Data['NameLF'];
	$NameFL = $Data['NameFL'];
	$Year = $Data['Year'];
	$query = mysqli_query($DBConn, "select EName from Results, Events where (SID='" . $_POST['SID'] . "' or SID2='" . $_POST['SID'] . "') and Results.EID = Events.EID group by EName order by EName;");
	if ( !$query ) {
		$ErrorString =  "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		break;
	}
	$NumRows = mysqli_num_rows($query);
	$CurrentRow = 1;
	$Events = array();
	while ( $CurrentRow <= $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		$Events[$Data['EName']] = $CurrentRow;
		$CurrentRow++;
	}
}
?>
<html>
<head><title>Student Summary</title>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1.1', {packages: ['corechart', 'imagelinechart']});
</script>
<link rel="stylesheet" type="text/css" href="Styles.css">
<link rel="stylesheet" media="(max-width: 800px)" href="MobileStyles.css" />
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
<?php
if ( isset($_POST['SID']) && $ErrorString == "" ) {
	echo '<h1>Student Summary: ' . $NameFL . '</h1>
';
} elseif ( isset($_POST['SID']) ) {
	echo '<h1>Student Summary Page</h1>
<h3>' . $ErrorString . '</h3>
';
} else {
	echo '<h1 id="Title">Student Summary Page</h1>';
}
MakeHeader();
?>
<form id="StudentSelect" action="StudentSummary.php" method="post">
Select a student: <?php
if ( isset($_POST['SID']) && $ErrorString == "" ) {
	echo Students(0,"StudentSelect",$_POST['SID']);
} else {
	echo Students(0,"StudentSelect");
}
?>
<input type="submit" value="select">
</form>
<div class="sidebar" style="display: float; float: left; padding-right: 10; width: 18%">
<?php
if ( isset($_POST['SID']) && $ErrorString == "" ) { do {
	echo '<h3>Year: ' . $Year . '</h3>
<br><h3>Events</h3>
';
	foreach($Events as $key => $value) {
		echo $key . "<br>
";
	}
	$query = mysqli_query($DBConn, "select distinct EName from Events, Results where Results.EID = Events.EID and (Results.SID='" . $_POST['SID'] . "' or Results.SID2='" . $_POST['SID'] . "') and Results.State = 1 order by EName;");
	echo '<br><h3>State Qualifications</h3>
';
	if ( !$query ) {
		$ErrorString =  "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		break;
	}
	if ( $ErrorString == "" && mysqli_num_rows($query) > 0 ) {
		$NumRows = mysqli_num_rows($query);
		$CurrentRow = 0;
		while ( $CurrentRow < $NumRows ) {
			$Data = mysqli_fetch_assoc($query);
			echo $Data['EName'] . "<br>
";
			$CurrentRow++;
		}
	} else {
		echo "No state qualifications<br>" . $ErrorString . "
";
	}
	echo '<br><h3>Medals</h3>
';
	$query = mysqli_query($DBConn, "select EName, TName, place from Events, Results, Tournaments where Results.EID = Events.EID and (Results.SID='" . $_POST['SID'] . "' or Results.SID2='" . $_POST['SID'] . "') and Results.broke = 1 and Results.TID = Tournaments.TID order by EName, place, Date;");
	if ( !$query ) {
		$ErrorString =  "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		break;
	}
	if ( $ErrorString == "" && mysqli_num_rows($query) > 0 ) {
		$NumRows = mysqli_num_rows($query);
		$CurrentRow = 0;
		$Event = "";
		while ( $CurrentRow < $NumRows ) {
			$Data = mysqli_fetch_assoc($query);
			if ( $Data['EName'] != $Event ) {
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
			echo '<span id="tab"></span>' . $Data['TName'] . " - " . $place . "<br>
";
			$CurrentRow++;
		}
	} else {
		echo "No medals<br>" . $ErrorString . "
";
	}
	echo "</div>
<div style='display: inline-block; width: 80%'>
<div id='visualization' style='width: 100%;'></div></div>
<script>
function MakeGraph() {
	var data = new google.visualization.DataTable();
	data.addColumn('string','Tournament');
";
	foreach ( $Events as $key => $value ) {
		echo "data.addColumn('number', '" . $key . "');
";
	}
	$query = mysqli_query($DBConn, "select TName from Results, Tournaments where (SID='" . $_POST['SID'] . "' or SID2='" . $_POST['SID'] . "') and Results.TID = Tournaments.TID group by Date order by Date;");
	if ( !$query ) {
		$ErrorString = "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		echo $ErrorString;
		break;
	}
	$NumRows = mysqli_num_rows($query);
	$CurrentRow = 0;
	echo "data.addRows(" . $NumRows . ");
";
	while ( $CurrentRow < $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		echo "data.setValue(" . $CurrentRow . ",0,'" . $Data['TName'] . "');
";
		$CurrentRow++;
	}
	$query = mysqli_query($DBConn, "select EName, Date, avg(PRanks/NumberRounds) as Ranks from Results, Tournaments, Events where Results.TID = Tournaments.TID and Results.EID = Events.EID and (Results.SID='" . $_POST['SID'] . "' or Results.SID2='" . $_POST['SID'] . "') group by Date, EName order by Date, EName;");
	if ( !$query ) {
		$ErrorString = "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		echo $ErrorString;
		break;
	}
	$NumRows = mysqli_num_rows($query);
	$CurrentRow = 0;
	$Row = -1;
	$Date1 = "";
	while ( $NumRows > $CurrentRow ) {
		$Data = mysqli_fetch_assoc($query);
		if ( $Data['Date'] != $Date1 ) {
			$Date1 = $Data['Date'];
			$Row++;
		}
		echo "data.setValue(" . $Row . "," . $Events[$Data['EName']] . "," . round($Data['Ranks'],2) . ");
";
		$CurrentRow++;
	}
	echo "var chart = new google.visualization.LineChart(document.getElementById('visualization'));
	chart.draw(data, {title: '" . $NameFL . " Summary', height: 500, vAxis: { title: 'Average Ranks', direction: -1, gridlines: {count: 10}, viewWindowMode:'explicit', viewWindow: {max: 0, min: 9}}, interpolateNulls: true});
	}
	google.setOnLoadCallback(MakeGraph);
</script>";
} while ( false ); } elseif ( isset($_POST['SID']) ) {
	echo $ErrorString;
}
?>
</body>
</html>

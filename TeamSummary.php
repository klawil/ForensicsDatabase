<?php
include "CommonFunctions.php";
?>
<html>
<head>
<title>Team Summary</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1.1', {packages: ['corechart', 'imagelinechart']});
</script>
</head>
<body>
<h1>Team Summary</h1>
<?php MakeHeader(); ?>
<div style="display: float; float: right;">
<h3>State Qualifiers</h3>
<?php
$query = mysqli_query($DBConn, 'select distinct SID2, concat(LName, ", ", FName) as Name, EName from Students, Events, Results, Tournaments where Results.TID = Tournaments.TID and Results.EID = Events.EID and Results.State = 1 and Results.SID = Students.SID order by EName, Date;');
if ( !$query ) {
	echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
} else {
	$NumRows = mysqli_num_rows($query);
	$CurrentRow = 0;
	$Event = "";
	while ( $CurrentRow < $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		if ( $Data['EName'] != $Event ) {
			$Event = $Data['EName'];
			echo "<b><u>" . $Event . "</b></u><br>
";
		}
		$Name = $Data['Name'];
		if ( $Data['SID2'] != NULL ) {
			$NameQuery = mysqli_query($DBConn, "select concat(LName, ', ', FName) as Name from Students where SID='" . $Data['SID2'] . "';");
			if ( !$query ) {
				echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
				break;
			}
			$NameData = mysqli_fetch_assoc($NameQuery);
			$Name = $Name . " and " . $NameData['Name'];
		}
		echo '<span id="tab"></span>' . $Name . '<br>
';
		$CurrentRow++;
	}
}
$query = mysqli_query($DBConn, 'select EName from Results, Events where Results.EID = Events.EID group by EName order by EName;');
if ( !$query ) {
	echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
} else {
	$NumRows = mysqli_num_rows($query);
	$CurrentRow = 1;
	$Events = array();
	while ( $CurrentRow <= $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		$Events[$Data['EName']] = $CurrentRow;
		$CurrentRow++;
	}
}
echo "</div>
<div id='visualization' style='width: 84%; height: 92%; display: float; float: left; margin-top: -50; margin-left: -50; margin-right: -50;'></div>
<script>
function MakeGraph() {
	var data = new google.visualization.DataTable();
	data.addColumn('string','Tournament');
	";
foreach ( $Events as $key => $value ) {
	echo "data.addColumn('number','" . $key . "');
	";
}
$query = mysqli_query($DBConn, "select TName, Date from Results, Tournaments where Results.TID = Tournaments.TID group by Date order by Date;");
if ( !$query ) {
	echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
} else {
	$CurrentRow = 1;
	$NumRows = mysqli_num_rows($query);
	$Tournaments = array();
	echo "data.addRows(" . $NumRows . ");
	";
	while ( $CurrentRow <= $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		echo "data.setValue(" . ($CurrentRow - 1) . ",0,'" . $Data['TName'] . "');
	";
		$Tournaments[$Data['Date']] = ($CurrentRow - 1);
		$CurrentRow++;
	}
}
$query = mysqli_query($DBConn, "select EName, Date, avg(PRanks/NumberRounds) as Ranks from Results, Tournaments, Events where Results.TID = Tournaments.TID and Results.EID = Events.EID group by Date, EName order by Date, EName;");
if ( !$query ) {
	echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
} else {
	$Row = -1;
	$Date1 = "";
	$CurrentRow = 0;
	$NumRows = mysqli_num_rows($query);
	while ( $CurrentRow < $NumRows ) {
		$Data = mysqli_fetch_assoc($query);
		if ( $Data['Date'] != $Date1 ) {
			$Date1 = $Data['Date'];
			$Row++;
		}
		echo "data.setValue(" . $Row . "," . $Events[$Data['EName']] . "," . round($Data['Ranks'],2) . ");
	";
		$CurrentRow++;
	}
}
echo "var chart = new google.visualization.LineChart(document.getElementById('visualization'));
	chart.draw(data, {title: 'Team Summary', vAxis: { title: 'Average Ranks', direction: -1, gridlines: {count: 7}, viewWindowMode:'explicit', viewWindow: {max: 0, min: 6}}, interpolateNulls: true});
}
google.setOnLoadCallback(MakeGraph);
</script>
";
?>
</body>
</html>

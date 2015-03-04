<?php
include "MySQLAuth.php";
include "CommonFunctions.php";
?>
<html>
<head>
<title>Team Summary</title>
<style>
#tab {
	padding-left:1em;
}
</style>
<script type="text/javascript" src="https://www.google.com/jsapi"></script>
<script type="text/javascript">
	google.load('visualization', '1.1', {packages: ['corechart', 'imagelinechart']});
</script>
</head>
<body>
<h1>Team Summary</h1>
<h3>State Qualifiers</h3>
<?php
$query = mysql_query('select distinct SID2, concat(LName, ", ", FName) as Name, EName from Students, Events, Results, Tournaments where Results.TID = Tournaments.TID and Results.EID = Events.EID and Results.State = 1 and Results.SID = Students.SID order by EName, Date;');
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
} else {
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$Event = "";
	while ( $CurrentRow < $NumRows ) {
		$Data = mysql_fetch_assoc($query);
		if ( $Data['EName'] != $Event ) {
			$Event = $Data['EName'];
			echo "<b><u>" . $Event . "</b></u><br>
";
		}
		$Name = $Data['Name'];
		if ( $Data['SID2'] != NULL ) {
			$NameQuery = mysql_query("select concat(LName, ', ', FName) as Name from Students where SID='" . $Data['SID2'] . "';");
			if (( mysql_errno() )) {
				echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
				break;
			}
			$NameData = mysql_fetch_assoc($NameQuery);
			$Name = $Name . " and " . $NameData['Name'];
		}
		echo '<span id="tab"></span>' . $Name . '<br>
';
		$CurrentRow++;
	}
}
$query = mysql_query('select EName from Results, Events where Results.EID = Events.EID group by EName order by EName;');
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
} else {
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 1;
	$Events = array();
	while ( $CurrentRow <= $NumRows ) {
		$Data = mysql_fetch_assoc($query);
		$Events[$Data['EName']] = $CurrentRow;
		$CurrentRow++;
	}
}
echo "<div id='visualization' style='width: 99%; height: 100%;'></div>
<script>
function MakeGraph() {
	var data = new google.visualization.DataTable();
	data.addColumn('string','Tournament');
	";
foreach ( $Events as $key => $value ) {
	echo "data.addColumn('number','" . $key . "');
	";
}
$query = mysql_query("select TName, Date from Results, Tournaments where Results.TID = Tournaments.TID group by Date order by Date;");
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
} else {
	$CurrentRow = 1;
	$NumRows = mysql_num_rows($query);
	$Tournaments = array();
	echo "data.addRows(" . $NumRows . ");
	";
	while ( $CurrentRow <= $NumRows ) {
		$Data = mysql_fetch_assoc($query);
		echo "data.setValue(" . ($CurrentRow - 1) . ",0,'" . $Data['TName'] . "');
	";
		$Tournaments[$Data['Date']] = ($CurrentRow - 1);
		$CurrentRow++;
	}
}
$query = mysql_query("select EName, Date, avg(PRanks/NumberRounds) as Ranks from Results, Tournaments, Events where Results.TID = Tournaments.TID and Results.EID = Events.EID group by Date, EName order by Date, EName;");
if (( mysql_errno() )) {
	echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
} else {
	$Row = -1;
	$Date1 = "";
	$CurrentRow = 0;
	$NumRows = mysql_num_rows($query);
	while ( $CurrentRow < $NumRows ) {
		$Data = mysql_fetch_assoc($query);
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
	chart.draw(data, {vAxis: { direction: -1, gridlines: {count: 7}, viewWindowMode:'explicit', viewWindow: {max: 0, min: 6}}, interpolateNulls: true});
}
google.setOnLoadCallback(MakeGraph);
</script>
";
?>
</body>
</html>

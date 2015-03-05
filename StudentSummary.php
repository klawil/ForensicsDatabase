<?php
include "MySQLAuth.php";
include "CommonFunctions.php";
if ( isset($_POST['SID']) ) {
	$ErrorString = "";
	$query = mysql_query("select concat(LName, ', ', FName) as NameLF, concat(FName, ' ', LName) as NameFL, Year from Students where SID='" . $_POST['SID'] . "';");
	if (( mysql_errno() )) {
		$ErrorString =  "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		break;
	}
	$Data = mysql_fetch_assoc($query);
	$NameLF = $Data['NameLF'];
	$NameFL = $Data['NameFL'];
	$Year = $Data['Year'];

	$query = mysql_query("select EName from Results, Events where (SID='" . $_POST['SID'] . "' or SID2='" . $_POST['SID'] . "') and Results.EID = Events.EID group by EName order by EName;");
	if (( mysql_errno() )) {
		$ErrorString =  "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		break;
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 1;
	$Events = array();
	while ( $CurrentRow <= $NumRows ) {
		$Data = mysql_fetch_assoc($query);
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
Select another student: <?php echo Students(0,"StudentSelect"); ?>
<input type="submit" value="select">
</form>
<div style="display: float; float: left; padding-right: 10; width: 18%">
<?php
if ( isset($_POST['SID']) && $ErrorString == "" ) {
	echo '<h3>Year: ' . $Year . '</h3>
<h3>Events</h3>
';
	foreach($Events as $key => $value) {
		echo $key . "<br>
";
	}
	$query = mysql_query("select distinct EName from Events, Results where Results.EID = Events.EID and (Results.SID='" . $_POST['SID'] . "' or Results.SID2='" . $_POST['SID'] . "') and Results.State = 1 order by EName;");
	echo '<h3>State Qualifications</h3>
';
	if (( mysql_errno() )) {
		$ErrorString =  "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		break;
	}
	if ( $ErrorString == "" && mysql_num_rows($query) > 0 ) {
		$NumRows = mysql_num_rows($query);
		$CurrentRow = 0;
		while ( $CurrentRow < $NumRows ) {
			$Data = mysql_fetch_assoc($query);
			echo $Data['EName'] . "<br>
";
			$CurrentRow++;
		}
	} else {
		echo "No state qualifications<br>" . $ErrorString . "
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
	$query = mysql_query("select TName from Results, Tournaments where (SID='" . $_POST['SID'] . "' or SID2='" . $_POST['SID'] . "') and Results.TID = Tournaments.TID group by Date order by Date;");
	if (( mysql_errno() )) {
		$ErrorString = "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		echo $ErrorString;
		break;
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	echo "data.addRows(" . $NumRows . ");
";
	while ( $CurrentRow < $NumRows ) {
		$Data = mysql_fetch_assoc($query);
		echo "data.setValue(" . $CurrentRow . ",0,'" . $Data['TName'] . "');
";
		$CurrentRow++;
	}
	$query = mysql_query("select EName, Date, avg(PRanks/NumberRounds) as Ranks from Results, Tournaments, Events where Results.TID = Tournaments.TID and Results.EID = Events.EID and (Results.SID='" . $_POST['SID'] . "' or Results.SID2='" . $_POST['SID'] . "') group by Date, EName order by Date, EName;");
	if (( mysql_errno() )) {
		$ErrorString = "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		echo $ErrorString;
		break;
	}
	$NumRows = mysql_num_rows($query);
	$CurrentRow = 0;
	$Row = -1;
	$Date1 = "";
	while ( $NumRows > $CurrentRow ) {
		$Data = mysql_fetch_assoc($query);
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
} elseif ( isset($_POST['SID']) ) {
	echo $ErrorString;
}
?>
</body>
</html>

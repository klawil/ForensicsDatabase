<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Tournament Management';
require_once 'restrictedpage.inc';

$ErrorString = '';

if ( isset($_POST['TournamentID']) ) { do {
	// Set TournamentID and validate it
	$TournamentID = MySQLEscape($_POST['TournamentID'],$DBConn);
	if ( !IsID($DBConn,$TournamentID,'TournamentID') ) {
		$ErrorString = 'Invalid tournament';
		unset($_POST['TournamentID']);
		break;
	}
	
	// Get Tournament Data
	$TournamentQuery = MySQLQuery($DBConn,'select TournamentName, NumRounds, NumJudges, NumElimRounds, NumElimJudges from Tournaments where TournamentID="' . $TournamentID . '";');
	if ( !$TournamentQuery['Result'] ) {
		$ErrorString = $TournamentQuery['Result'];
		break;
	}
	$TournamentData = mysqli_fetch_assoc($TournamentQuery['Query']);
} while(false); }

require_once 'header.inc';
foreach ( $_POST as $Name => $Value ) {
	if ( is_array($Value) ) {
		echo $Name . '=>[';
		foreach ( $Value as $Name2 => $Value2 ) {
			echo $Name2 . '=>' . $Value2 . ', ';
		}
		echo ']';
	} else {
		echo $Name . '=>' . $Value . '<br>';
	}
}
?>
<style>
input[type=number] {
	width: 3em;
}
</style>
<?php if ( isset($TournamentData['TournamentName']) ) { ?>
<h2><?php echo $TournamentData['TournamentName']; ?></h2>
<?php } ?>
<h3>Insert results from tournament</h3>
<div id="PostMessage" class="ErrorMessage"><?php echo $ErrorString; ?></div>
<?php if ( !isset($_POST['TournamentID']) ) { ?>
<form id="TournamentSelect" action="ResultInsert.php" method="post">
Select a Tournament: <?php echo CreateList($DBConn, 'Tournaments'); ?> <input type="submit" value="Select Tournament">
</form>
<?php
} else {
?>
<form id="ResultForm" action="ResultInsert.php" method="post">
<input type="hidden" name="TournamentID" value="<?php echo $TournamentID; ?>">
Student: <?php echo CreateList($DBConn,'Students'); ?><br><br>
<div id="PartnerSelect" class="hidden">Partner: <?php echo CreateList($DBConn,'Students',NULL,NULL,'PartnerID'); ?><br><br></div>
Event: <?php echo CreateList($DBConn,'Events'); ?><br>
<table class="Table">
<tr>
	<th></th>
<?php
for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) {
	echo '<th colspan="2">Judge ' . $Judge . '</th>';
}
?>
</tr>
<tr>
	<th>Round</th>
<?php
for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) {
	echo '<th>Rank</th><th>Qual</th>';
}
?>
</tr>
<?php
	for ( $Round = 1; $Round <= $TournamentData['NumRounds']; $Round++ ) {
		echo '
	<tr>
		<td>Round ' . $Round . '</td>';
		for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) {
			echo '<td><input type="number" name="Round[' . $Round. '][' . $Judge . '][\'Rank\']"></td><td><input type="number" name="Round[' . $Round . '][' . $Judge . '][\'Qual\']"></td>';
		}
		echo '</tr>';
	}
?>
</table>
<input type="checkbox" name="broke" onchange="ShowHideElims()">Broke to elimination rounds<br>
<input type="checkbox" name="State">Qualified for the state tournament<br>
<div id="ElimTable" class="hidden">
<table class="Table">
<tr>
	<th></th>
<?php
	for ( $Judge = 1; $Judge <= $TournamentData['NumElimJudges']; $Judge++ ) {
		echo '<th colspan="2">Judge ' . $Judge . '</th>';
	}
?>
</tr>
<tr>
	<th>Round</th>
<?php
	for ( $Judge = 1; $Judge <= $TournamentData['NumElimJudges']; $Judge++ ) {
		echo '<th>Rank</th><th>Qual</th>';
	}
?>
</tr>
<?php
	// Create an elimination name array
	$ElimArray = [1 => "Finals", 2 => "Semis", 3 => "Quarters", 4 => "Octos", 5 => "Double Octos", 6 => "Triple Octos"];
	
	// Create the rows
	for ( $Round = $TournamentData['NumElimRounds']; $Round >= 1; $Round-- ) {
		echo '<tr><td>' . $ElimArray[$Round] . '</td>';
		for ( $Judge = 1; $Judge <= $TournamentData['NumElimJudges']; $Judge++ ) {
			echo '<td><input type="number" name="Judge[' . $Round. '][' . $Judge . '][\'Rank\']"></td><td><input type="number" name="Judge[' . $Round . '][' . $Judge . '][\'Qual\']"></td>';
		}
		echo '</tr>';
	}
?>
</table><br>
</div>
Place: <input type="number" name="place"><br><br>
<input type="Submit" value="Submit">
<?php
}
?>
</table>
</form>
<script>
function ShowHideElims() {
	if ( document.getElementById("broke").checked ) {
		DisplayStyle = "inline";
	} else {
		DisplayStyle = "none";
	}
	document.getElementById("ElimTable").style.display = DisplayStyle;
}
</script>
</body>
</html>

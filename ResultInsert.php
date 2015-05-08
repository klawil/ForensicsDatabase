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
<form id="ResultForm" target="ResultInsert.php" method="post">
Student: <?php echo CreateList($DBConn,'Students'); ?><br><br>
<div id="PartnerSelect" class="hidden">Partner: <?php echo CreateList($DBConn,'Students',NULL,NULL,'PartnerID'); ?><br><br></div>
Event: <?php echo CreateList($DBConn,'Events'); ?><br>
<table class="Table">
<tr>
	<th></th><?php for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) { ?><th colspan="2">Judge <?php echo $Judge; ?></th><?php } ?>
</tr>
<tr>
	<th>Round</th><?php for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) { ?><th>Rank</th><th>Qual</th><? } ?>
</tr>
<?php
	for ( $Round = 1; $Round <= $TournamentData['NumRounds']; $Round++ ) {
?>
<tr>
	<td>Round <?php echo $Round; ?></td><?php for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) { ?><td><input type="number" id="Round[<?php echo $Round; ?>][<?php echo $Judge; ?>]['Rank']"></td><td><input type="number" id="Round[<?php echo $Round; ?>][<?php echo $Judge; ?>]['Qual']"></td><?php } ?>
</tr>
<input type="checkbox" id="broke" onchange="ShowHideElims()">Broke to elimination rounds<br>
<input type="checkbox" id="State">Qualified for the state tournament<br>
<div id="ElimTable">
<table class="Table">
<tr>
	<th></th><?php for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) { ?><th colspan="2">Judge <?php echo $Judge; ?></th><?php } ?>
</tr>
<tr>
	<th>Round</th><?php for ( $Judge = 1; $Judge <= $TournamentData['NumJudges']; $Judge++ ) { ?><th>Rank</th><th>Qual</th><? } ?>
</tr>
</table>
</div>
<?php
	}
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

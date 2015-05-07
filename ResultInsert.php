<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Tournament Management';
require_once 'restrictedpage.inc';

$ErrorString = '';

require_once 'header.inc';
?>
<style>
input[type=number] {
	width: 3em;
}
</style>
<h3>Insert results from a tournament</h3>
<div id="PostMessage" class="ErrorMessage"><?php echo $ErrorString;?></div>
<?php if ( !isset($_POST['TournamentID']) ) { ?>
<form id="TournamentSelect" action="ResultInsert.php" method="post">
Select a Tournament: <?php echo CreateList($DBConn, 'Tournaments', NULL, NULL, NULL, NULL, 'TournamentSelect');?> <input type="submit" value="Select Tournament">
</form>
<?php } else {
	echo $_POST['TournamentID'];
}?>
</body>
</html>

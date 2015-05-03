<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'Student Management';
$StudentQuery = MySQLQuery($DBConn,'select * from Students;');
if ( !$StudentQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $StudentQuery['Query'];
	require_once 'ErrorPage.inc';
}
require_once 'header.inc';
if ( isset($_POST['SID']) ) {
	
}
?>
<h3>Update, create, and delete students</h3>
<table class="Table">
<tr><th>First Name</th><th>Last Name</th><th>Novice Season</th><th></th></tr>
<?php
$NumRows = mysqli_num_rows($StudentQuery['Query']);
$CurrentRow = 1;
while ( $CurrentRow <= $NumRows ) {
	$StudentData = mysqli_fetch_assoc($StudentQuery['Query']);
	echo '<tr><td id="FName' . $StudentData['SID'] . '">' . $StudentData['FName'] . '</td><td><input type="text" id="LName' . $StudentData['SID'] . '" value="' . $StudentData['LName'] . '"></td><td>' . CreateSeasonList($DBConn,NULL,$StudentData['NoviceYear'],"NYear" . $StudentData['SID']) . '</td><td><input type="button" onclick="SubmitStudent(' . $StudentData['SID'] . ')" value="Save Changes"></td></tr>';
	$CurrentRow++;
}
// LName FName NoviceYear SID
?>
<tr><td><input type="text" id="FName" width="100%"></td><td><input type="text" id="LName" width="100%"></td><td><?php echo CreateSeasonList($DBConn,NULL,NULL,'NYear');?></td><td><input type="button" onclick="SubmitStudent()" value="Create Student"></td></tr>
</table>
<script>
function SubmitStudent(SID) {
	SID = SID || -1;
	
}
</script>
</body>
</html>

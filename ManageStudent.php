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
	echo '<tr><td id="FName' . $StudentData['SID'] . '">' . $StudentData['FName'] . '</td><td id="LName' . $StudentData['SID'] . '">' . $StudentData['LName'] . '</td><td id="NYear' . $StudentData['SID'] . '">' . $StudentData['NoviceYear'] . '</td><td><input type="button" onclick="SubmitStudent(' . $StudentData['SID'] . ')"></td></tr>';
	$CurrentRow++;
}
// LName FName NoviceYear SID
?>
<tr><td><input type="text" id="FName" width="100%"></td><td><input type="text" id="LName" width="100%"></td><td><input type="text" id="NYear" width="100%"></td><td><input type="button" onclick="SubmitStudent()"></td></tr>
</table>
</body>
</html>

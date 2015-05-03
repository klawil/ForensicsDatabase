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
	echo '<tr><td>' . $StudentData['FName'] . '</td><td>' . $StudentData['LName'] . '</td><td>' . $StudentData['NoviceYear'] . '</td></tr>';
	$CurrentRow++;
}
// LName FName NoviceYear SID
?>
</table>
</body>
</html>

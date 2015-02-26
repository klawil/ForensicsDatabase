<?php
echo '<html>
<head>
<title>New Student</title>
</head>
<body>
';
if (( isset($_POST['FName']) )) {
	include "MySQLAuth.php";
	$tbl = "Students";
	if ( strlen($_POST['FName']) > 20 ) {
		echo 'First Name is too long
';
	} elseif ( strlen($_POST['LName']) > 20 ) {
		echo 'Last Name is too long
';
	} else {
		$query = mysql_query("INSERT INTO Students SET FName='" . $_POST['FName'] . "', LName='" . $_POST['LName'] . "', Year=" . $_POST['Year'] . ";");
		if ( $query = "" ) {
			echo 'Error processing entry.
';
		} else {
		echo $_POST['FName'] . " " . $_POST['LName'] . " added.
";
		}
	}
}
echo '<form id="NewStudent" action="NewStudent.php" method="post">
<table>
    <tr><td>First Name</td><td><input type="text" name="FName" onblur="CheckFName();"></td><td id="FNameWarn"></td></tr>
    <tr><td>Last Name</td><td><input type="text" name="LName" onblur="CheckLName();"></td><td id="LNameWarn"></td></tr>
    <tr><td>Year</td><td><select form="NewStudent" name="Year"><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option></select></td></tr>
</table>
<input type="submit" value="Submit">
</form>
<script>
function CheckFName(){
    var FName = document.forms["NewStudent"]["FName"].value;
    if ( FName.length > 20 ) {
        document.getElementById("FNameWarn").innerHTML = "Must be  20 characters or less";
    } else {
        document.getElementById("FNameWarn").innerHTML = "";
    }
}
function CheckLName(){
    var LName = document.forms["NewStudent"]["LName"].value;
    if ( LName.length > 20 ) {
        document.getElementById("LNameWarn").innerHTML = "Must be  20 characters or less";
    } else {
        document.getElementById("LNameWarn").innerHTML = "";
    }
}
</script>
</body>
</html>';
?>

<html>
<head>
<title>New Student</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
</head>
<body>
<h1>New Student</h1>
<?php
include 'CommonFunctions.php';
MakeHeader();
if (( isset($_POST['FName']) )) {
	$tbl = "Students";
	if ( strlen($_POST['FName']) > 20 ) {
		echo 'First Name is too long
';
	} elseif ( strlen($_POST['LName']) > 20 ) {
		echo 'Last Name is too long
';
	} else {
		$query = mysqli_query($DBConn, "INSERT INTO Students SET FName='" . $_POST['FName'] . "', LName='" . $_POST['LName'] . "', Year=" . $_POST['Year'] . ";");
		if ( !$query ) {
			echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
			break;
		} else {
		echo $_POST['FName'] . " " . $_POST['LName'] . " added.
";
		}
	}
}
if ( $GLOBALS['CanUserEdit'] != 1 ) {
	echo '<h2>Authentication Error: You do not have the permission to access this page</h2>
</body>
</html>';
	return 0;
}
?>
<form id="NewStudent" action="NewStudent.php" method="post">
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
</html>

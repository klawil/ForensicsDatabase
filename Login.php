<?php
include "MySQLAuth.php";
if ( isset($_POST['UName']) ) {
	$query = mysql_query("select password from users where UName='" . $_POST['UName'] . "';");
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		return 0;
	}
	if ( mysql_num_rows($query) == 0 ) {
		echo "No user with that username found.";
		return 0;
	} elseif ( mysql_num_rows($query) > 1 ) {
		echo "More than one user with that user name.";
		return 0;
	}
	$data = mysql_fetch_assoc($query);
	if ( password_verify($_POST['PWord'], $data['password']) ) {
		echo "True";
	} else {
		echo "False";
	}
	return 0;
}
?>
<html>
<head><title>Login</title></head>
<body>
<form id="Login" action="Login.php" method="post">
<input type="text" name="UName"><br>
<input type="password" name="PWord"><br>
<input type="submit" value="Submit"><br>
</form>
</body>
</html>

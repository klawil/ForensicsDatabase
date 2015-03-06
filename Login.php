<?php
include "MySQLAuth.php";
include "CommonFunctions.php";
if ( isset($_POST['UName']) ) {
	$query = mysql_query("select password from users where UName='" . $_POST['UName'] . "';");
	if (( mysql_errno() )) {
		echo "Error - MySQL error " . mysql_errno() . ": " . mysql_error() . ".";
		return 0;
	} elseif ( mysql_num_rows($query) == 0 ) {
		echo "No user with that name";
		return 0;
	} elseif ( mysql_num_rows($query) > 1 ) {
		echo "More than one user with that name";
		return 0;
	}
	$data = mysql_fetch_assoc($query);
	if ( password_verify($_POST['PWord'], $data['password']) ) {
		if ( SetAuthCookie($_POST['UName']) == 1 ) {
			echo "true";
		}
	} else {
		echo "Incorrect password";
	}
	return 0;
}
?>

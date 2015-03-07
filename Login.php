<?php
include "CommonFunctions.php";
if ( isset($_POST['UName']) ) {
	$query = mysqli_query($GLOBALS['DBConn'], "select password from users where UName='" . $_POST['UName'] . "';");
	if ( ! $query ) {
		echo "Error - MySQL error: " . mysqli_error() . ".";
		return 0;
	} elseif ( mysqli_num_rows($query) == 0 ) {
		echo "No user with that name";
		return 0;
	} elseif ( mysqli_num_rows($query) > 1 ) {
		echo "More than one user with that name";
		return 0;
	}
	$data = mysqli_fetch_assoc($query);
	if ( password_verify($_POST['PWord'], $data['password']) ) {
		if ( SetAuthCookie($_POST['UName']) == 1 ) {
			echo "true";
		}
	} else {
		echo "Incorrect password";
	}
	return 0;
}
if ( isset($_POST['Logout']) ) {
	unset($_COOKIE[$GLOBALS['CookieName']]);
	setcookie($GLOBALS['CookieName'], NULL, 0, "/");
	echo "true";
}
?>

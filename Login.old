<?php
include "CommonFunctions.php";
if ( isset($_POST['UName']) ) {
	$query = mysqli_query($GLOBALS['DBConn'], "select password from users where UName='" . $_POST['UName'] . "';");
	if ( ! $query ) {
		echo "Error - MySQL error: " . mysqli_error() . ".";
		return 0;
	} elseif ( mysqli_num_rows($query) == 0 ) {
		echo "Incorrect UserName/Password";
		return 0;
	} elseif ( mysqli_num_rows($query) > 1 ) {
		echo "Username error - contact William";
		return 0;
	}
	$data = mysqli_fetch_assoc($query);
	if ( password_verify($_POST['PWord'], $data['password']) ) {
		if ( SetAuthCookie($_POST['UName']) == 1 ) {
			echo "true";
			$myfile = fopen("/var/log/forensics/general.log","a");
			fwrite($myfile,$_POST['UName'] . " logged in from IP " . $_SERVER['REMOTE_ADDR'] . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
			fclose($myfile);
		}
	} else {
		echo "Incorrect UserName/Password";
	}
	return 0;
}
if ( isset($_POST['Logout']) ) {
	unset($_COOKIE[$GLOBALS['CookieName']]);
	setcookie($GLOBALS['CookieName'], NULL, 0, "/");
	$myfile = fopen("/var/log/forensics/general.log","a");
	fwrite($myfile,$GLOBALS['UserName'] . " logged out from IP " . $_SERVER['REMOTE_ADDR'] . " on " . date('Y-m-d') . " at " . date('H:i:s') . "\n");
	fclose($myfile);
	echo "true";
}
?>

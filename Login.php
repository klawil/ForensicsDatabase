<?php
require_once 'include.inc';

// Handle logging a user out
if ( isset($_POST['Logout']) ) {
	setcookie($GLOBALS['CookieName'],NULL,0,'/');
	echo 'true';
	return 0;
}

// Handle logging a user in
if ( isset($_POST['UName']) ) {
	// Global error variable
	$IncorrectString = 'Incorrect username or password';
	
	// Check password is received
	if ( !isset($_POST['PWord']) ) {
		echo 'Password must be entered';
		return 0;
	}
	
	// Escape variables
	$UN = strtolower(MySQLEscape($_POST['UName'], $DBConn));
	$PW = MySQLEscape($_POST['PWord'], $DBConn);
	
	// Get password hash and check username existence
	$UserString = 'select password from Users where UName="' . $UN . '";';
	$UserQuery = MySQLQuery($DBConn,$UserString);
	if ( !$UserQuery['Result'] ) {
		echo $UserQuery['Query'];
		return 0;
	} elseif ( mysqli_num_rows($UserQuery['Query']) != 1 ) {
		echo $IncorrectString;
		return 0;
	}
	$Data = mysqli_fetch_assoc($UserQuery['Query']);
	if ( password_verify($PW,$Data['password']) ) {
		if ( SetAuthCookie($UN,$DBConn) == true ) {
			echo 'true';
			WriteLog($UN . ' logged in');
			return 0;
		} else {
			echo 'Error setting cookie';
			return 0;
		}
	} else {
		echo $IncorrectString;
		return 0;
	}
}

// Any other action
require_once '404.html';
?>

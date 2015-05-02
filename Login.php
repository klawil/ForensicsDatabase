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
	
	// Check password is received
	if ( !isset($_POST['PWord']) ) {
		echo 'Password must be entered';
		return 0;
	}
}

// Any other action
require_once '404.html';
?>

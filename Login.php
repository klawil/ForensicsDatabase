<?php
require_once 'include.inc';
if ( isset($_POST['Logout']) ) {
	setcookie($GLOBALS['CookieName'],NULL,0,'/');
	echo 'true';
	return 0;
}
SetAuthCookie($_POST['UName'], $DBConn);
echo 'true';
?>

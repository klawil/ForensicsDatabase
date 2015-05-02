<?php
require_once 'include.inc';
if ( isset($_POST['Logout']) ) {
	setcookie($GLOBALS['CookieName'],NULL,0,'/');
}
?>

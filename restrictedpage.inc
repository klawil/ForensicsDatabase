<?php
if ( $GLOBALS['CanUserEdit'] == 0 ) {
	echo 'Location: http://' . $_SERVER['SERVER_NAME'] . '/';
	die();
}
?>

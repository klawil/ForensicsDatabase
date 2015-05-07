<?php
if ( $GLOBALS['CanUserEdit'] == 0 ) {
	echo 'You must be logged in to administer changes';
	header('Location: http://' . $_SERVER['SERVER_NAME'] . '/');
	die();
}
?>

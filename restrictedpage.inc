<?php
if ( $GLOBALS['CanUserEdit'] == 0 ) {
	if ( sizeof($_POST) == 0 ) {
		header('Location: http://' . $_SERVER['SERVER_NAME'] . '/');
	} else {
		echo 'You must be logged in to administer changes';
	}
	die();
}
?>

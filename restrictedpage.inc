<?php
if ( $GLOBALS['CanUserEdit'] == 0 ) {
	header('Location: http://' . $_SERVER['SERVER_NAME'] . '/');
	die();
}
?>

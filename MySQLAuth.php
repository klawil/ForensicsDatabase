<?php
$UN = 'forensics';
$PW = 'A15j89%%8JsTk991LexzQ#';
$DB = 'kmc';
$HOST = 'localhost';
$GLOBALS['DBConn'] = mysqli_connect($HOST, $UN, $PW, $GLOBALS['DBName']);
if ( !$GLOBALS['DBConn'] ) {
	echo 'Connection failed: ' . mysqli_connect_error();
	return 0;
}
?>

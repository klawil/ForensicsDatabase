<?php
$UN = 'forensics';
$PW = 'A15j89%%8JsTk991LexzQ#'; //Credentials to connect to MySQL
$DB = 'kmc'; //DB Name - will be changed on a db by db basis
$HOST = 'localhost';
$GLOBALS['DBConn'] = mysqli_connect($HOST, $UN, $PW, $GLOBALS['DBName']);
if ( !$GLOBALS['DBConn'] ) {
	echo 'Connection failed: ' . mysqli_connect_error();
	return 0;
}
?>

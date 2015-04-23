<?php
$UN = 'forensics';
$PW = 'A15j89%%8JsTk991LexzQ#'; // Credentials to connect to MySQL
$GLOBALS['DBName'] = array_shift((explode('.',$_SERVER['HTTP_HOST']))); // DB Name - set to the subdomain
$HOST = 'localhost';
$DBPermCon = mysqli_connect($HOST, $UN, $PW, 'Forensics');
if ( !$DBPermCon ) {
	echo 'Connection failed: ' . mysqli_connect_error();
	return 0;
}
$ConnectVar = mysqli_connect();
$PermQuery = mysqli_query($DBPermCon,'select * from Subdomains where Subdomain="' . mysqli_real_escape_string($ConnectVar,$GLOBALS['DBNAME']) . '";');
$GLOBALS['DBConn'] = mysqli_connect($HOST, $UN, $PW, $GLOBALS['DBName']);
if ( !$GLOBALS['DBConn'] ) {
	echo 'Connection failed: ' . mysqli_connect_error();
	return 0;
}
?>

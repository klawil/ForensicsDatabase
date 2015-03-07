<?php
$UN = "forensics";
$PW = "A15j89%%8JsTk991LexzQ#";
$DB = "Forensics_2015";
$HOST = "localhost";
$DBConn = mysqli_connect($HOST, $UN, $PW, $DB);
if ( !$DBConn ) {
	echo "Connection failed: " . mysqli_connect_error();
	return 0;
}
?>

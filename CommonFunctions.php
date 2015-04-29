<?php
include 'MySQLAuth.php'; // File that connects to MySQL database
$GLOBALS['CookieName'] = 'forensics_db_auth_token'; // Name of the cookie used to make login persistent
$GLOBALS['SecretWord'] = 'ForensicsSECRET'; // MD5'd with username to cookie name
$GLOBALS['UserName'] = ''; // Stores the username
$GLOBALS['CanUserEdit'] = 0; // Stores the admin ability of the user
function SetAuthCookie($UN) {
	// Sets an authoriztion cookie
	// $UN - the username to set the cookie for
	$ExpDate = time() + (86400 * 7);
	$MD5 = md5($UN . $GLOBALS['SecretWord'] . $ExpDate);
	$Cookie = $UN . "," . $MD5;
	$query = mysqli_query($GLOBALS['DBConn'], "update users set cookie='" . $MD5 . "', cookieExp='" . $ExpDate . "' where UName='" . $UN . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	setcookie($GLOBALS['CookieName'], $Cookie, $ExpDate, "/");
	return 1;
}
function WriteToLog($LogString = Null) {
	// Writes a log file
	// $LogString - String to use for the log file
	if ( $LogString == Null ) {
		$LogString = '';
		if ( $GLOBALS['UserName'] != '' ) {
			$LogString = 'User ' . $GLOBALS['UserName'] . ' from ';
		}
		$LogString = $LogString . 'IP ' . $_SERVER['REMOTE_ADDR'] . ' accessed ' . basename($_SERVER['PHP_SELF']);
	}
	$LogFile = fopen("/var/log/forensics/general.log","a");
	fwrite($LogFile,$LogString . ' on ' . date('Y-m-d') . ' at ' . date('H:i:s') . "\n");
}
CheckAuthorization();
?>

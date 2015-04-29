<?php
$GLOBALS['LogFile'] = '/var/log/forensics/general.log'; // Location of the log file
$GLOBALS['ErrorFile'] = '/var/log/forensics/error.log'; // Location of the error log file
$GLOBALS['CookieName'] = 'forensics_db_auth_token'; // Name of the cookie used to make login persistent
$GLOBALS['SecretWord'] = 'ForensicsSECRET'; // MD5'd with username to cookie name
$GLOBALS['UserName'] = ''; // Stores the username
$GLOBALS['CanUserEdit'] = 0; // Stores the editing ability of the user
function WriteLog($LogString = NULL) {
	// Write to a log file
	// @param $LogString - string to use if default is not wanted
	// @return - boolean of success
	
	// Build log string if none is sent
	if ( $LogString == NULL ) {
		$LogString = '';
		if ( $GLOBALS['UserName'] != '' ) {
			$LogString = 'User ' . $GLOBALS['UserName'] . ' from ';
		}
		$LogString = $LogString . 'IP ' . $_SERVER['REMOTE_ADDR'] . ' accessed ' . basename($_SERVER['PHP_SELF']);
	}
	
	// Open and write to the log file
	$LogFile = fopen($GLOBALS['LogFile'],'a');
	if ( !$LogFile ) {
		return false;
	}
	fwrite($LogFile,$LogString . ' Stamp: ' . date('Y-m-d') . ' at ' . date('H:i:s') . "\n");
	fclose($LogFile);
	return true;
}
function ErrorLog($ErrorString) {
	// Write to an error log file
	// @param $ErrorString - string to log
	// @return - boolean of success
	
	// Open and write to the log file
	$LogFile = fopen($GLOBALS['ErrorFile'],'a');
	if ( !$LogFile ) {
		return false;
	}
	fwrite($LogFile,$ErrorString . ' Stamp: ' . date('Y-m-d') . ' at ' . date('H:i:s') . "\n");
	fclose($LogFile);
	return true;
}
function CheckCookieAuth($DBConn) {
	// Check's a user's cookie authentication
	// @param $DBConn - connection to a MySQL database
	// @return - 0 if unsuccessful and 1 if logged in
	
	// Check if the cookie is set
	if ( isset($_COOKIE[$GLOBALS['CookieName']]) ) {
		
		// Get cookie details
		$CookieArray = explode(',',$_COOKIE[$GLOBALS['CookieName']]);
		$GLOBALS['UserName'] = MySQLEscape($CookieArray[0],$DBConn);
		$Cookie = $CookieArray[1];
		
		// Check user existence and get user cookie data
		$UserQuery = MySQLQuery($DBConn,'select * from users where UName="' . $GLOBALS['UserName'] . '";');
		if ( !$UserQuery['Result'] ) {
			ErrorLog($GLOBALS['DBName'],$GLOBALS['UserName'] . ': ' . $UserQuery['Query']);
			$GLOBALS['UserName'] = '';
			return 0;
		}
		if ( mysqli_num_rows($UserQuery['Query']) == 0 ) {
			ErrorLog($GLOBALS['DBName'],$GLOBALS['UserName']);
			$GLOBALS['UserName'] = '';
			return 0;
		} elseif ( mysqli_num_rows($UserQuery['Query']) > 1 ) {
			ErrorLog($GLOBALS['DBName'],$GLOBALS['UserName']);
			$GLOBALS['UserName'] = '';
			return 0;
		}
		$UserData = mysqli_fetch_assoc($UserQuery['Query']);
		
		// Check user data against supplied cookie
		if ( $Cookie != $UserData['cookie'] || time() > $UserData['cookieExp'] ) {
			setcookie($GLOBALS['CookieName'], "", time() - 3600);
			$GLOBALS['UserName'] = '';
			return 0;
		}
		
		// Set administrative access
		$GLOBALS['CanUserEdit'] = $UserData['CanMod'];
		return 1;
	}
	return 0;
}
function SetCookie($UN, $DBConn) {
	// Sets authorization cookie
	// @param $UN - username to set the cookie for
	// @param $DBConn - connection to MySQL database
	// @return true on success, error string on failure
	
	// Set expiration date and create cookie contents
	$ExpTime = time() + (86400 * 7);
	$MD5 = md5($UN . $GLOBALS['SecretWord'] . $ExpTime);
	$MD5 = MySQLEscape($MD5, $DBConn);
	$Cookie = $UN . "," . $MD5;
	
	// Insert the cookie into the users table
	$CookieString = 'update users set cookie="' . $MD5 '", cookieExp="' . $ExpTime . '" where UName="' . $UN . '";';
	$CookieQuery = MySQLQuery($DBConn,$CookieString);
	if ( !$CookieQuery['Result'] ) {
		return $CookieQuery['Query'];
	}
	
	// Set the cookie
	setcookie($GLOBALS['CookieName'], $Cookie, $ExpDate, "/");
	return true;
}
?>

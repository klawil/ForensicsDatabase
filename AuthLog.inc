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
		$UserQuery = MySQLQuery($DBConn,'select * from Users where UName="' . $GLOBALS['UserName'] . '";');
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
function SetAuthCookie($UN, $DBConn) {
	// Sets authorization cookie
	// @param $UN - username to set the cookie for
	// @param $DBConn - connection to MySQL database
	// @return true on success, error string on failure
	
	// Set expiration date and create cookie contents
	$ExpTime = time() + (86400 * 7);
	$MD5 = md5($UN . $GLOBALS['SecretWord'] . $ExpTime);
	$MD5 = MySQLEscape($MD5, $DBConn);
	$Cookie = $UN . "," . $MD5;
	
	// Insert the cookie into the Users table
	$CookieString = 'update Users set cookie="' . $MD5 . '", cookieExp="' . $ExpTime . '" where UName="' . $UN . '";';
	$CookieQuery = MySQLQuery($DBConn,$CookieString);
	if ( !$CookieQuery['Result'] ) {
		return $CookieQuery['Query'];
	}
	
	// Set the cookie
	setcookie($GLOBALS['CookieName'], $Cookie, $ExpDate, "/");
	return true;
}
function CreateUser($UName, $Password, $FName, $LName, $Email, $DBConn) {
	// Creates a new user and emails the admin of the domain about the creation
	// @param $UName - username for the new user
	// @param $Password - password for the new user
	// @param $FName - First name of the new user
	// @param $LName - Last name of the new user
	// @param $Email - Email of the user
	// @param $DBConn - connection to a MySQL database
	// @return Array - ['Done','Error']
	
	// Validate variables
	$UserInfo = compact('UName','Password','FName','LName','Email');
	$CheckArray = [['variable' => 'UName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,30);},'Error' => 'Username is too long'],
	['variable' => 'FName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,70);},'Error' => 'First Name is too long'],
	['variable' => 'LName','IsSet' => 1, 'Validate' => function($var){return IsLength($var,70);},'Error' => 'Last Name is too long'],
	['variable' => 'Email','IsSet' => 1, 'Validate' => function($var){return IsLength($var,150);},'Error' => 'Email is too long']];
	$Validation = ValidateArray($UserInfo,$CheckArray);
	if ( !$Validation['Pass'] ) {
		$ReturnArray['Done'] = false;
		$ReturnArray['Error'] = $Validation['Error'];
		return $ReturnArray;
	}
	
	// Escape variables
	foreach ( $UserInfo as $Key => $Value ) {
		if ( $Key != 'Password' ) { // Don't escape password
			$UserInfo[$Key] = MySQLEscape($Value,$DBConn);
		}
	}
	
	function IsUQ($Key,$Value,$DBConn) {
		// Checks if a value is unique
		// @param $Key - the column name 
		// @param $Value - the value to check
		// @param $DBConn - connection to a MySQL database
		// @return Array - ['Unique','Error']
		
		// Query database
		$UNQuery = MySQLQuery($DBConn,'select * from Users where ' . $Key . '="' . $Value . '";');
		if ( !$UNQuery['Result'] ) {
			$ReturnArray['Unique'] = false;
			$ReturnArray['Error'] = $UNQuery['Query'];
			return $ReturnArray;
		} elseif ( mysqli_num_rows($UNQuery['Query']) != 0 ) {
			$ReturnArray['Unique'] = false;
			$ReturnArray['Error'] = 'That ' . $Key . ' is already in use';
			return $ReturnArray;
		}
		$ReturnArray['Unique'] = true;
		return $ReturnArray;
	}
	
	// Check username
	$UNUQ = IsUQ('UName',$UserInfo['UName'],$DBConn);
	if ( !$UNUQ['Unique'] ) {
		$ReturnArray['Done'] = false;
		$ReturnArray['Error'] = $UNUQ['Error'];
		return $ReturnArray;
	}
	
	// Check email
	$EmailUQ = IsUQ('Email',$UserInfo['Email'],$DBConn);
	if ( !$EmailUQ['Unique'] ) {
		$ReturnArray['Done'] = false;
		$ReturnArray['Error'] = $EmailUQ['Error'];
		return $ReturnArray;
	}
	
	// Create validation string
	$VString = md5($UserInfo['UName'] . $GLOBALS['SecretWord']);
	$UserInfo['EmailCode'] = $VString;
	$UserInfo['EmailConf'] = 0;
	
	// Hash password
	$UserInfo['Password'] = password_hash($UserInfo['Password'],PASSWORD_DEFAULT);
	
	// Set Modification Ability
	$UserInfo['CanMod'] = 0;
	
	// Build insert string
	$QueryString = '';
	foreach ( $UserInfo as $Key => $Value ) {
		if ( $QueryString == '' ) {
			$QueryString = 'insert into Users set ' . $Key . '="' . $Value . '"';
		} else {
			$QueryString = $QueryString . ', ' . $Key . '="' . $Value . '"';
		}
	}
	$QueryString = $QueryString . ';';
	
	// Set email paramters
	$AdminEmail = $GLOBALS['DBData']['AdminEmail'];
	$header = 'Content-type:text/html;charset=UTF-8' . "\r\n" . 'From: noreply@forensicsdb.com' . "\r\n" . 'BCC: root@forensicsdb.com';
	
	// Send email to subdomain admin
	$message = '<html>
<h3>A new user has been added to your subdomain</h3><br>
Name: ' . $UserInfo['FName'] . ' ' . $UserInfo['LName'] . '<br>
Username: ' . $UserInfo['UName'] . '<br>
Email: ' . $UserInfo['Email'] . '
<html>';
	$Mail = mail('root <root>, Domain Admin <' . $AdminEmail . '>','New User on ForensicsDB',$message,$header);
	if ( !$Mail ) {
		$ReturnArray['Done'] = false;
		$ReturnArray['Error'] = 'Error sending mail to admin';
		return $ReturnArray;
	}
	
	// Send validation email to user
	$message = '<html>
<h3>A new user has been created with this email!</h3><br>
If this was not you, please click <a href="http://forensicsdb.com/reject.php">this link</a><br>
Name: ' . $UserInfo['FName'] . ' ' . $UserInfo['LName'] . '<br>
Username: ' . $UserInfo['UName'] . '<br>
Email: ' . $UserInfo['Email'] . '
<html>';
	$Mail = mail($UserInfo['Email'],'New User on ForensicsDB',$message,$header);
	if ( !$Mail ) {
		$ReturnArray['Done'] = false;
		$ReturnArray['Error'] = 'Error sending mail to subdomain admin';
		return $ReturnArray;
	}
	
	// Insert user into database
	$InsQuery = MySQLQuery($DBConn,$QueryString);
	if ( !$InsQuery['Result'] ) {
		$ReturnArray['Done'] = false;
		$ReturnArray['Error'] = $InsQuery['Query'];
		return $ReturnArray;
	}
	$ReturnArray['Done'] = true;
	return $ReturnArray;
}
?>

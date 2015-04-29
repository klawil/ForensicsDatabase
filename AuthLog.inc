<?php
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
$GLOBALS['CookieName'] = 'forensics_db_auth_token'; // Name of the cookie used to make login persistent
$GLOBALS['SecretWord'] = 'ForensicsSECRET'; // MD5'd with username to cookie name
$GLOBALS['UserName'] = ''; // Stores the username
$GLOBALS['CanUserEdit'] = 0; // Stores the editing ability of the user
?>

<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'New User';
require_once 'header.inc';
$UserData['UName'] = '';
$UserData['FName'] = '';
$UserData['LName'] = '';
$UserData['Email'] = '';
if ( isset($_POST['UName']) ) {
	// Check data
	$ErrorString = '';
	$VarArray = ['UName' => 'Username is required','FName' => 'First name is required','LName' => 'Last name is required','Email' => 'Email is required','Password' => 'Password is required','PasswordVerify' => 'Re-enter your password to confirm it'];
	foreach ( $VarArray as $Name => $Error ) {
		if ( !isset($_POST[$Name]) ) {
			if ( $ErrorString = '' ) {
				$ErrorString = $Error;
			} else {
				$ErrorString = $ErrorString . '; ' . $Error;
			}
			$UserData[$Name] = '';
		} else {
			$UserData[$Name] = $_POST[$Name];
		}
	}
	
	// Check passwords
	if ( $ErrorString == '' && $UserData['Password'] == $UserData['PasswordVerify'] ) {
		$ErrorString = 'Passwords do not match';
	}
	
	// Insert User
	if ( $ErrorString == '' ) {
		$CreateUser = CreateUser($UserData['UName'], $UserData['Password'], $UserData['FName'], $UserData['LName'], $UserData['Email'], $DBConn);
		if ( !$CreateUser['Done'] ) {
			$ErrorString = $CreateUser['Error'];
		} else {
			echo '<h3>Success!</h3><br>
' . $UserData['FName'] . ' ' . $UserData['LName'] . ' using user name' . $UserData['UName'] . ' was successfully added!<br>
Navigate to a different page to log in
</body>
</html>';
			SetAuthCookie($UserData['UName'],$DBConn);
			return 0;
		}
	}
}
?>
</body>
</html>

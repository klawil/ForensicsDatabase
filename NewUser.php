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
	$VarArray = ['UName' => 'Username','FName' => 'First name','LName' => 'Last name','Email' => 'Email','Password' => 'Password','PasswordVerify' => 'Second password'];
	foreach ( $VarArray as $Name => $Error ) {
		if ( !isset($_POST[$Name]) ) {
			if ( $ErrorString = '' ) {
				$ErrorString = $Error . ' is required';
			} else {
				$ErrorString = $ErrorString . '; ' . $Error . ' is required';
			}
			$UserData[$Name] = '';
		} else {
			$UserData[$Name] = $_POST[$Name];
		}
	}
	
	// Check password
	if ( $ErrorString == '' && $UserData['Password'] == '' ) {
		$ErrorString = 'You must enter a password';
	} elseif ( $ErrorString == '' && strlen($UserData['Password']) < 5 ) {
		$ErrorString = 'Your password must be at least 5 characters';
	} elseif ( $ErrorString == '' && $UserData['Password'] != $UserData['PasswordVerify'] ) {
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
<h2>Create a New User</h2>
<?php
if ( $ErrorString != '' ) {
	echo '<h3>' . $ErrorString . '</h3>';
}
?>
<form id="NewUser" action="NewUser.php" method="post">
First Name: <input type="text" name="FName" value="<?php echo $UserData['FName']; ?>"><br>
Last Name: <input type="text" name="LName" value="<?php echo $UserData['LName']; ?>"><br>
User Name: <input type="text" name="UName" value="<?php echo $UserData['UName']; ?>"><br>
Email: <input type="text" name="Email" value="<?php echo $UserData['Email']; ?>"><br>
Password: <input type="password" name="Password"><br>
Confirm Password: <input type="password" name="PasswordVerify"><br>
<input type="submit" value="Create User">
</form>
</body>
</html>

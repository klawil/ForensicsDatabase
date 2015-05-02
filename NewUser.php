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
First Name: <input type="text" name="FName" id="FName" onblur="CheckLength('FName',70);" value="<?php echo $UserData['FName']; ?>"><div id="FNameAlert" class="alert"></div><br>
Last Name: <input type="text" name="LName" id="LName" onblur="CheckLength('LName',70);" value="<?php echo $UserData['LName']; ?>"><div id="LNameAlert" class="alert"></div><br>
User Name: <input type="text" name="UName" id="UName" onblur="CheckLength('UName',30);" value="<?php echo $UserData['UName']; ?>"><div id="UNameAlert" class="alert"></div><br>
Email: <input type="text" name="Email" id="Email" onblur="CheckLength('Email',150);" value="<?php echo $UserData['Email']; ?>"><div id="EmailAlert" class="alert"></div><br>
Password: <input type="password" name="Password" id="Password" onblur="CheckLength('Password',30,5);"><div id="PasswordAlert" class="alert"></div><br>
Confirm Password: <input type="password" name="PasswordVerify" id="PasswordVerify" onblur="PasswordVerify();"><div id="PasswordVerify" class="alert"></div><br>
<input type="submit" value="Create User">
</form>
<script>
// UName: 30, FName: 70, LName: 70, Email: 150, PWord: 5-30
function CheckLength(Name,Max,Min) {
	Min = Min || 0;
	if ( document.getElementById(Name).value.length == 0 ) {
		HTMLString = 'Required';
	if ( document.getElementById(Name).value.length > Max ) {
		HTMLString = 'Must be less than ' + Max + ' characters';
	} else if ( Min == 0 && document.getElementById(Name).value.length < Min ) {
		HTMLString = 'Must be more than ' + Min + ' characters';
	} else {
		HTMLString = '';
	}
	document.getElementById(Name + 'Alert').innerHTML = HTMLString;
}
</script>
</body>
</html>

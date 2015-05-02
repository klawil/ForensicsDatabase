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
<center>
<h2>Create a New User</h2>
<?php
if ( $ErrorString != '' ) {
	echo '<h3>' . $ErrorString . '</h3>';
}
?>
<form id="NewUser" action="NewUser.php" method="post">
<table>
<tr><td align='right'>First Name</td><td><input type="text" name="FName" id="FName" onblur="CheckLength('FName',70);" value="<?php echo $UserData['FName']; ?>" required></td><td><div id="FNameAlert" class="alert"></div></td></tr>
<tr><td align='right'>Last Name</td><td><input type="text" name="LName" id="LName" onblur="CheckLength('LName',70);" value="<?php echo $UserData['LName']; ?>" required></td><td><div id="LNameAlert" class="alert"></div></td></tr>
<tr><td align='right'>User Name</td><td><input type="text" name="UName" id="UName" onblur="CheckLength('UName',30);" value="<?php echo $UserData['UName']; ?>" required></td><td><div id="UNameAlert" class="alert"></div></td></tr>
<tr><td align='right'>Email</td><td><input type="text" name="Email" id="Email" onblur="CheckLength('Email',150,0,1);" value="<?php echo $UserData['Email']; ?>" required></td><td><div id="EmailAlert" class="alert"></div></td></tr>
<tr><td align='right'>Password</td><td><input type="password" name="Password" id="Password" onblur="CheckLength('Password',30,5);" required></td><td><div id="PasswordAlert" class="alert"></div></td></tr>
<tr><td align='right'>Confirm Password</td><td><input type="password" name="PasswordVerify" id="PasswordVerify" onblur="PasswordVerifyFunc();" required></td><td><div id="PasswordVerifyAlert" class="alert"></div></td></tr>
<tr><td align='center' colspan='3'><input type="submit" value="Create User"></td></tr>
</table>
</form>
</center>
<script>
function CheckLength(Name,Max,Min,Email) {
	Min = Min || 0;
	Email = Email || 0;
	document.getElementById(Name + 'Alert').style.display = 'inline';
	if ( document.getElementById(Name).value.length == 0 ) {
		HTMLString = 'Required';
	} else if ( document.getElementById(Name).value.length > Max ) {
		HTMLString = 'Must be less than ' + Max + ' characters';
	} else if ( Min != 0 && document.getElementById(Name).value.length < Min ) {
		HTMLString = 'Must be more than ' + Min + ' characters';
	} else {
		if ( Email == 0 ) {
			HTMLString = '';
			document.getElementById(Name + 'Alert').style.display = 'none';
		} else {
			
		}
	}
	document.getElementById(Name + 'Alert').innerHTML = HTMLString;
}
function PasswordVerifyFunc() {
	document.getElementById('PasswordVerifyAlert').style.display = 'inline';
	if ( document.getElementById('Password').value != document.getElementById('PasswordVerify').value ) {
		HTMLString = 'Passwords do not match';
	} else {
		HTMLString = '';
		document.getElementById('PasswordVerifyAlert').style.display = 'none';
	}
	document.getElementById('PasswordVerifyAlert').innerHTML = HTMLString;
}
</script>
</body>
</html>

<html>
<head><title>User Creation</title></head>
<link rel="stylesheet" type="text/css" href="Styles.css">
<body>
<h1>New User</h1>
<?php
include 'CommonFunctions.php';
MakeHeader();
if ( isset($_POST['UName']) ) {
	$iswrong = 0;
	$UName = $_POST['UName'];
	if ( $_POST['FName'] == "" ) {
		echo 'You must enter a first name<br>';
		$iswrong = 1;
	} elseif ( strlen($_POST['FName']) ) {
		echo 'First Name must be less than 30 characters<br>';
		$iswrong = 1;
	}
	if ( $_POST['LName'] == "" ) {
		echo 'You must enter a last name<br>';
		$iswrong = 1;
	} elseif ( strlen($_POST['LName']) ) {
		echo 'Last Name must be less than 30 characters<br>';
		$iswrong = 1;
	}
	if ( $_POST['Email'] == "" ) {
		echo 'You must enter an email<br>';
		$iswrong = 1;
	}
	if ( $_POST['UName'] == "" ) {
		echo 'You must enter a user name<br>';
		$iswrong = 1;
	} elseif ( strlen($_POST['UName']) ) {
		echo 'Username must be less than 20 characters<br>';
		$iswrong = 1;
	}
	if ( $_POST['PWord'] == "" ) {
		echo 'You must enter a password<br>';
		$iswrong = 1;
	}
	if ( $iswrong == 0 ) {
		$query = mysqli_query($DBConn, "select * from users where UName='" . $_POST['UName'] . "';");
		if ( ! (mysqli_num_rows($query) == 0 ) ) {
			echo 'That username already exists<br>';
			$UName = "";
			$iswrong = 1;
		}
	}
	if ( $iswrong == 1 ) {
		echo '<form id="UserForm" action="NewUser.php" method="post">
First Name: <input type="text" name="FName" value="' . $_POST['FName'] . '"><br>
Last Name: <input type="text" name="LName" value="' . $_POST['LName'] . '"><br>
Email: <input type="text" name="Email" value="' . $_POST['Email'] . '"><br>
Username: <input type="text" name="UName" value="' . $UName . '"><br>
Password: <input type="password" name="PWord"><br>
<input type="submit" value="Submit">
</form>
</body>
</html>';
		return 0;
	}
	$query = mysqli_query($DBConn, "insert into users set UName='" . $_POST['UName'] . "', FName='" . $_POST['FName'] . "', LName='" . $_POST['LName'] . "', Email='" . $_POST['Email'] . "', password='" . password_hash($_POST['PWord'], PASSWORD_DEFAULT) . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	$query = mysqli_query($DBConn, "INSERT INTO Tournaments SET TName='" . $_POST['TName'] . "', Date='" . $_POST['Date'] . "', NumRounds='" . $_POST['NumRounds'] . "', NumFinalsJudges='" . $_POST['NumFinalsJudges'] . "';");
	if ( !$query ) {
		echo "Error - MySQL error: " . mysqli_error($DBConn) . ".";
		return 0;
	}
	echo 'User ' . $_POST['FName'] . ' ' . $_POST['LName'] . ' (' . $_POST['UName'] . ') has been added.
<a href="/">Return to Main Page</a>';
	SetAuthCookie($_POST['UName']);
	Authorize();
	shell_exec('text -u timeatwork.wk -p Kt305@1K3g -n 3166315474 -m "New user added: ' . $_POST['FName'] . ' ' . $_POST['LName'] . '"');
} else {
	echo '<form id="UserForm" action="NewUser.php" method="post"><br>
First Name: <input type="text" name="FName"><br>
Last Name: <input type="text" name="LName"><br>
Email: <input type="text" name="Email"><br>
Username: <input type="text" name="UName"><br>
Password: <input type="password" name="PWord"><br>
<input type="submit" value="Submit">
</form>';
}
?>
</body>
</html>

<?php
require_once 'include.inc';
$GLOBALS['PageName'] = 'User Management';

// Redirect away from page if not a site administrator
if ( $GLOBALS['UserData']['Email'] != $GLOBALS['DBData']['AdminEmail'] && $GLOBALS['UserName'] != 'admin' ) {
	if ( sizeof($_POST) == 0 ) {
		header('Location: http://' . $_SERVER['SERVER_NAME'] . '/');
	} else {
		echo 'You must be the site administrator to access this feature';
	}
	die();
}

// Prevent admin modification/deletion
if ( isset($_POST['UName']) && $_POST['UName'] == 'admin' ) {
	echo 'The admin account cannot be modified';
	return 0;
}

$DoQuery = false;

// Handle Deletion
if ( isset($_POST['delete']) ) {
	// Check for username
	if ( !isset($_POST['UName']) ) {
		echo 'User name is required';
		return 0;
	}
	$UserData['UName'] = MySQLEscape($_POST['UName'],$DBConn);
	
	// Validate ID
	$CheckArray = [['variable' => 'UName', 'IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'UName');}, 'Error' => 'Invalid username']];
	$Validation = ValidateArray($UserData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Email the user to let them know they are deleted
	$Body = '<h3>Your account on ForensicsDB.com has been deleted</h3><br>The school administrator has deleted your account. If you believe this was in error, please email accounts@forensicsdb.com with your username and the school you created the account to use with.';
	$Email = EmailUser($UserData['UName'],'ForensicsDB Account Deletion',$Body,$DBConn);
	if ( !$Email['Done'] ) {
		echo $Email['Error'] . ' while emailing the user';
		return 0;
	}
	
	// Create Post String
	$UserString = 'delete from Users where UName="' . $UserData['UName'] . '";';
	$DoQuery = true;
}

// Handle update
if ( !$DoQuery && isset($_POST['UName']) ) {
	// Names of variables to put in array
	$Variables = ['UName' => 'User Name','CanMod' => 'Modification privileges'];
	
	// Set all the variables
	foreach ( $Variables as $Name => $Desc ) {
		if ( isset($_POST[$Name]) ) {
			if ( $_POST[$Name] == '' ) {
				echo $Desc . ' is required';
				return 0;
			}
			$UserData[$Name] = MySQLEscape(urldecode($_POST[$Name]),$DBConn);
		}
	}
	
	// Create testing array
	$CheckArray = [['variable' => 'UName','IsSet' => 1, 'Validate' => function($var,$DBConn){return IsID($DBConn,$var,'UName');}, 'Error' => 'Invalid username'],
	['variable' => 'CanMod', 'IsSet' => 1, 'Validate' => function($var){return IsBool($var);}, 'Error' => 'Invalid modification option']];
	
	// Validate variables
	$Validation = ValidateArray($UserData,$CheckArray,$DBConn);
	if ( !$Validation['Pass'] ) {
		echo $Validation['Error'];
		return 0;
	}
	
	// Create Query string
	$UserString = 'update Users set CanMod="' . $UserData['CanMod'] . '" where UName="' . $UserData['UName'] . '";';
	$DoQuery = true;
}

// Execute Query
if ( $DoQuery ) {
	$UserQuery = MySQLQuery($DBConn,$UserString);
	if ( !$UserQuery['Result'] ) {
		echo $UserQuery['Query'];
		return 0;
	}
	echo 'true';
	return 0;
}

// Create quey of all users
$UserQuery = MySQLQuery($DBConn,'select UName, LName, FName, Email, CanMod from Users order by LName, FName, UName, Email;');
if ( !$UserQuery['Result'] ) {
	$GLOBALS['ErrorMessage'] = $UserQuery['Query'];
	require_once 'ErrorPage.inc';
}

// create header
require_once 'header.inc';
?>
<div class="container">
<h3>Modify user permissions</h3>
<div id="PostMessage" class="alert"></div>
<table class="Table">
<tr>
	<th>Username</th>
	<th>Last Name</th>
	<th>First Name</th>
	<th>Email</th>
	<th><span title="Admins can enter and modify results, tournaments, students, etc">Admin</span></th>
	<th></th>
	<th></th>
</tr>
<?php
// Set up loop
$NumRows = mysqli_num_rows($UserQuery['Query']);
$CurrentRow = 1;

// Loop through the query results
while ( $CurrentRow <= $NumRows ) {
	// Get data
	$UserData = mysqli_fetch_assoc($UserQuery['Query']);
	$UName = $UserData['UName'];
	
	// Create HTML table?>
<tr>
	<td><span title="Username"><?php echo $UserData['UName'];?></span></td>
	<td><span title="Last Name"><?php echo $UserData['LName'];?></span></td>
	<td><span title="First Name"><?php echo $UserData['FName'];?></span></td>
	<td><span title="Email"><?php echo $UserData['Email'];?></span></td>
	<td><span title="Admins can enter and modify results, tournaments, students, etc"><input type="checkbox" id="CanMod<?php echo $UName;?>"<?php if ( $UserData['CanMod'] ) { echo ' checked';}?>></span></td>
	<td><span title="Delete this user"><input type="button" value="Delete User" onclick="DeleteUser('<?php echo $UName;?>')"></span></td>
	<td><span title="Save the admin status of this user"><input type="button" value="Save User" onclick="SubmitUser('<?php echo $UName;?>')"></span></td>
</tr>
<?php
	$CurrentRow++;
}
?>
</table>
</div>
<script>
// Create array to check for changes
var ChangeArray = ["CanMod"];

// The name of the item that has the name of the row in it
var NameID = "UName";

function PostToPage(PostString) {
	// Alert user
	document.getElementById("PostMessage").innerHTML = "Processing request...";
	document.getElementById("PostMessage").style.display = "inline";
	
	// Encode string
	PostString = encodeURI(PostString);
	
	// Set up post
	if ( window.XMLHttpRequest ) {
		xmlhttp = new XMLHttpRequest();
	} else {
		xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	xmlhttp.open("POST","ManageUser.php",true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
	xmlhttp.send(PostString);
	xmlhttp.onreadystatechange = function() {
		if ( xmlhttp.readyState == 4 && xmlhttp.status == 200 ) {
			// Handle successful response
			response = xmlhttp.responseText;
			if ( response == 'true' ) {
				// Reload page if success
				location.reload();
			} else {
				// Show error if error
				document.getElementById("PostMessage").style.display = "none";
				window.alert(response);
			}
		} else if ( xmlhttp.readyState == 4 ) {
			// Handle unsuccessful response
			document.getElementById("PostMessage").style.display = "none";
			window.alert("Error: Status code " + xmlhttp.status);
		}
	}
}
function SubmitUser(UName) {
	// Set Element Name
	CanModElement = "CanMod" + UName;
	
	// create PostString
	PostString = "UName=" + encodeURIComponent(UName);
	
	// Add CanMod to PostString
	if ( document.getElementById(CanModElement).checked ) {
		PostString = PostString + "&CanMod=1";
	} else {
		PostString = PostString + "&CanMod=0";
	}
	
	// Execute Post
	PostToPage(PostString);
}
function DeleteUser(UName) {
	// Check if they are certain
	if ( !window.confirm("DANGER DANGER!!\nThis will PERMANENTLY erase this event.\n\nFOREVER\n\nDo you still want to do this?") ) {
		return 0;
	}
	
	// Create PostString
	PostString = "delete=1&UName=" + UName;
	
	// Execute Post
	PostToPage(PostString);
}
</script>
</body>
</html>

<?php
include "CommonFunctions.php";
?>
<html>
<head><title>Issue Reporting</title>
<link rel="stylesheet" type="text/css" href="Styles.css">
</head>
<body>
<h1><div id="Header" style="width: 100%;">Report an Issue with the Website</div></h1>
<?php MakeHeader();
if ( isset($_POST['Desc']) ){
	$Desc = preg_replace(array("/\n/","/\r/"),"",$_POST['Desc']);
	if ( strlen($Desc) > 255) {
		$ErrorString = 'Error - Issue description must be less than 255 characters. You used ' . strlen($_POST['Desc']) . '.';
	} elseif ( $_POST['Name'] == "" ) {
		$ErrorString = 'Error - A name must be entered.';
	} elseif ( $_POST['Page'] == "" ) {
		$ErrorString = 'Error - A page must be entered.';
	} elseif ( $Desc == "" ) {
		$ErrorString = 'Error - A description must be entered.';
	}
	if ( isset($ErrorString) ) {
		echo $ErrorString . '<br>
<form id="IssueSubmit" name="IssueSubmit" action="IssueReport.php" method="post">
Name: <input type="text" name="Name" value="' . $_POST['Name'] . '"><br>
Page Issue is On: <input type="text" name="Page" value="' . $_POST['Page'] . '"><br>
Describe the issue:<br>
<textarea rows="5" cols="50" name="Desc" form="IssueSubmit">' . $Desc . '</textarea><br>
<input type="submit" value="Submit">
</body>
</html>';
		return 0;
	} else {
		$Desc = str_replace('"','[QUOTE]',$Desc);
		$Name = str_replace('"','[QUOTE]',$_POST['Name']);
		$Page = str_replace('"','[QUOTE]',$_POST['Page']);
		shell_exec('echo "Issue Report\n\nName: ' . $Name . '\nPage: ' . $Page . '\nDesc:\n' . $Desc . '" | mail -s "Issue Report" william');
		echo "Issue Catalogued<br>";
	}
}
?>
<form id="IssueSubmit" name="IssueSubmit" action="IssueReport.php" method="post">
Name: <input type="text" name="Name"><br>
Page Issue is On: <input type="text" name="Page"><br>
Describe the issue:<br>
<textarea rows='5' cols='50' name="Desc" form="IssueSubmit"></textarea><br>
<input type='submit' value='Submit'>
</form>
</body>
</html>

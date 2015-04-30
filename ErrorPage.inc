<?php
$GLOBALS['PageName'] = 'Error Experienced';
require_once 'header.inc';
?>
<center>
<h1>Well this is embarrassing!</h1>
<h2>There seems to have been an error with that request</h2><br>
<?php
$EmailString = '<a href="mailto:webmaster@forensicsdb.com?Subject=Error%20Occured">webmaster@forensicsdb.com</a>';
if ( isset($GLOBALS['ErrorMessage']) ) {
	echo '<h3>The error message is: ' . $GLOBALS['ErrorMessage'] . '<br>
If you see that a lot, engrave the message on a solid gold tablet and send it to us.<br><br>
Or you could just send us an email at ' . $EmailString . '!</h3>';
} else {
	echo '<h3>If you continue to experienc this error, send us an email at ' . $EmailString . ' with as much detail as possible.<br>Thank you!</h3>';
}
?>
<br><h3>You can head back to the main page for your school's site <a href="<?php echo $_SERVER['HTTP_HOST'] ?>">here</a> or go to the ForensicsDB homeage <a href="http://forensicsdb.com/">here</a>!</h3>
</center>
</body>
</html>
<?php exit(0);?>

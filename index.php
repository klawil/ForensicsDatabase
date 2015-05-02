<?php
require_once 'include.inc';
$GLOBALS['PageName'] = $GLOBALS['DBData']['SchoolName'];
require_once 'header.inc';
?>
<h1>Site under construction</h1>
<?php
foreach ( $GLOALS['DBData'] as $Key => $Value ) {
	echo $Key . '=>' . $Value . '<br>';
}
?>
</body>
</html>

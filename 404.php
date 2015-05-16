<?php
require_once 'include.inc';
$GLOBALS['PageName'] = '404 Not Found';
require_once 'header.inc';
?>
<center>
<h1>Oops! You've caught us speechless!</h1>
<img src="/404.png" alt="This was a really cool meme!" style="width:100%;max-width:636px;padding-top:20px">
<h3>We can't find that page on our server.<br>Must be a bad link!<br>
<?php if ( strpos($_SERVER['HTTP_REFERER'],'forensicsdb') ) { echo '<a href="' . $_SERVER['HTTP_REFERER'] . '">Go Back</a> or '; } ?>
Try <a href="http://forensicsdb.com">This One</a> instead!</h3>
</center>
</body>
</html>

<?php
if ( $GLOBALS['CanUserEdit'] == 0 ) {
	
	// Handle a post request from an unauthorized user
	if ( sizeof($_POST) != 0 ) {
		echo 'You must be an administrator to access this feature';
		die();
	}

	// Begin section for handling page request from unauthorized user

	// Create the header for page
	require_once 'header.inc';
?>
<!-- Section for the user to log in -->
<div class="container text-center">
	<h2>This is a restricted page</h2>
	<h3>You must have an administrator account to access this page</h3>
	<h3>Please log in to continue</h3>
	<div class="col-lg-4 col-lg-offset-4 text-center">
		<div id="LoginMessageMain"></div>
		<input type="text" id="UserNameMain" class="form-control" placeholder="Username">
		<input type="password" id="PWordMain" class="form-control" placeholder="Password">
		<button type="submit" onclick="LoginUserMain()" class="btn btn-default">Login</button>
	</div>
</div>
<script>
function LoginUserMain() {
	// Function to log in the user or display a message if the login fails
	event.preventDefault(); // prevent the form submission from redirecting the page

	// Create the post string of username and password
	PString = "UName=" + document.getElementById("UserNameMain").value + "&PWord=" + document.getElementById("PWordMain").value;
	
	// Execute the post
	PostToPage(PString,"Login.php","LoginMessageMain");
}
</script>
<?php
	die();
}
?>

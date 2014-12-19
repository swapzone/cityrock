<?php

include_once('_init.php');

$title = "Login";

$content = "
	<form action='{$root_directory}/index.php' method='post'>
		<input type='text' placeholder='Username' name='username' />
		<input type='password' name='password' />
		<input type='submit' value='Anmelden' class='button' />
	</form>";

$content_class = "login";
$hide_navigation = true;

?>

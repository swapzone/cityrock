<?php

include_once('_init.php');

$title = "Login";

$content = "
	<form action='{$root_directory}/' method='post'>
		<input type='text' placeholder='Nutzername' name='username' />
		<input type='password' placeholder='Passwort' name='password' />
		<input type='submit' value='Anmelden' class='button' />
	</form>";

$content_class = "login";
$hide_navigation = true;

?>

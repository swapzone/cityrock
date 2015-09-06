<?php

include_once('_init.php');

$title = "Nutzerprofil";

$content = "Eine Übersicht über das Nutzerprofil. Roles: ";

foreach ($_SESSION['user']['roles'] as $role) {
	$content .= $role['title'];
}

include('_main.php');
?>

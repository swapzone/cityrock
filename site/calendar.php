<?php

include_once('_init.php');

$title = "Kalenderansicht";

if(isset($_SESSION['user'])) {
	$user_id = $_SESSION['user']['id'];

	$content = "
	<div id='calendar-filter' class='filter'>
		<span event-type='all' class='all active'>Alle Termine</span>
		<span event-type='user' user-id='{$user_id}'>Meine Termine</span>
		<span event-type='open'>Offene Termine</span>
	</div>
	<div id='calendar'></div>";
}
else {
	$content = "Du musst dich erst einloggen, um den Kalender anzeigen zu können.";
}

$content_class = "calendar";
include('_main.php');
?>

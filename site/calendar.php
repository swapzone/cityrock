<?php

include_once('_init.php');

$title = "Kalenderansicht";
$content = "
<div id='filter' class='filter'>
	<span class='active'>Alle Termine</span>
	<span>Meine Termine</span>
	<span>Offene Termine</span>
</div>
<div id='calendar'></div>";

$content_class = "calendar";
include('_main.php');
?>

<?php
	include('_func.php');

	header('Content-Type: text/html; charset=utf-8');

	$confirmationCode = $_GET['id'];
	$confirmationCode = urldecode($confirmationCode);

	if(confirmRegistrant($confirmationCode))
		echo "Du hast deine Teilnahme erfolgreich bestätigt!";
	else
		echo "Leider ist etwas schief gegangen. Probiere es später noch einmal.";
?>

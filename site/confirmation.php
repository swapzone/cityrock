<?php

include_once('_init.php');

if(isset($_POST['confirmation'])) {
	
	if($_POST['action'] == "delete") {

		$table_name = $_POST['table'];
		$item_description = $_POST['description'];
		$item_id = $_POST['id'];
		
		if(deleteItem($item_id, $table_name)) {
			$title = "Bestätigung";
			$content = "{$item_description} wurde erfolgreich gelöscht.";
		}
		else {
			$title = "Fehler";
			$content = "{$item_description} konnte nicht gelöscht werden.";
		}
	}
	else if($_POST['action'] == "move") {

		$registrant_id = $_POST['registrant_id'];
		$old_course_id = $_POST['old_course_id'];
		$new_course_id = $_POST['new_course_id'];
	
		if(moveRegistrant($registrant_id, $old_course_id, $new_course_id)) {
			$title = "Bestätigung";
			$content = "Der Teilnehmer wurde in den Kurs mit der ID {$new_course_id} verschoben.";
		}
		else {
			$title = "Fehler";
			$content = "Der Teilnehmer konnte nicht verschoben werden.";
		}
	}
	else {
		$content = "Die Aktion konnte nicht durchgeführt werden.";
	}
	
	include('_main.php');
}
else {
	include('error.php');
}
?>

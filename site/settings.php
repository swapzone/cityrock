<?php

include_once('_init.php');

if(isset($_POST['subject'])) {
	if(storeSettings($_POST)) {
		$title = "Einstellungen";
		$content = "Einstellungen wurden gespeichert.";
	}
	else {
		$title = "Einstellungen";
		$content = "Fehler! Einstellungen konnte nicht gespeichert werden.";
	}
}
else {
	$title = "Einstellungen";
	$content = "
		<form>
			<h3>Email Editor</h3>
			<label for='subject'>Betreff</label> 
			<input type='text' placeholder='Deine Anmeldung' name='subject' id='subject'>
			<label for='text'>Nachricht</label>
			<textarea placeholder='Deine Nachricht' name='text' id='text' rows='8'></textarea>
			<label for='time'>Wieviel Tage vor dem Kurs soll die Email verschickt werden?</label> 
			<input type='text' placeholder='2' name='time' id='time'>
			<p>Keine weiteren Einstellungen vorhanden.</p>
			<input type='submit' value='Speichern' class='button'>
		</form>";
}

$content_class = "settings";
include('_main.php');
?>

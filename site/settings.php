<?php

include_once('_init.php');

// include config_lite library
require_once('lib/config/Lite.php');
$config = new Config_Lite('basic.cfg');

if(isset($_POST['subject'])) {
	$config['email'] = array('subject' => "{$_POST['subject']}",
													 'body' => "{$_POST['body']}", 
													 'notification' => $_POST['notification']);
	
	if($config->save()) {
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
		<form method='post'>
			<h3>Email Editor</h3>
			<label for='subject'>Betreff</label> 
			<input type='text' value='{$config['email']['subject']}' name='subject'>
			<label for='text'>Nachricht</label>
			<textarea name='body' rows='8'>{$config['email']['body']}</textarea>
			<label for='time'>Wieviel Tage vor dem Kurs soll die Erinnerungsemail verschickt werden?</label> 
			<input type='text' value='{$config['email']['notification']}' name='notification'>
			<p>Keine weiteren Einstellungen vorhanden.</p>
			<a href='./' class='button error'>Abbrechen</a>	
			<input type='submit' value='Speichern' class='button'>
		</form>";
}

$content_class = "settings";
include('_main.php');
?>

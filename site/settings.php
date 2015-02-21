<?php

include_once('_init.php');

// include config_lite library
require_once('lib/config/Lite.php');
$config = new Config_Lite('basic.cfg');

if(isset($_POST['save'])) {
	$config['email'] = array('subject-confirm' => "{$_POST['subject-confirm']}",
													 'body-confirm' => "{$_POST['body-confirm']}", 
													 'subject-reminder' => "{$_POST['subject-reminder']}",
													 'body-reminder' => "{$_POST['body-reminder']}", 
													 'notification' => $_POST['notification']);

	$config['system'] = array('deadline' => $_POST['deadline'],
													 	'administration' => $_POST['administration'], 
													 	'administration-list' => "{$_POST['administration-list']}");
	
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
			<h4>Erinnerungsmail</h4>
			<label for='subject-reminder'>Betreff</label> 
			<input type='text' value='{$config['email']['subject-reminder']}' name='subject-reminder'>
			<label for='body-reminder'>Nachricht</label>
			<textarea name='body-reminder' rows='8'>{$config['email']['body-reminder']}</textarea>
			<label for='notification'>Wieviel Tage vor dem Kurs soll die Erinnerungsemail verschickt werden?</label> 
			<input type='text' value='{$config['email']['notification']}' name='notification'>
			<h4>Bestätigungsmail</h4>
			<label for='subject-confirm'>Betreff</label> 
			<input type='text' value='{$config['email']['subject-confirm']}' name='subject-confirm'>
			<label for='body-confirm'>Nachricht</label>
			<textarea name='body-confirm' rows='8'>{$config['email']['body-confirm']}</textarea>
			<h3>Systemeinstellungen</h3>
			<label for='deadline'>Wieviel Tage vor Kursbeginn ist Anmeldeschluss?</label> 
			<input type='text' value='{$config['system']['deadline']}' name='deadline'>
			<label for='administration'>Wieviel Tage vor Kursbeginn soll die Teilnehmerliste an die Verwaltung geschickt werden?</label> 
			<input type='text' value='{$config['system']['administration']}' name='administration'>
			<label for='administration-list'>An welche Email-Adressen soll die Liste geschickt werden? <br />Bitte die Adressen mit // voneinander trennen.</label>
			<textarea name='administration-list' rows='6'>{$config['system']['administration-list']}</textarea>
			<input type='hidden' name='save' value='1'>
			<p>Keine weiteren Einstellungen vorhanden.</p>
			<a href='./' class='button error'>Abbrechen</a>	
			<input type='submit' value='Speichern' class='button'>
		</form>";
}

$content_class = "settings";
include('_main.php');
?>

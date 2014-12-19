<?php

include_once('_init.php');

if(isset($_POST['new']) && isset($_POST['username']) && isset($_POST['password'])) {
	$success = addUser($_POST['username'], $_POST['password'], 1);
	
	if($success) {
		$title = "Neuer Nutzer";
		$content = "Nutzer wurde erstellt.";	
	}
	else {
		$title = "Neuer Nutzer";
		$content = "Fehler: Nutzer konnte nicht erstellt werden.";	
	}
}
else {
	if(isset($_GET["id"])) {
		if($_GET["id"] == "new") {
			$title = "Neuer Nutzer";
			$content = "
			 	<form method='post'>
					<label for='username'>Nutzername</label>
					<input type='text' placeholder='Nutzername' name='username' id='username'>
					<label for='password'>Passwort (gut merken!)</label>
					<input type='password' placeholder='Passwort' name='password' id='password'>
					<input type='hidden' name='new' value='true'>
					<a href='./' class='button error'>Abbrechen</a>	
					<input type='submit' class='button' value='Hinzufügen'>
				</form>";
		}
	}
	else {
		$title = "Nutzerübersicht";
		$content = "
			<div class='list'>
				<span class='list-heading'>
					<span>Nutzername</span>
					<span>Rolle</span>
					<span></span>
				</span>";

		$content .= "
					<span class='list-item'>
						<span>Vincent</span>
						<span>Administrator</span>
						<span></span>
					</span>
					<span class='list-item'>
						<span>Empfang</span>
						<span>Administrator</span>
						<span><a href='#'>löschen</a></span>
					</span>";

		$users = getUsers();

		foreach($users as $user) {
			$content .= "
					<span class='list-item'>
						<span>Vincent</span>
						<span>Administrator</span>
						<span><a href='#' class='button delete inactive'>löschen</a></span>
					</span>";
		}

		$content .= "
				</div>
				<div class='action-bar'>
					<a href='./user/new' class='button'>Nutzer hinzufügen</a>
				</div>";
	}
}

$content_class = "user";
include('_main.php');
?>

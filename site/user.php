<?php

include_once('_init.php');

if(isset($_POST['username']) && isset($_POST['password'])) {
	$success = addUser($_POST['username'], $_POST['password'], 1);
	
	if($success)
		$content = "
				<h2>Neuen Nutzer hinzufügen</h2>
				<p>Nutzer wurde erstellt.</p>";	
	else
		$content = "
				<h2>Neuen Nutzer hinzufügen</h2>
				<p>Fehler: Nutzer konnte nicht erstellt werden.</p>";	
}
else {
	if(isset($_GET["id"])) {
		if($_GET["id"] == "new") {
			$content = "
			 	<h2>Neuen Nutzer hinzufügen</h2>
				<form action='{$root_directory}/user' method='post'>
					<label for='username'>Nutzername</label>
					<input type='text' placeholder='Nutzername' name='username' id='username'>
					<label for='password'>Passwort (gut merken!)</label>
					<input type='password' placeholder='Passwort' name='password' id='password'>
					<input type='submit'>
				</form>";
		}
	}
	else {
			$content = "
				<h2>Nutzerübersicht</h2>
				<div class='user-list'>
					<span class='user-list-heading'>
						<span>Nutzername</span>
						<span>Rolle</span>
						<span></span>
					</span>";

		$content .= "
					<span class='user-list-item'>
						<span>Vincent</span>
						<span>Administrator</span>
						<span></span>
					</span>
					<span class='user-list-item'>
						<span>Empfang</span>
						<span>Administrator</span>
						<span><a href='#'>löschen</a></span>
					</span>";

		$users = getUsers();

		foreach($users as $user) {
			$content .= "
					<span class='user-list-item'>
						<span>Vincent</span>
						<span>Administrator</span>
						<span><a href='#' class='button delete inactive'>löschen</a></span>
					</span>";
		}

		$content .= "
				</div>
				<div class='action-bar'>
					<a href='./user/new' class='button new mobile'>Nutzer hinzufügen</a>
				</div>";
	}
}

include('_main.php');
?>

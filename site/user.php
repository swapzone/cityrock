<?php

include_once('_init.php');

$required_roles = array('Administrator');

if(User::withUserObjectData($_SESSION['user'])->hasPermission($required_roles)) {

	if(isset($_POST['new']) && isset($_POST['username']) && isset($_POST['password'])) {
		$success = addUser($_POST['username'], md5($_POST['password']), $_POST['role']);

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
					<form method='post' onsubmit='return cityrock.validateForm(this);'>
						<label for='username'>Nutzername</label>
						<input type='text' placeholder='Nutzername' name='username'>
						<label for='password'>Passwort (gut merken!)</label>
						<input type='password' placeholder='Passwort' name='password'>
						<label for='role'>Zugewiesene Rolle</label>
						<select name='role'>";

				foreach(getRoles() as $role) {
					$content .= "<option value='{$role['id']}'>{$role['title']}</option>";
				}

				$content .= "
						</select>
						<input type='hidden' name='new' value='true'>
						<a href='../' class='button error'>Abbrechen</a>
						<input type='submit' class='button' value='Hinzufügen'>
					</form>";
			}
			else {
				// show user profile
				$userObj = new User($_GET["id"]);

				// TODO visualize user data
				$content .= "User id: " . $userObj->serialize()['id'];
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

			$users = getUsers();

			foreach($users as $user) {
				$content .= "
						<span class='list-item'>
							<span><a href='?id={$user['id']}'>{$user['username']}</a></span>
							<span>{$user['roles']}</span>
							<span>";

				if($user['deletable']) {
					$content .= "
								<form action='{$root_directory}/confirmation' method='post'>
									<input type='hidden' name='confirmation' value='true'>
									<input type='hidden' name='action' value='delete'>
									<input type='hidden' name='description' value='Nutzer'>
									<input type='hidden' name='table' value='user'>
									<input type='hidden' name='id' value='{$user['id']}'>
									<a href='#' class='confirm'>löschen</a>
								</form>";
				}

				$content .= "
							</span>
						</span>";
			}

			$content .= "
					</div>
					<div class='action-bar'>
						<a href='./user/new' class='button'>Nutzer hinzufügen</a>
					</div>";
		}
	}
}
else {
	$title = "Nutzerübersicht";
	$content = "Du hast keine Berechtigung für diesen Bereich der Website.";
}

$content_class = "user";
include('_main.php');
?>

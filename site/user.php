<?php

include_once('_init.php');

$required_roles = array('Administrator');

if(User::withUserObjectData($_SESSION['user'])->hasPermission($required_roles)) {

	/***********************************************************************/
	/* Process form data												   */
	/***********************************************************************/
	if(isset($_POST['role'])) {
		if(isset($_POST['delete_role'])) {
			// delete role from user object
			$success = removeRole($_POST['user_id'], $_POST['role']);

			if ($success) echo "SUCCESS";
			else echo "ERROR";
		}
		else {
			// add new role to user object
			$success = addRole($_POST['user_id'], $_POST['role']);

			if ($success) echo "SUCCESS";
			else echo "ERROR";
		}
	}
	else {
		if (isset($_POST['modify'])) {
			$user_data_array = array();

			// avoid deactivating one's own account
			if ($_POST['user_id'] != $_SESSION['user']['id']) {
				$user_data_array['active'] = $_POST['active'] ? 1 : 0;
			}

			$user_data_array['username'] = $_POST['username'];
			$user_data_array['first_name'] = $_POST['first_name'];
			$user_data_array['last_name'] = $_POST['last_name'];
			$user_data_array['phone'] = $_POST['phone'];
			$user_data_array['email'] = $_POST['email'];

			$success = User::updateUserData($user_data_array, $_POST['user_id']);

			// modify POST user object
			if ($success) {
				foreach ($user_data_array as $key => $value) {
					$_SESSION['user'][$key] = $value;
				}
			}

			if ($success)
				$content = "Die Nutzerdaten wurden erfolgreich gespeichert.";
			else
				$content = "Fehler: Die Nutzerdaten konnten nicht gespeichert werden.";
		} else if (isset($_POST['new']) && isset($_POST['username']) && isset($_POST['password'])) {
			$success = addUser($_POST['username'], md5($_POST['password']), $_POST['role']);

			if ($success) {
				$title = "Neuer Nutzer";
				$content = "Nutzer wurde erstellt.";
			} else {
				$title = "Neuer Nutzer";
				$content = "Fehler: Nutzer konnte nicht erstellt werden.";
			}
		} else {
			if (isset($_GET["id"])) {
				if ($_GET["id"] == "new") {
					$title = "Neuer Nutzer";
					$content = "
						<form method='post' onsubmit='return cityrock.validateForm(this);'>
							<label for='username'>Nutzername</label>
							<input type='text' placeholder='Nutzername' name='username'>
							<label for='password'>Passwort (gut merken!)</label>
							<input type='password' placeholder='Passwort' name='password'>
							<label for='role'>Zugewiesene Rolle</label>
							<select name='role'>";

					foreach (getRoles() as $role) {
						$content .= "<option value='{$role['id']}'>{$role['title']}</option>";
					}

					$content .= "
							</select>
							<input type='hidden' name='new' value='true'>
							<a href='../' class='button error'>Abbrechen</a>
							<input type='submit' class='button' value='Hinzufügen'>
						</form>";
				} else {
					// show user profile
					$user = User::withUserId($_GET["id"])->serialize();

					$qualification_list = "<ul class='qualification-list'>";
					foreach ($user['qualifications'] as $qualification) {

						$description = $qualification['description'];
						$hasQualification = $qualification['user_id'] != null;

						if ($hasQualification) {
							$qualification_list .= "<li>{$description}";

							if ($qualification['date'])
								$qualification_list .= " vom {$qualification['date']}</li>";
							else
								$qualification_list .= "</li>";
						}
					}
					$qualification_list .= "</ul>";

					if ($qualification_list == "<ul class='qualification-list'></ul>")
						$qualification_list = "<p style='font-style: italic; margin-top: 0.5em; margin-bottom: 0.2em;'>Es wurden noch keine Qualifikationen hinterlegt.</p>";

					$roles_list = "<ul class='roles-list'>";
					foreach ($user['roles'] as $role) {
						$roles_list .= "<li>{$role['title']} <a href='#' class='remove-role' role='{$role['id']}' style='margin-left: 1em;'>Entfernen</a></li>";
					}
					$roles_list .= "</ul>";

					$checked = $user['active'] ? 'checked' : '';
					$deactivateCheckbox = $user['id'] === $_SESSION['user']['id'] ? 'disabled' : '';

					$content .= "
						<form id='user_data_form' method='post' onsubmit='return cityrock.validateProfile(this);'>
							<span class='list'>
								<span class='list-item'>
									<span>Nutzer ID</span>
									<span id='user-id-text'>{$user['id']}</span>
								</span>
								<span class='list-item'>
									<span>Nutzername</span>
									<span id='username-text'>{$user['username']}</span>
								</span>
								<span class='list-item'>
									<span>Vorname</span>
									<span id='first-name-text'>{$user['first_name']}</span>
								</span>
								<span class='list-item'>
									<span>Nachname</span>
									<span id='last-name-text'>{$user['last_name']}</span>
								</span>
								<span class='list-item'>
									<span>Email</span>
									<span id='email-text'>{$user['email']}</span>
								</span>
								<span class='list-item'>
									<span>Telefonnummer</span>
									<span id='phone-text'>{$user['phone']}</span>
								</span>
							</span>
							<span class='list'>
								<span class='list-item'>
									Der Nutzer hat folgende Qualifikationen: {$qualification_list}
								</span>
							</span>
							<span class='list'>
								<span class='list-item'>
									Der Nutzer hat folgende Rollen: {$roles_list}
									<a href='#' id='user-add-role'>Weitere Rolle hinzufügen</a>
									<select id='user-add-role-selection' name='role' style='display: none;'>
										<option style='display: none;' selected></option>";

					foreach (getRoles() as $role) {
						$content .= "<option value='{$role['id']}'>{$role['title']}</option>";
					}

					$content .= "
									</select>
								</span>
							</span>
							<span class='list'>
								<span class='list-item'>
									<span class='{$deactivateCheckbox}'>
										<input type='checkbox' name='active' id='active' {$deactivateCheckbox} {$checked} />
										<label for='active'>Nutzerkonto aktiviert</label>
									</span>
								</span>
								<input type='hidden' name='modify' />
								<input type='hidden' name='user_id' value='{$user['id']}' />
							</span>
						</form>
						<span style='display: inline-block;' id='delete-user'>
							<form class='inline' action='{$root_directory}/confirmation' method='post'>
								<input type='hidden' name='confirmation' value='true'>
								<input type='hidden' name='action' value='delete'>
								<input type='hidden' name='description' value='Nutzer'>
								<input type='hidden' name='table' value='user'>
								<input type='hidden' name='id' value='{$user['id']}'>
								<a href='#' class='button error confirm'>Löschen</a>
							</form>
						</span>
						<a href='{$root_directory}/user' class='button'>Zurück</a>
						<a href='#' id='edit-user' class='button'>Editieren</a>";
				}
			} else {
				$title = "Nutzerübersicht";

				$content = "
				<!-- COURSE FILTER -->
				<div class='user-filter' id='filter'>
					<span class='all active'>Alle</span>
					<span>Kletterbetreuer</span>
					<span>Führerschein</span>
				</div>";

				$content .= "
					<div class='list'>
						<span class='list-heading'>
							<span>Nutzername</span>
							<span>Rolle</span>
							<span></span>
						</span>";

				$users = getUsers();

				foreach ($users as $user) {
					$user = $user->serialize();

					$roles_list = "";
					foreach ($user['roles'] as $role) {
						$roles_list .= ", " . $role['title'];
					}
					$roles_list = substr($roles_list, 2);

					$qualifications_list = "";
					foreach ($user['qualifications'] as $qualification) {
						if ($qualification['user_id'] != null)
							$qualifications_list .= " " . strtolower($qualification['description']);
					}

					$content .= "
							<span class='list-item {$qualifications_list}'>
								<span><a href='?id={$user['id']}'>{$user['username']}</a></span>
								<span>{$roles_list}</span>
								<span>";

					if ($user['deletable']) {
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

		$content_class = "user";
		include('_main.php');
	}
}
else {
	$title = "Nutzerübersicht";
	$content = "Du hast keine Berechtigung für diesen Bereich der Website.";

	$content_class = "user";
	include('_main.php');
}
?>

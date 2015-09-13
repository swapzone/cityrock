<?php

include_once('_init.php');
include_once('inc/user.php');

$title = "Nutzerprofil";

$user = $_SESSION['user'];

/***********************************************************************/
/* Process form data												   */
/***********************************************************************/
if(isset($_POST['modify'])) {
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$password = $_POST['password'];

	$user_data_array = array();

	// do not accept empty passwords
	if($password) $user_data_array['password'] = md5($password);

	$user_data_array['phone'] = $phone;
	$user_data_array['email'] = $email;

	$success = User::updateUserData($user_data_array, $user['id']);

	// modify POST user object
	if($success) {
		foreach ($user_data_array as $key => $value) {
			$_SESSION['user'][$key] = $value;
		}
	}

	$qualifications_array = array();

	foreach($_POST as $key => $value) {

		if(is_numeric($key) && $value) {
			if(!array_key_exists($key, $qualifications_array)) {
				$qualifications_array[$key] = null;
			}
		}
		else if(strpos($key, 'date-') == 0 && $value) {
			$qualification_id = substr($key, 5);

			if(is_numeric($qualification_id)) {
				$qualifications_array[$qualification_id] = $value;
			}
		}
	}

	$success = User::updateUserQualifications($qualifications_array, $user['id']) ? $success : false;

	// modify POST user object
	if($success) 
		$_SESSION['user']['qualifications'] = User::getQualifications($user['id']);

	if($success)		
		$content = "Deine Daten wurde erfolgreich gespeichert.";	
	else 
		$content = "Fehler: Deine Daten konnten nicht gespeichert werden.";	
}
else {
	$content .= "
	<p class='page-label'>Eine Übersicht über dein Nutzerprofil.</p>
	<form method='post' onsubmit='return cityrock.validateProfile(this);'>
		<span class='list'>
			<span class='list-item'>
				<span>Nutzername</span>
				<span>{$user['username']}</span>
			</span>
			<span class='list-item'>
				<span>Telefonnummer</span>
				<span id='phone-text'>{$user['phone']}</span>
			</span>
			<span class='list-item'>
				<span>Email</span>
				<span id='email-text'>{$user['email']}</span>
			</span>
			<span class='list-item'>
				<span>Passwort</span>
				<span id='password-text'>*******</span>
			</span>
		</span>
		<span class='list'>";

	foreach ($user['qualifications'] as $qualification) {

		$description = strtolower($qualification['description']);
		$checked = $qualification['user_id'] == null ? '' : 'checked';

		$content .= "
				<span class='list-item'>
					<span>
						<input type='checkbox' name='{$qualification['id']}' id='{$description}' {$checked} />
						<label for='{$description}'>{$qualification['description']}</label>
					</span>";

		if($qualification['date_required'] == 1) {
			$content .= " 		
					<span id='{$description}-date'>
						Datum des Kurses? 
						<input type='text' id='{$description}-date-input' name='date-{$qualification['id']}' placeholder='01.01.1906' value='{$qualification['date']}' />
					</span>";
		}

		$content .="		
				</span>";
	}

	$content .= "
				<input type='hidden' name='modify' />
				<a href='{$root_directory}/' class='button'>Zurück</a>
				<a href='#' id='edit-user' class='button'>Bearbeiten</a>
			</span>
		</form>";

}

$content_class = "profile";
include('_main.php');

?>
<?php

function login($username, $password) {

	// TODO Write login logic 

	return true;
}


function getCourses($course_type) {
	
	// TODO sort by date descending
}

function getUsers() {

	// TODO retrieve users
}


/**
 *
 * $role = 1: admin
 * $role = 2: staff
 */
function addUser($username, $password, $role) {

	// TODO implement

	// returns true on success
	return false;
}

function getMonth($date_string) {
	$date = new DateTime($date_string);

	// see http://php.net/manual/en/function.date.php
	return date('F', $date); 
}

function renderNavigation($entries) {
 $menu_string = "
		<ul>
			<li class='active'><a href='./course'>Kursverwaltung</a></li>		
			<li><a href='./user'>Nutzerverwaltung</a></li>	
			<li><a href='./settings'>Einstellungen</a></li>
			<li class='mobile'><a href='./profile'>Profil</a></li>";

	foreach($entries as $entry) {
		$menu_string .= "		
			<li class='mobile'><a href='./{$entry}'>{$entry}</a></li>";
	}

	$menu_string .= "
		</ul>";

	return $menu_string;
}

?>

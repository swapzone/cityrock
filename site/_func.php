<?php

/*****************************************************************************/
/* Course functionality																											 */
/*****************************************************************************/

function getCourses($course_type) {
	
	// TODO sort by date descending
}

function getCourse($course_id) {

	// TODO

}

function addCourse($course_type, $num_registrants, $num_staff, $dates) {

	// TODO

	return true;
}

/*****************************************************************************/
/* User functionality																												 */
/*****************************************************************************/

function getUsers() {

	//$db = createConnection();

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

/*****************************************************************************/
/* Registrants functionality																								 */
/*****************************************************************************/

function getRegistrants($course_id) {
	
	// TODO

}

function moveRegistrant($item_id) {

	// TODO

	return true;
}

/*****************************************************************************/
/* Database functionality																										 */
/*****************************************************************************/

function createConnection() {

	$servername = "localhost";
	$username = "cityrock";
	$password = "Start123";

	$db = new mysqli($servername, $username, $password);

	if ($db->connect_error) {
		die("Connection failed: " . $db->connect_error);
	} 

	// close the connection
	// $db->close();

	return $db;
}

/*****************************************************************************/
/* General functionality																										 */
/*****************************************************************************/

function login($username, $password) {

	// TODO Write login logic 

	return true;
}

function deleteItem($item_id, $table_name) {
	
	// TODO

	return true;
}

function storeSettings($settings) {

	// TODO implement

	// returns true on success
	return true;
}

function getMonth($date_string) {
	$date = new DateTime($date_string);

	// see http://php.net/manual/en/function.date.php
	return date('F', $date); 
}

function renderNavigation($entries) {

	$root_directory = "/cityrock";

	$menu_string = "
		<ul>
			<li class='active'><a href='{$root_directory}/course'>Kursverwaltung</a></li>		
			<li><a href='{$root_directory}/user'>Nutzerverwaltung</a></li>	
			<li><a href='{$root_directory}/settings'>Einstellungen</a></li>
			<li class='mobile'><a href='{$root_directory}/index?logout'>Logout</a></li>";

	foreach($entries as $entry) {
		$menu_string .= "		
			<li class='mobile'><a href='{$root_directory}/{$entry}'>{$entry}</a></li>";
	}

	$menu_string .= "
		</ul>";

	return $menu_string;
}

?>

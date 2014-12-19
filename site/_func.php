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

	$db = createConnection();

	$result = $db->query("SELECT id, username, first_name, last_name, deletable FROM user;");

	$user_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   	$user_array[] = $row;
		}
	} 

	foreach($user_array as $key => $user) {
		$id = $user['id'];

		$roles = $db->query("SELECT title FROM role WHERE id=(SELECT role_id FROM user_has_role WHERE user_id=$id);"); 
		
		$role_string = "";
		if ($roles->num_rows > 0) {
			while($role = $roles->fetch_assoc()) {
		   	$role_array[] = $role["title"];
			}

			$user_array[$key]['roles'] = $role_array[0];
		} 
	}
	$db->close();

	return $user_array;
}

/**
 *
 * $role = 1: admin
 * $role = 2: staff
 *
 * Returns true on success.
 */
function addUser($username, $password, $role) {

	$db = createConnection();

	$result = $db->query("INSERT INTO user (username, password) VALUES ('$username', '$password');");
	if($result)
		$result = $db->query("INSERT INTO user_has_role (user_id, role_id) VALUES ((SELECT id FROM user WHERE username='$username'), $role);");

	return $result;
}


function getRoles() {
	
	$db = createConnection();

	$result = $db->query("SELECT id, title FROM role;");

	$role_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   	$role_array[] = $row;
		}
	} 
	$db->close();

	return $role_array;
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
	$password = "e5e|^4aSqxbm&t";
	$database = "cityrock";

	$db = new mysqli($servername, $username, $password, $database);

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

	//echo $item_id . " ";
	//echo $table_name;
	
	$db = createConnection();

	$result = $db->query("DELETE FROM {$table_name} WHERE id={$item_id};");
	$db->close();

	return $result;
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

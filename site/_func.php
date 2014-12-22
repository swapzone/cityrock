<?php

/*****************************************************************************/
/* Course functionality																											 */
/*****************************************************************************/

/**
 *
 */
function getCourses($course_type_id) {

	$db = createConnection();
	
	$sql = "SELECT id, course_type_id, max_participants 
					FROM course";
	if(isset($course_type_id)) 
		$sql .= " WHERE course_type_id=$course_type_id;";
	else 
		$sql .= ";";

	$result = $db->query($sql);

	$course_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   	$course_array[] = $row;
		}
	} 
	
	foreach($course_array as $key=>$course) {
		
		$result = $db->query("SELECT start 
													FROM date 
													WHERE course_id={$course['id']} 
													ORDER BY start;");

		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			$course_array[$key]['date'] = new DateTime($row['start']);
		} 
	}

	$db->close();

	usort($course_array, "courseSort");
	return $course_array;
}

/**
 *
 */
function getCourse($course_id) {

	$db = createConnection();

	$result = $db->query("SELECT max_participants, course_type_id 
												FROM course 
												WHERE id=$course_id;");
	
	if ($result->num_rows > 0) {
		$result = $result->fetch_assoc();
	} 

	$dates = $db->query("SELECT start, duration 
											 FROM date 
											 WHERE course_id={$course_id} 
											 ORDER BY start;");

	$dates_array = array();
	if ($dates->num_rows > 0) {
		while($row = $dates->fetch_assoc()) {
	   	$dates_array[] = 
				[
					"date" => new DateTime($row['start']),
					"duration" => $row['duration']
				];
		}
	} 
	
	$result['dates'] = $dates_array;

	$db->close();

	return $result;
}

/**
 *
 */
function getCourseTypes() {

	$db = createConnection();
	
	$result = $db->query("SELECT id, title FROM course_type;");

	$course_type_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   	$course_type_array[$row['id']] = $row['title'];
		}
	} 

	$db->close();

	return $course_type_array;
}

/**
 *
 */
function addCourse($course_type, $num_registrants, $num_staff, $dates) {

	$db = createConnection();
	
	$result = $db->query("INSERT INTO course (course_type_id, max_participants, min_staff) 
												VALUES ($course_type, $num_registrants, $num_staff);");
	$course_id = $db->insert_id;

	if($result) {
		foreach($dates as $date) {

			$datetime_string = $date['date'] . " " . $date['time'];
			$datetime = DateTime::createFromFormat('d.m.Y G:i', $datetime_string);
			$mysql_time = $datetime->format('Y-m-d H:i:s');

			$result = $db->query("INSERT INTO date (start, duration, course_id) 
														VALUES ('$mysql_time', {$date['duration']}, $course_id);");
		}
	}
	
	$db->close();

	return $result;
}

/**
 *
 */
function updateCourse($id, $course_type, $num_registrants, $num_staff, $dates) {

	$db = createConnection();
	
	$db->query("UPDATE course 
							SET course_type_id=$course_type, 
									max_participants=$num_registrants,
									min_staff=$num_staff 
							WHERE id=$id;");

	$result = $db->query("DELETE FROM date 
												WHERE course_id=$id;");

	if($result) {
		foreach($dates as $date) {

			$datetime_string = $date['date'] . " " . $date['time'];
			$datetime = DateTime::createFromFormat('d.m.Y G:i', $datetime_string);
			$mysql_time = $datetime->format('Y-m-d H:i:s');

			$result = $db->query("INSERT INTO date (start, duration, course_id) 
														VALUES ('$mysql_time', {$date['duration']}, $id);");
		}
	}
	
	$db->close();

	return $result;
}

/*****************************************************************************/
/* User functionality																												 */
/*****************************************************************************/

/**
 *
 */
function getUsers() {

	$db = createConnection();

	$result = $db->query("SELECT id, username, first_name, last_name, deletable 
												FROM user;");

	$user_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   	$user_array[] = $row;
		}
	} 

	foreach($user_array as $key => $user) {
		$id = $user['id'];

		$roles = $db->query("SELECT title 
												 FROM role 
												 WHERE id=(
													SELECT role_id 
													FROM user_has_role 
													WHERE user_id=$id);"); 
		
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

	$result = $db->query("INSERT INTO user (username, password) 
												VALUES ('$username', '$password');");
	if($result)
		$result = $db->query("INSERT INTO user_has_role (user_id, role_id) 
													VALUES ((SELECT id 
																	 FROM user 
																	 WHERE username='$username'), $role);");

	$db->close();

	return $result;
}

/**
 *
 */
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

/**
 *
 */
function getRegistrants($course_id) {
	
	$db = createConnection();

	$result = $db->query("SELECT id, first_name, last_name, birthday, city 
												FROM registrant AS a
												WHERE EXISTS (
													SELECT 1
													FROM course_has_registrant AS b
													WHERE b.course_id={$course_id} AND a.id=b.registrant_id 
													GROUP BY b.registrant_id
													HAVING count(*) > 0);");

	$registrants_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   	$registrants_array[] = $row;
		}
	} 
	$db->close();

	//echo "Number of registrants: " . count($registrants_array);
	return $registrants_array;
}

/**
 *
 */
function moveRegistrant($registrant_id, $old_course_id, $new_course_id) {

	$db = createConnection();
	
	$db->query("UPDATE course_has_registrant
							SET course_id=$new_course_id
							WHERE (registrant_id=$registrant_id AND 
										 course_id=$old_course_id);");

	$result = ($db->affected_rows > 0) ? true : false;

	$db->close();

	return $result;
}

/*****************************************************************************/
/* Database functionality																										 */
/*****************************************************************************/

/**
 *
 */
function createConnection() {

	$servername = "localhost";
	$username = "cityrock";
	$password = "e5e|^4aSqxbm&t";
	$database = "cityrock";

	$db = new mysqli($servername, $username, $password, $database);

	if ($db->connect_error) {
		die("Connection failed: " . $db->connect_error);
	} 

	return $db;
}

/*****************************************************************************/
/* General functionality																										 */
/*****************************************************************************/

/**
 *
 */
function login($username, $password) {

	$db = createConnection();

	$result = $db->query("SELECT password 
												FROM user 
												WHERE username='{$username}';");
	$db->close();

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		return $row['password'] === md5($password);			
	} 	
	return false;
}

/**
 *
 */
function deleteItem($item_id, $table_name) {
	
	$db = createConnection();

	$result = $db->query("DELETE FROM {$table_name} WHERE id={$item_id};");
	$db->close();

	return $result;
}

/**
 *
 */
function storeSettings($settings) {

	// TODO implement

	// returns true on success
	return true;
}

/**
 *
 */
function courseSort($a, $b) {
	return $a->date > $b->date;
}

/**
 *
 */
function getMonth($date) {

	$months = ["Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember"];
	return $months[$date->format('n')-1]; 
}

/**
 *
 */
function getEndTime($date, $duration) {
	$date->add(new DateInterval('PT'. $duration .'M'));
	
	return $date->format('h:i');
}

/**
 *
 */
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

<?php
require_once('inc/database.php');
require_once('inc/user.php');

/*****************************************************************************/
/* Course functionality																											 */
/*****************************************************************************/

/**
 * Finds all courses with the given course type or all courses if no type is 
 * given.
 *
 * @param int $course_type_id
 * @param int $archive
 * @return array of course arrays
 */
function getCourses($archive = false, $course_type_id = null) {

	$db = Database::createConnection();
	
	$sql = "SELECT id, course_type_id, title, max_participants, participants_age, min_staff, staff_deadline, interval_designator, street, zip, city, phone
		  	FROM course";

	if($course_type_id != null)
		$sql .= " WHERE course_type_id=$course_type_id";

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

	$filteredCourseArray = array();

	$now = new DateTime();

	foreach($course_array as $course) {
		if($archive) {
			if($course['date'] < $now)
				$filteredCourseArray[] = $course;
		}
		else {
			if($course['date'] > $now)
				$filteredCourseArray[] = $course;
		}
	}

	$db->close();

	usort($filteredCourseArray, "courseSort");
	return $filteredCourseArray;
}

/**
 * Find the course with the given id.
 *
 * @param int $course_id
 * @return array of courses
 */
function getCourse($course_id) {

	$db = Database::createConnection();

	$result = $db->query("SELECT course_type_id, title, max_participants, participants_age, min_staff, staff_deadline, interval_designator, street, zip, city, phone
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
	   	$dates_array[] = array( "date" => new DateTime($row['start']),
					"duration" => $row['duration']);
		}
	} 
	
	$result['dates'] = $dates_array;

	$db->close();

	return $result;
}

/**
 * Finds all course types.
 *
 * @return array of course types with $key='id' and $value='title'
 */
function getCourseTypes() {

	$db = Database::createConnection();
	
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
 * Inserts a course with the given course data and dates into the database.
 *
 * @param array $course_data
 * @param array $dates array of dates
 * @return boolean true in case it was successful
 */
function addCourse($course_data, $dates) {

	$db = Database::createConnection();

	$key_list = "";
	$value_list = "";
	foreach($course_data as $key=>$value) {
		$key_list .= ", " . $key;

		if(is_numeric($value))
			$value_list .= ", " . $value;
		else
			$value_list .= ", '" . $value . "'";
	}
	$key_list = substr($key_list, 2);
	$value_list = substr($value_list, 2);

	//echo "Keys: " . $key_list;
	//echo "Values: " . $value_list;

	$result = $db->query("INSERT INTO course ({$key_list})
						  VALUES ({$value_list});");
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
 * Updates a course with the given id and parameters.
 *
 * @param int $id
 * @param array $course_data
 * @param array $dates array of dates
 * @return boolean true in case it was successful
 */
function updateCourse($id, $course_data, $dates) {

	$db = Database::createConnection();

	$update_list = "";
	foreach($course_data as $key=>$value) {
		$update_list .= ", " . $key . "=";

		if(is_numeric($value))
			$update_list .= $value;
		else
			$update_list .= "'" . $value . "'";
	}
	$update_list = substr($update_list, 2);

	//echo "Update List: " . $update_list. "<br />";

	$db->query("UPDATE course 
				SET {$update_list}
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
 * Finds and returns all users.
 *
 * @return array of user arrays
 */
function getUsers() {

	$db = Database::createConnection();

	$result = $db->query("SELECT id
					      FROM user;");

	$db->close();

	$user_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$user_array[] = User::withUserId($row['id']);
		}
	}

	return $user_array;
}

/**
 * Adds a new user to the database with the given parameters.
 *
 * @param string $username
 * @param string $password
 * @param array §roles array of role ids
 * @return boolean true in case it was successful
 */
function addUser($username, $password, $roles) {

	$db = Database::createConnection();

	$result = $db->query("INSERT INTO user (username, password) 
						  VALUES ('$username', '$password');");

	$user_id = $db->insert_id;

	if($result) {

		$sql = "INSERT INTO user_has_role (user_id, role_id) VALUES ";

		for ($i = 0; $i < count($roles); ++$i)
		{
		    if ($i > 0) $sql .= ", ";
		    $sql .= "($user_id, {$roles[i]})";
		}
		
		$result = $db->query($sql);
	}

	$db->close();

	return $result;
}

/**
 * Add new role to user object.
 *
 * @param $user_id
 * @param $role_id
 * @return boolean true in case it was successful
 */
function addRole($user_id, $role_id) {

	$db = Database::createConnection();

	$result = $db->query("INSERT INTO user_has_role (user_id, role_id)
					      VALUES ($user_id, $role_id);");

	$db->close();

	return $result;
}

/**
 * Remove role from user object.
 *
 * @param $user_id
 * @param $role_id
 * @return boolean true in case it was successful
 */
function removeRole($user_id, $role_id) {

	$db = Database::createConnection();

	$result = $db->query("DELETE FROM user_has_role
 						  WHERE user_id={$user_id} AND role_id={$role_id};");

	$db->close();

	return $result;
}

/**
 * Finds and returns all user roles.
 *
 * @return array of user roles
 */
function getRoles() {
	
	$db = Database::createConnection();

	$result = $db->query("SELECT id, title FROM role;");

	$roles_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   		$roles_array[] = $row;
		}
	} 

	$db->close();

	return $roles_array;
}

/*****************************************************************************/
/* Registrants functionality																								 */
/*****************************************************************************/

/**
 * Adds a registrant to the database.
 *
 * @param int $course_id
 * @param string $firstname
 * @param string $lastname
 * @param string $street
 * @param string $zip
 * @param string $city
 * @param string $birthday
 * @param string $email
 * @return boolean true in case it was successful
 */
function addRegistrant($course_id, $firstname, $lastname, $street, $zip, $city, $birthday, $email) {

	$db = Database::createConnection();

	$firstname = $db->real_escape_string($firstname);
	$lastname = $db->real_escape_string($lastname);
	$street = $db->real_escape_string($street);
	$zip = $db->real_escape_string($zip);
	$city = $db->real_escape_string($city);
	$email = $db->real_escape_string($email);

	$result = $db->query("INSERT INTO registrant (first_name, last_name, street, zip, city, birthday, email) 
												VALUES ('$firstname', '$lastname', '$street', '$zip', '$city', '$birthday', '$email');");
	$registrant_id = $db->insert_id;

	if($result) 
		$result = $db->query("INSERT INTO course_has_registrant (course_id, registrant_id, confirmed) 
													VALUES ($course_id, $registrant_id, 1);");
	$db->close();

	return $result;
}

/**
 * Finds and returns all registrants.
 *
 * @return array of registrant arrays
 */
function getRegistrants($course_id) {
	
	$db = Database::createConnection();

	$result = $db->query("SELECT id, first_name, last_name, street, zip, city, birthday, email, phone 
												FROM registrant AS a
												WHERE EXISTS (
													SELECT 1
													FROM course_has_registrant AS b
													WHERE b.course_id={$course_id} AND a.id=b.registrant_id AND b.confirmed=1
													GROUP BY b.registrant_id
													HAVING count(*) > 0);");

	$registrants_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   	$registrants_array[] = $row;
		}
	} 
	$db->close();

	return $registrants_array;
}

/**
 * Moves a registrant from one course to another.
 *
 * @param int $registrant_id
 * @param int $old_course_id
 * @param int $new_course_id
 * @return boolean true in case it was successful
 */
function moveRegistrant($registrant_id, $old_course_id, $new_course_id) {

	$db = Database::createConnection();
	
	$db->query("UPDATE course_has_registrant
				SET course_id=$new_course_id
				WHERE (registrant_id=$registrant_id AND 
					 course_id=$old_course_id);");

	$result = ($db->affected_rows > 0) ? true : false;

	$db->close();

	return $result;
}

/*****************************************************************************/
/* General functionality																										 */
/*****************************************************************************/

/**
 * Checks the given user details for authentication purposes. 
 *
 * @param string $username
 * @param string $password
 * @return int user id or -1 if not valid
 */
function login($username, $password) {

	$db = Database::createConnection();

	// make sure that the user isn't trying to do some SQL injection
	$username = $db->real_escape_string($username);

	$result = $db->query("SELECT id, password, active
						  FROM user 
						  WHERE username='{$username}';");
	$db->close();

	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();
		if ($row['password'] === md5($password) && $row['active'])
			return $row['id'];		
	} 	

	return -1;
}

/**
 *	Deletes any item with the given item id in the given table.
 *
 * @param int $item_id
 * @param string $table_name
 * @return boolean true in case it was successful
 */
function deleteItem($item_id, $table_name) {
	
	$db = Database::createConnection();

	$result = $db->query("DELETE FROM {$table_name} WHERE id={$item_id};");
	$db->close();

	return $result;
}

/**
 * Sort function for course arrays.
 *
 * @param array $a
 * @param array $b
 * @return boolean true if $a is later than $b
 */
function courseSort($a, $b) {
	return $a['date'] > $b['date'];
}

/**
 *
 *
 * @return array
 */
function getIntervals() {

	$db = Database::createConnection();

	$result = $db->query("SELECT * FROM repeat_interval;");

	$interval_array = array();

	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$interval_array[] = $row;
		}
	}
	$db->close();

	return $interval_array;
}

/**
 * Returns the German month name for a given date.
 *
 * @param date $date
 * @return string
 */
function getMonth($date) {

	$months = array("Januar", "Februar", "März", "April", "Mai", "Juni", "Juli", "August", "September", "Oktober", "November", "Dezember");
	return $months[$date->format('n')-1]; 
}

/**
 * Calculates the end time for a given start time and duration.
 *
 * @param date $date
 * @param date $duration
 * @return string
 */
function getEndTime($date, $duration) {

	$date->add(new DateInterval('PT'. $duration .'M'));
	return $date->format('H:i');
}

/**
 * Renders the menu with optional entries.
 *
 * @param User $user object
 * @return string
 */
function renderNavigation($user) {

	$root_directory = "/cityrock";

	$admin_menu_items = "";

	if($user->hasPermission(array('Administrator'))) {
		$admin_menu_items = "
			<li class='active'><a href='{$root_directory}/course'>Kursverwaltung</a></li>
			<li><a href='{$root_directory}/archive'>Kursarchiv</a></li>
			<li><a href='{$root_directory}/user'>Nutzerverwaltung</a></li>
			<li><a href='{$root_directory}/settings'>Einstellungen</a></li>";
	}

	return "
		<ul>
			{$admin_menu_items}

			<li><a href='{$root_directory}/events'>Veranstaltungen</a></li>
			<li><a href='{$root_directory}/calendar'>Kalenderansicht</a></li>
			<li><a href='{$root_directory}/profile'>Mein Profil</a></li>
			<li class='mobile'><a href='{$root_directory}/index?logout'>Logout</a></li>
		</ul>";
}
?>

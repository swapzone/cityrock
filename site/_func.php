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
function getCourses($archive = false, $course_type_id = null, $start = null, $end = null) {

	$db = Database::createConnection();
	
	$sql = "SELECT course.id, course.course_type_id, course.title, course.max_participants, course.participants_age, course.min_staff, course.staff_deadline, course.interval_designator, course.street, course.zip, course.city, course.phone, date.start, date.duration, course_has_staff.user_id AS staff_id, repeat_interval.num_days AS day_interval, repeat_interval.num_months AS month_interval, repeat_interval.weekend
		  	FROM course
		  	LEFT JOIN date
		  	ON course.id=date.course_id
		  		LEFT JOIN course_has_staff
    			ON course.id=course_has_staff.course_id
    				LEFT JOIN repeat_interval
        			ON course.interval_designator=repeat_interval.id";

	if($course_type_id != null)
		$sql .= " WHERE course_type_id=$course_type_id";

	if($start && $end) {
		$startString = $start->format('Y-m-d H:i:s');
		$endString = $end->format('Y-m-d H:i:s');

		$sql .= " WHERE (DATE(start) BETWEEN '{$startString}' AND '{$endString}') OR repeat_interval.num_days>0 OR repeat_interval.num_months>0";
	}
	else {
		$tempDate = new DateTime();
		$dateString = $tempDate->format('Y-m-d H:i:s');

		if ($archive) {
			$sql .= " WHERE DATE(start) <= '{$dateString}' AND repeat_interval.num_days=0 AND repeat_interval.num_months=0";
		}
		else {
			$sql .= " WHERE DATE(start) >= '{$dateString}' OR repeat_interval.num_days>0 OR repeat_interval.num_months>0";
		}
	}

	$sql .= " ORDER BY course.id, start;";

	$result = $db->query($sql);

	$course_array = array();
	if ($result->num_rows > 0) {
		$last_id = -1;

		while($row = $result->fetch_assoc()) {
			if($row['id'] != $last_id) {
				$course_array[] = $row;
				$last_id = $row['id'];
			}
			else {
				$additional_staff = $row['staff_id'];

				if($additional_staff) {
					end($course_array);

					$all_staff = explode(",", $course_array[key($course_array)]['staff_id']);

					if(!in_array($additional_staff, $all_staff))
						$course_array[key($course_array)]['staff_id'] .= ',' . $additional_staff;

					reset($course_array);
				}
			}
		}
	} 

	foreach($course_array as $key=>$course) {
		$course_array[$key]['date'] = new DateTime($course_array[$key]['start']);
	}

	$db->close();

	usort($course_array, "courseSort");
	return $course_array;
}

/**
 * Find the course with the given id.
 *
 * @param int $course_id
 * @return array of courses
 */
function getCourse($course_id) {

	$db = Database::createConnection();

	$result = $db->query("SELECT course_type_id, title, max_participants, participants_age, min_staff, staff_deadline, interval_designator, street, zip, city, phone, repeat_interval.num_days AS day_interval, repeat_interval.num_months AS month_interval, repeat_interval.weekend
						  FROM course
						  	LEFT JOIN repeat_interval
        					ON course.interval_designator=repeat_interval.id
						  WHERE course.id={$course_id};");
	
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
	
	$result = $db->query("SELECT id, title, color FROM course_type;");

	$course_type_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
	   		$course_type_array[$row['id']] = $row;
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

/**
 *
 *
 * @param $course_id
 * @return array
 */
function getStaff($course_id) {

	$db = Database::createConnection();

	$result = $db->query("SELECT user_id
					      FROM course_has_staff
					      WHERE course_id={$course_id}");

	$db->close();

	$user_array = array();
	if ($result->num_rows > 0) {
		while($row = $result->fetch_assoc()) {
			$user_array[] = User::withUserId($row['user_id']);
		}
	}

	return $user_array;
}

/**
 *
 *
 * @param $courses
 * @param $interval_start
 * @param $interval_end can be null, then all courses until the end of the year will be returned
 * @return array
 */
function createIntervalDates($courses, $interval_start, $interval_end = null) {

	$interval_courses = array();

	if($interval_end == null) {
		$interval_end = clone $interval_start;

		$Y = $interval_end->format('Y');

		$interval_end->setDate($Y , 11 , 31);
	}

	foreach($courses as $course) {

		$course_date = clone $course['date'];

		if($course['day_interval'] > 0) {

			$weekend = $course['weekend'];

			while($course_date < $interval_end) {
				$course_date->add(new DateInterval('P' . $course['day_interval'] . 'D'));

				if($course_date > $interval_start) {
					// check if course is on weekends also
					if($weekend || !$weekend && $course_date->format('N') < 6) {
						$tempCourse = $course; // arrays are asigned by copy
						$tempCourse['date'] = clone $course_date;
						$interval_courses[] = $tempCourse;
					}
				}
			}
		}
		else if($course['month_interval']) {

			while($course_date < $interval_end) {
				$course_date->add(new DateInterval('P' . $course['month_interval'] . 'M'));

				if($course_date > $interval_start) {
					$tempCourse = $course; // arrays are asigned by copy
					$tempCourse['date'] = clone $course_date;
					$interval_courses[] = $tempCourse;
				}
			}
		}
	}

	return $interval_courses;
}

/**
 *
 *
 * @param $courses
 * @param $start_date
 * @return array
 */
function removePastDates($courses, $start_date) {

	$valid_dates = array();

	foreach($courses as $course) {
		if($course['date'] > $start_date) $valid_dates[] = $course;
	}

	return $valid_dates;
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

/**
 *
 * @param $course_id
 * @param $user_id
 * @return mixed
 */
function addStaff($course_id, $user_id) {

	$db = Database::createConnection();

	$result = $db->query("INSERT INTO course_has_staff (course_id, user_id)
						  VALUES ({$course_id}, {$user_id});");

	$db->close();

	return $result;
}

/**
 *
 *
 * @param $course_id
 * @param $user_id
 * @return mixed
 */
function removeStaff($course_id, $user_id) {

	$db = Database::createConnection();

	$result = $db->query("DELETE FROM course_has_staff
						  WHERE course_id={$course_id} AND user_id={$user_id};");

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

	$administration_menu = "";
	$admin_menu = "";

	if($user->hasPermission(array('Administrator', 'Verwaltung'))) {
		$administration_menu = "
			<li class='active'><a href='{$root_directory}/course'>Kursverwaltung</a></li>
			<li class='menu-separator'><a href='{$root_directory}/archive'>Kursarchiv</a></li>";
	}

	if($user->hasPermission(array('Administrator'))) {
		$admin_menu= "
			<li><a href='{$root_directory}/user'>Nutzerverwaltung</a></li>
			<li class='menu-separator'><a href='{$root_directory}/settings'>Einstellungen</a></li>";
	}

	return "
		<ul>
			<li><a href='{$root_directory}/calendar'>Belegungsplan</a></li>
			<li><a href='{$root_directory}/events'>Terminliste</a></li>
			<li class='menu-separator'><a href='{$root_directory}/profile'>Mein Profil</a></li>
			{$administration_menu}
			{$admin_menu}
			<li class='mobile'><a href='{$root_directory}/index?logout'>Logout</a></li>
		</ul>";
}
?>

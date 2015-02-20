<?php
/*****************************************************************************/
/* Course functionality																											 */
/*****************************************************************************/

/**
 * Finds all courses with the given course type or all courses if no type is 
 * given.
 *
 * @param int $course_type_id
 * @return array of course arrays
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
		
		$result = $db->query("SELECT start, duration
													FROM date 
													WHERE course_id={$course['id']} 
													ORDER BY start;");
		
		$dates_array = array();
		if ($result->num_rows > 0) {
			while($row = $result->fetch_assoc()) {
	   		$dates_array[] = array( "date" => new DateTime($row['start']),
					"duration" => $row['duration']);
			}
		} 
		$course_array[$key]['dates'] = $dates_array;
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
 * Inserts a course with the given parameters into the database.
 *
 * @param int $course_type
 * @param int $num_registrants
 * @param int $num_staff
 * @param array $dates array of dates
 * @return boolean true in case it was successful
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
 * Updates a course with the given id and parameters.
 *
 * @param int $id
 * @param int $course_type
 * @param int $num_registrants
 * @param int $num_staff
 * @param array $dates array of dates
 * @return boolean true in case it was successful
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
/* Registrants functionality																								 */
/*****************************************************************************/

/**
 * Finds and returns all registrants.
 *
 * @return array of registrant arrays
 */
function getRegistrants($course_id) {
	
	$db = createConnection();

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

	$db = createConnection();

	$firstname = $db->real_escape_string($firstname);
	$lastname = $db->real_escape_string($lastname);
	$street = $db->real_escape_string($street);
	$zip = $db->real_escape_string($zip);
	$city = $db->real_escape_string($city);
	$email = $db->real_escape_string($email);

	$result = $db->query("INSERT INTO registrant (first_name, last_name, street, zip, city, birthday, email) 
												VALUES ('$firstname', '$lastname', '$street', '$zip', '$city', '$birthday', '$email');");
	$registrant_id = $db->insert_id;

	// enter new registrant into course-registrant table
	if($result) 
		$result = $db->query("INSERT INTO course_has_registrant (course_id, registrant_id, confirmed) 
													VALUES ($course_id, $registrant_id, 0);");

	$date = new DateTime();
	$mysql_time = $date->format('Y-m-d H:i:s');

	$key = crypt("$email/{$date->format('U')}");
	$key = str_replace('$', 'd', $key);
	$key = str_replace('.', 'p', $key);
	$key = str_replace('/', 'u', $key);

	// enter new confirmation set for registrant
	if($result)	
		$result = $db->query("INSERT INTO confirmation (registrant_id, course_id, activation_key, registration_date)
													VALUES ($registrant_id, $course_id, '$key', '$mysql_time');");	

	$db->close();

	return $result ? $key : null;
}

/**
 * Confirm registrant.
 *
 */
function confirmRegistrant($confirmation_code) {

	$db = createConnection();
	
	$result = $db->query("SELECT registrant_id, course_id
												FROM confirmation
												WHERE activation_key='$confirmation_code';");	


	if ($result->num_rows > 0) {
		$row = $result->fetch_assoc();

		$registrant_id = $row['registrant_id'];
		$course_id = $row['course_id'];
		
		$result = $db->query("UPDATE course_has_registrant
													SET confirmed=1
													WHERE registrant_id=$registrant_id AND course_id=$course_id;");	
	}
	else {
		$db->close();	
		return false;
	}

	$db->close();	
	
	return $result;	
}

/*****************************************************************************/
/* Database functionality																										 */
/*****************************************************************************/

/**
 * Creates a database connection and returns the database handle.
 *
 * @return object database handle
 */
function createConnection() {

	$servername = "localhost";
	$username = "h1011m1";
	$password = "hitovo53";
	$database = "h1011m1";

	$db = new mysqli($servername, $username, $password, $database);

	if ($db->connect_error) {
		die("Connection failed: " . $db->connect_error);
	} 

	$db->set_charset("utf8");

	return $db;
}

/*****************************************************************************/
/* General functionality																										 */
/*****************************************************************************/

/**
 *	Deletes any item with the given item id in the given table.
 *
 * @param int $item_id
 * @param string $table_name
 * @return boolean true in case it was successful
 */
function deleteItem($item_id, $table_name) {
	
	$db = createConnection();

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
	return $a['dates'][0]['date'] > $b['dates'][0]['date'];
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
	return $date->format('H');
}
?>

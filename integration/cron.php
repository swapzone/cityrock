<?php
	include('_func.php');

	// include config_lite library
	require_once('config/Lite.php');
	$config = new Config_Lite('verwaltung/basic.cfg');

	header('Content-Type: text/html; charset=utf-8');

	$deadlineReminder = $config['email']['notification'];
	$deadlineAdministration = $config['system']['administration'];

	// get all courses within the given deadlines
	$db = createConnection();
	
	$result = $db->query("SELECT start, course_id
												FROM date 
												ORDER BY course_id, start;"); 
	
	$reminderArray = array();
	$administrationArray = array();

	if ($result->num_rows > 0) {
		$tempCourseId = null;

		while($row = $result->fetch_assoc()) {
			// only take first day of each course to compare against
			if($tempCourseId && $row['course_id'] != $tempCourseId) {

				$courseDate = new DateTime($row['start']);
				$currentDate = new DateTime();
				// $currentDate->setDate(2015, 3, 11); // DEBUG
			
				// check for reminder deadlines
				$modString = '-'.$deadlineReminder.' days';
				$deadline = clone $courseDate;
				$deadline->modify($modString);

				if($currentDate->format('d.m.Y') == $deadline->format('d.m.Y')) 
					$reminderArray[] = $row['course_id'];
	
				// check for administration deadlines
				$modString = '-'.$deadlineAdministration.' days';
				$deadline = clone $courseDate;
				$deadline->modify($modString);

				if($currentDate->format('d.m.Y') == $deadline->format('d.m.Y')) 
					$administrationArray[] = $row['course_id'];
			}

			$tempCourseId = $row['course_id'];
		}
	} 

	$db->close();	

	if(count($reminderArray)) {

		foreach($reminderArray as $courseId) {

			// send email reminder to participants
			$subject = $config['email']['subject-reminder'];
			$body = $config['email']['body-reminder'];

			// retrieve dates of course and place into email body
			$courseData = getCourse($courseId);
			$courseDates = $courseData['dates'];

			$courseDatesString = "";
			foreach($courseDates as $courseDate) {
				
				$date = $courseDate['date'];
				$duration = $courseDate['duration'];
				$month = getMonth($date);

				$courseDatesString .= "{$date->format('d.')} {$month}, {$date->format('H:i')}-" . 
					getEndTime($date, $duration, true) . " Uhr\n";
			}

			$body = str_replace('[%dates]', $courseDatesString, $body);

			// retrieve participants list
			$registrants = getRegistrants($courseId);
		
			foreach($registrants as $registrant) {			
				
				// send mail to every participant
				$success = sendMail($registrant['email'], $subject, $body);

				if(!$success) {
					echo "Erinnerungsmail an {$registrant['email']} für Kurs mit der
								Kurs-ID $courseId konnte nicht gesendet werden.";
				}
			}
		}
	}

	if(count($administrationArray)) {

		// send participants list to administration
		$adminEmailsList = $config['system']['administration-list'];	
		$subject = "Teilnehmerliste für bevorstehenden Kletterkurs";
		
		foreach($administrationArray as $courseId) {

			// retrieve course data
			$courseData = getCourse($courseId);

			// retrieve participants list
			$registrants = getRegistrants($courseId);

			// create the link for the participants list for the given course
			// http://www.cityrock.de/verwaltung/course/$id/registrants/print

			$registrantsListLink = "http://www.cityrock.de/verwaltung/course/" 
				. $courseId . "/registrants/print";

			$body = "Die Kursliste kann unter $registrantsListLink abgerufen werden.";

			// parse email adresses from list
			$emailArray = explode('//', $adminEmailsList);
			
			// send list to all email address
			foreach($emailArray as $email) {
				$email = trim($email);
				
				$success = sendMail($email, $subject, $body);

				if(!$success) {
					echo "Kursliste für den Kurs mit der Kurs-ID $courseId konnte 
								nicht gesendet werden.";
				}
			}
		}
	}

	/**
	 * Send email with pre-defined attributes.
	 *
	 */
	function sendMail($email, $subject, $body) {

		$to = "$email";

		$header  = "MIME-Version: 1.0\n"; 
		$header .= "Content-Type: text/plain; charset=utf-8\n"; 
		$header .= 'From: Cityrock <info@cityrock.de>' . "\n";
		$header .= "Reply-To: Cityrock <info@cityrock.de>\n"; 
	
		// send email and return status
		return mail($to, $subject, $body, $header);

		/*
		// DEBUG
		echo "Sende Email: <br /> ";		
		echo "Empfänger: $email <br />";
		echo "Betreff: $subject <br />";
		echo "Nachricht: $body <br /><br />";

		return true;
		*/
	}
?>

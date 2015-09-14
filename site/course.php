<?php

include_once('_init.php');

$required_roles = array('Administrator');

if(User::withUserObjectData($_SESSION['user'])->hasPermission($required_roles)) {

	$course_types = getCourseTypes();

	/***********************************************************************/
	/* Process form data												   */
	/***********************************************************************/
	if(isset($_POST['type'])) {
		// add all given dates
		$dates = array();

		$counter = 1;
		while($counter < 6) {
			if($_POST["date-$counter"]) {
				$date = array(
					"date" => $_POST["date-$counter"],
					"time" => $_POST["time-$counter"],
					"duration" => $_POST["duration-$counter"]
				);

				$dates[] = $date;
			}
			$counter++;
		}

		$course_data = array();

		$course_data['course_type_id'] = $_POST['type'];

		if($_POST['title'])
			$course_data['title'] = $_POST['title'];

		if($_POST['interval'] >= 0)
			$course_data['interval_designator'] = $_POST['interval'];

		if($_POST['staff'])
			$course_data['min_staff'] = $_POST['staff'];

		if($_POST['staff_deadline'])
			$course_data['staff_deadline'] = $_POST['staff_deadline'];

		if($_POST['registrants'])
			$course_data['max_participants'] = $_POST['registrants'];

		if($_POST['registrants_age'])
			$course_data['participants_age'] = $_POST['registrants_age'];

		if($_POST['street'])
			$course_data['street'] = $_POST['street'];

		if($_POST['phone'])
			$course_data['phone'] = $_POST['phone'];

		if($_POST['zip_city']) {
			$address_array = explode(" ", $_POST['zip_city']);

			if (count($address_array) > 1) {
				if (is_numeric($address_array[0])) {
					$course_data['zip'] = $address_array[0];
					unset($address_array[0]);
				}

				$course_data['city'] = join(' ', $address_array);
			}
		}

		if(isset($_POST['id'])) {
			// update course
			$success = updateCourse($_POST['id'], $course_data, $dates);

			$title = "Kurs editieren";

			if($success)
				$content = "Kurs wurde erfolgreich editiert.";
			else
				$content = "Fehler: Kurs konnte nicht editiert werden.";
		}
		else {
			// create course
			$success = addCourse($course_data, $dates);

			$title = "Neuer Kurs";

			if($success)
				$content = "Kurs wurde erfolgreich erstellt.";
			else
				$content = "Fehler: Kurs konnte nicht erstellt werden.";
		}
	}
	else {
		if(isset($_GET["id"])) {
			if($_GET["id"] == "new") {
				/***********************************************************************/
				/* Course new 										                   */
				/***********************************************************************/
				$title = "Neuer Kurs";
				$content = "
					<form method='post' onsubmit='return cityrock.validateForm(this);'>
						<label for='type'>Kurstyp</label>
						<select name='type' id='type'>";

				foreach($course_types as $key=>$value) {
					$content .= "<option value='{$key}'>{$value}</option>";
				}

				$content .= "
						</select>
						<label for='title'>Kunde/Titel</label>
						<input type='text' placeholder='' name='title'>
						<label for='date-1'>Datum (in der Form <span class='italic'>dd.mm.yyyy</span>)</label>
						<input type='text' placeholder='z.B. 02.10.2015' name='date-1' class='date'>
						<label for='time-1'>Startuhrzeit (in der Form <span class='italic'>hh:mm</span>)</label>
						<input type='text' placeholder='z.B. 09:00' name='time-1' class='time'>
						<label for='duraration-1'>Dauer (in Minuten)</label>
						<input type='text' name='duration-1' class='duration'>
						<span class='add-day'>
							<a href='#' id='add-day'>Tag hinzufügen</a>
						</span>
						<label for='interval'>Wiederholen</label>
						<select name='interval'>";

				$intervalArray = getIntervals();

				foreach($intervalArray as $interval) {
					$selected = $interval['description'] == "nie" ? "selected" : "";

					$content .= "<option value='{$interval['id']}' {$selected}>{$interval['description']}</option>";
				}

				$content .= "
						</select>
						<label for='staff'>Anzahl Übungsleiter</label>
						<input type='text' placeholder='' name='staff'>
						<label for='staff_deadline'>Bis wieviele Tage vorher dürfen sich ÜL noch austragen?</label>
						<input type='text' name='staff_deadline' value='2'>
						<label for='registrants'>Maximale Anzahl an Teilnehmern</label>
						<input type='text' placeholder='' name='registrants'>
						<label for='registrants_age'>Alter der Teilnehmer</label>
						<input type='text' placeholder='' name='registrants_age'>
						<br />
						<h3>Adresse der Veranstaltung</h3>
						<label for='street'>Straße</label>
						<input type='text' placeholder='' name='street'>
						<label for='zip_city'>PLZ/Ort</label>
						<input type='text' placeholder='Bitte mit Leerzeichen zwischen PLZ und Ort eingeben' name='zip_city'>
						<label for='phone'>Telefon</label>
						<input type='text' placeholder='' name='phone'>

						<input type='hidden' value='1' name='days'>
						<a href='{$root_directory}/course' class='button error'>Abbrechen</a>
						<input type='submit' value='Erstellen' class='button'>
					</form>";
			}
			else {
				/***********************************************************************/
				/* Course edit																											   */
				/***********************************************************************/
				if(isset($_GET["action"]) && $_GET["action"] == "edit") {
					$course_id = $_GET["id"];
					$course = getCourse($course_id);
					$number_of_days = count($course['dates']);

					$title = "Kurs editieren";
					$content = "
						<form method='post' onsubmit='return cityrock.validateForm(this);'>
							<label for='type'>Kurstyp</label>
							<select name='type' id='type'>";

					foreach($course_types as $key=>$title) {
						if($course['course_type_id'] == $key)
							$content .= "<option selected value='{$key}'>{$title}</option>";
						else
							$content .= "<option value='{$key}'>{$title}</option>";
					}

					$content .= "
							</select>
							<label for='title'>Kunde/Titel</label>
							<input type='text' name='title' value='{$course['title']}'>";

					$counter = 1;
					foreach($course['dates'] as $date) {
						$content .= "
							<div class='day-container'>
								<h3 class='inline'>Tag {$counter}</h3><span>(<a href='#' class='remove-day'>entfernen</a>)</span>
								<label for='date-{$counter}'>Datum (in der Form <span class='italic'>dd.mm.yyyy</span>)</label>
								<input type='text' value='{$date['date']->format('d.m.Y')}' name='date-{$counter}' class='date'>
								<label for='{$counter}'>Startuhrzeit (in der Form <span class='italic'>hh:mm</span>)</label>
								<input type='text' value='{$date['date']->format('h:i')}' name='time-{$counter}' class='time'>
								<label for='duraration-{$counter}'>Dauer (in Minuten)</label>
								<input type='text' name='duration-{$counter}' class='duration' value='{$date['duration']}'>
							</div>";

						$counter++;
					}

					$content .= "
							<span class='add-day'>
								<a href='#' id='add-day'>Tag hinzufügen</a>
							</span>
							<label for='interval'>Wiederholen</label>
							<select name='interval'>";

					$intervalArray = getIntervals();

					foreach($intervalArray as $interval) {
						if($course['interval_designator'] != null)
							$selected = $interval['id'] == $course['interval_designator'] ? "selected" : "";
						else
							$selected = $interval['description'] == "nie" ? "selected" : "";

						$content .= "<option value='{$interval['id']}' {$selected}>{$interval['description']}</option>";
					}

					$content .= "
							</select>
							<label for='staff'>Anzahl Übungsleiter</label>
							<input type='text' name='staff' value='{$course['min_staff']}'>
							<label for='staff_deadline'>Bis wieviele Tage vorher dürfen sich ÜL noch austragen?</label>
							<input type='text' name='staff_deadline' value='{$course['staff_deadline']}'>
							<label for='registrants'>Maximale Anzahl an Teilnehmern</label>
							<input type='text' name='registrants' value='{$course['max_participants']}'>
							<label for='registrants_age'>Alter der Teilnehmer</label>
							<input type='text' name='registrants_age' value='{$course['participants_age']}'>
							<br />
							<h3>Adresse der Veranstaltung</h3>
							<label for='street'>Straße</label>
							<input type='text' name='street' value='{$course['street']}'>
							<label for='zip_city'>PLZ/Ort</label>
							<input type='text' name='zip_city' value='{$course['zip']} {$course['city']}'>
							<label for='phone'>Telefon</label>
							<input type='text' name='phone' value='{$course['phone']}'>

							<input type='hidden' value='{$number_of_days}' name='days'>
							<input type='hidden' value='{$course_id}' name='id'>
							<a href='./' class='button error'>Abbrechen</a>
							<input type='submit' value='Speichern' class='button'>
						</form>";
				}
				else {
					/***********************************************************************/
					/* Course details																										   */
					/***********************************************************************/
					$course_id = $_GET["id"];
					$course = getCourse($course_id);
					$registrants = getRegistrants($course_id);
					$staff = getStaff($course_id);
					$staff_num = count($staff);
					$all_users = getUsers();

					$staff_list = "<span class='staff-list''>";
					$index = 1;
					foreach($staff as $user) {
						$userObj = $user->serialize();
						$staff_list .= "<span>ÜL {$index}: {$userObj['first_name']} {$userObj['last_name']} (<a href='#' user-id='{$userObj['id']}' class='remove-staff''>entfernen</a>)</span>";
						$index++;
					}
					$staff_list .= "</span>";

					$user_list = "<option value='-1' style='display:none;'></option>";
					foreach($all_users as $user) {
						$userObj = $user->serialize();

						$user_name = $userObj['first_name'] . " " . $userObj['last_name'];
						if(trim($user_name) == "") $user_name = $userObj['username'];

						$user_list .= "<option value='{$userObj['id']}'> {$user_name}</option>";
					}

					$showAddStaffLink = $course['min_staff'] > count($staff) ? "" : "display: none;";

					$title = "Kursdetails";
					$content = "
						<span class='list'>
							<span class='list-item' style='display: none;'>
								<span style='display: none;'>Kurs ID</span>
								<span style='display: none;' id='course-id'>{$course_id}</span>
							</span>
							<span class='list-item'>
								<span>Kunde/Titel</span><span>{$course['title']}</span>
							</span>
							<span class='list-item'>
								<span>Kurstyp</span>
								<span>{$course_types[$course['course_type_id']]}</span>
							</span>
							<span class='list-item'>
								<span>Maximale Teilnehmerzahl</span>
								<span>{$course['max_participants']}</span>
							</span>
							<span class='list-item'>
								<span>Bereits registrierte Teilnehmer</span>
								<span>" . count($registrants) ." (<a href='./{$course_id}/registrants'>anzeigen</a>)</span>
							</span>
							<span class='list-item'>
								<span>Alter der Teilnehmer</span>
								<span>{$course['participants_age']}</span>
							</span>
							<span class='list-item'>
								<span>Übungsleiter</span>
								<span>
									{$staff_num} / {$course['min_staff']}
									{$staff_list}
									<span style='{$showAddStaffLink}'>
										<a href='#' id='add-staff'>Übungsleiter hinzufügen</a>
										<select id='staff-list' style='display: none'>
											{$user_list}
										</select>
									</span>
								</span>
							</span>";

					$counter = 1;
					foreach($course['dates'] as $date) {

						$content .= "
							<span class='list-item'>
								<span>Datum (Tag $counter)</span>
								<span>{$date['date']->format('d.m.Y')}</span>
							</span>
							<span class='list-item'>
								<span>Uhrzeit (Tag $counter)</span>
								<span>{$date['date']->format('h:i')} - " . getEndTime($date['date'], $date['duration']) . " Uhr</span>
							</span>";

						$counter++;
					}

					$content .= "
							<span class='list-item'>
								<span>Addresse</span><span>{$course['street']}, {$course['plz']} {$course['city']}</span>
							</span>
						</span>
						<span>
							<form class='inline' action='{$root_directory}/confirmation' method='post'>
								<input type='hidden' name='confirmation' value='true'>
								<input type='hidden' name='action' value='delete'>
								<input type='hidden' name='description' value='Kurs'>
								<input type='hidden' name='table' value='course'>
								<input type='hidden' name='id' value='{$course_id}'>
								<a href='#' class='button error confirm'>Löschen</a>
							</form>
						</span>
						<a href='{$root_directory}/course/{$course_id}/edit' class='button'>Editieren</a>
						<a href='{$root_directory}/course' class='button'>Zurück</a>";
				}
			}
		}
		else {
			/***********************************************************************/
			/* Course overview													   */
			/***********************************************************************/
			$title = "Kursübersicht";
			$content = "
				<label for='course-filter'>Wähle einen Kurstyp, um die Liste zu filtern: </label>
				<select class='filter' name='course-filter'>
					<option value='Alle'>Alle</option>";

			foreach($course_types as $key=>$type) {
				$content .= "<option value='$type'>{$type}</option>";
			}

			$content .= "
				</select>
				<div class='list'>
					<span class='list-heading'>
						<span>Kurstyp</span>
						<span>Datum</span>
						<span class='no-mobile'>Plätze</span>
						<span class='no-mobile'>Anmeldungen</span>
						<span></span>
					</span>";

			$courses = getCourses();

			$month = null;
			foreach($courses as $course) {

				// check if course was in the past
				if($course['date'] > new DateTime()) {

					$registrants = getRegistrants($course['id']);
					$num_registrants = count($registrants);

					if(getMonth($course['date']) != $month) {
						$month = getMonth($course['date']);
						$content .= "<span class='course-list-month'>{$month}</span>";
					}

					$item_class = strtolower($course_types[$course['course_type_id']]);

					$content .= "
						<span class='list-item $item_class'>
							<span>{$course_types[$course['course_type_id']]}</span>
							<span>{$course['date']->format('d.m.Y')}</span>
							<span class='no-mobile'>{$course['max_participants']}</span>
							<span class='no-mobile'>$num_registrants (<a href='./course/{$course['id']}/registrants'>Liste</a>)</span>
							<span><a href='./course/{$course['id']}'>Details</a></span>
						</span>";
				}
			}

			$content .= "
				</div>
				<a href='./course/new' class='button'>Kurs hinzufügen</a>";
		}
	}
}
else {
	$title = "Kursübersicht";
	$content = "Du hast keine Berechtigung für diesen Bereich der Website.";
}

$content_class = "course";
include('_main.php');
?>

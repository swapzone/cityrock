<?php

include_once('_init.php');

$course_types = getCourseTypes();

/***********************************************************************/
/* Process form data																								   */
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

	if(isset($_POST['id'])) {
		// update course
		$success = updateCourse($_POST['id'], $_POST['type'], $_POST['registrants'], 2, $dates);

		$title = "Kurs editieren";
		
		if($success)		
			$content = "Kurs wurde erfolgreich editiert.";	
		else 
			$content = "Fehler: Kurs konnte nicht editiert werden.";	
	}
	else {
		// create course
		$success = addCourse($_POST['type'], $_POST['registrants'], 2, $dates);

		$title = "Neuer Kurs";
		
		if($success)		
			$content = "Kurs wurde erfolgreich erstellt.";	
		else 
			$content = "Fehler: Kurs konnte nicht erstellt werden.";	
	}
}
else {
	if(isset($_GET["id"])) {
		/***********************************************************************/
		/* Course new 																											   */
		/***********************************************************************/
		if($_GET["id"] == "new") {
			$title = "Neuer Kurs";
			$content = "
				<form method='post' onsubmit='return cityrock.validateForm(this);'>
					<label for='type'>Kurstyp</label>
					<select name='type' id='type'>";
	
			foreach($course_types as $key=>$title) {
				$content .= "<option value='{$key}'>{$title}</option>";
			}

			$content .= "
					</select>
					<label for='date-1'>Datum (in der Form <span class='italic'>dd.mm.yyyy</span>)</label>
					<input type='text' placeholder='z.B. 02.10.2015' name='date-1' class='date'>
					<label for='time-1'>Startuhrzeit (in der Form <span class='italic'>hh:mm</span>)</label>
					<input type='text' placeholder='z.B. 09:00' name='time-1' class='time'>
					<label for='duraration-1'>Dauer (in Minuten)</label>
					<input type='text' name='duration-1' class='duration'>
					<span class='add-day'>
						<a href='#' id='add-day'>Tag hinzufügen</a>
					</span>
					<label for='registrants'>Maximale Anzahl an Teilnehmern</label>
					<input type='text' name='registrants'>
					<input type='hidden' value='1' name='days'>			
					<a href='./' class='button error'>Abbrechen</a>	
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
						</select>";
			
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
						<label for='registrants'>Maximale Anzahl an Teilnehmern</label>
						<input type='text' name='registrants' value='{$course['max_participants']}'>
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

				$title = "Kursdetails";
				$content = "
					<span class='list'>
						<span class='list-item'>
							<span>Kurs ID</span><span>{$course_id}</span>
						</span>
						<span class='list-item'>
							<span>Kurstyp</span><span>{$course_types[$course['course_type_id']]}</span>
						</span>
						<span class='list-item'>
							<span>Maximale Teilnehmerzahl</span><span>{$course['max_participants']}</span>
						</span>
						<span class='list-item'>
							<span>Bereits registrierte Teilnehmer</span>
							<span>" . count($registrants) ." (<a href='./{$course_id}/registrants'>anzeigen</a>)</span>
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
					</span>
					<span>
						<form class='inline' action='{$root_directory}/confirmation' method='post'>
							<input type='hidden' name='confirmation' value='true'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='description' value='Kurs'>
							<input type='hidden' name='table' value='course'>
							<input type='hidden' name='id' value='{$course_id}'>
							<a href='#' class='button error confirm'>löschen</a>
						</form>		
					</span>
					<a href='{$root_directory}/course/{$course_id}/edit' class='button'>Editieren</a>
					<a href='{$root_directory}/course' class='button'>Zurück</a>";
			}
		}
	}
	else {
		/***********************************************************************/
		/* Course overview																									   */
		/***********************************************************************/
		$title = "Kursübersicht";
		$content = "
			<!-- COURSE FILTER -->
			<div class='course-filter' id='filter'>
				<span class='all active'>Alle</span>";

		foreach($course_types as $key=>$title) {
			$content .= "<span>{$title}</span>";
		}

		$content .= "
			</div>
			<div class='list'>
				<span class='list-heading'>
					<span>Kurstyp</span>
					<span>Datum</span>
					<span class='no-mobile'>Teilnehmer</span>
					<span></span>
				</span>";

		$courses = getCourses();
		
		$month = null;
		foreach($courses as $course) {
			
			if(getMonth($course['date']) != $month) {
				$month = getMonth($course['date']);
				$content .= "<span class='course-list-month'>{$month}</span>";
			}

			$item_class = strtolower($course_types[$course['course_type_id']]);			

			$content .= "
				<span class='list-item $item_class'>
					<span>{$course_types[$course['course_type_id']]}</span>
					<span>{$course['date']->format('d.m.Y')}</span>
					<span class='no-mobile'>max. {$course['max_participants']} (<a href='./course/{$course['id']}/registrants'>Liste</a>)</span>
					<span><a href='./course/{$course['id']}'>Details</a></span>
				</span>";
		}

		$content .= "
			</div>
			<a href='./course/new' class='button'>Kurs hinzufügen</a>";
	}
}

$content_class = "course";
include('_main.php');
?>

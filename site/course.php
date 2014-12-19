<?php

include_once('_init.php');

if(isset($_POST['type'])) {
	// TODO implement add logic for multiple dates
	$success = addCourse($_POST['type']);
	
	if($success) {
		$title = "Neuer Kurs";
		$content = "Kurs wurde erstellt.";	
	}	
	else {
		$title = "Neuer Kurs";
		$content = "Fehler: Kurs konnte nicht erstellt werden.";	
	}
}
else {
	if(isset($_GET["id"])) {
		if($_GET["id"] == "new") {
			$title = "Neuer Kurs";
			$content = "
				<form method='post' onsubmit='return cityrock.validateForm(this);'>
					<label for='type'>Kurstyp</label>
					<select name='type' id='type'>
						<option>Vorstieg</option>
						<option>Toprope</option>
						<option>Schnupper</option>
					</select>
					<label for='date'>Datum (in der Form <span class='italic'>dd.mm.yyyy</span>)</label>
					<input type='text' placeholder='z.B. 02.10.2015' name='date'>
					<label for='time'>Startuhrzeit (in der Form <span class='italic'>hh:mm</span>)</label>
					<input type='text' placeholder='z.B. 09:00' name='time'>
					<label for='duraration'>Dauer (in Minuten)</label>
					<input type='text' name='duration'>
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
			$course_id = $_GET["id"];
			$course = getCourse($course_id);
			

			$title = "Kursdetails";
			$content = "
				<span class='list'>
					<span class='list-item'>
						<span>Kurs ID</span><span>{$course_id}</span>
					</span>
					<span class='list-item'>
						<span>Kurstyp</span><span>Vorstieg</span>
					</span>
					<span class='list-item'>
						<span>Maximale Teilnehmerzahl</span><span>15</span>
					</span>
					<span class='list-item'>
						<span>Bereits registrierte Teilnehmer</span><span>8 ( <a href='./{$course_id}/registrants'>anzeigen</a> )</span>
					</span>
					<span class='list-item'>
						<span>Datum (Tag 1)</span><span>12.12.2015</span>
					</span>
					<span class='list-item'>
						<span>Uhrzeit (Tag 1)</span><span>12:00 - 17:00 Uhr</span>
					</span>
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
				<a href='./' class='button'>Zurück</a>";
		}
	}
	else {
		$title = "Kursübersicht";
		$content = "
			<!-- COURSE FILTER -->
			<div class='course-filter' id='filter'>
				<span class='all active'>Alle</span>
				<span>Schnupper</span>
				<span>Toprope</span>
				<span>Vorstieg</span>
			</div>
			<div class='list'>
				<span class='list-heading'>
					<span>Kurstyp</span>
					<span>Datum</span>
					<span class='no-mobile'>Teilnehmer</span>
					<span></span>
				</span>";

		$content .= "
				<span class='list-item vorstieg'>
					<span>Vorstiegskurs</span>
					<span>10.12.2015</span>
					<span class='no-mobile'>
						15 (<a href='./course/123/registrants'>anzeigen</a>)
					</span>
					<span><a href='./course/123'>Details</a></span>
				</span>
				<span class='list-item vorstieg'>
					<span>Vorstiegskurs</span>
					<span>15.12.2015</span>
					<span class='no-mobile'>
						10 (<a href='./course/123/registrants'>anzeigen</a>)
					</span>
					<span><a href='./course/123'>Details</a></span>
				</span>
				<span class='list-item toprope'>
					<span>Toprope</span>
					<span>21.12.2015</span>
					<span class='no-mobile'>
						15 (<a href='./course/123/registrants'>anzeigen</a>)
				</span>
					<span><a href='./course/123'>Details</a></span>
				</span>";

		$courses = getCourses();

		$month = null;
		foreach($courses as $course) {

			if(getMonth($course['date']) != $month) {
				$month = getMonth($course['date']);
				$content .= "<span class='course-list-month'>{$month}</span>";
			}
			$content .= "
				<span class='list-item vorstieg'>
					<span>Vorstiegskurs</span>
					<span>10.12.2015</span>
					<span class='no-mobile'>15</span>
					<span class='no-mobile'><a href='./course/123/registrants'>Liste</a></span>
					<span><a href='./course/123'>Details</a></span>
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

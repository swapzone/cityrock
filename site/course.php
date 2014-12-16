<?php

include_once('_init.php');

if(isset($_POST['username']) && isset($_POST['password'])) {
	$success = addUser($_POST['username'], $_POST['password'], 1);
	
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
				<form method='post'>
					<label for='username'>Kurstyp</label>
					<input type='text' placeholder='Nutzername' name='username' id='username'>
					<input type='submit' value'Erstellen' class='button'>
				</form>";
		}
		else {
			$title = "Kursdetails";
			$content = "Für Kurs mit der ID=" . $_GET["id"] . ".";
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

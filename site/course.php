<?php

include_once('_init.php');

if(isset($_POST['username']) && isset($_POST['password'])) {
	$success = addUser($_POST['username'], $_POST['password'], 1);
	
	if($success)
		$content = "
				<h2>Neuen Kurs hinzufügen</h2>
				<p>Kurs wurde erstellt.</p>";	
	else
		$content = "
				<h2>Neuen Kurs hinzufügen</h2>
				<p>Fehler: Kurs konnte nicht erstellt werden.</p>";	
}
else {
	if(isset($_GET["id"])) {
		if($_GET["id"] == "new") {
			$content = "
			 	<h2>Neuen Kurs hinzufügen</h2>
				<form action='{$root_directory}/user' method='post'>
					<label for='username'>Kurstyp</label>
					<input type='text' placeholder='Nutzername' name='username' id='username'>
					<input type='submit'>
				</form>";
		}
		else {
			$content = "
				<h2>Kursdetails</h2>				
				Für Kurs mit der ID=" . $_GET["id"] . ".";
		}
	}
	else {
		$content = "
			<div class='action-bar'>
				<!-- COURSE FILTER -->
				<div class='course-filter' id='filter'>
					<span class='all active'>Alle</span>
					<span>Schnupper</span>
					<span>Toprope</span>
					<span>Vorstieg</span>
				</div>
			</div>
			<div class='course-list'>
				<span class='course-list-heading'>
					<span>Kurstyp</span>
					<span>Datum</span>
					<span class='no-mobile'>Teilnehmer</span>
					<span class='no-mobile'>Teilnehmerliste</span>
					<span></span>
				</span>";

		$content .= "
				<span class='course-list-item vorstieg'>
					<span>Vorstiegskurs</span>
					<span>10.12.2015</span>
					<span class='no-mobile'>15</span>
					<span class='no-mobile'><a href='./course/123/registrants'>anzeigen</a></span>
					<span><a href='./course/123'>Details</a></span>
				</span>
				<span class='course-list-item vorstieg'>
					<span>Vorstiegskurs</span>
					<span>15.12.2015</span>
					<span class='no-mobile'>10</span>
					<span class='no-mobile'><a href='./course/123/registrants'>anzeigen</a></span>
					<span><a href='./course/123'>Details</a></span>
				</span>
				<span class='course-list-item toprope'>
					<span>Toprope</span>
					<span>21.12.2015</span>
					<span class='no-mobile'>15</span>
					<span class='no-mobile'><a href='./course/123/registrants'>anzeigen</a></span>
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
				<span class='course-list-item vorstieg'>
					<span>Vorstiegskurs</span>
					<span>10.12.2015</span>
					<span class='no-mobile'>15</span>
					<span class='no-mobile'><a href='./course/123/registrants'>anzeigen</a></span>
					<span><a href='./course/123'>Details</a></span>
				</span>";
		}

		$content .= "
			</div>
			<div class='action-bar'>
				<a href='./course/new' class='button new'>Kurs hinzufügen</a>
			</div>";
	}
}

include('_main.php');
?>

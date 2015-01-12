<?php

include_once('_init.php');

if(isset($_POST['new'])) {
	/***********************************************************************/
	/* Add registrant to database																				   */
	/***********************************************************************/
	$success = false;

	if(preg_match("/\d{2}.\d{2}.\d{4}/", $_POST['birthday'])) {
		$birthday = DateTime::createFromFormat('d.m.Y', $_POST['birthday']);

		$success = addRegistrant($_GET['id'], $_POST['firstname'], $_POST['lastname'], 
														 $_POST['street'], $_POST['zip'], $_POST['city'], 
														 $birthday, $_POST['email']);
	}

	$title = "Neuer Teilnehmer";
	
	if($success)		
		$content = "Teilnehmer wurde erfolgreich hinzugefügt.";	
	else 
		$content = "Fehler: Teilnehmer konnte nicht hinzugefügt werden.";	
	
	include('_main.php');
}
else {
	$course = getCourse($_GET['id']);
	$registrants = getRegistrants($_GET['id']);

	if(isset($_GET['action'])) {
		if($_GET['action'] == 'print') {
			require('./lib/fpdf/fpdf.php');
			/***********************************************************************/
			/* Print registrant list																						   */
			/***********************************************************************/
			$title = utf8_decode("Teilnehmerliste für den");
			$title .= " " . $course['dates'][0]['date']->format('j.n.Y');

			$pdf = new FPDF();
			$pdf->SetMargins(15, 20);

			$pdf->AddPage();
			$pdf->SetFont('Arial','B',16);
			$pdf->Cell(0,18,$title,0,1,'L');

			foreach($registrants as $registrant) {
				$birthday = date('j.n.Y',strtotime($registrant['birthday']));

				$firstname = utf8_decode($registrant['first_name']);
				$lastname = utf8_decode($registrant['last_name']);

				$text = "{$firstname} {$lastname}";
				$text .= " ($birthday)";
				$pdf->SetFont('Arial','B',12);
				$pdf->Cell(0,5,$text,0,0,'L');
				$pdf->ln();
				
				$street = utf8_decode($registrant['street']);
				$city = utf8_decode($registrant['city']);

				$text = "{$street}, {$registrant['zip']} {$city}";
				$pdf->SetFont('Arial','',12);
				$pdf->Cell(0,5,$text,0,0,'L');
				$pdf->ln();

				$text = "{$registrant['email']}, {$registrant['phone']}";
				$pdf->Cell(0,5,$text,0,0,'L');
				$pdf->ln();

				$pdf->ln();
			}

			$pdf->Output();
		}
		else if($_GET['action'] == 'new') {
			/***********************************************************************/
			/* Add registrant																										   */
			/***********************************************************************/
			$title = "Teilnehmer hinzufügen";
			$content = "
				<form method='post' onsubmit='return cityrock.validateForm(this);'>
					<label for='firstname'>Vorname</label>
					<input type='text' placeholder='Max' name='firstname'>
					<label for='lastname'>Nachname</label>
					<input type='text' placeholder='Mustermann' name='lastname'>
					<label for='street'>Straße und Hausnummer</label>
					<input type='text' placeholder='Musterstraße 2' name='street'>
					<label for='zip'>Postleitzahl</label>
					<input type='text' placeholder='123456' name='zip' class='zip'>
					<label for='city'>Ort</label>
					<input type='text' placeholder='Musterstadt' name='city'>
					<label for='birthday'>Geburtsdatum</label>
					<input type='text' placeholder='01.01.1900' name='birthday' class='date'>
					<label for='email'>Email Adresse</label>
					<input type='text' placeholder='max@mustermann.de' name='email' class='email'>
					<input type='hidden' value='true' name='new'>
					<a href='./' class='button error'>Abbrechen</a>
					<input type='submit' value='Erstellen' class='button'>
				</form>";

			$content_class = "registrants";
			include('_main.php');
		}
	}
	else {
		/***********************************************************************/
		/* Show all registrants																							   */
		/***********************************************************************/
		$title = "Teilnehmer";
		$content = "
			<p>Liste der Teilnehmer, die sich für Kurs {$_GET['id']} registriert haben.</p>
			<div class='list'>
				<span class='list-heading'>
					<span>Name</span>
					<span>Geburtsdatum</span>
					<span class='no-mobile'>Ort</span>
					<span class='no-mobile'></span>
					<span></span>
				</span>";

		foreach($registrants as $registrant) {
			$birthday = date('j.n.Y',strtotime($registrant['birthday']));

			$content .= "
				<span class='list-item'>
					<span>{$registrant['first_name']} {$registrant['last_name']}</span>
					<span>$birthday</span>
					<span class='no-mobile'>{$registrant['city']}</span>
					<span class='no-mobile registrant-move'><a href='#' class='move' id='{$registrant['id']}'>verschieben</a></span>
					<span>
						<form action='{$root_directory}/confirmation' method='post'>
							<input type='hidden' name='confirmation' value='true'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='description' value='Teilnehmer'>
							<input type='hidden' name='table' value='registrant'>
							<input type='hidden' name='id' value='{$registrant['id']}'>
							<a href='#' class='confirm'>löschen</a>
						</form>		
					</span>
				</span>";
		}

		$content .= "
			</div>
			<span id='move-registrant'>
		  	<form action='{$root_directory}/confirmation' method='post' class='inline'>
					<label for='new_course_id' class='inline'>Verschieben nach:</label>
					<select name='new_course_id' class='inline'>";

		$alternatives = getCourses($course['course_type_id']);

		$counter = 0;
		foreach($alternatives as $alternative) {
		
			if($alternative['id'] != $_GET['id']) {
				$date = $alternative['date']->format('j.n.Y');

				$content .= "<option value='{$alternative['id']}'>$date</option>";

				if(++$counter>10) break;
			}
		}
			
		$content .= "    	
			    </select>
					<input type='hidden' name='confirmation' value='true'>
					<input type='hidden' name='action' value='move'>
					<input type='hidden' name='description' value='Teilnehmer'>
					<input type='hidden' name='table' value='registrant'>
					<input type='hidden' name='old_course_id' value='{$_GET['id']}'>
					<input type='hidden' name='registrant_id' value='-1'>
			    <input type='submit' class='button button-move-item' value='Verschieben'>
			  </form>
			  <a href='#' class='button error remove-move-item'>Abbrechen</a>
			</span>
			<a href='{$root_directory}/course' class='button'>Zurück</a>
			<a href='{$root_directory}/course/{$_GET['id']}/registrants/new' class='button'>Teilnehmer hinzufügen</a>
			<a href='{$root_directory}/course/{$_GET['id']}/registrants/print' class='button' target='_blank'>Drucken</a>";

		$content_class = "registrants";
		include('_main.php');
	}
}
?>

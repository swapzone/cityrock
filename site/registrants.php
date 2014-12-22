<?php

include_once('_init.php');

$course = getCourse($_GET['id']);
$registrants = getRegistrants($_GET['id']);

if(isset($_GET['action'])) {
	require('./lib/fpdf/fpdf.php');

	$title = utf8_decode("Teilnehmerliste für den");
	$title .= " " . $course['dates'][0]['date']->format('j.n.Y');

	$pdf = new FPDF();
	$pdf->SetMargins(25, 20);

	$pdf->AddPage();
	$pdf->SetFont('Arial','B',16);
	$pdf->Cell(0,18,$title,0,1,'L');
	$pdf->SetFont('Arial','',14);

	foreach($registrants as $registrant) {
		$birthday = date('j.n.Y',strtotime($registrant['birthday']));
		$text = "{$registrant['first_name']} {$registrant['last_name']}";
		$text .= " ($birthday)";
		$text .= " aus {$registrant['city']}";

		$pdf->Cell(0,10,$text,0,0,'L');
		$pdf->ln();
	}

	$pdf->Output();
}
else {
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
    	<form action='{$root_directory}/confirmation' method='post'>
				<label for='new_course_id'>Verschieben nach:</label>
				<select name='new_course_id'>";

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
		<a href='{$root_directory}/course/{$_GET['id']}/registrants/print' class='button' target='_blank'>Drucken</a>";

	$content_class = "registrants";
	include('_main.php');
}
?>

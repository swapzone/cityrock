<?php

include_once('_init.php');

$registrants = getRegistrants($_GET['id']);

if(isset($_GET['action'])) {
	require('./lib/fpdf/fpdf.php');

	$title = utf8_decode("Teilnehmerliste für den");
	$title .= " " . getCourseDate($_GET['id'])[0]->format('j.n.Y');

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

		$content .= "
			<span class='list-item'>
				<span>{$registrant['first_name']} {$registrant['last_name']}</span>
				<span>{$registrant['birthday']}</span>
				<span class='no-mobile'>{$registrant['city']}</span>
				<span class='no-mobile registrant-move'><a href='' class='move'>verschieben</a></span>
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
		<a href='{$root_directory}/course' class='button'>Zurück</a>
		<a href='{$root_directory}/course/{$_GET['id']}/registrants/print' class='button' target='_blank'>Drucken</a>";

	include('_main.php');
}
?>

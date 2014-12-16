<?php

include_once('_init.php');

$title = "Teilnehmer";
$content = "
	<p>Liste der Teilnehmer, die sich für Kurs " . $_GET['id'] . " registriert haben.</p>
	<div class='list'>
		<span class='list-heading'>
			<span>Name</span>
			<span>Geburtsdatum</span>
			<span class='no-mobile'>Ort</span>
			<span class='no-mobile'></span>
			<span></span>
		</span>";

$content .= "
		<span class='list-item'>
			<span>Max Mustermann</span>
			<span>10.12.1988</span>
			<span class='no-mobile'>Musterstadt</span>
			<span class='no-mobile registrant-move'><a href='#'>verschieben</a></span>
			<span><a href='#'>löschen</a></span>
		</span>
		<span class='list-item'>
			<span>Max Mustermann</span>
			<span>10.12.1988</span>
			<span class='no-mobile'>Musterstadt</span>
			<span class='no-mobile registrant-move'><a href='#'>verschieben</a></span>
			<span><a href='#'>löschen</a></span>
		</span>
		<span class='list-item'>
			<span>Max Mustermann</span>
			<span>10.12.1988</span>
			<span class='no-mobile'>Musterstadt</span>
			<span class='no-mobile registrant-move'><a href='#'>verschieben</a></span>
			<span><a href='#'>löschen</a></span>
		</span>";

// TODO add course ID
$registrants = getRegistrants(123);


foreach($registrants as $registrant) {

	$content .= "
		<span class='list-item'>
			<span>Max Mustermann</span>
			<span>10.12.1988</span>
			<span class='no-mobile'>Musterstadt</span>
			<span class='no-mobile registrant-move link'>verschieben</span>
			<span><a href='#'>löschen</a></span>
		</span>";
}

$content .= "
	</div>
	<a href='#' class='button'>Drucken</a>";

include('_main.php');
?>

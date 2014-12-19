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
			<span class='no-mobile registrant-move'><a href='#' class=''>verschieben</a></span>
			<span>
				<form action='{$root_directory}/confirmation' method='post'>
					<input type='hidden' name='confirmation' value='true'>
					<input type='hidden' name='action' value='delete'>
					<input type='hidden' name='description' value='Teilnehmer'>
					<input type='hidden' name='table' value='registrant'>
					<input type='hidden' name='id' value='123'>
					<a href='#' class='confirm'>löschen</a>
				</form>		
			</span>
		</span>
		<span class='list-item'>
			<span>Max Mustermann</span>
			<span>10.12.1988</span>
			<span class='no-mobile'>Musterstadt</span>
			<span class='no-mobile registrant-move'><a href='#' class=''>verschieben</a></span>
			<span>
				<form action='{$root_directory}/confirmation' method='post'>
					<input type='hidden' name='confirmation' value='true'>
					<input type='hidden' name='action' value='delete'>
					<input type='hidden' name='description' value='Teilnehmer'>
					<input type='hidden' name='table' value='registrant'>
					<input type='hidden' name='id' value='123'>
					<a href='#' class='confirm'>löschen</a>
				</form>		
			</span>
		</span>
		<span class='list-item'>
			<span>Max Mustermann</span>
			<span>10.12.1988</span>
			<span class='no-mobile'>Musterstadt</span>
			<span class='no-mobile registrant-move'><a href='#' class=''>verschieben</a></span>
			<span>
				<form action='{$root_directory}/confirmation' method='post'>
					<input type='hidden' name='confirmation' value='true'>
					<input type='hidden' name='action' value='delete'>
					<input type='hidden' name='description' value='Teilnehmer'>
					<input type='hidden' name='table' value='registrant'>
					<input type='hidden' name='id' value='123'>
					<a href='#' class='confirm'>löschen</a>
				</form>		
			</span>
		</span>";

$registrants = getRegistrants($_GET['id']);

foreach($registrants as $registrant) {

	$content .= "
		<span class='list-item'>
			<span>Max Mustermann</span>
			<span>10.12.1988</span>
			<span class='no-mobile'>Musterstadt</span>
			<span class='no-mobile registrant-move'><a href='' class=''>verschieben</a></span>
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
	<a href='#' class='button'>Drucken</a>";

include('_main.php');
?>

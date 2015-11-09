<?php
include('_func.php');

// include config_lite library
require_once('config/Lite.php');
$config = new Config_Lite('verwaltung/basic.cfg');

header('Content-Type: text/html; charset=utf-8');

$courses = getCourses(3);
$year = new DateTime();
$year = $year->format('Y');

$deadlineLimit = $config['system']['deadline'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Willkommen im [cityrock] - Kletterkurs für Anfänger und Fortgeschrittene" />
<meta name="keywords" content="Kletterkurs, Kletterhalle, Klettern, Kletterzentrum, Stuttgart, cityrock" />
<title>Kletter-Schnupperkurs für Einsteiger im [cityrock] - Indoorklettern in der Stuttgarter City</title>
<link href="alles.css" rel="stylesheet" type="text/css" />
<style type="text/css">
<!--

	.course-row td:first-child {
		padding-right: 10px;		
	}

	.course-row-date {
		font-weight: bold;
	}

	.Stil1 {font-weight: bold}
-->
</style>
</head>

<body>
<div id="header">
  <? include ("header.php"); ?></div>
<div id="seitenrahmen">
  <? include ("menu.php"); ?>
</div>
<div id="content">  <table border="0" align="right" cellpadding="0" cellspacing="0">
    <tr>
      <td width="380" height="330" align="right" valign="top"><img src="img/fotos/halle1.jpg" alt="" height="240" width="320" border="0" /><br /></td>
    </tr>
  </table>	
  <span class="ueber">Kletter-Schnupperkurs<br /></span>
  <p>Der Schnupperkurs ist die perfekte Gelegenheit, das Klettern einmal auszuprobieren. In zwei Stunden lernen die Teilnehmerinnen und Teilnehmer einige grundlegende Klettertechniken und können, gesichert von unseren erfahrenen Betreuern, ihr Klettertalent an vielen verschiedenen Wänden austesten. Die nötige  Ausrüstung gibt's natürlich dazu - und wer das Klettern als seinen Sport entdeckt hat, erhält den weiterführenden <a href="grundkurs.php">Toprope-Kurs</a> um 10,- Euro günstiger.</p>
  <p><strong><br />
    Kursprogramm und Leistungen:</strong><br />
    <br />
    - Grundlegende Klettertechniken<br />
    - Sicherung durch unsere Betreuer<br />
    - Ausrüstung (Klettergurt, HMS-Karabiner, Kletterschuhe)<br />
    - Gutschein über 10,- für weiterführenden Kurs<br />
  - Versicherung<br />
  <br />
  <strong><br />
  Preis pro Person:</strong> 25,- Euro </p>
<p>Mitzubringen ist bequeme Sportkleidung. <br />
  Für Verpflegung muss selbst gesorgt werden.</p>
<br />
    <br />

    <div style='margin: 1em 0 0.3em 0;'>
    	<strong>Termine <?php echo $year; ?></strong>
    </div>
	<table>
	<?php
		$now = new DateTime();

		foreach($courses as $course) {

			if($course['dates'][0]['date'] > $now) {	

				if($course['dates'][0]['date']->format(Y) != $year) {
					$year = $course['dates'][0]['date']->format(Y);

					echo "
						</table>
						<div style='margin: 1em 0 0.3em 0;'><strong>Termine {$year}</strong></div>
						<table>";
				}

				$registrants = getRegistrants($course['id']);
				$placesAvailable = $course['max_participants'] - count($registrants);
			
				$date = $course['dates'][0]['date'];
				$duration = $course['dates'][0]['duration'];

				$deadline = clone $date;	
				$modString = '-'.$deadlineLimit.' days';
				$deadline->modify($modString);

				$day = $date->format('d.');;
				$month = getMonth($date);

				$color = "#1975FF";
				$text = "&gt; Online-Anmeldung";
				$link = "<a href='anmeldung.php?id={$course['id']}' style='color: {$color};'>{$text}</a>";

				if($placesAvailable < 5) {
					$color = "#CC3300";
					$text = "&gt; Online-Anmeldung";
					$link = "<a href='anmeldung.php?id={$course['id']}' style='color: {$color};'>{$text}</a>";
				}
				if($placesAvailable <= 0) {
					$color = "#990000";
					$text = "&gt; Kurs ausgebucht";
					$link = "<span style='color: {$color};'>{$text}</span>";
				}
				if($now>$deadline) {
					$color = "#990000";
					$text = "&gt; Anmeldung nicht mehr möglich";
					$link = "<span style='color: {$color};'>{$text}</span>";
				}							

				echo "
					<tr class='course-row'>
						<td><span class='course-row-date'>{$day} {$month}</span>, {$date->format('H')}-" . getEndTime($date, $duration) . " Uhr</td>
						<td>{$link}</td>
					</tr>";
			}
		}
	?>
	</table>
    <br />
  	Für Gruppen ab 4 Personen bieten wir extra Termine auf Anfrage an.<br />
  	<br />
	<br />	
	<b>Legende:<br /></b>
	<font color="#1975FF">Ausreichend freie Plätze</font><br />
	<font color="#CC3300">Wenige freie Plätze</font><br />
	<font color="#990000">Kurs ausgebucht</font>
</div>
</body>
</html>

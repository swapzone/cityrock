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
<div id="content">  <table
  " border="0" align="right" cellpadding="0" cellspacing="0">
    <tr>
      <td width="380" height="330" align="right" valign="top"><img src="img/fotos/halle1.jpg" alt="" height="240" width="320" border="0" /><br /></td>
    </tr>
  </table>	
  <span class="ueber">Kletter-Schnupperkurs<br />
  <br />
  </span>
  <p>
		Der Schnupperkurs bietet die perfekte Gelegenheit, das Klettern  einmal auszuprobieren. 
		In 4 Stunden lernen die Teilnehmer die grundlegende Kletter- und Sicherungstechnik und können unter Anleitung 
		unserer erfahrenen MitarbeiterInnen selbstständig erste Erfahrungen  als Kletternde sowie auch als Sichernde sammeln.<br />
    <br />
    Alter: Ab 14 Jahre<br />
    Kosten pro Person: € 50,-<br />
    <br />
    [cityrock]® stellt an diesem Tag alles benötigte Klettermaterial (Klettergurt, Karabiner, Kletterschuhe und Seile) zur Verfügung. 
		Eigenes Material kann jedoch ebenfalls verwendet werden. Für Verpflegung muss selbst gesorgt werden.<br />
    <br />

    <b>Termine <?php echo $year; ?></b><br />
		<table>
		<?php

			foreach($courses as $course) {

				if($course['dates'][0]['date'] > new DateTime()
						&& $course['dates'][0]['date']->format(Y) == $year) {	

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
					if($placesAvailable == 0) {
						$color = "#990000";
						$text = "&gt; Kurs ausgebucht";
						$link = "<span style='color: {$color};'>{$text}</span>";
					}
					if(new DateTime()>$deadline) {
						$color = "#990000";
						$text = "&gt; Anmeldung nicht mehr möglich";
						$link = "<span style='color: {$color};'>{$text}</span>";
					}							

					echo "
						<tr>
							<td>{$day} {$month}, {$date->format('H')}-" . getEndTime($date, $duration) . " Uhr</td>
							<td>{$link}</td>
						</tr>";
				}
			}
		?>
		</table>
    <br />
    Für Gruppen ab 4 Personen bieten wir extra Termine auf Anfrage an.<br />
    <br />
    Weitere Infos und Anmeldung  bei Sportreferent <a href="kontakt.php">Rainer Öhrle</a>.</p>
		<br />	
		<b>Legende:<br /></b>
		<font color="#1975FF">Ausreichend freie Plätze</font><br />
		<font color="#CC3300">Wenige freie Plätze</font><br />
		<font color="#990000">Kurs ausgebucht</font>
</div>
</body>
</html>

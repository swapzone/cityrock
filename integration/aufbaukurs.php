<?php
include('_func.php');

// include config_lite library
require_once('config/Lite.php');
$config = new Config_Lite('verwaltung/basic.cfg');

header('Content-Type: text/html; charset=utf-8');

$courses = getCourses(2);
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

		<title>Kletterkurs für  Fortgeschrittene im [cityrock] Stuttgart</title>
		<link href="alles.css" rel="stylesheet" type="text/css" />
		<style type="text/css">
			<!--

			.course-table {
				display: table;
			}
			
			.table-row {
				display: table-row;
			}

			.table-column {
				display: table-cell;
				padding-right: 10px;
			}
	
			.table-column.date {
				font-weight: bold;
			}

			.booking-link {
				display: block;
				margin-bottom: 18px;
				margin-top: 2px;
			}

			.Stil1 {font-weight: bold}

			-->
		</style>
	</head>

	<body>
		<div id="header">
		  <? include ('header.php'); ?></div>
		<div id="seitenrahmen">
		  <? include ('menu.php'); ?>
		</div>
		
		<div id="content"> 
      <table width="450" border="0" align="right" cellpadding="0" cellspacing="0">
				<tr>
			   	<td width="670" height="600" align="right" valign="top"><img src="img/fotos/kurse1.jpg" width="400" height="489" alt="Vorstiegskurs im Cityrock" /><br /></td>
		    </tr>
			</table>	 
			<span class="ueber">Aufbaukurs Vorstiegsklettern</span><br /><br />
			<p>
				Der Aufbaukurs &quot;Vorstiegsklettern&quot; ist ideal für Kletterer, 
				die das Topropeklettern bereits beherrschen und nun den nächsten Schritt - das Vorstiegsklettern - erlernen möchten.<br />
				<br />
Insbesondere beim Vorstiegsklettern und -sichern wird ein Kurs unter professioneller Anleitung   dringend empfohlen. Um Sicherungsfehler zu vermeiden, ist nicht nur viel Aufmerksamkeit, sondern auch einige Erfahrung notwendig. In diesem Kurs erlernt ihr Schritt für Schritt das richtige Sicherungsverhalten von Profis. Um eine solide Basis zu schaffen, bleibt zudem viel Zeit, das Gelernte ausführlich zu üben.<br />
				<br />
		      <strong>Nach erfolgreicher Teilnahme erhält jede/r TeilnehmerIn den Kletterschein &quot;Vorstieg&quot; des DAV</strong> und ist in der Lage, selbstständig und sicher an künstlichen Kletterwänden  im Vorstieg zu klettern und zu sichern.<br />
				<br />
				Der Kurs dauert zweimal vier Stunden und findet an zwei aufeinanderfolgenden Tagen statt. 
				[cityrock]® stellt für die Dauer des Kurses das benötigte Material zur Verfügung. Eigenes Material darf auch verwendet werden.
			<br />
			</p>
<p><strong>Voraussetzungen: </strong>Teilnehmen kann jeder, der den Schwerigkeitsgrad <strong>5+ (UIAA)</strong> im Toprope sowie die Toprope-Sicherungstechnik 
      mit mindestens einem Sicherungsgerät sicher beherrscht. Die Teilnahme an einem Toprope-Kletterkurs ist keine zwingende Voraussetzung. </p>
			<p>
				<br />
				<br />
			</p>  

			<table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				  <td valign="top"><span class="ueber">Eckdaten</span><br />
			      <br /></td>
				  <td valign="top">&nbsp;</td>
				  <td colspan="2" valign="top"><strong class="ueber">Termine</strong></td>
			  </tr>
				<tr>
					<td width="15%" valign="top">
						Dauer:<br /><br /><br /><br />
						Alter:<br /><br />
						Kosten:<br /><br />
						Teilnehmer:<br />
			      <br />
			      <b>Legende:</b></td>
					<td width="34%" valign="top">Zweitägig (jws. 4 Stunden)<br />
				    Uhrzeit siehe Termine<br /><br /><br />
				    Ab 14 Jahre<br /><br />
				    € 95,-<br /><br />
				    Maximal 12 Personen<br /><br />
				    <font color="#1975FF">Ausreichend freie Plätze</font><br />
                    <font color="#CC3300">Wenige freie Plätze</font><br />
                    <font color="#990000">Kurs ausgebucht</font></td>
					<td width="51%" colspan="2" valign="top"><strong><?php echo $year; ?></strong><br />
						<br />
						<div>
						<?php
							foreach($courses as $course) {

								if($course['dates'][0]['date'] > new DateTime()) {		
			
									if($course['dates'][0]['date']->format(Y) != $year) {
										$year = $course['dates'][0]['date']->format(Y);

										echo "<div style='margin: 1em 0 0.3em 0;'><strong>{$year}</strong></div>";
									}
									
									$registrants = getRegistrants($course['id']);
									$placesAvailable = $course['max_participants'] - count($registrants);

									$deadline = clone $course['dates'][0]['date'];	
									$modString = '-'.$deadlineLimit.' days';
									$deadline->modify($modString);

									$datesString = "";
	
									foreach($course['dates'] as $date) {
										$duration = $date['duration'];
										$date = $date['date'];
								
										$day = $date->format('d.');
										$month = getMonth($date);

										$datesString .= "
											<span class='table-row'>
												<span class='table-column date'>
													$day $month
												</span>
												<span class='table-column time'>
													{$date->format('H')}-" . getEndTime($date, $duration) . " Uhr
												</span>
											</span>";
									}
						
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
										<span class='course-table'>
											{$datesString}
										</span>
										<span class='booking-link'>{$link}</span>";
								}
							}
						?>
						</div>          
					  <br />
					  Für Gruppen ab 4 Personen bieten wir Kurse zu extra Terminen an. Für eine Anfrage bitte <a href="kontakt.php">Kontakt</a> zu uns aufnehmen.
					</td>
				</tr>
				<tr>
					<td valign="top">&nbsp;</td>
					<td valign="top">&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		  <br />
			<br />
			<br />
		</div>
	</body>
</html>

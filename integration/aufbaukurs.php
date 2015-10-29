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
			   	<td width="670" height="600" align="right" valign="top"><img src="img/fotos/vorstieg1.jpg" width="400" alt="Vorstiegskurs" /><br />
			   	  <br />
			   	  <img src="img/fotos/vorstieg2.jpg" width="400" alt="Vorstiegskurs" /><br />
			   	  <br />
			   	  <br />
			   	  <br /><br />
				</td>
		    </tr>
			</table>	 
			<span class="ueber">Aufbaukurs Vorstiegsklettern</span><br /><br />
			
				Der Aufbaukurs &quot;Vorstiegsklettern&quot; ist ideal für Kletterer, 
				die das Topropeklettern bereits beherrschen und nun den nächsten Schritt wagen wollen.<br />
				<br />
				Beim Vorstiegsklettern wird ein Kurs unter professioneller Anleitung   dringend empfohlen. Um Sicherungsfehler zu vermeiden, ist viel Aufmerksamkeit und einige Erfahrung notwendig. In diesem Kurs erlernt ihr Schritt für Schritt das richtige Sicherungsverhalten von Profis und habt viel Zeit, das Gelernte ausführlich zu üben. Mit diesen soliden Kenntnissen könnt ihr euch sowohl im Toprope als auch im Vorstieg absolut sicher  an künstlichen Kletterwänden bewegen - außerdem erhält jede/r TeilnehmerIn nach dem Kurs den <strong>Kletterschein &quot;Vorstieg&quot; des DAV</strong>.<br />
				</p>
				<p><strong>Voraussetzungen: </strong><br />
				Teilnehmen kann jeder, der den Schwerigkeitsgrad <strong>5+ (UIAA)</strong> im Toprope sowie die<strong> Toprope-Sicherungstechnik 
      			mit mindestens einem Sicherungsgerät</strong> sicher beherrscht. Die Teilnahme an einem Toprope-Kletterkurs ist keine zwingende Voraussetzung.<br />
      			<br />
				[cityrock]® stellt für die Dauer des Kurses das benötigte Material zur Verfügung. Eigenes Material darf auch mitgebracht werden.
			<br />
		    </p>
			<p>&nbsp;</p>  

			<table width="40%" border="0" cellspacing="0" cellpadding="0">
				<tr>
				  <td valign="top"><span class="ueber">Eckdaten</span><br />
			      <br /></td>
				  <td valign="top">&nbsp;</td>
			  </tr>
				<tr>
					<td width="15%" valign="top">
						Dauer:<br /><br /><br />
						Alter:<br /><br />
						Kosten:<br /><br />
						Teilnehmer:<br />
		              	<br />
		              	<br />
						<br />
					</td>
				  	<td width="34%" valign="top">Zweitägig (jws. 4 Stunden)<br />
					    Uhrzeit siehe Termine<br /><br />
					    Ab 14 Jahre<br /><br />
					    € 95,-<br /><br />
					    Maximal 12 Personen
					    <br />
	                    <br />
	                    <br />
	                </td>
				</tr>
				<tr>
					<td colspan="2" valign="top"><strong class="ueber">Termine</strong>
					    <div style='margin: 1em 0 0.5em 0;'>
							<strong><?php echo $year; ?></strong>
						</div>
						<div>
						<?php
							foreach($courses as $course) {

								if($course['dates'][0]['date'] > new DateTime()) {	

									if($course['dates'][0]['date']->format(Y) != $year) {
										$year = $course['dates'][0]['date']->format(Y);

										echo "<div style='margin: 1em 0 0.5em 0;'><strong>{$year}</strong></div>";
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
									if($placesAvailable <= 0) {
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
					    Für Gruppen ab 4 Personen bieten wir Kurse zu extra Terminen an. Für eine Anfrage bitte <a href="kontakt.php">Kontakt</a> zu uns aufnehmen.<br />
					    <br />
					    Legende:<br />
					    <font color="#1975FF">Ausreichend freie Plätze</font><br />
	                    <font color="#CC3300">Wenige freie Plätze</font><br />
	                    <font color="#990000">Kurs ausgebucht</font>
	                    <br />
                    </td>
				</tr>
			</table>
    		<br />
			<br />
			<br />
		</div>
	</body>
</html>

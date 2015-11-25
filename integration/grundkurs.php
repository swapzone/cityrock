<?php
include('_func.php');

// include config_lite library
require_once('config/Lite.php');
$config = new Config_Lite('verwaltung/basic.cfg');

header('Content-Type: text/html; charset=utf-8');

$courses = getCourses(1);
$year = new DateTime();
$year = $year->format('Y');

$deadlineLimit = $config['system']['deadline'];
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Willkommen im [cityrock] - Kletterkurs für Anfänger und Fortgeschrittene" />
<meta name="keywords" content="Kletterhalle, Klettern, Kletterzentrum, Stuttgart, cityrock" />
<title>Kletterkurs für Einsteiger im [cityrock] - Der Fels in der Stadt. Klettern im Zentrum von Stuttgart</title>
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

	#kursaus {
		position:absolute;
		width:124px;
		height:31px;
		z-index:6;
		left: 24px;
		top: 924px;
	}
-->
</style>
</head>

<body onload="MM_preloadImages('/img/klet_r.gif','img/klet.gif','/img/klet_l.gif')">
<div id="header">
  <? include ("header.php"); ?></div>
<div id="seitenrahmen">
  <? include ("menu.php"); ?>
</div>
<div id="content">
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<tr>
		  <td width="330" height="250" valign="top"><span class="ueber">Kletterschein Toprope</span><br />
		    <br />
		   Der Kurs <strong>Kletterschein Toprope</strong> bietet den perfekten Einstieg in die Sportkletterei. Hier lernt ihr neben klettertechnischen Basics insbesondere die <strong>Toprope-Sicherungstechnik</strong> unter fachmännischer Anleitung - nach dem Kurs könnt ihr euch selbstständig und sicher an allen künstlichen Kletterwänden  mit Toprope-Routen bewegen.
		    <p>Ideal ist der Kurs insbesondere für Neulinge, die eine solide Basis schaffen wollen - Vorkenntnisse sind nicht erforderlich. Auch für erfahrene Kletterer, die vorhandenes Wissen auffrischen und erweitern möchten, ist der Kurs empfehlenswert. Wie bei all unseren Kursen werden die Teilnehmer von erfahrenen, ausgebildeten Trainern betreut.</p>
		    <p>Mit erfolgreicher Teilnahme am Kurs erhält die/der TeilnehmerIn den <strong>Kletterschein &quot;Toprope&quot; des DAV</strong>.</p>
		    <p>[cityrock]® stellt an beiden Tagen das benötigte Klettermaterial zur Verfügung. Eigenes Material darf mitgebracht werden.<br />
<br />
		    </p>
		    <table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td valign="top"><span class="ueber">Eckdaten</span><br />
	      <br /></td>
		  <td valign="top">&nbsp;</td>
	  </tr>
		<tr>
     	<td valign="top" width="12%">Dauer:<br /><br /><br />
     		<br />
	    Alter:<br /><br />
     		Kosten:<br /><br />
       	Teilnehmer:</td>
    	<td width="37%" valign="top">8 Stunden<br />
    	  Zweit&auml;gig (jws. 4 Stunden)<br />
      	Uhrzeit siehe Termin<br />
      	<br />
      	Ab 14 Jahre<br /><br />				&euro; 95,-<br /><br />
      	Maximal 12 Personen<span class="ueber"><br />
      	<br />
<br />
	    </span></td>
		</tr>
			
		<tr>
			<td colspan="2" valign="top"><strong class="ueber">Termine<br />
			  <br />
			</strong><strong><?php echo $year; ?></strong><br />
            <br />
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
            <b>Legende:<br />
            </b> <font color="#1975FF">Ausreichend freie Plätze</font><br />
            <font color="#CC3300">Wenige freie Plätze</font><br />
            <font color="#990000">Kurs ausgebucht</font><br />
            <br />
            Für Gruppen ab 4 Personen bieten wir Kletterkurse zu extra Terminen an. Für eine Terminvereinbarung bitte Kontakt zu uns aufnehmen. <strong class="ueber">		</strong></td>
      </tr>
	</table>	          
			</td>
      <td width="477" align="right" valign="top"><img src="img/fotos/toprope3.jpg" alt="Kletterschein Toprope" width="400" /><br />
<br />

<img src="img/fotos/kurse3.jpg" alt="Toprope-Kurs" /><br />
<br /><img src="img/fotos/toprope4.jpg" width="400" alt="Toprope-Kurs" />
<br /><br /></td>
    </tr>
  </table>
  <br />
	<br />
	<br />

		

  <br />
  <br />
  <br />
  <br />
</div>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
<div class="Stil1" id="kursaus">Kletterkurs für Anfänger und Fortgeschrittene im [cityrock] Stuttgart - Klettern im Zentrum von Stuttgart</div>
</body>
</html>

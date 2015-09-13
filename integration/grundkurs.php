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
		  <td width="308" height="250" valign="top"><span class="ueber">Kletterschein Toprope</span><br />
		    <br />
		    Der Kurs <b>Kletterschein Toprope</b> bietet den perfekten Einstieg in die Sportkletterei. 
				Neben klettertechnischen Basics wird insbesondere die Sicherungstechnik in Toproperouten vermittelt.<br />
		    <br />
		    Ideal ist der Kurs für Kletteranfänger, die unter fachmännischer Anleitung eine solide Basis schaffen wollen, 
				um sich sicher und verantwortungsbewusst an künstlichen Kletterwänden bewegen zu können. 
				Außerdem bietet der Kurs die Möglichkeit, vorhandenes Wissen aufzufrischen und zu erweitern. 
				Wie bei all unseren Kursen werden die Teilnehmer von erfahrenen, ausgebildeten Trainern betreut.<br />
		    <br />
		    Der Kurs dauert zweimal vier Stunden und findet am Samstag und Sonntag eines Wochenendes statt. 
				Mit erfolgreicher Teilnahme an diesem Kurs erhält die/der TeilnehmerIn den Kletterschein &quot;Toprope&quot; des DAV.<br />
	      <br />
	      <br />
	      <strong>Ausrüstung</strong><br />
				<br />
		    [cityrock]® stellt an beiden Tagen alles benötigte Klettermaterial zur Verfügung, 
				eigenes Material kann natürlich auch verwendet werden. Mitzubringen sind Hallenschuhe und bequeme Sportkleidung, 
				für Verpflegung muss selbst gesorgt werden.<br />	          
			</td>
      <td width="477" align="right" valign="top"><img src="img/fotos/toprope1.jpg" alt="Kletterschein Toprope" /></td>
    </tr>
  </table>
	<br />
	<br />
	<br />

	<table width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		  <td valign="top"><span class="ueber">Eckdaten</span><br />
	      <br /></td>
		  <td valign="top">&nbsp;</td>
		  <td valign="top"><strong class="ueber">Termine</strong></td>
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
      	Maximal 12 Personen<br />
      	<br />
      	<b>Legende:<br />
      	</b> <font color="#1975FF">Ausreichend freie Plätze</font><br />
        <font color="#CC3300">Wenige freie Plätze</font><br />
        <font color="#990000">Kurs ausgebucht</font><span class="ueber"><br />
	    </span></td>

			<td width="51%" valign="top"><strong><?php echo $year; ?></strong><br />
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
				Für Gruppen ab 4 Personen bieten wir Kletterkurse zu extra Terminen an. Für eine Terminvereinbarung bitte Kontakt zu uns aufnehmen. 
		  </td>			
		</tr>
			
		<tr>
			<td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
      <td valign="top">&nbsp;</td>
    </tr>
	 	<tr>
			<td valign="top">&nbsp;</td>
			<td valign="top">&nbsp;</td>
			<td>
				<br /></td>
		</tr>
	</table>	

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

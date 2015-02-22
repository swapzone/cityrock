<?php
header('Content-Type: text/html; charset=utf-8');

$show_form;
$show_feedback;

if(isset($_GET['done'])) {
	// the contact form was sent
	$show_form ="display: none;";
	$show_feedback ="";
}
else {
	//the contact form was not sent
	$show_form ="";
	$show_feedback ="display: none;";
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="description" content="Willkommen im [cityrock] - Der Kletterhalle im Zentrum von Stuttgart" />
<meta name="keywords" content="Kletterhalle, Klettern, Kletterzentrum, Stuttgart, cityrock" />
<title>Gruppen [cityrock] - Die Kletterhalle im Zentrum von Stuttgart</title>
<link href="alles.css" rel="stylesheet" type="text/css" />
<style>
#form {
	margin: 20px 0 20px 0;
	width: 480px;
}

#buttons {
	/*margin-left: 118px;*/
	margin-top: 10px;
}


.left {
   float: left;
   text-align: right;
   width: 110px;
   margin-right: 10px;
   margin-top: 5px;
}

.right {
   margin-left: 120px;
   display: block;
   margin-top: 5px;
}

#feedback {
	margin: 20px 0 20px 0;
}
	
</style>
<script type="text/javascript">
	function validateForm() {
		if(document.forms["anmeldung"]["conditions"].checked == false ) {
			alert(unescape("Sie m%FCssen die Teilnahmebedingungen akzeptieren, um sich anmelden zu können."));
	  		return false;
		}

		if(document.forms["anmeldung"]["name"].value == "" || document.forms["anmeldung"]["firstname"].value == "" || 
			document.forms["anmeldung"]["street"].value == "" || document.forms["anmeldung"]["postal"].value == "" || 
			document.forms["anmeldung"]["city"].value == "" || document.forms["anmeldung"]["birthday"].value == "" || 
			document.forms["anmeldung"]["email"].value == "" || document.forms["anmeldung"]["id"].value == "" ) {
				alert(unescape("Bitte alle Felder ausf%FCllen%21"));
		  		return false;
			}
		
		var x=document.forms["anmeldung"]["email"].value;

		var atpos=x.indexOf("@");
		var dotpos=x.lastIndexOf(".");
		
		if (atpos<1 || dotpos<atpos+2 || dotpos+2>=x.length) {
			alert(unescape("Keine g%FCltige E-Mail Adresse%21"));
		  	return false;
		}
	}
	
</script>
</head>

<body>
<div id="header">
  <?
include ("header.php");
 ?></div>
<div id="seitenrahmen">
  <?
include ("menu.php");
 ?>
</div>
<div id="content">
 <span class="ueber">Anmeldung zu den Kletterkursen</span><br />
 	
  <!--
  <br />
  Aus administrativen Gründen müssen die Kletterkurse als Freizeiten beim Freizeitenreferat angemeldet werden.<br />
  <br />
  <br />
  <b>Online-Anmeldung </b><br />
  Hier gelangen Sie zur <a href="https://freizeiten.ejus-online.de/index.php?id=13">Online-Anmeldung</a> für die Kletterkurse.<br />
  <br />
  -->
  <br />
  <b>Online-Anmeldung</b><br />
	<div id="form" style="<?php echo $show_form; ?>">
	<form action="./feedback.php" name="anmeldung" target="_self" method="post" onsubmit="return validateForm();" accept-charset="UTF-8">
		<fieldset>
			<legend>Anmeldeformular</legend>
			<label for="id" class="left">Kursnummer:</label>
			<input type="text" id="id" name="id" size="45" class="right" disabled="true" value="<?php echo $_GET['id']; ?>" />
			<label for="lastname" class="left">Nachname:</label>
			<input type="text" id="lastname" name="lastname" size="45" class="right" />
			<label for="firstname" class="left">Vorname:</label>
			<input type="text" id="firstname" name="firstname" size="45" class="right" />
			<label for="street" class="left">Straße:</label>
			<input type="text" id="street" name="street" size="45" class="right" />
			<label for="postal" class="left">PLZ:</label>
			<input type="text" id="postal" name="postal" size="45" class="right" />
			<label for="city" class="left">Ort:</label>
			<input type="text" id="city" name="city" size="45" class="right" />
			<label for="birthday" class="left">Geburtsdatum:</label>
			<input type="text" id="birthday" name="birthday" size="45" class="right" />
			<label for="email" class="left">Email:</label>
			<input type="text" id="email" name="email" size="45" class="right" />
			<label for="phone" class="left">Telefon:</label>
			<input type="text" id="phone" name="phone" size="45" class="right" />
			<br />
			<input type="checkbox" name="conditions" value="conditions">
				<span>Ich habe die <a href="reisebedingungen.php">Teilnahmebedingungen</a> gelesen und akzeptiere diese.</span>
			<input type="hidden" name="course" value="<?php echo $_GET['id']; ?>" />
			
			<div id="buttons">
				<script type="text/javascript">
					document.write("<input type='submit' value='abschicken' name='send' >");
				</script>
				<input type="reset" value="zur&uuml;cksetzen" name="reset" >
			</div>
		</fieldset>
	</form>
	<script type="text/javascript">
		
	</script>
	</div>
	
	<div id="alternative" style="<?php echo $show_form; ?>">
		<b><br />
		<br />
	  Schriftliche Anmeldung</b><br />
	  	<p>
		Lade dir alternativ zur Online-Anmeldung das Anmeldeformular als <a href="anhang/anmeldeformular.pdf">PDF-Datei (70 KB)</a> oder als <a href="anhang/anmeldeformular.doc">Word-Dokument (280 KB)</a> herunter. Aus rechtlichen Gr&uuml;nden sind dem Anmeldeformular die Reisebedingungen und allgemeine Hinweise beigef&uuml;gt.<br />
		Bitte drucke und fülle die Formulare aus. Beachte dabei die jeweiligen Veranstaltungsnummern:<br />
		<br />
		Kletterschein Toprope: <b>13070</b><br />
		Aufbaukurs Vorstieg: <b>13080</b>  <br />
		<br />
		Schicke die ausgefüllten Formulare per Post oder Fax an:<br />
		<br />
		Evangelische Jugend Stuttgart - Haus 44<br />
		Kletteranlage [cityrock]®<br />
		Fritz - Elsas - Strasse 44<br />
		70174 Stuttgart<br />
	    <br />
	    <br />
	    <b>Noch Fragen?</b> Wir helfen gerne weiter!<br />
				    <br />
	    info@cityrock.de <br />
	    Tel: 0711 / 18771 - 31<br />
			Fax: 0711 / 18771 - 99<br />
	    <br />
	  </p>
    </div>
    
<div id="feedback" style="<?php echo $show_feedback; ?>">
		Vielen Dank für Deine Anmeldung. In Kürze wirst du eine Email erhalten, in der Du die Teilnahme nochmals bestätigen musst.	
	</div>
</div>
<table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td>&nbsp;</td>
  </tr>
</table>
</body>
</html>

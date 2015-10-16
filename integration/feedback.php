<?php
	include('_func.php');

	// include config_lite library
	require_once('config/Lite.php');
	$config = new Config_Lite('verwaltung/basic.cfg');

	header('Content-Type: text/html; charset=utf-8');
	
	// form content
	$courseId = $_POST['course'];
 	$email = $_POST['email'];
 	$lastname = $_POST['lastname'];
	$firstname = $_POST['firstname'];
 	$street = $_POST['street'];
	$postal = $_POST['postal'];
	$city = $_POST['city'];
	$birthday = $_POST['birthday'];
	$phone = $_POST['phone'];

	if($courseId == "" || $email == "" || $lastname == "" || $firstname == "" || $street == "" || $postal == "" || $city == "" || $birthday == "" || $phone == "") {
		echo "Leider sind die Eingaben aus dem Formular nicht vollständig. Bitte probiere es nochmal.";
	}
	else {
		// add registrant to list and retrieve confirmation code
		$confirmationCode = addRegistrant($courseId, $firstname, $lastname, $street, $postal, $city, $birthday, $email, $phone);

		if(!$confirmationCode)
			echo "Leider ist etwas schief gegangen. Bitte probiere es später nochmal.";

		$confirmationCode = urlencode($confirmationCode);

		// create link with confirmation code
		$to = "$email";

	 	$subject = $config['email']['subject-confirm'];
		$subject = $subject;
		
		$header  = "MIME-Version: 1.0\n"; 
    $header .= "Content-Type: text/plain; charset=utf-8\n"; 
    $header .= 'From: Cityrock <info@cityrock.de>' . "\n";
    $header .= "Reply-To: Cityrock <info@cityrock.de>\n"; 
		
		// prepare email body and encode with utf-8
		$confirmationLink = "http://www.cityrock.de/confirmation.php?id=$confirmationCode";

		$body = $config['email']['body-confirm'];
		$body = str_replace('[%confirm]', $confirmationLink, $body);
		$body = $body;
		
		// send email
		$sent = mail($to, $subject, $body, $header) ;
	
		if($sent) {
			echo "<script language=javascript>window.location = 'http://www.cityrock.de/anmeldung.php?done';</script>";
		}
		else {
			echo "<script language=javascript>window.location = 'http://www.cityrock.de/anmeldung.php?error';</script>";
		}
	}
?>

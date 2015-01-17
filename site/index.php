<?php

include_once('_init.php');

// preprocess url																		  
$url = $_SERVER["REQUEST_URI"];

if(strlen($root_directory)) {
	$regex = "/\\$root_directory/";
	$url = preg_replace($regex, "", $url, 1);
}

if(strpos($url, "/api") === 0) {
	/***********************************************************************/
	/* REST Api called, instantiate Slim framework											   */
	/***********************************************************************/
	include_once('_api.php');
}
else {
	/***********************************************************************/
	/* REST Api NOT called, present the website													   */
	/***********************************************************************/
	$title = "Herzlich Willkommen!";
	$content = "Du bist auf der Verwaltungsplattform des [cityrock] Stuttgart gelandet.";

	include('_main.php');
}
?>


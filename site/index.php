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
	require_once 'lib/Slim/Slim.php';
	require_once('_api.php');

	\Slim\Slim::registerAutoloader();

	$app = new \Slim\Slim(array(
		'debug' => true
	));

	// configure response
	$response = $app->response();
	$response->header('content-type', 'application/json');

	// API group
	$app->group('/api', function () use ($app) {

		$app->get('/course/:type/list', function ($type) {
			echo getCoursesJSON($type);
		});

		$app->get('/course/:type/:id', function ($type, $id) {
			echo "Course of type $type with ID: $id";
		});

		$app->get('/course/:id', function ($id) {
			echo getCourseJSON($id);
		});

		$app->get('/config', function () {	
			echo 'Configuration object';
		});

		$app->get('/registrants/list', function() {
			// set response header to pdf
			echo 'Registrants list';
		});

		$app->post('/registrant', function() {
			// add registrant to database and return confirmation key
		});
	});

	$app->run();
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


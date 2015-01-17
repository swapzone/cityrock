<?php 
require_once '_func.php';
require_once 'lib/Slim/Slim.php';

\Slim\Slim::registerAutoloader();

$app = new \Slim\Slim(array(
	'debug' => true
));

// configure response
$response = $app->response();
$response->header('content-type', 'application/json');

// API group
$app->group('/api', function () use ($app) {

	/* Get course details */
	$app->get('/course/:id', function ($id) {
		echo apiGetCourseData($id);
	});

	$app->get('/course/:id/registrants', function($courseId) {
			$app = \Slim\Slim::getInstance();
			$app->response->header('content-type', 'application/pdf');
			echo apiGetRegistrantsList($courseId);
	});

	/* Get course overviews */
	$app->get('/courses/:type', function ($type) {
		echo apiGetCourses($type);
	});

	/* Get config data */
	$app->get('/config', function () {	
		echo apiGetConfig();
	});

	/* Registrants api */
	$app->get('/registrant/confirmation/:id', function($id) {
		echo apiConfirmRegistrant($id);
	});

	$app->post('/registrant', function() {
		apiAddRegistrant();
	});
});

$app->run();

/***********************************************************************/
/* API functions																										   */
/***********************************************************************/

function apiGetCourses($type) {

	return '[{
			"id" 				: 1,
			"date" 			: "31.01.2015/01.02.1015",
			"deadline"	: "29.01.2015",
			"available" : 4
		},
		{
			"id" 				: 3,
			"date" 			: "17.03.2015/18.03.1015",
			"deadline"	: "15.03.2015",
			"available" : 9
		}
	]';
}

function apiGetCourseData($id) {

	return '{
		"date" 			: "17.03.2015/18.03.1015",
		"deadline"	: "15.03.2015",
		"available" : 4
	}';
}

function apiGetConfig() {

	return '{
		"email" : {
			"confirm" : {
					"subject" : "Deine Anmeldung",
					"message" : "Test Inhalt"
			},
			"reminder" : {
					"subject" : "Nicht vergessen",
					"message" : "In zwei Tagen geht\'s los"
			},
			"notification" : 2
		},
		"system" : {
			"deadline" : 1,
			"administration" : 4,
			"administration-list" : "info@cityrock.de // vincent@whatever.de"
		}
	}';
}

function apiAddRegistrant($data) {
	// add registrant to database
	// TODO return status message
}

function apiConfirmRegistrant($id) {

	return '{ 
		"status" 	: "success",
		"message" : "Bla Bla"
  }';
}

function apiGetRegistrantsList($courseId) {

	return null;
}

?>

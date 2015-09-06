<?php

require_once('_func.php');

// add session for user authentication
session_start();

// configuration parameters
$root_directory = "/cityrock";

// variables used throughout the templates
$profile = null;
$title = null;
$content = null;

$navigation = renderNavigation();

$content_class = null;
$hide_navigation = false;

?>

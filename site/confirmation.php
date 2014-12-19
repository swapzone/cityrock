<?php

include_once('_init.php');

if(isset($_POST['confirmation'])) {

	
	include('_main.php');
}
else {
	include('error.php');
}
?>

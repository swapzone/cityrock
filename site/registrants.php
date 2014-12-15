<?php

include_once('_init.php');

$navigation = "
	<ul>
		<li><a href='#'>Test</a></li>		
		<li><a href='#'>Test 2</a></li>	
	</ul>";


$content = "Registrants for course id=" . $_GET['id'] . ".";

include('_main.php');
?>

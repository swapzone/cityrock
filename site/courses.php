<?php

include_once('_init.php');


$navigation = "
	<ul>
		<li><a href='#'>Test</a></li>		
		<li><a href='#'>Test 2</a></li>	
	</ul>";

if(isset($_GET["id"]))
	 $content = "Show course with id=" . $_GET["id"] . ".";
else
		$content = "Show course overview. <a href='/cityrock/courses/'>link</a>";

include('_main.php');
?>

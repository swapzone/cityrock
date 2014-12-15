<?php
// add session for user authentication
session_start();

if(isset($_GET['logout']))
	unset($_SESSION['authenticated']);

// check if user is authenticated
if(!$_SESSION['authenticated']) {

	// check if user sent the login form
	if(isset($_POST['username']) && isset($_POST['password'])) {
		if(login($_POST['username'], $_POST['password'])) {
			$_SESSION['authenticated'] = true;
			$profile = "<a href='./index.php?logout'>Logout</a>";
		}
	}
	else {
		$profile = null;
		$title = null;
		$navigation = null;
		$content = null;

		include_once('login.php');
	}
}
else
	$profile = "<a href='./index.php?logout'>Logout</a>";

if(!$content_class)
	$content_class = "basic";
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1" />
	<meta name="description" content="[cityrock] Verwaltungsplattform">
	<meta name="keywords" content="Kletterhalle, Klettern, Kletterzentrum, Stuttgart, cityrock">
	<title>[cityrock] Verwaltungsplattform</title>
	<link href="<?php echo $root_directory; ?>/styles/style.min.css" rel="stylesheet" type="text/css">
	<script></script>
</head>
<body>
	<!-- header -->
	<header class="header">
		<div class="container">
			<nav class="header-navigation <?php if($hide_navigation) echo 'header-navigation-hide'; ?>">
 				<a href="#" class="navigation-menu-toggle"><i class="fa fa-bars"></i>Menü</a>
			</nav>
			<div class="header-info">
				<a href="http://www.cityrock.com" target="_blank">
					<img src="<?php echo $root_directory; ?>/images/logo.jpg" alt="[cityrock] Logo" class="header-logo">
				</a>
				<h1>Verwaltungsplattform</h1>
			</div>
			<?php if(count($profile)): ?>
				<!-- user profile -->
				<div class="header-profile">
					<?php echo $profile; ?>
				</div>
			<?php endif; ?>			
		</div>
	</header>
	
	<div class="main">
		<div class="<?php echo $content_class; ?>">
			<?php if(count($navigation)): ?>
			<!-- navigation -->
			<nav class="navigation" id="navigation">
				<?php echo $navigation; ?>
			</nav>
			<?php endif; ?>
		
			<!-- content -->
			<div class="content">
				<?php 
					echo "<h1>" . $title . "</h1>";
					echo "<p>" . $content . "</p>"; 
				?>
			</div>
		</div>
	</div>

	<!-- footer -->
	<footer class="footer">
		<div class="reference">
			Powered by <a href="http://www.clowdfish.com" target="_blank">clowdfish.com</a>
			<img src="<?php echo $root_directory; ?>/images/clowdfish.png" alt="clowdfish Logo" />
		</div>
	</footer>
  <script type="text/javascript" src="<?php echo $root_directory; ?>/scripts/script.min.js"></script>
</body>
</html>

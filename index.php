<?php

/*
 * Prontotype
 * A lightweight server-side framework to help you quickly build interactive, data-driven HTML prototypes. 
 */
 
define('PRONTOTYPE_VERSION', '1.0-beta2');
$requiredVersion = '5.3.1';

try {
	if ( strnatcmp(phpversion(), $requiredVersion) < 0 ) {
		throw new Exception("Sorry, you need to be running PHP <strong>{$requiredVersion}</strong> or greater to run Prontotype.");
	}
	$app = require __DIR__ . '/app/bootstrap.php';
	$app->run();
} catch ( \Exception $e ) {
	$error = $e->getMessage();
	echo <<<EOD
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	
	<link rel="stylesheet" href="/app/assets/css/bootstrap.min.css" type="text/css" media="all">
	<link rel="stylesheet" href="/app/assets/css/system.css" type="text/css" media="all">

	<title>An error has occurred.</title>
</head>
<body>
	
	<div id="pt-content">
		<div class="alert">
			<h4 class="alert-heading">An error has occurred.</h4>
			{$error}
		</div>	
	</div>
	
</body>	
</html>
EOD;
}
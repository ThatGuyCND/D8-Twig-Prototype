<?php

/*
 * Prontotype
 * A lightweight server-side framework to help you quickly build interactive, data-driven HTML prototypes. 
 */
 
$app = require __DIR__ . '/app/bootstrap.php';

try {
	$app->run();
} catch ( \Exception $e ) {
	$error = $e->getMessage();
	echo <<<EOD

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	
	<link rel="stylesheet" href="/app/assets/css/bootstrap.min.css" type="text/css" media="screen">
	<style type="text/css" media="screen">
		body {
			background: #EFEFEF;
		}
		
		#pt-content {
			padding: 15px 20px 1px 20px;
			margin: 30px auto;
			max-width: 500px;
			background: #FFF;
			border-radius: 8px;
		}
	</style>

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
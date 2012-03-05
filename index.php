<?php

/*
 * Prontotype
 * A lightweight server-side framework to help you quickly build interactive, data-driven HTML prototypes. 
 */
 
$app = require __DIR__ . '/app/bootstrap.php';

try {
	$app->run();
} catch ( \Exception $e ) {
	echo('<p>' . $e->getMessage() . '</p>');
}
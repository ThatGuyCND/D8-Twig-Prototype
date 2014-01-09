<?php

try {
    
    require __DIR__ . '/vendor/autoload.php';
    
    $app = new Prontotype\Application(array(
        'root'       => __DIR__,
        'cache'      => __DIR__ . '/cache',
        'vendor'     => __DIR__ . '/vendor',
        'config'     => __DIR__ . '/config'
    ));
    
    $app->run();
    
} catch ( \Exception $e ) {
    echo '<h1>An error has occurred</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
}

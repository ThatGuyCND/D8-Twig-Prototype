<?php

try {
    
    require __DIR__ . '/vendor/autoload.php';
    
    $app = new Prontotype\Application(array(
        'root'       => __DIR__,
        'cache'      => __DIR__ . '/cache',
        'prototypes' => __DIR__ . '/prototypes',
        'vendor'     => __DIR__ . '/vendor',
    ));
    
    $app->doHealthCheck();
    $app->run();
    
} catch ( \Exception $e ) {
    echo '<h1>An error has occurred</h1>';
    echo '<p>' . $e->getMessage() . '</p>';
}

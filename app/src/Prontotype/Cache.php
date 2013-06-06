<?php

namespace Prontotype;

Class Cache {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }

}


// if ( $app['config']['cache_path'] ) {
//     $cache_path = DOC_ROOT . '/' . trim($app['config']['cache_path'],'/');
//     if ( is_writable($cache_path) ) {
//         define('CACHE_PATH', $cache_path );
//     } else {
//         throw new \Exception('The specified cache directory <strong>' . $cache_path . '</strong> could not be written to. Please check the directory permissions and refresh.');
//     }
// } else {
//     define('CACHE_PATH', null );
// }
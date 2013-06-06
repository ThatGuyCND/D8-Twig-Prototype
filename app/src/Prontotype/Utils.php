<?php

namespace Prontotype;

Class Utils {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function generateUrlPath($route)
    {
        $url = $this->app['url_generator']->generate($route);
        if ( ! $this->app['config']['clean_urls'] && strpos( $url, 'index.php' ) === false ) {
            $url = '/index.php' . $url;
        }
        return $url;
    }
    
}

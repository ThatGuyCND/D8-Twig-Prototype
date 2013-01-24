<?php

namespace Prontotype\Twig;

class HelperExtension extends \Twig_Extension
{
    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getName()
    {
        return 'helper';
    }
    
    public function getGlobals()
    {
        return array(
            'now' => time(),
            'current_url' => strtok($_SERVER['REQUEST_URI'], '?'),
            'uri' => $this->app['uri'],
            'data' => $this->app['data'],
            'session' => $this->app['session'],
            'cache' => $this->app['cache'],
            'pages' => $this->app['pages'],
            'scrap' => $this->app['scrap'],
            'store' => $this->app['store'],
            'config' => $this->app['config'],
            'utils' => $this->app['utils'],
            'request' => $this->app['pt_request'],
        );
    }
}

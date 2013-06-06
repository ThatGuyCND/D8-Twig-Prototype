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
            'request' => $this->app['pt.request'],
            'config'  => $this->app['config'],
            'session' => $this->app['session'],
            'pages'   => $this->app['pt.pagetree'],
            'page'    => $this->app['pt.pagetree']->getCurrent(),
            // 'data'        => $this->app['data'],
            // 'scrap'       => $this->app['scrap'],
            // 'store'       => $this->app['store'],
            // 'faker'       => $this->app['faker'],
            // 
        );
    }
}

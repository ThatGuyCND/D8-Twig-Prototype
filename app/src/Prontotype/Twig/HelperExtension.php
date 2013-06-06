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
            'pt' => array(
                'config'  => $this->app['config'],
                'session' => $this->app['session'],            
                'request' => $this->app['pt.request'],
                'pages'   => $this->app['pt.pagetree'],
                'page'    => $this->app['pt.pagetree']->getCurrent(),
                'user'    => $this->app['pt.store']->get('user'),
            )

            // 'data'        => $this->app['data'],
            // 'scrap'       => $this->app['scrap'],
            // 'store'       => $this->app['store'],
            // 'faker'       => $this->app['faker'],
            // 
        );
    }
}

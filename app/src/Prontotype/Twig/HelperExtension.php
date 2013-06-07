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
                'config'  => $this->app['pt.config'],      
                'request' => $this->app['pt.request'],
                'pages'   => $this->app['pt.pagetree'],
                'page'    => $this->app['pt.pagetree']->getCurrent(),
                'user'    => $this->app['pt.user_manager']->getCurrentUser(),
                'data'    => $this->app['pt.data'],
                'store'   => $this->app['pt.store'],
                'notifications'   => $this->app['pt.notifications'],
                'scraper' => $this->app['pt.scraper'],
                'urls'    => array(
                    'user' => array(
                        'login' => $this->app['pt.utils']->generateUrlPath('user.login'),
                        'logout' => $this->app['pt.utils']->generateUrlPath('user.logout')
                    ),
                    'auth' => array(
                        'form' => $this->app['pt.utils']->generateUrlPath('auth.login'),
                        'login' => $this->app['pt.utils']->generateUrlPath('auth.check'),
                        'logout' => $this->app['pt.utils']->generateUrlPath('auth.logout')
                    )
                )
            )
        );
    }
}

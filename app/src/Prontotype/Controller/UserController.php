<?php

namespace Prontotype\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        
        $controllers->match('/login', function (Request $request) use ($app) {
                    
           
                    
        })
        ->method('GET|POST')
        ->bind('user.login');
        
                
        $controllers->get('/logout', function (Request $request) use ($app) {
            
    
                
        })->bind('user.logout');
        
    
        return $controllers;
    }
}

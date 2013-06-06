<?php

namespace Prontotype\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthController implements ControllerProviderInterface {
    
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        
        $controllers->get('/login', function() use ($app) {
            
            if ( $app['pt.auth']->check() ) {
                return $app->redirect('/');
            }
            
            return $app['twig']->render('PT/pages/authenticate.twig', array(
                'auth_path' => $app['pt.request']->generateUrlPath('authenticate')
            ));
            
        })->bind('authenticate');
        
        
        $controllers->post('/login', function() use ($app) {
            
            if ( $app['pt.auth']->attemptLogin($app['request']->get('username'), $app['request']->get('password')) ) {
                return $app->redirect('/');
            } else {
                return $app->redirect($app['pt.request']->generateUrlPath('authenticate'));
            }
            
        })->bind('do_authenticate');
        
        
        $controllers->get('/logout', function($result) use ($app) {
            
            $app['pt.auth']->logout();
            return $app->redirect($app['pt.request']->generateUrlPath('authenticate'));

        })
        ->value('result', null)
        ->bind('de_authenticate');
        
        
        return $controllers;
    }
}


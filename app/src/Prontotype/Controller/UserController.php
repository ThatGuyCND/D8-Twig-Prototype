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
        
        
        $controllers->post('/login', function (Request $request) use ($app) {
            
            $identifyBy = $app['pt.config']['user']['identify'];
            $authBy = $app['pt.config']['user']['auth'];
            
            if ( $app['pt.user_manager']->attemptLogin($app['request']->get($identifyBy), $app['request']->get($authBy)) ) {
                return $app->redirect($app['pt.user_manager']->getLoginRedirectUrlPath($request->get('redirect')));
            } else {
                return $app->redirect($request->headers->get('referer'));
            }
            
        })->bind('user.login');
        
                
        $controllers->get('/logout', function (Request $request) use ($app) {
            
            $app['pt.user_manager']->logoutUser();
            return $app->redirect($app['pt.user_manager']->getLogoutRedirectUrlPath($request->get('redirect')));
            
        })->bind('user.logout');
        
    
        return $controllers;
    }
}

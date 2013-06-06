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
        
        $controllers->get('login', function () use ( $app ) {
            
            if ( $app['session']->has($app['config']['prefix'] . 'authed-user') ) {
                return $app->redirect('/');
            }
            
            return $app['twig']->render('PT/pages/authenticate.twig', array(
                'auth_path' => $app['pt.request']->generateUrlPath('authenticate')
            ));
            
        })->bind('authenticate');
        
        
        $controllers->post('login', function () use ( $app ) {

            if ( $app['request']->get('username') === $app['config']['authenticate']['username'] && $app['request']->get('password') === $app['config']['authenticate']['password'] ) {
                $userHash = $userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
                $currentUser = $app['session']->set( $app['config']['prefix'] . 'authed-user', $userHash );
                return $app->redirect('/');
            } else {
                $app['session']->getFlashBag()->set('error', 'error');
                $app['session']->remove( $app['config']['prefix'] . 'authed-user' );
                return $app->redirect($app['pt.request']->generateUrlPath('authenticate'));
            }

        })->bind('do_authenticate');
        
        
        $controllers->get('logout', function ( $result ) use ( $app ) {
            
            $app['session']->remove( $app['config']['prefix'] . 'authed-user' );
            return $app->redirect($app['pt.request']->generateUrlPath('authenticate'));

        })
        ->value('result', null)
        ->bind('de_authenticate');
        
        
        return $controllers;
    }
}


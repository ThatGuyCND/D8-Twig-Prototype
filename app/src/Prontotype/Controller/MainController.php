<?php

namespace Prontotype\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MainController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $triggers = $app['config']['triggers'];
        
        $controllers->match('/' . $triggers['login'], function ( Request $request ) use ( $app, $triggers ) {
                    
            $user_id = $request->get('user');
            if ( ! $user_id ) {
                $user = true;
            } else {
                $user = $app['data']->find('users.' . $user_id);
                $user = $user ? $user : true;
            }
                    
            $app['store']->set('user', $user);
                    
            if ( $request->get('redirect') ) {
                return $app->redirect($request->get('redirect'));
            } else {
                return $app->redirect('/'); // redirect to homepage
            }
                    
        })
        ->method('GET|POST')
        ->bind('do_login');
                
                
        $controllers->get('/' . $triggers['logout'], function ( Request $request ) use ( $app, $triggers ) {
                    
            $app['store']->clear('user');
                    
            if ( $request->get('redirect') ) {
                return $app->redirect($request->get('redirect'));
            } else {
                return $app->redirect('/'); // redirect to homepage
            }
                
        })->bind('do_logout');
        
        
        // everything else...
        $controllers->match('/{route}', function ( $route ) use ( $app ) {
            
            if ( ! $page = $app['pt.pagetree']->getByRoute($route) ) {
                $app->abort(404);
            }
                            
            try {
                return $app['twig']->render($page->getTemplatePath(), array());
            } catch ( \Exception $e ) {
                return $app['twig']->render('PT/pages/error.twig', array(
                    'message'=>$e->getMessage()
                ));
            }
        })
        ->method('GET|POST')
        ->assert('route', '.+')
        ->value('route', '');
    
        return $controllers;
    }
}

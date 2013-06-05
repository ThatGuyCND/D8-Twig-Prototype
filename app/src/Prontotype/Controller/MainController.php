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
                
                
        $controllers->get('/' . $triggers['data'] . '/{data_path}', function ( $data_path ) use ( $app ) {
            
            $result = $app['data']->find(str_replace('/','.',$data_path));
                    
            if ( ! $result ) {
                $app->abort(404);
            } else {
                return $app->json($result);
            }
                
        })
        ->assert('data_path', '.+')
        ->value('data_path', '')
        ->bind('get_json_data');
        
        
        $controllers->get('/' . $triggers['export'], function () use ( $app ) {
            
            
        
        })->bind('export');
                
                
        $controllers->get('/' . $triggers['shorturl']  . '/{page_id}', function ( $page_id ) use ( $app ) {
                    
            if ( ! $page = $app['pagetree']->getById($page_id) ) {
                $app->abort(404);
            }
                    
            return $app->redirect($page->getUrlPath(), 301);
                    
        })->bind('short_url');
                
        
        // everything else...
        $controllers->match('/{route}', function ( $route ) use ( $app ) {
            
            if ( ! $page = $app['pagetree']->getByRoute($route) ) {
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

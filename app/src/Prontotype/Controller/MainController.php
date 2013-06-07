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
        
        $controllers->match('/{route}', function ( $route ) use ( $app ) {
            
            if ( ! $page = $app['pt.pagetree']->getByRoute($route) ) {
                $app->abort(404);
            }
                            
            try {
                return $app['twig']->render($page->getTemplatePath(), array());
            } catch ( \Exception $e ) {
                return $app['twig']->render('pt/pages/error.twig', array(
                    'message'=>$e->getMessage()
                ));
            }
        })
        ->method('GET|POST')
        ->assert('route', '.+')
        ->value('route', '')
        ->bind('catchall');
    
        return $controllers;
    }
}

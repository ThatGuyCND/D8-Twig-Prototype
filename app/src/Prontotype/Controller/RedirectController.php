<?php

namespace Prontotype\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        
        $controllers->get('/{page_id}', function ( $page_id ) use ( $app ) {
                    
            if ( ! $page = $app['pt.pagetree']->getById($page_id) ) {
                $app->abort(404);
            }
            return $app->redirect($page->getUrlPath(), 301);
            
        })->bind('short_url');
    
        return $controllers;
    }
}

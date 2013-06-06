<?php

namespace Prontotype\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        $triggers = $app['config']['triggers'];
         
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
        
        return $controllers;
    }
}

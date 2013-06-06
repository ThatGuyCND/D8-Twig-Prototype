<?php

namespace Prontotype\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AssetController implements ControllerProviderInterface {
    
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];
        
        
        return $controllers;
    }
}


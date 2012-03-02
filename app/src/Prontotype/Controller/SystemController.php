<?php

namespace Prontotype\Controller;

use Silex\Application;
use Silex\ControllerProviderInterface;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class SystemController implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
		$controllers = new ControllerCollection();
		
		$controllers->get('authenticate', function () use ( $app ) {

			return new Response( $app['twig']->render('PT/pages/authenticate.html'), 401 );

		})->bind('authenticate');
		
		$controllers->post('authenticate', function () use ( $app ) {

			return new Response( $app['twig']->render('PT/pages/authenticate.html'), 401 );

		})->bind('authenticate');
		
        return $controllers;
    }
}


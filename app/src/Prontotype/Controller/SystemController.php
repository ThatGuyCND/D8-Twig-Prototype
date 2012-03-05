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
		
		
		$controllers->get('auth/{result}', function ( $result ) use ( $app ) {
			
			return $app['twig']->render('PT/pages/authenticate.html', array(
				'auth_path' => $app['url_generator']->generate('authenticate'),
				'result' => $result
			));

		})
		->value('result', null)
		->bind('authenticate');
		
		
		$controllers->post('auth', function () use ( $app ) {

			if ( $app['request']->get('username') === $app['config']['authenticate']['username'] && $app['request']->get('password') === $app['config']['authenticate']['password'] ) {
				
				$userHash = $userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);

				$currentUser = $app['session']->set( $app['config']['prefix'] . 'authed-user', $userHash );
				
				return $app->redirect('/');
			} else {
				return $app->redirect($app['url_generator']->generate('authenticate', array('result'=>'error')));
			}

		})->bind('do_authenticate');
		
		
		$controllers->get('deauth', function ( $result ) use ( $app ) {
			
			$app['session']->set( $app['config']['prefix'] . 'authed-user', null );
			return $app->redirect($app['url_generator']->generate('authenticate'));

		})
		->value('result', null)
		->bind('de_authenticate');
		
		
        return $controllers;
    }
}


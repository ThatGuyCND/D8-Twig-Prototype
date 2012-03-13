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
		
		
		$controllers->get('auth', function () use ( $app ) {
			
			if ( $app['session']->has($app['config']['prefix'] . 'authed-user') ) {
				return $app->redirect('/');
			}
			
			return $app['twig']->render('PT/pages/authenticate.html', array(
				'auth_path' => $app['url_generator']->generate('authenticate')
			));
			
		})->bind('authenticate');
		
		
		$controllers->post('auth', function () use ( $app ) {

			if ( $app['request']->get('username') === $app['config']['authenticate']['username'] && $app['request']->get('password') === $app['config']['authenticate']['password'] ) {
				
				$userHash = $userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
				$currentUser = $app['session']->set( $app['config']['prefix'] . 'authed-user', $userHash );
				
				return $app->redirect('/');
			} else {
				$app['session']->setFlash('error', 'error');
				$app['session']->remove( $app['config']['prefix'] . 'authed-user' );
				return $app->redirect($app['url_generator']->generate('authenticate'));
			}

		})->bind('do_authenticate');
		
		
		$controllers->get('deauth', function ( $result ) use ( $app ) {
			
			$app['session']->remove( $app['config']['prefix'] . 'authed-user' );
			return $app->redirect($app['url_generator']->generate('authenticate'));

		})
		->value('result', null)
		->bind('de_authenticate');
		
		
        return $controllers;
    }
}


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
		$controllers = new ControllerCollection();
		$triggers = $app['config']['triggers'];
		
		$controllers->post('/' . $triggers['login'], function () use ( $app ) {
			
			// TODO: implement
			echo 'login';
			
		})->bind('do_login');
		
		
		$controllers->get('/' . $triggers['logout'], function () use ( $app ) {
			
			// TODO: logout
			echo 'logout';

		})->bind('do_logout');
		
		
		$controllers->get('/' . $triggers['assets'], function () use ( $app ) {
			
			// TODO: get compiled assets (like LESS files etc)
			echo 'assets';

		})->bind('get_assets');
		
		
		$controllers->get('/' . $triggers['data'], function () use ( $app ) {
			
			// TODO: get JSON representation of data
			echo 'data';

		})->bind('get_json_data');
		
		
		$controllers->get('/' . $triggers['shorturl']  . '/{page_id}', function ( $page_id ) use ( $app ) {
			
			if ( ! $page = $app['pagetree']->getPageById($page_id) ) {
				$app->abort(404);
			}
			
			return $app->redirect($page->nice_url, 301);
			
		})->bind('short_url');
		
		
		// everything else...
		$controllers->get('/{route}', function ( $route ) use ( $app ) {
			
			if ( ! $page = $app['pagetree']->getPage($route) ) {
				$app->abort(404);
			}

			try {
				return $app['twig']->render($page->fs_path, array());
			} catch ( \Exception $e ) {
				// TODO: handle template errors nicely here
				return $app['twig']->render('PT/pages/error.html', array(
					'message'=>$e->getMessage()
				));
			}

		})
		->assert('route', '.+')
		->value('route', '');
		
        return $controllers;
    }
}


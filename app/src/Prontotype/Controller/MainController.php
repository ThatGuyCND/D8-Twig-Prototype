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
		
		$controllers->match('/' . $triggers['login'], function ( Request $request ) use ( $app, $triggers ) {
			
			$user_id = $request->get('user');
			if ( ! $user_id ) $app->abort(404);
			
			$user = $app['data']->find('users.' . $user_id);
			$user = $user ? $user : array( 'id' => $user_id );
			
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
		
		
		$controllers->get('/' . $triggers['assets'] . '/{asset_path}.{format}', function ( $asset_path, $format ) use ( $app ) {
			
			if ( ! $output = $app['assets']->convert( $format, $asset_path . '.' . $format ) ) {
				$app->abort(404);
			}
			
			return new Response( $output, 200, array(
				'Content-Type' => $app['assets']->contentType($format)
			));
			
		})
		->assert('asset_path', '.+')
		->value('asset_path', '')
		->bind('get_assets');
		
		
		$controllers->get('/' . $triggers['data'] . '/{data_path}', function ( $data_path ) use ( $app ) {
			
			// TODO: get JSON representation of data
			$result = $app['data']->find(str_replace('/','.',$data_path));
			
			if ( ! $result ) {
				$app->abort(404);
			} else {
				return json_encode($result);
			}

		})
		->assert('data_path', '.+')
		->value('data_path', '')
		->bind('get_json_data');
		
		
		$controllers->get('/' . $triggers['shorturl']  . '/{page_id}', function ( $page_id ) use ( $app ) {
			
			if ( ! $page = $app['pagetree']->getPageById($page_id) ) {
				$app->abort(404);
			}
			
			return $app->redirect($page->nice_url, 301);
			
		})->bind('short_url');
		
		
		// everything else...
		$controllers->get('/{route}', function ( $route ) use ( $app ) {
			
			$routes = $app['config']['routes'] ? $app['config']['routes'] : array();
			
			foreach( $routes as $routeSpec => $endRoute ) {
				$routeSpec = trim($routeSpec,'/');
				$routeSpec = str_replace(array('(:any)','(:num)','/'), array('(.*)','(\d*)','\/'), $routeSpec);
				$routeSpec = '/^' . $routeSpec . '$/';
				if ( preg_match( $routeSpec, $route ) ) {
					$route = $endRoute;
					break;
				}
			}
			
			if ( ! $page = $app['pagetree']->getPage($route) ) {
				$app->abort(404);
			}
			
			if ( $user = $app['store']->get('user') ) {
				$app['twig']->addGlobal('user', $user);
			}

			try {
				return $app['twig']->render($page->fs_path, array());
			} catch ( \Exception $e ) {
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


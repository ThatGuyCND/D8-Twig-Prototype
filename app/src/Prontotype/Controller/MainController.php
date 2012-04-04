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
			if ( ! $user_id ) {
				$user = array();
			} else {
				$user = $app['data']->find('users.' . $user_id);
				$user = $user ? $user : array();
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
			
			// lets see if we need to do any checking of custom routes
			$routes = $app['config']['routes'] ? $app['config']['routes'] : array();
			$replacements = array();
			if ( count($routes) ) {
				foreach( $routes as $routeSpec => $endRoute ) {
					// see if there are any page ID placeholders that need parsing out
					if ( preg_match('/\(:id=([^\)]*)\)/', $routeSpec, $matches) ) {
						if ( $routePage = $app['pagetree']->getPageById($matches[1]) ) {
							$replacements[] = trim(str_replace($app['config']['index'], '', $routePage->nice_url),'/');
							$routeSpec = str_replace(
								array($matches[0],$app['config']['index']),
								array($routePage->nice_url,''),
								$routeSpec
							);
						} else {
							continue;
						}
					}
					$routeSpec = trim($routeSpec,'/');
					// replace helper placeholders
					$routeSpec = str_replace(
						array('(:any)','(:num)','(:all)','/'),
						array('([^/]*)','(\d*)','(.*)','\/'),
						$routeSpec
					);
					$routeSpec = '/^' . $routeSpec . '$/';
					if ( preg_match( $routeSpec, $route, $matches ) ) {
						// we have a match!
						for( $i = 0; $i < count($matches); $i++ ) {
							if ( $i !== 0) {
								$replacements[] = $matches[$i];
							}
						}
						$route = $endRoute;
						break;
					}
				}
				
				// replace and reference tokens in the route
				// '(:id=test)/hello': '$1'
				$replacementTokens = array();
				for( $j = 0; $j < count($replacements); $j++ ) {
					$replacementTokens['$' . ($j+1)] = $replacements[$j];
				}
				$route = str_replace(array_keys($replacementTokens), array_values($replacementTokens), $route);
			
				// replace any page ID placeholders in the route itself
				if ( preg_match('/\(:id=(.*)\)/', $route, $matches) ) {
					$routePage = $app['pagetree']->getPageById($matches[1]);
					$route = str_replace(array($matches[0],$app['config']['index']), array($routePage->nice_url,''), $route);
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


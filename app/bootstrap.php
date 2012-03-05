<?php

define('DS', '/');

// define a few paths
define('DOC_ROOT', realpath(__DIR__ . '/../'));
define('APP_PATH', DOC_ROOT . '/app');

define('TEMPLATES_PATH', DOC_ROOT . '/structure');
define('PAGES_PATH', TEMPLATES_PATH . '/pages');

define('APP_TEMPLATES_PATH', APP_PATH . '/views');
define('VENDOR_PATH', APP_PATH . '/vendor');

define('DATA_PATH', DOC_ROOT . '/data');

require_once VENDOR_PATH . '/silex.phar';
require_once VENDOR_PATH . '/Less/lessc.inc.php';

$app = new Silex\Application();

$loader->registerNamespaces(array(
	'Symfony'			=> APP_PATH . '/vendor/symfony/src',	
	'AmuSilexExtension' => APP_PATH . '/vendor/amu-silex-extensions/src',
	'Twig' 				=> APP_PATH . '/vendor/twig/lib',
	'Prontotype'		=> APP_PATH . '/src',
));

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

$app->register(new AmuSilexExtension\SilexConfig\YamlConfig(array(
	APP_PATH . "/config.yml",
	DOC_ROOT . "/config.yml"
)));

$twigopts = array(
	'strict_variables' => false
);

if ( $app['config']['cache_path'] ) {
	define('CACHE_PATH', DOC_ROOT . '/' . trim($app['config']['cache_path'],'/') );
	$twigopts['cache'] = CACHE_PATH;
} else {
	define('CACHE_PATH', null );
}

$app->register(new TwigServiceProvider(), array(
    'twig.path' 		=> array( TEMPLATES_PATH.'/', APP_TEMPLATES_PATH.'/' ),
    'twig.class_path' 	=>  APP_PATH . '/vendor/twig/lib',
	'twig.options' 		=> $twigopts
));

$app->register(new Prontotype\Provider\PagetreeProvider());
$app->register(new Prontotype\Provider\PagesProvider());
$app->register(new Prontotype\Provider\AssetsProvider());
$app->register(new Prontotype\Provider\UriProvider());
$app->register(new Prontotype\Provider\DataProvider());
$app->register(new Prontotype\Provider\CacheProvider());
$app->register(new Prontotype\Provider\StoreProvider());
$app->register(new Prontotype\Provider\UtilsProvider());

$app->before(function () use ($app) {
		
	$authPage = array(
		$app['url_generator']->generate('authenticate'),
		$app['url_generator']->generate('de_authenticate')
	);

	$app['twig']->addGlobal('uri', $app['uri']);
	$app['twig']->addGlobal('data', $app['data']);
	$app['twig']->addGlobal('session', $app['session']);
	$app['twig']->addGlobal('cache', $app['cache']);
	$app['twig']->addGlobal('pages', $app['pages']);
	$app['twig']->addGlobal('store', $app['store']);
	$app['twig']->addGlobal('config', $app['config']);
	$app['twig']->addGlobal('utils', $app['utils']);
	
	if ( ! in_array($app['request']->getRequestUri(), $authPage) )
	{
		if ( ! empty($app['config']['authenticate']) && ! empty($app['config']['authenticate']['username']) && ! empty($app['config']['authenticate']['password']) ) {
		
			$currentUser = $app['session']->get( $app['config']['prefix'] . 'authed-user' );
			$userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
			
			if ( empty( $currentUser ) || $currentUser !== $userHash )
			{
				return $app->redirect($app['url_generator']->generate('authenticate')); // not logged in, redirect to auth page
			}
		}
	}

});

$app->error(function (\Exception $e, $code) use ($app) {
	
	switch( $code )
	{
		case '404':
			$template = 'PT/pages/404.html';
		break;
		default:
			$template = 'PT/pages/error.html';
		break;
	}
	
	return new Response( $app['twig']->render($template, array(
		'message' => $e->getMessage()
	)), $code );
});

$app->mount('/_system/', new Prontotype\Controller\SystemController());
$app->mount('/', new Prontotype\Controller\MainController());

return $app;

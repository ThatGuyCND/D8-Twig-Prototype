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
	$cache_path = DOC_ROOT . '/' . trim($app['config']['cache_path'],'/');
	if ( is_writable($cache_path) ) {
		define('CACHE_PATH', $cache_path );
		$twigopts['cache'] = CACHE_PATH;
	} else {
		exit('The specified cache directory (' . $cache_path . ') could not be written to. Please check the permissions and refresh.');
	}
} else {
	define('CACHE_PATH', null );
}

$app->register(new TwigServiceProvider(), array(
    'twig.path' 		=> array( TEMPLATES_PATH.'/', APP_TEMPLATES_PATH.'/' ),
    'twig.class_path' 	=>  APP_PATH . '/vendor/twig/lib',
	'twig.options' 		=> $twigopts
));
	
// register services

$app['assets'] = $app->share(function( $app ) {
    return new Prontotype\Service\Assets( $app, DOC_ROOT );
});

$app['cache'] = $app->share(function( $app ) {
    return new Prontotype\Service\Cache( $app, CACHE_PATH );
});

$app['data'] = $app->share(function( $app ) {
    return new Prontotype\Service\Data( $app );
});

$app['pages'] = $app->share(function( $app ) {
    return new Prontotype\Service\Pages( $app );
});

$app['pagetree'] = $app->share(function( $app ) {
    return new Prontotype\Service\Pagetree\Parser( DOC_ROOT, PAGES_PATH, $app );
});

$app['store'] = $app->share(function( $app ) {
    return new Prontotype\Service\Store( $app );
});

$app['uri'] = $app->share(function( $app ) {
    return new Prontotype\Service\Uri( $app );
});

$app['utils'] = $app->share(function() {
    return new Prontotype\Service\Utils();
});

// pre/post/error handlers

$app->before(function () use ($app) {
		
	$authPage = array(
		$app['uri']->generate('authenticate'),
		$app['uri']->generate('de_authenticate')
	);

	$app['twig']->addGlobal('uri', $app['uri']);
	$app['twig']->addGlobal('data', $app['data']);
	$app['twig']->addGlobal('session', $app['session']);
	$app['twig']->addGlobal('cache', $app['cache']);
	$app['twig']->addGlobal('pages', $app['pages']);
	$app['twig']->addGlobal('store', $app['store']);
	$app['twig']->addGlobal('config', $app['config']);
	$app['twig']->addGlobal('utils', $app['utils']);
	
	$authRequired = ( ! empty($app['config']['authenticate']) && ! empty($app['config']['authenticate']['username']) && ! empty($app['config']['authenticate']['password']) ) ? true : false;
	
	if ( ! in_array($app['request']->getRequestUri(), $authPage) )
	{
		if ( $authRequired )
		{
			$currentUser = $app['session']->get( $app['config']['prefix'] . 'authed-user' );
			$userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
			
			if ( empty( $currentUser ) || $currentUser !== $userHash )
			{
				return $app->redirect($app['uri']->generate('authenticate')); // not logged in, redirect to auth page
			}
		}
	}
	elseif ( ! $authRequired )
	{
		// redirect visits to the auth pages to the homepage if no auth is required.	
		return $app->redirect('/');
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

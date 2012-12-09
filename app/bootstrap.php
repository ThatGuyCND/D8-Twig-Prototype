<?php

define('DS', '/');

// define a few paths
define('DOC_ROOT', realpath(__DIR__ . '/..'));
define('APP_PATH', DOC_ROOT . '/app');
define('VENDOR_PATH', APP_PATH . '/vendor');

define('APP_TEMPLATES_PATH', APP_PATH . '/views');

require_once VENDOR_PATH . '/Less/lessc.inc.php';

// not using the Silex Phar because of hosting issues with running PHAR archives.
require_once VENDOR_PATH . '/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';
use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
    'Silex'   			=> VENDOR_PATH . '/silex/src',
	'Symfony'			=> VENDOR_PATH . '/symfony/src',
	'AmuSilexExtension' => VENDOR_PATH . '/amu-silex-extensions/src',
	'Twig' 				=> VENDOR_PATH . '/twig/lib',
	'Prontotype'		=> APP_PATH . '/src',
));

$loader->registerPrefixes(array(
    'Pimple' => VENDOR_PATH . '/pimple/lib',
));

$loader->register();

use Silex\Application;
use Silex\Provider\TwigServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Application();

$app->register(new Prontotype\Service\Prototype());

define('BASE_PATH', $app['prototype']['base_path']);
define('TEMPLATES_PATH', BASE_PATH . '/structure');
define('PAGES_PATH', TEMPLATES_PATH . '/pages');
define('DATA_PATH', BASE_PATH . '/data');

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new AmuSilexExtension\SilexConfig\YamlConfig(array(
	APP_PATH . "/config.yml",
	DOC_ROOT . "/config.yml",
	BASE_PATH. "/config.yml"
)));

if ( $app['config']['cache_path'] ) {
	$cache_path = DOC_ROOT . '/' . trim($app['config']['cache_path'],'/');
	if ( is_writable($cache_path) ) {
		define('CACHE_PATH', $cache_path );
	} else {
		throw new \Exception('The specified cache directory <strong>' . $cache_path . '</strong> could not be written to. Please check the directory permissions and refresh.');
	}
} else {
	define('CACHE_PATH', null );
}

$app->register(new TwigServiceProvider(), array(
    'twig.path' 		=> array( TEMPLATES_PATH.'/', APP_TEMPLATES_PATH.'/' ),
    'twig.class_path' 	=>  APP_PATH . '/vendor/twig/lib',
	'twig.options' 		=> array(
		'strict_variables' 	=> false,
		'cache'				=> CACHE_PATH ? CACHE_PATH . '/' . $app['prototype']['domain'] . '/twig' : false,
		'auto_reload'		=> true,
		'debug'		=> $app['config']['debug']
	)
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
	$app['twig']->addGlobal('request', new Prontotype\Service\Request($app));

    $app['twig']->addExtension(new Twig_Extension_Debug());
	
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

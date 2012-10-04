<?php

define('DS', '/');

/* Define a few globally available paths */
define('DOC_ROOT', realpath(__DIR__ . '/../'));
define('APP_PATH', DOC_ROOT . '/app');
define('TEMPLATES_PATH', DOC_ROOT . '/structure');
define('PAGES_PATH', TEMPLATES_PATH . '/pages');
define('APP_TEMPLATES_PATH', APP_PATH . '/views');
define('VENDOR_PATH', APP_PATH . '/vendor');
define('DATA_PATH', DOC_ROOT . '/data');

require_once APP_PATH . '/vendor/autoload.php';

use Symfony\Component\Translation\Loader\YamlFileLoader;

$app = new Silex\Application();

$app->register(new Silextend\Config\YamlConfig(array(
	APP_PATH . "/config.yml",
	DOC_ROOT . "/config.yml"
)));

date_default_timezone_set($app['config']['timezone']);

if ( $app['config']['cache']['path'] ) {
	$cache_path = DOC_ROOT . '/' . trim($app['config']['cache']['path'],'/');
	if ( is_writable($cache_path) ) {
		define('CACHE_PATH', $cache_path );
	} else {
		throw new \Exception('The specified cache directory <strong>' . $cache_path . '</strong> could not be written to. Please check the directory permissions and refresh.');
	}
} else {
	define('CACHE_PATH', null );
}

if ( $app['debug'] ) {
	error_reporting(E_ALL ^ E_WARNING);
} else {
	error_reporting(0);
}

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// set up twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' 		=> array( TEMPLATES_PATH.'/', APP_TEMPLATES_PATH.'/' ),
	'twig.options' 		=> array(
		'strict_variables' 	=> false,
		'cache'				=> CACHE_PATH ? CACHE_PATH . '/twig' : false,
		'auto_reload'		=> true
	)
));

$app->register(new SilexAssetic\AsseticExtension(), array(
	'assetic.path_to_web' => __DIR__ . '/../public/assets/compiled',
	'assetic.options' => array(
		'debug' => $app['config']['debug'],
		'auto_dump_assets' => TRUE,
		'formulae_cache_dir' => ''
	),
    'assetic.filters' => $app->protect(function($fm) {
        $fm->set('less', new Assetic\Filter\LessphpFilter(
            __DIR__ . '/../vendor/leafo/lessphp/lessc.inc.php'
        ));
        $fm->set('scss', new Assetic\Filter\ScssphpFilter(
            __DIR__ . '/../vendor/leafo/scssphp/scss.inc.php'
        ));
    }),
    'assetic.assets' => $app->protect(function($am, $fm) use ($app) {
		foreach ( $app['config']['assetic']['assets'] as $key => $opts ) {
			$app['monolog']->debug('assetic', $app['config']['assetic']['assets']);
			$am->set($key, new Assetic\Asset\AssetCache(
				new Assetic\Asset\GlobAsset(
					__DIR__ . '/../' . trim($opts['file'], '/'),
					array($fm->get($opts['filter']))
				),
				new Assetic\Cache\FilesystemCache(__DIR__ . '/../tmp/cache/assetic')
			));
			$am->get($key)->setTargetPath($opts['target']);
		}
    })
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

$app['pt_request'] = $app->share(function() {
    return new Prontotype\Service\Request();
});

// extend twig a little...
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addGlobal('now',	time());
	$twig->addGlobal('uri', $app['uri']);
	$twig->addGlobal('data', $app['data']);
	$twig->addGlobal('session', $app['session']);
	$twig->addGlobal('cache', $app['cache']);
	$twig->addGlobal('pages', $app['pages']);
	$twig->addGlobal('store', $app['store']);
	$twig->addGlobal('config', $app['config']);
	$twig->addGlobal('utils', $app['utils']);
	$twig->addGlobal('request', $app['pt_request']);
    return $twig;
}));


// $app->before(function () use ($app) {
// 		
// 	$authPage = array(
// 		$app['uri']->generate('authenticate'),
// 		$app['uri']->generate('de_authenticate')
// 	);
// 	
// 	$authRequired = ( ! empty($app['config']['authenticate']) && ! empty($app['config']['authenticate']['username']) && ! empty($app['config']['authenticate']['password']) ) ? true : false;
// 	
// 	if ( ! in_array($app['request']->getRequestUri(), $authPage) ) {
// 		if ( $authRequired ) {
// 			$currentUser = $app['session']->get( $app['config']['prefix'] . 'authed-user' );
// 			$userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
// 			
// 			if ( empty( $currentUser ) || $currentUser !== $userHash ) {
// 				return $app->redirect($app['uri']->generate('authenticate')); // not logged in, redirect to auth page
// 			}
// 		}
// 	} elseif ( ! $authRequired ) {
// 		// redirect visits to the auth pages to the homepage if no auth is required.	
// 		return $app->redirect('/');
// 	}
// 
// });

$app->error(function (\Exception $e, $code) use ($app) {
	
	switch( $code ) {
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

// $app->mount('/_system/', new Prontotype\Controller\SystemController());
$app->mount('/', new Prontotype\Controller\MainController());


return $app;
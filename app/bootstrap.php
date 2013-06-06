<?php

define('VERSION', '3.0');

/* Define globally available application paths */
define('DOC_ROOT', realpath(__DIR__ . '/..'));
define('APP_PATH', DOC_ROOT . '/app');
define('VENDOR_PATH', APP_PATH . '/vendor');
define('PROTOYPES_PATH', DOC_ROOT . '/prototypes');
define('APP_TEMPLATES_PATH', APP_PATH . '/views');

if ( ! file_exists(APP_PATH . '/vendor/autoload.php') ) {
    throw new Exception("You need to install and run <a href=\"http://getcomposer.org\">Composer</a> before Prontoype will work. <a href=\"http://prontotype.allmarkedup.com/#setup\">Read the documentation for more details &rarr;</a>");
}

require_once APP_PATH . '/vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Prontotype\Service\Prototype($app));

date_default_timezone_set($app['config']['timezone']);
$app['debug'] = $app['config']['debug'];

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// set up twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'         => array( $app['pt.prototype.paths.templates'] . '/', APP_TEMPLATES_PATH . '/' ),
    'twig.options'      => array(
        'strict_variables'  => false,
        'cache'             => false, // TODO Enable caching
        'auto_reload'       => true,
        'debug'             => $app['config']['debug'],
        'autoescape'        => false
    )
));
        
$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addExtension(new Twig_Extension_Debug());
    $twig->addExtension(new Prontotype\Twig\HelperExtension($app));
    return $twig;
}));

$app['pt.request'] = $app->share(function() use ($app) {
    return new Prontotype\Request($app);
});

$app['pt.pagetree'] = $app->share(function($app) {
    return new Prontotype\PageTree\Manager($app);
});

$app['pt.store'] = $app->share(function($app) {
    return new Prontotype\Store($app);
});

$app['pt.extensions'] = $app->share(function($app) {
    $ext = new Prontotype\Extension\Manager($app);
    $ext->load($app['config']['extensions']);
    return $ext;
});

$app['pt.auth'] = $app->share(function($app) {
    return new Prontotype\Auth( $app );
});

$app->before(function () use ($app) {
    if ( ! $app['pt.auth']->check() ) {
        return $app->redirect($app['pt.request']->generateUrlPath('authenticate')); // not logged in, redirect to auth page
    }
    $app['pt.extensions']->before();
});

$app->after(function() use ($app) {
    $app['pt.extensions']->after();
});

$app->error(function(\Exception $e, $code) use ($app) {
    
    switch( $code ) {
        case '404':
            $template = 'PT/pages/404.twig';
            break;
        default:
            $template = 'PT/pages/error.twig';
            break;
    }
    
    return new Symfony\Component\HttpFoundation\Response($app['twig']->render($template, array(
        'message' => $e->getMessage()
    )), $code);
});

$app->mount('/' . $app['config']['triggers']['auth'], new Prontotype\Controller\AuthController());
$app->mount('/' . $app['config']['triggers']['shorturl'], new Prontotype\Controller\RedirectController());
$app->mount('/' . $app['config']['triggers']['data'], new Prontotype\Controller\DataController());
$app->mount('/' . $app['config']['triggers']['assets'], new Prontotype\Controller\AssetController());
$app->mount('/', new Prontotype\Controller\MainController());

return $app;




// if ( $app['config']['cache_path'] ) {
//     $cache_path = DOC_ROOT . '/' . trim($app['config']['cache_path'],'/');
//     if ( is_writable($cache_path) ) {
//         define('CACHE_PATH', $cache_path );
//     } else {
//         throw new \Exception('The specified cache directory <strong>' . $cache_path . '</strong> could not be written to. Please check the directory permissions and refresh.');
//     }
// } else {
//     define('CACHE_PATH', null );
// }



// // register services
// 
// $app['cache'] = $app->share(function( $app ) {
//     return new Prontotype\Service\Cache( $app, CACHE_PATH );
// });
// 
// $app['data'] = $app->share(function( $app ) {
//     return new Prontotype\Service\Data( $app );
// });
// 
// $app['scrap'] = $app->share(function( $app ) {
//     return new Prontotype\Service\Scraper\Scraper( $app );
// });
// 

// 
// $app['uri'] = $app->share(function( $app ) {
//     return new Prontotype\Service\Uri( $app );
// });

// 
// $app['faker'] = $app->share(function() use ( $app ) {
//     return Faker\Factory::create();
// });
// 
// $app['exporter'] = $app->share(function() use ( $app ) {
//     return new Prontotype\Service\Exporter($app);
// });
// 
// // deal with extensions...
// 
// $extensionManager = new Prontotype\Extension\Manager(EXTENSIONS_PATH, $app);
// $extensionManager->loadExtensions($app['config']['extensions']);
// 
// // add user to twig env before dumping assets (which causes twig to init)
// if ( $user = $app['store']->get('user') ) {
//     $app['twig']->addGlobal('user', $user);
// }
// 
// if ($app['assetic.options']['auto_dump_assets']){
//     $dumper = $app['assetic.dumper'];
//     if (isset($app['twig'])) {
//         $dumper->addTwigAssets();
//     }
//     $dumper->dumpAssets();
// }
// 
// // import all PT macros
// foreach( glob(APP_PATH . '/views/PT/macros/*.twig') as $path ) {
//     $pathinfo = pathinfo($path);
//     $app['twig']->addGlobal($pathinfo['filename'], $app['twig']->loadTemplate('PT/macros/' . $pathinfo['basename']));
// }
// 
// // import all prototype macros
// if ( file_exists(TEMPLATES_PATH . '/macros') ) {
//     foreach( glob(TEMPLATES_PATH . '/macros/*.twig') as $path ) {
//         $pathinfo = pathinfo($path);
//         $app['twig']->addGlobal($pathinfo['filename'], $app['twig']->loadTemplate('/macros/' . $pathinfo['basename']));
//     }
// }
// 
// $app->before(function () use ($app, $extensionManager) {
//     
//     $authPage = array(
//         $app['uri']->generate('authenticate'),
//         $app['uri']->generate('de_authenticate')
//     );
//     
//     $ip_whitelist = $app['config']['authenticate']['ip_whitelist'];
//     if ( (is_array($ip_whitelist) && in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)) || is_string($ip_whitelist) && $_SERVER['REMOTE_ADDR'] ===  $ip_whitelist) {
//         $authRequired = false;
//     } else {
//         $authRequired = ( ! empty($app['config']['authenticate']) && ! empty($app['config']['authenticate']['username']) && ! empty($app['config']['authenticate']['password']) ) ? true : false;       
//     }
//     
//     if ( ! in_array($app['request']->getRequestUri(), $authPage) ) {
//         if ( $authRequired ) {
//             $currentUser = $app['session']->get( $app['config']['prefix'] . 'authed-user' );
//             $userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
//             if ( empty( $currentUser ) || $currentUser !== $userHash ) {
//                 return $app->redirect($app['uri']->generate('authenticate')); // not logged in, redirect to auth page
//             }
//         }
//     } elseif ( ! $authRequired ) {
//         // redirect visits to the auth pages to the homepage if no auth is required.    
//         return $app->redirect('/');
//     }
//     
//     $extensionManager->before();
// 
// });
// 
// $app->after(function () use ($app, $extensionManager) {
//     $extensionManager->after();
// });
// 
// $app->error(function (\Exception $e, $code) use ($app) {
//     
//     switch( $code ) {
//         case '404':
//             $template = 'PT/pages/404.twig';
//             break;
//         default:
//             $template = 'PT/pages/error.twig';
//             break;
//     }
//     
//     return new Symfony\Component\HttpFoundation\Response( $app['twig']->render($template, array(
//         'message' => $e->getMessage()
//     )), $code );
// });
// 
// $app->mount('/_system/', new Prontotype\Controller\SystemController());
// $app->mount('/', new Prontotype\Controller\MainController());

return $app;

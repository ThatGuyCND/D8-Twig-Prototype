<?php

define('DS', '/');
define('VERSION', '2.0');

/* Define globally available application paths */
define('DOC_ROOT', realpath(__DIR__ . '/..'));
define('APP_PATH', DOC_ROOT . '/app');
define('VENDOR_PATH', APP_PATH . '/vendor');

define('APP_TEMPLATES_PATH', APP_PATH . '/views');

if ( ! file_exists(APP_PATH . '/vendor/autoload.php') ) {
    throw new Exception("You need to install and run <a href=\"http://getcomposer.org\">Composer</a> before Prontoype will work. <a href=\"http://prontotype.allmarkedup.com/#setup\">Read the documentation for more details &rarr;</a>");
}

require_once APP_PATH . '/vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Prontotype\Service\Prototype($app));


return $app;

/* Identify the prototype */
$app->register(new Prontotype\Service\Prototype($app));

/* Define globally available prototype paths */
define('BASE_PATH', realpath(DOC_ROOT . '/' . $app['prototype']['base_path']));
define('TEMPLATES_PATH', BASE_PATH . '/structure');
define('PAGES_PATH', TEMPLATES_PATH . '/pages');
define('DATA_PATH', BASE_PATH . '/data');
define('EXTENSIONS_PATH', BASE_PATH . '/extensions');

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silextend\Config\YamlConfig(array(
    APP_PATH  . "/config.yml",
    BASE_PATH . "/" . $app['prototype']['config_file'],
)));

date_default_timezone_set($app['config']['timezone']);

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

$app->register(new Silex\Provider\SessionServiceProvider());
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());

// set up twig
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path'         => array( TEMPLATES_PATH.'/', APP_TEMPLATES_PATH.'/' ),
    'twig.options'      => array(
        'strict_variables'  => false,
        'cache'             => CACHE_PATH ? CACHE_PATH . '/' . $app['prototype']['domain'] . '/twig' : false,
        'auto_reload'       => true,
        'debug'             => $app['config']['debug'],
        'autoescape'        => false
    )
));

// set up Assetic
$app->register(new SilexAssetic\AsseticServiceProvider());
$app['assetic.path_to_web'] = DOC_ROOT;
$app['assetic.options'] = array(
    'auto_dump_assets' => $app['config']['assets']['auto_generate'],
    'debug'            => $app['config']['debug']
);
$app['assetic.filter_manager'] = $app['assetic.filter_manager'] = $app->share(
    $app->extend('assetic.filter_manager', function($fm, $app) {
        $fm->set('less', new Assetic\Filter\LessphpFilter(
            VENDOR_PATH . '/leafo/lessphp/lessc.inc.php'
        ));
        $fm->set('scss', new Assetic\Filter\ScssphpFilter(
           VENDOR_PATH . '/leafo/scssphp/scss.inc.php'
       ));
       return $fm;
    })
);

$app['twig'] = $app->share($app->extend('twig', function($twig, $app) {
    $twig->addExtension(new Twig_Extension_Debug());
    $twig->addExtension(new Prontotype\Twig\HelperExtension($app));
    return $twig;
}));
    
// register services

$app['cache'] = $app->share(function( $app ) {
    return new Prontotype\Service\Cache( $app, CACHE_PATH );
});

$app['data'] = $app->share(function( $app ) {
    return new Prontotype\Service\Data( $app );
});

$app['scrap'] = $app->share(function( $app ) {
    return new Prontotype\Service\Scraper\Scraper( $app );
});

$app['pagetree'] = $app->share(function( $app ) {
    return new Prontotype\Service\PageTree\Manager( $app );
});

$app['store'] = $app->share(function( $app ) {
    return new Prontotype\Service\Store( $app );
});

$app['uri'] = $app->share(function( $app ) {
    return new Prontotype\Service\Uri( $app );
});

$app['pt_request'] = $app->share(function() use ( $app ) {
    return new Prontotype\Service\Request($app);
});

$app['faker'] = $app->share(function() use ( $app ) {
    return Faker\Factory::create();
});

$app['exporter'] = $app->share(function() use ( $app ) {
    return new Prontotype\Service\Exporter($app);
});

// deal with extensions...

$extensionManager = new Prontotype\Extension\Manager(EXTENSIONS_PATH, $app);
$extensionManager->loadExtensions($app['config']['extensions']);

// add user to twig env before dumping assets (which causes twig to init)
if ( $user = $app['store']->get('user') ) {
    $app['twig']->addGlobal('user', $user);
}

if ($app['assetic.options']['auto_dump_assets']){
    $dumper = $app['assetic.dumper'];
    if (isset($app['twig'])) {
        $dumper->addTwigAssets();
    }
    $dumper->dumpAssets();
}

// import all PT macros
foreach( glob(APP_PATH . '/views/PT/macros/*.twig') as $path ) {
    $pathinfo = pathinfo($path);
    $app['twig']->addGlobal($pathinfo['filename'], $app['twig']->loadTemplate('PT/macros/' . $pathinfo['basename']));
}

// import all prototype macros
if ( file_exists(TEMPLATES_PATH . '/macros') ) {
    foreach( glob(TEMPLATES_PATH . '/macros/*.twig') as $path ) {
        $pathinfo = pathinfo($path);
        $app['twig']->addGlobal($pathinfo['filename'], $app['twig']->loadTemplate('/macros/' . $pathinfo['basename']));
    }
}

$app->before(function () use ($app, $extensionManager) {
    
    $authPage = array(
        $app['uri']->generate('authenticate'),
        $app['uri']->generate('de_authenticate')
    );
    
    $ip_whitelist = $app['config']['authenticate']['ip_whitelist'];
    if ( (is_array($ip_whitelist) && in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)) || is_string($ip_whitelist) && $_SERVER['REMOTE_ADDR'] ===  $ip_whitelist) {
        $authRequired = false;
    } else {
        $authRequired = ( ! empty($app['config']['authenticate']) && ! empty($app['config']['authenticate']['username']) && ! empty($app['config']['authenticate']['password']) ) ? true : false;       
    }
    
    if ( ! in_array($app['request']->getRequestUri(), $authPage) ) {
        if ( $authRequired ) {
            $currentUser = $app['session']->get( $app['config']['prefix'] . 'authed-user' );
            $userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
            if ( empty( $currentUser ) || $currentUser !== $userHash ) {
                return $app->redirect($app['uri']->generate('authenticate')); // not logged in, redirect to auth page
            }
        }
    } elseif ( ! $authRequired ) {
        // redirect visits to the auth pages to the homepage if no auth is required.    
        return $app->redirect('/');
    }
    
    $extensionManager->before();

});

$app->after(function () use ($app, $extensionManager) {
    $extensionManager->after();
});

$app->error(function (\Exception $e, $code) use ($app) {
    
    switch( $code ) {
        case '404':
            $template = 'PT/pages/404.twig';
            break;
        default:
            $template = 'PT/pages/error.twig';
            break;
    }
    
    return new Symfony\Component\HttpFoundation\Response( $app['twig']->render($template, array(
        'message' => $e->getMessage()
    )), $code );
});

$app->mount('/_system/', new Prontotype\Controller\SystemController());
$app->mount('/', new Prontotype\Controller\MainController());

return $app;

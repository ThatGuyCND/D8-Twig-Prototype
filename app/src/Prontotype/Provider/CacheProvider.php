<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Cache;

class CacheProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['cache'] = $app->share(function( $app ) {
		    return new Cache( $app, CACHE_PATH );
		});
    }
}

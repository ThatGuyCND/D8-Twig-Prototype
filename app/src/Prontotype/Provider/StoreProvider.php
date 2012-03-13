<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Store;

class StoreProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['store'] = $app->share(function( $app ) {
		    return new Store( $app );
		});
    }
}

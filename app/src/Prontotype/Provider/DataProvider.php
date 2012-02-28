<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Data;

class DataProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['data'] = $app->share(function( $app ) {
		    return new Data( $app );
		});
    }
}

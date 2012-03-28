<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Uri;

class UriProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['uri'] = $app->share(function( $app ) {
		    return new Uri( $app );
		});
    }
}

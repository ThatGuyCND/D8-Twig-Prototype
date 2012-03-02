<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Assets;

class AssetsProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['assets'] = $app->share(function( $app ) {
		    return new Assets( $app, DOC_ROOT );
		});
    }
}

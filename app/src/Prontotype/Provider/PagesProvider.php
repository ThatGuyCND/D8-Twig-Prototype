<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Pages;

class PagesProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['pages'] = $app->share(function( $app ) {
		    return new Pages( $app );
		});
    }
}

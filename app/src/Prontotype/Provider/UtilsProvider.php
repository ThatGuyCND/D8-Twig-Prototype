<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Utils;

class UtilsProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['utils'] = $app->share(function() {
		    return new Utils();
		});
    }
}

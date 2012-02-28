<?php 

namespace Prontotype\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;

use Prontotype\Service\Pagetree\Parser;

class PagetreeProvider implements ServiceProviderInterface
{
    public function register(Application $app)
    {
		$app['pagetree'] = $app->share(function( $app ) {
		    return new Parser( DOC_ROOT, PAGES_PATH, $app );
		});
    }
}

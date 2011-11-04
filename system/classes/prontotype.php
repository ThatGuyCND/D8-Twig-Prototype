<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Prontotype {
    
    public static $version = '0.1.0';
    
    public static function start()
    {
        // start output buffering
        ob_start();
        
        // load up the configs
        Config::load();

		if ( Config::get('caching') )
		{
			define('CACHE_PATH', SYSTEM_PATH . '_cache' . DS);

			$template_cache = CACHE_PATH . 'templates' . DS;
			$data_cache = CACHE_PATH . 'data' . DS;
			$asset_cache = CACHE_PATH . 'assets' . DS;

			if ( ! file_exists( $template_cache ) and ( ! @mkdir( $template_cache ) or ! @chmod( $template_cache, 0771 ) ) )
			{
				$template_cache = NULL;		
			}

			if ( ! file_exists( $data_cache ) and ( ! @mkdir( $data_cache ) or ! @chmod( $data_cache, 0771 ) ) )
			{
				$data_cache = NULL;		
			}
			
			if ( ! file_exists( $asset_cache ) and ( ! @mkdir( $asset_cache ) or ! @chmod( $asset_cache, 0771 ) ) )
			{
				$asset_cache = NULL;		
			}

			define('TEMPLATE_CACHE', $template_cache);
			define('DATA_CACHE', $data_cache);
			define('ASSET_CACHE', $asset_cache);
		}
		else
		{
			define('TEMPLATE_CACHE', NULL);
			define('DATA_CACHE', NULL);
			define('ASSET_CACHE', NULL);
		}
    }
    
    
    public static function finish()
	{
        // end output buffering
		echo ob_get_clean();
	}

}

/* End of file classes/prontotype.php */
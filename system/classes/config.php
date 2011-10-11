<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Config {
    
    public static $items = array();
   
    public static function load()
    {       
        $app_config = require( SYSTEM_PATH.'config.php' );
        
        if ( file_exists( DOCROOT.'config.php' ) )
        {
            $user_config = require( DOCROOT.'config.php' );
            self::$items = array_merge( $app_config, $user_config );
        }
        else
        {
            self::$items = $app_config;
        }
    }
    
    public static function get( $item, $default = NULL )
    {
        if ( isset(static::$items[$item]) )
		{
			return static::$items[$item];
		}

		return $default;
    }
    
}

/* End of file classes/config.php */
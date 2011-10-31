<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Config {
    
    public static $items = array();
   
    public static function load()
    {   
		$yaml = new sfYamlParser();
		
		$app_config = $yaml->parse(file_get_contents(SYSTEM_PATH.'config.yml'));
        
        if ( file_exists( DOCROOT.'config.yml' ) )
        {
            $user_config = $yaml->parse(file_get_contents(DOCROOT.'config.yml'));
			$user_config = $user_config ? $user_config : array();
            self::$items = array_merge( $app_config, $user_config );
        }
        else
        {
            self::$items = $app_config;
        }
    }
    
    public static function get( $item, $default = NULL )
    {
        if ( isset(self::$items[$item]) )
		{
			return self::$items[$item];
		}

		return $default;
    }
    
}

/* End of file classes/config.php */
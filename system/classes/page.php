<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');


class Page {
    
    protected $path;
    
    protected $uri;
    
    public function __construct( $uri )
    {
        $self = get_class($this);
        
		$this->uri = $uri;
        $this->path = $self::path($uri);
    }
            
    public static function path( $uri )
    {
        return self::uri_to_path($uri);
    }
    
    public static function uri_to_path( $uri )
    {         
        $segments = $uri->segments();
        $path = PAGES_PATH;
		$ext = Config::get('file_extension');

		$path = rtrim($path . $uri, '/');
		
		$rel_path = str_replace(SITE_PATH, '', $path);
		
		if ( file_exists($path . '.' . $ext) )
		{
			return $rel_path . '.' . $ext;
		}
		elseif ( file_exists($path . '/index.' . $ext) )
		{
			return $rel_path . '/index.' . $ext;
		}
		
		return null;
    }

    public function exists()
    {
		return !!$this->path;
    }

    public function get_path()
    {
        return $this->path;
    }

}

/* End of file classes/page.php */
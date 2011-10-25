<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class URI {
	
	public $uri = '';

	public $segments = array();
	
	public static $uri_prefix;
	
	public function __construct($uri = NULL)
	{
		if ($uri === NULL)
		{
			$uri = $this->detect();
		}
		
		$uri = trim($uri, '/');

		$parts = preg_split("/\\.([^.\\s]{2,4}$)/", $uri, NULL, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
		
		$this->uri = count($parts) ? $parts[0] : $uri;
   
		if ( empty($this->uri) )
		{
			$this->segments = array();	
		}
		else
		{
			$this->segments = explode('/', $this->uri);
		}
	}
	
	public function __get($name)
	{
		$num = str_replace('segment_', '', $name) - 1;
		return array_key_exists( $num, $this->segments ) ? $this->segments[$num] : NULL;
	}
	
	public function __isset( $name )
	{
		$num = str_replace('segment_', '', $name) - 1;
		return array_key_exists( $num, $this->segments ) ? TRUE : FALSE;
	}
	
	public function segments()
	{
		return $this->segments;
	}
	
	public function get()
	{
		return $_GET;
	}
	
	public function querystring( $pairs = NULL )
	{
		$get = $_GET;
		if ( !empty($pairs) )
		{
			foreach ( $pairs as $name => $value )
			{
				$get[$name] = $value;
			}
		}
		return http_build_query($get);
	}
	
	public function last_segment()
	{
		$num = count($this->segments);
		
		if ( ! $num )
		{
			return NULL;
		}

		return $this->segments[$num-1];
	}
	
	public function string()
	{
		return $this->uri;
	}
	
	public function redirect( $url )
	{
		header('Location: '. $url);
		exit();
	}
	
	protected function detect()
	{
		$index_file = Config::get('index_file');
		
		self::$uri_prefix = empty($index_file) ? $index_file : '';
		
		if ( ! empty($_SERVER['PATH_INFO']) )
		{
			$uri = $_SERVER['PATH_INFO']; // use it if we got it...
		}
		else
		{
			if ( isset($_SERVER['REQUEST_URI']) )
			{
				$uri = $_SERVER['REQUEST_URI'];
				
				if ( ! empty($index_file) )
				{
				   $uri = str_replace( $index_file, '', $uri );
				}
				
				list($uri) = explode('?',$uri);
			}
			else
			{
				throw new Exception('The URI cannot be detected.');
			}
		}

		return $uri;
	}

	public function __toString()
	{
		return $this->uri;
	}
}

/* End of file uri.php */
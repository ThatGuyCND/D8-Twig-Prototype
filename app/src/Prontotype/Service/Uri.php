<?php

namespace Prontotype\Service;

Class Uri {
	
	public $uri = '';
	
	public $segments = array();
	
	public static $uri_prefix;
	
	public function __construct( $app )
	{
		$uri = $this->detect();
		$this->app = $app;
		
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
		
	public function base()
	{
	   	$scheme = isset($_SERVER['https']) ? 'https' : 'http';
	
	  	$hostname = $_SERVER['SERVER_NAME'];
	  	$port = $_SERVER['SERVER_PORT'];
	
		if ( ($port == '80' && $scheme == 'http') or ($port == '443' && $scheme == 'https') )
		{
		    $base = $scheme . '://' . $hostname . '/';
		}
		else
		{
		    $base = $scheme . '://' . $hostname . ':' . $port . '/';
		}
		return $base;
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
		
	public function redirect( $url, $status = 302 )
	{
		header('Location: '. $url, TRUE, $status );
		exit();
	}
			
	protected function detect()
	{
		$index_file = 'index.php';
			
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
	
	public function generate( $routeName )
	{
		$url = $this->app['url_generator']->generate($routeName);
		if ( !empty($this->app['config']['index']) && strpos( $url, $this->app['config']['index'] ) === false )
		{
			$url = '/' . $this->app['config']['index'] . $url;
		}
		return $url;
	}
	
	public function __toString()
	{
		return $this->uri;
	}
}

/* End of file uri.php */

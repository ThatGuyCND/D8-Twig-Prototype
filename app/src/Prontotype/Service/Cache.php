<?php

namespace Prontotype\Service;

Class Cache {
	
	protected $app;
	
	protected $cache_path = null;
	
	public function __construct( $app, $cache_path )
	{
		$this->app = $app;
		$this->cache_path = $cache_path;
	}
	
	public function set( $type, $key, $content )
	{
		if ( ! $this->cache_path ) {
			return false;
		}

		try {
			$this->forcefileContents( $this->makePath( $type, $key ), serialize($content) );
		} catch ( \Exception $e ) {
			return false;
		}
		
		return true;
	}
	
	public function get( $type, $key, $newerThan = null )
	{
		if ( ! $this->cache_path ) {
			return null;
		}
		
		$cache_file = $this->makePath( $type, $key );
		
		if (  file_exists( $cache_file ) and $newerThan < filemtime( $cache_file ) )
		{
			return unserialize( file_get_contents( $cache_file ) );
		}
		
		return null;
	}
	
	public function clear( $type = NULL, $key = NULL )
	{
		if ( ! $this->cache_path ) {
			return null;
		}
		// TODO
	}
	
	protected function makePath( $type, $key )
	{
		$key = $this->encodeKey( $key );	
		return $this->cache_path . '/' . $type . '/' . $key . '.cache';
	}
	
	protected function encodeKey( $key )
	{
		return base64_encode( $key );
	}
	
	protected function forcefileContents( $location, $contents )
	{
		$file = basename($location);
		$dir = dirname($location);
		
		if ( ! is_dir($dir) )
		{
			mkdir($dir, 0771, true);
		}
		

		file_put_contents($location, $contents);
		chmod($location, 0644);
	}

}

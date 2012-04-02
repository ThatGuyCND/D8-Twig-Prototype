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
	
	public function clear( $type, $key = NULL )
	{
		if ( ! $this->cache_path ) {
			return null;
		}
		
		if ( $type && $key ) {
			return unlink($this->makePath($type, $key));
		} else {
			$this->forceRemoveDir( $this->cache_path . '/' . $type );
		} 
	}
	
	public function mtime( $type, $key )
	{
		if ( ! $this->cache_path ) {
			return false;
		}
		
		return filemtime($this->makePath( $type, $key ));
	}
	
	public function exists( $type, $key )
	{
		if ( ! $this->cache_path ) {
			return false;
		}
		
		return file_exists($this->makePath( $type, $key ));
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
	
	protected function forceRemoveDir($dir)
	{
		foreach ( glob($dir . '/*') as $file )
		{
			if ( is_dir($file) ) {
				$this->rrmdir( $file );
			} else {
				unlink($file);
			}
		}
		rmdir($dir);
	}

}

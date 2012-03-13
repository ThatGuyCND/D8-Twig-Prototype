<?php

namespace Prontotype\Service;

Class Cache {
	
	protected $app;
	
	public function __construct( $app, $cache_path )
	{
		$this->app = $app;
		$this->cache_path = $cache_path;
	}
	
	public function set( $key, $content )
	{
		$key = $this->encodeKey( $key );
	
		// TODO
		
		// file_put_contents( DATA_CACHE . '/' .$parts['filename'] . '.cache', serialize($data) );
	}
	
	public function get( $key )
	{
		// $file_mtime = filemtime( $file );
		// $cache_file = DATA_CACHE . '/' . $parts['filename'] . '.cache';
		// 
		// if (  file_exists( $cache_file ) and $file_mtime < filemtime( $cache_file ) )
		// {
		// 	// cached version is newer, use that
		// 	return unserialize( file_get_contents( $cache_file ) );
		// }
		
		return NULL;
	}
	
	public function clear( $key = NULL )
	{
		// TODO
	}
	
	protected function encodeKey( $key )
	{
		return base64_encode( $key );
	}

}

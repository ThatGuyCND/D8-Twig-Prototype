<?php

namespace Prontotype\Service;

class Assets {
	
	protected $app;

	public function __construct( $app, $root )
	{
		$this->app = $app;
		$this->root_path = $root;
	}
	
	public function convert( $format, $path )
	{
		$fullpath = $this->root_path . '/' . $path;

		if ( ! file_exists( $fullpath ) ) {
			return NULL;
		}
		
		if ( $contents = $this->app['cache']->get( $fullpath ) )
		{
			return $contents;
		}
				
		$converter = 'convert' . ucwords(strtolower($format));
		
		if ( method_exists( $this, $converter ) )
		{
			return $this->$converter( $fullpath );
		}
		
		return NULL;
	}
	
	public function contentType( $format )
	{
		return 'text/css';
	}
	
	public function convertLess( $path )
	{
		$less = new \lessc( $path );
		return $less->parse();
	}
	
	public function convertCss( $path )
	{
		return file_get_contents($path);
	}
	
}



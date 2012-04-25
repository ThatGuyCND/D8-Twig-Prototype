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
		
		if ( $contents = $this->app['cache']->get( 'assets', $fullpath, filemtime($fullpath) ) )
		{
			echo '<pre>';
			print_r('from cache');
			echo '</pre>';
			
			return $contents;
		}
		
		$converter = 'convert' . ucwords(strtolower($format));
		
		if ( method_exists( $this, $converter ) )
		{
			$output = $this->$converter( $fullpath );
			$this->app['cache']->set( 'assets', $fullpath, $output );
			return $output;
		}
		
		return NULL;
	}
	
	public function contentType( $format )
	{
		$format = strtolower($format);
		switch( $format )
		{
			case 'css':
			case 'less':
				$type = 'text/css';
				break;
			default:
				$type = 'text/html';
				break;
		}
		return $type;
	}
	
	public function convertLess( $path )
	{
		$less = new \lessc( $path );
		$importPaths = (array)$this->app['config']['less_import_paths'];
		$less->importDir = array_merge( array($this->root_path), $importPaths);
		return $less->parse();
	}
	
	public function convertCss( $path )
	{
		return file_get_contents($path);
	}
	
}



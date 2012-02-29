<?php

namespace Prontotype\Service;

class Assets {
	
	protected $app;

	public function __construct( $app )
	{
		$this->app = $app;
	}
	
	public function convert( $format, $path )
	{
		return 'asd';
	}
	
	public function contentType( $format )
	{
		return 'text/css';
	}
	
}



<?php

namespace Prontotype\Service;

Class Pages {
	
	protected $app;
	
	protected $current = NULL;
	
	function __construct( $app )
	{
		$this->app = $app;
	}
	
	function current()
	{
		if ( ! $this->current )
		{
			$uri = $this->app['uri'];
			$this->current = $this->getPage($uri->string());
		}
		return $this->current;
	}
	
	function getById( $id )
	{
		
		return $this->app['pagetree']->getPageById($id);
	}
	
	function getByPath( $path )
	{
		return $this->app['pagetree']->getPage($path);
	}
	
	function link( $id )
	{
		$page = @$this->getById($id);
		if ( $page ) return $page->nice_url;
		return '#';
	}
	
	function getAll()
	{
		return $this->app['pagetree']->getPageTree();
	}
	
}

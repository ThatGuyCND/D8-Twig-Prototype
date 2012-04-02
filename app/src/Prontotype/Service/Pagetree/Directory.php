<?php

namespace Prontotype\Service\Pagetree;

Class Directory {
	
	public $id = NULL;
	
	public $fs_path;
	
	public $url;
	
	public $nice_url;
	
	public $name;
	
	public $nice_name;
	
	public $children;
	
	public $has_index;
	
	public $type = 'directory';
	
	public $level;
	
	protected $uriSegments;
			
	function __construct( $path, $pages_root_path, $uriSegments, $configProvider )
	{
		$this->pages_root_path = $pages_root_path;
		$this->uriSegments = $uriSegments;
		$this->configProvider = $configProvider;
		$this->build_data( $path );
	}
	
	public function is_parent()
	{
		$uri = $this->uriProvider;
		$uri_segments = $uri->segments();
		$page_segments = explode( '/', trim($this->nice_url, '/') );
		
		$num_uri_segments = count( $uri_segments );
		$num_page_segments = count( $page_segments );
		
		if ( $num_page_segments > $num_uri_segments )
		{
			return false; // cant be a parent as the page has more segments
		}
		
		for ( $i = 0; $i < $num_page_segments; $i++ )
		{
			if ( $uri_segments[$i] !== $page_segments[$i] )
			{
				return false;
			}
		}
		
		return true;
	}
	
	protected function build_data( $path )
	{
		$pages_path = rtrim($this->pages_root_path, DS);
		$dir_path = str_replace(array($pages_path), '', $path);
		
		$this->level = count(explode('/',$dir_path)) -1;

		$route_dir_path = preg_replace( Parser::$folder_format_regex, '/', $dir_path );
		$dir_path = '/pages' . $dir_path;
		
		$segments = explode( DS, trim($route_dir_path, DS) );
		
		$this->nice_url = $this->url = $route_dir_path;
		
		$this->name = end($segments);

		$this->fs_path = $dir_path;
		
		$this->nice_name = Parser::title_case(str_replace(array('_','-'), ' ', $this->name));
	}
	
	public function add_children( $children )
	{
		$this->children = $children;
		
		$this->has_index = false;
		foreach( $children as $child )
		{			
			if ( $child->name === 'index' )
			{
				$this->has_index = true;
				$this->url = $child->url;
				$this->nice_url = $child->nice_url;
				break;
			}
		}
	}
	
	
}

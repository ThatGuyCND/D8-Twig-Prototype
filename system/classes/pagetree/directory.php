<?php

Class Pagetree_Directory {
	
	public $id = NULL;
	
	public $fs_path;
	
	public $url;
	
	public $nice_url;
	
	public $name;
	
	public $nice_name;
	
	public $children;
	
	public $has_index;
		
	function __construct( $path )
	{
		$this->build_data( $path );
	}
	
	protected function build_data( $path )
	{
		$pages_path = rtrim(PAGES_PATH, DS);
		$dir_path = str_replace(array($pages_path), '', $path);
		$level = count(explode('/',$dir_path));
		$route_dir_path = preg_replace( Pagetree::$folder_format_regex, '/', $dir_path );
		$dir_path = '/pages' . $dir_path;
		
		$segments = explode( DS, trim($route_dir_path, DS) );
		
		$this->name = end($segments);
		
		$this->fs_path = $dir_path;
		
		$this->nice_name = Helpers::title_case(str_replace(array('_','-'), ' ', $this->name));
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
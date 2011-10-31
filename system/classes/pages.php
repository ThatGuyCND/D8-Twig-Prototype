<?php

Class Pages {
	
	protected $router;
	
	function __construct()
	{
		$this->router = Pagetree::instance();
	}
	
	function get_by_id( $id )
	{
		return $this->router->page_from_id($id);
	}
	
	function get_by_path( $path )
	{
		return $this->router->page_from_url($path);
	}
	
	function get_all()
	{
		return $this->router->get_page_tree();
	}
	
}

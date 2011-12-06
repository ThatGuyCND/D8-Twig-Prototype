<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Pages {
	
	protected $page_tree;
	
	protected $current = NULL;
	
	function __construct()
	{
		$this->page_tree = Pagetree::instance();
	}
	
	function current()
	{
		if ( ! $this->current )
		{
			$uri = new URI();
			$this->current = $this->get_by_path($uri->string());
		}
		return $this->current;
	}
	
	function get_by_id( $id )
	{
		return $this->page_tree->page_from_id($id);
	}
	
	function get_by_path( $path )
	{
		return $this->page_tree->page_from_url($path);
	}
	
	function link( $id )
	{
		$page = @$this->get_by_id($id);
		if ( $page ) return $page->nice_url;
		return '#';
	}
	
	function get_all()
	{
		return $this->page_tree->get_page_tree();
	}
	
}

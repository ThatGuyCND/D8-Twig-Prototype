<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Pagetree {
	
	protected static $instance;
	
	protected $page_tree = NULL;
	
	protected $route_map = array();
	
	protected $id_map = array();
	
	public static $folder_format_regex = '/\/((\d)*[\._\-])/';
		
	public static function instance()
	{
		if ( ! isset( self::$instance ) )
		{
			$c = __CLASS__;
			self::$instance = new $c();
		}
		return self::$instance;
	}
    
    protected function __construct()
    {
        ///...
    }

	public function page_from_url( $url_path )
	{
		$this->load_pages();

		$url_path = '/' . trim( $url_path, '/' );

		if ( $url_path == '/' )
		{
			$url_path = '/index'; // homepage
		}

		if ( isset($this->route_map[$url_path]) )
		{
			return $this->route_map[$url_path];
		}
		elseif ( isset($this->route_map[$url_path.'/index']) )
		{
			return $this->route_map[$url_path.'/index'];
		}

		return NULL;
	}

	public function path_from_url( $url_path )
	{
		$page = $this->page_from_url( $url_path );
		return $page ? $page->fs_path : NULL;
	}
	
	public function page_from_id( $id )
	{
		$this->load_pages();
		return isset( $this->id_map[$id] ) ? $this->id_map[$id] : NULL;
	}
	
	public function path_from_id( $id )
	{
		$page = $this->page_from_id( $id );
		return $page ? $page->fs_path : NULL;
	}
	
	public function get_page_tree()
	{
		return $this->page_tree;
	}

 	protected function load_pages()
	{
		if ( $this->page_tree === NULL )
		{
			$pages = new RecursiveDirectoryIterator(PAGES_PATH);
			$this->page_tree = $this->parse_directory( $pages );
		}
	}
	
	protected function parse_directory( RecursiveDirectoryIterator $iterator )
	{
		$page_tree = array();
		
		foreach ( $iterator as $file )
		{
			if ( $file->isFile() )
			{
				$item = new Pagetree_Page( $file->getPathname() );
				$this->route_map[$item->url] = $item;
				if ( $item->id )
				{
					$this->id_map[$item->id] = $item;
				}
			}
			elseif ( $iterator->hasChildren() )
			{
				$item = new Pagetree_Directory( $file->getPathname() );
				
				$children = $this->parse_directory( $iterator->getChildren() );
				
				$item->add_children( $children );
			}
			
			$page_tree[] = $item;
		}
		return $page_tree;
	}
}
<?php

namespace Prontotype\Service\Pagetree;

Class Parser {
	
	protected $page_tree = NULL;
	
	protected $route_map = array();
	
	protected $id_map = array();
	
	protected $dir_map = array();
	
	protected $pages_path;
	
	protected $app;
	
	public static $folder_format_regex = '/\/((\d)*[\._\-])/';
	
	public function __construct( $root_path, $pages_path, $app )
	{
		$this->root_path = $root_path;
		$this->pages_path = $pages_path;
		$this->app = $app;
	}

	public function getPage( $url_path )
	{
		$this->loadPages();
		
		$url_path = '/' . trim( $url_path, '/' );

		if ( $url_path == '/' )
		{
			$url_path = '/index'; // homepage
		}
		
		if ( ! empty($this->app['config']['index']) )
		{
			$url_path = '/' . $this->app['config']['index'] . $url_path;
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

	public function getPath( $url_path )
	{
		$page = $this->getPage( $url_path );
		return $page ? $page->fs_path : NULL;
	}
	
	public function getPageById( $id )
	{
		$this->loadPages();
		return isset( $this->id_map[$id] ) ? $this->id_map[$id] : NULL;
	}
	
	public function getPathById( $id )
	{
		$page = $this->pageFromId( $id );
		return $page ? $page->fs_path : NULL;
	}
	
	public function getPageTree()
	{
		return $this->page_tree;
	}
	
	public function getDirMap()
	{
		return $this->dir_map;
	}
	
	protected function thereIsAFileNewerThan( $mtime )
	{
		$it = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator( $this->pages_path ));
		foreach( $it as $item )
		{
			if ( $item->isFile() && strpos( $item->getFilename(), '.' ) !== 0 && filemtime($item) > $mtime )
			{
				return true;
			}
		}
		return false;
	}

 	protected function loadPages()
	{
		if ( $this->page_tree === NULL )
		{
			$cacheTime = $this->app['cache']->mtime('structure', 'page_tree');
			$routeMapExists = $this->app['cache']->exists('structure', 'route_map') ? true : null;
			$idMapExists = $this->app['cache']->exists('structure', 'id_map') ? true : null;
			$dirMapExists = $this->app['cache']->exists('structure', 'dir_map') ? true : null;
			
			if ( ! $cacheTime or $this->thereIsAFileNewerThan( $cacheTime ) or ! isset( $routeMapExists, $idMapExists, $dirMapExists ) )
			{
				$pages = new \RecursiveDirectoryIterator( $this->pages_path );
				$this->page_tree = $this->parseDirectory( $pages );

				$this->app['cache']->set('structure', 'page_tree', $this->page_tree );
				$this->app['cache']->set('structure', 'route_map', $this->route_map );
				$this->app['cache']->set('structure', 'id_map', $this->id_map );
				$this->app['cache']->set('structure', 'dir_map', $this->dir_map );
			}
			else
			{
				$this->page_tree = $this->app['cache']->get('structure', 'page_tree' );
				$this->route_map = $this->app['cache']->get('structure', 'route_map' );
				$this->id_map = $this->app['cache']->get('structure', 'id_map' );
				$this->dir_map = $this->app['cache']->get('structure', 'dir_map' );
			}
		}
	}
	
	protected function parseDirectory( \RecursiveDirectoryIterator $iterator )
	{
		$page_tree = array();
		
		foreach ( $iterator as $file )
		{
			$item = NULL;
			if ( $file->isFile() && strpos( $file->getFilename(), '.' ) !== 0 )
			{
				$uriProvider = new \stdClass(); // TEMP
				$item = new Page($file->getPathname(), $this->pages_path, $this->app['uri']->string(), (array)$this->app['config'] );
					
				$this->route_map[$item->url] = $item;
				if ( $item->id )
				{
					$this->id_map[$item->id] = $item;
				}
			}
			elseif ( $iterator->hasChildren() )
			{
				$uriProvider = new \stdClass(); // TEMP
				$item = new Directory( $file->getPathname(), $this->pages_path, $this->app['uri']->segments(), (array)$this->app['config'] );
				
				$children = $this->parseDirectory( $iterator->getChildren() );
				
				$item->add_children( $children );
				
				$this->dir_map[$item->url] = $item;
			}
			if ( $item ) $page_tree[] = $item;
		}
		uasort($page_tree, function( $a, $b ){			
			return strnatcasecmp($a->fs_path, $b->fs_path);
		});
		return $page_tree;
	}
	
	public static function title_case( $title )
	{ 
		$smallwordsarray = array('of','a','the','and','an','or','nor','but','is','if','then','else','when','at','from','by','on','off','for','in','out','over','to','into','with');
		
		$words = explode(' ', $title); 
		foreach ($words as $key => $word) 
		{ 
			if ($key == 0 or !in_array($word, $smallwordsarray))
			{
				$words[$key] = ucwords(strtolower($word)); 
			}
		}
		
		$newtitle = implode(' ', $words); 
		return $newtitle; 
	}
	
}

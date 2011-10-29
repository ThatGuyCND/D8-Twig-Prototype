<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Router {
	
	protected static $instance;
	
	protected $page_tree = NULL;
	
	protected $route_map = array();
	
	protected $id_map = array();
	
	protected $filename_format_regex = '/^((\d)*[\._\-])?([^\[]*)?(\[([\d\w-_]*?)\][\._\-]?)?(.*?)\.html$/';
	
	protected $folder_format_regex = '/\/((\d)*[\._\-])/';
	
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
		return $page ? $page['template_path'] : NULL;
	}
	
	public function page_from_id( $id )
	{
		$this->load_pages();
		return isset( $this->id_map[$id] ) ? $this->id_map[$id] : NULL;
	}
	
	public function path_from_id( $id )
	{
		$page = $this->page_from_id( $id );
		return $page ? $page['template_path'] : NULL;
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
			$this->page_tree = $this->parse_pages_dir( $pages );
		}
	}
		
	protected function parse_filename( $filename )
	{
		$info = array();
		preg_match($this->filename_format_regex,$filename,$info);
		$parts = array(
			'template' => $filename,
			'route' => $info[3] . $info[6],
			'deindexed_route' => ( $info[3] . $info[6] === 'index' ) ? '' : $info[3] . $info[6],
			'id' => $info[5],
		);
		return $parts;
	}
	
	protected function make_nice_name( $name, $level )
	{
		if ( $level == 1 && $name == 'index' )
		{
			return Config::get('homepage_nicename');
		}
		elseif ( $name == 'index' )
		{
			return Config::get('index_nicename');
		}
		
		return Helpers::title_case(str_replace(array('_','-'), ' ', $name));
	}
		
	protected function parse_pages_dir( RecursiveDirectoryIterator $iterator, $level = 0 )
	{
		$pages = array();
		
		$level++;
		
		foreach ( $iterator as $file )
		{
			$current = array();
			$pages_path = rtrim(PAGES_PATH, DS);

			if ( $file->isDir() )
			{
				$dir_path = str_replace($pages_path, '', $file->getPathname());
			}
			else
			{
				$dir_path = str_replace($pages_path, '', $file->getPath());
			}
			
			$route_dir_path = preg_replace($this->folder_format_regex, '/', $dir_path);
			
			$dir_path = '/pages' . $dir_path;
			
			if ( $file->isFile() )
			{
				$pathinfo = $this->parse_filename(str_replace(PAGES_PATH, '', $file->getFilename()));	
				$current['template_path'] = $dir_path . DS . $pathinfo['template'];
				
				$current['url'] = $route_dir_path . DS . $pathinfo['route'];
				
				$current['link_url'] = str_replace('/index', '', $current['url']);
				
				if ( empty($current['link_url']) ) $current['link_url'] = '/'; // homepage
				
				if ( $pathinfo['id'] )
				{
					$current['id'] = $pathinfo['id'];	
				}
			}
			
			$current['level'] = $level;
			
			if ( $file->isFile() )
			{
				$current['name'] = $pathinfo['route'];
				$current['nice_name'] = $this->make_nice_name($current['name'], $current['level']);
				$current['type'] = 'file';
			}
			else
			{
				$segments = explode( DS, trim($route_dir_path, DS) );
				$current['name'] = end($segments);
				$current['nice_name'] = $this->make_nice_name($current['name'], $current['level']);
				$current['type'] = 'directory';
				$current['children'] = $this->parse_pages_dir($iterator->getChildren(), $level );
				
				$current['has_index'] = false;
				foreach( $current['children'] as $child )
				{
					if ( $child['name'] === 'index' )
					{
						$current['has_index'] = true;
						$current['template_path'] = $dir_path;
						$current['url'] = $child['url'];
						$current['link_url'] = $child['link_url'];
						break;
					}
				}
			}
						
			$pages[] = $current;
			
			if ( $current['type'] == 'file' )
			{
				$this->route_map[$current['url']] = $current;	
			}
			
			if ( isset($current['id']) )
			{
				$this->id_map[$current['id']] = $current;
			}
		}
		return $pages;
	}
}
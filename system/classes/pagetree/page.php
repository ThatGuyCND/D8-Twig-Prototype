<?php

Class Pagetree_Page {
	
	public $id = NULL;
	
	public $fs_path;
	
	public $url;
	
	public $nice_url;
	
	public $short_url = NULL;
	
	public $name;
	
	public $nice_name;
	
	public $type = 'page';
	
	protected $name_format_regex = '/^((\d)*[\._\-])?([^\[]*)?(\[([\d\w-_]*?)\][\._\-]?)?(.*?)\.html$/';
	
	function __construct( $path )
	{
		$this->build_data( $path );
	}
	
	public function is_current()
	{
		$uri = new URI();
		return ( trim($this->nice_url,'/') == trim( $uri->string(),'/') );
	}
		
	protected function build_data( $path )
	{
		$pages_path = rtrim(PAGES_PATH, DS);
		$info = pathinfo( $path );
		$dir_path = str_replace(array($pages_path, '/'.$info['basename']), '', $path);
		$level = count(explode('/',$dir_path));
		$route_dir_path = preg_replace( Pagetree::$folder_format_regex, '/', $dir_path );
		$dir_path = '/pages' . $dir_path;

		$filename_info = $this->parse_filename(str_replace(PAGES_PATH, '', $info['basename']));

		$this->level = $level;
		
		$this->fs_path = $dir_path . DS . $filename_info['fs_name'];
		
		$this->url = $route_dir_path . DS . $filename_info['route_name'];
		
		$this->nice_url = str_replace('/index', '', $this->url);
		
		if ( empty($this->nice_url) )
		{
			$this->nice_url = '/'; // homepage	
		}
		
		if ( ! empty( $filename_info['id'] ) )
		{
			$this->id = $filename_info['id'];
			$this->short_url = Config::get('short_url_trigger') . '/' . $this->id;
		}
		
		$this->name = $filename_info['route_name'];
		$this->nice_name = $this->make_nice_name($this->name, $this->level);
	}
	
	protected function parse_filename( $filename )
	{
		$info = array();
		preg_match($this->name_format_regex, $filename, $info);
		$parts = array(
			'fs_name' => $filename,
			'route_name' => $info[3] . $info[6],
			'deindexed_route_name' => ( $info[3] . $info[6] === 'index' ) ? '' : $info[3] . $info[6],
			'id' => $info[5],
		);
		return $parts;
	}
	
	protected function make_nice_name( $name, $level = 0 )
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
	
}
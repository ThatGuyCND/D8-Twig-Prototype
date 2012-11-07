<?php

namespace Prontotype\Service\Pagetree;

Class Page {
	
	public $id = NULL;
	
	public $fs_path;
	
	public $url;
	
	public $nice_url;
	
	public $short_url = NULL;
	
	public $name;
	
	public $nice_name;
	
	public $type = 'page';
	
	public $level;
	
	protected $pages_root_path;
	
	protected $requestUri;
	
	protected $configProvider;
	
	protected $name_format_regex = '/^((\d)*[\._\-])?([^\[]*)?(\[([\d\w-_]*?)\][\._\-]?)?(.*?)\.html$/';
	
	function __construct( $path, $pages_root_path, $requestUri, $configProvider )
	{
		$this->pages_root_path = $pages_root_path;
		$this->requestUri = $requestUri;
		$this->configProvider = $configProvider;
		$this->build_data( $path );
	}
	
	public function is_current()
	{
		$uri = new \Prontotype\Service\Uri();
		return ( trim($this->nice_url,'/') == trim( $uri->string(),'/') );
	}
		
	protected function build_data( $path )
	{
		$pages_path = rtrim($this->pages_root_path, DS);
		
		$info = pathinfo( $path );
		
		$dir_path = str_replace(array($pages_path, '/'.$info['basename']), '', $path);
		
		$level = count(explode('/',$dir_path));
		
		$route_dir_path = preg_replace( Parser::$folder_format_regex, '/', $dir_path );
		
		$dir_path = '/pages' . $dir_path; // TODO: fix this!

		$filename_info = $this->parse_filename(str_replace($this->pages_root_path, '', $info['basename']));

		$this->level = $level;
		
		$this->fs_path = $dir_path . DS . $filename_info['fs_name'];
		
		$this->url = $route_dir_path . DS . $filename_info['route_name'];
				
		$this->nice_url = str_replace('/index', '', $this->url);
		
		if ( ! empty($this->configProvider['index']) )
		{
			$this->url = '/' . $this->configProvider['index'] . $this->url;
			$this->nice_url = '/' . $this->configProvider['index'] . $this->nice_url;
		}
		
		if ( empty($this->nice_url) )
		{
			$this->nice_url = '/'; // homepage	
		}
		
		if ( ! empty( $filename_info['id'] ) )
		{
			$this->id = $filename_info['id'];
			$this->short_url = $this->configProvider['triggers']['shorturl'] . '/' . $this->id;
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
			return $this->configProvider['nice_names']['home'];
		}
		elseif ( $name == 'index' )
		{
			return $this->configProvider['nice_names']['index'];
		}
		
		return Parser::title_case(str_replace(array('_','-'), ' ', $name));
	}
	
}

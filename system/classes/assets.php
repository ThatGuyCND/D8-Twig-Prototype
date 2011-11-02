<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Assets {
	
	protected $uri;
	
	protected $asset_defaults = array(
		'less' => array( 'parser' => 'less', 'media' => 'all' ),
		'css'  => array( 'media' => 'all' ),
		'js'   => array(),
	);
	
	protected $pronto_js = array(
		'jquery' 	=> 'js/jquery.min.js',
		'cookie' 	=> 'js/jquery.cookie.min.js',
		'json2'  	=> 'js/json2.js',
		'pronto-js' => 'js/pronto.js',
	);
	
	protected $pronto_css = array(
		'pronto-css' => 'css/pronto.css',
	);

	protected $pronto_defaults = array(
		'jquery' 	 => TRUE,
		'cookie' 	 => TRUE,
		'json2' 	 => FALSE,
		'pronto-js'  => TRUE,
		'pronto-css' => TRUE,
	);
	
	public function __construct()
	{
		$this->uri = new URI();
	}
	
	public function link( $path, $opts = array() )
	{
		if ( $item = $this->process( $path, $opts ) )
		{
			return $this->build_el( $item );
		}
		return NULL;
	}
	
	public function url( $path, $opts = array() )
	{
		if ( $item = $this->process( $path, $opts ) )
		{
			return $item['url'];
		}
		return NULL;
	}
	
	public function pronto( $opts = array() )
	{
		$output_files = array();
		
		$opts = array_merge( $this->pronto_defaults, $opts );
		
		foreach( $this->pronto_css as $alias => $css_file )
		{
			if ( $opts[$alias] )
			{
				$file = array( 
					'extension' => 'css',
					'url'       => str_replace( DOCROOT, $this->uri->base(), PT_ASSETS_PATH . $css_file ),
					'opts'      => $this->asset_defaults['css']
				);
				$output_files[] = $this->build_el( $file );
			}
		}
		
		foreach( $this->pronto_js as $alias => $js_file )
		{
			if ( $opts[$alias] )
			{
				$file = array( 
					'extension' => 'js',
					'url'       => str_replace( DOCROOT, $this->uri->base(), PT_ASSETS_PATH . $js_file ),
					'opts'      => $this->asset_defaults['js']
				);
				$output_files[] = $this->build_el( $file );
			}
		}
		
		if ( $opts['pronto-js'] )
		{
			$config  = '{';
			$config .= '"prefix" : "' . Config::get('prefix') . '", ';
			$config .= '"cookie_lifetime" : "' . Config::get('cookie_lifetime') . '", ';
			$config .= '"json_data_trigger" : "' . Config::get('json_data_trigger') . '"';
			$config .= '}';
			$output_files[] = '<script>PT.configure(' . $config . ');</script>';
		}
		
		return implode($output_files,"\n");
	}
	
	protected function process( $path, $opts = array() )
	{
		$item = array();
		
		$item['path'] = ltrim($path,'/');
		$item['full_path'] = PUBLIC_PATH . $item['path'];

		if ( file_exists( $item['full_path'] ) )
		{
			$item = array_merge($item, pathinfo( $item['full_path'] ));
			
			if ( array_key_exists( $item['extension'], $this->asset_defaults ) )
			{
				$item['opts'] = array_merge( $this->asset_defaults[$item['extension']], $opts );

				// if we need to parse the file, do it here
				if ( $parser = @$item['opts']['parser'] )
				{
					$parser_method = 'parse_' . $parser;
	
					if ( method_exists( $this, $parser_method ) )
					{
						$item['output_path'] = $this->$parser_method( $item );
					}
				}
				
				if ( ! isset( $item['output_path'] ) )
				{
					$item['output_path'] = $item['full_path'];
				}
			}
			
			if ( $item['output_path'] )
			{
				$item['url'] = str_replace( DOCROOT, $this->uri->base(), $item['output_path'] );
				return $item;	
			}
		}
		
		return false;
	}
	
	protected function build_el( $item )
	{
		switch ( $item['extension'] )
		{
			case 'less':
			case 'css':
				$output = '<link rel="stylesheet" href="' . $item['url'] . '"';
				$output .= ' media="' . $item['opts']['media'] . '"';
				$output .= Config::get('xhtml_tags') ? ' />' : '>';
			break;
			case 'js':
				$output = '<script src="' . $item['url'] . '"></script>';
			break;
		}

		return $output;
	}
	
	protected function parse_less( $item )
	{
		require_once SYSTEM_PATH . '/vendor/Less/lessc.inc.php';
				
		if ( ASSET_CACHE )
		{
			$output_path = ASSET_CACHE . 'css' . DS . $item['filename'] . '.css';
			if ( ! file_exists( $output_path ) or filemtime( $item['full_path'] ) > filemtime( $output_path ) )
			{
				// regenerate
				try
				{
					$less = new lessc($item['full_path']);
					Helpers::file_force_contents( $output_path, $less->parse() );
				}
				catch ( Exception $e )
				{
					if ( Config::get('debug') )
					{
						throw new Exception('Fatal error when compiling LESS file: '.$ex->getMessage());
					}
					else
					{
				    	return false;
					}
				}
			}
			return $output_path;
		}
		return FALSE;
	}
	
}

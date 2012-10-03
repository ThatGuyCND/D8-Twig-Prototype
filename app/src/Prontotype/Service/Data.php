<?php

namespace Prontotype\Service;

use Symfony\Component\Yaml\Yaml;

class Data {
	
	protected $data = array();
	protected $app;
	
	protected $extensions_map = array(
		'csv'  => 'csv',
		'yml'  => 'yml',
		'yaml' => 'yml',
		'json' => 'json',
	);
	    
    public function __construct( $app )
    {
        $this->app = $app;
    }

	public function __get( $name )
	{
		if ( ! isset( $this->data[$name] ) )
		{
			$this->data[$name] = $this->load( $name );
		}
		return $this->data[$name];
	}
	
	public function __isset( $name )
	{
		return $this->$name ? TRUE : FALSE;
	}
	
	// convenience function - for use when templates need to load data from a file whose name they won't know until runtime
	public function get( $file )
	{
		return $this->$file;
	}
	
	public function file( $file )
	{
		return $this->$file;
	}
	
	public function external( $url, $type )
	{
		$data = $this->make_external_request($url);
		if ( isset($this->extensions_map[strtolower($type)]))
		{
			$parser = 'parse_' . $this->extensions_map[strtolower($type)];
			
			if ( method_exists( $this, $parser ) )
			{
				$data = $this->$parser( $url );
			}
		}
		return $data;
	}

    public function find( $path )
    {
		$pathparts = explode( '.', trim( $path, '.') );
				
		$data = $this->{$pathparts[0]};

		unset($pathparts[0]);
		$pathparts = array_values($pathparts);

		if ( count( $pathparts) )
		{
			foreach ( $pathparts as $key )
			{
				if ( isset( $data[$key] ) )
				{
					$data = $data[$key];
				}
				else
				{
					$data = NULL;
					break;
				}
			}
		}

        return $data;
    }

	protected function load( $name )
	{
		$data = array();
		$datafiles = glob( DATA_PATH . '/'. $name . '.*' );

		if ( count( $datafiles ) )
		{
			foreach( $datafiles as $file )
			{
				// if there are multiple files with the same name but different extensions, merge the contents of them together
				$filedata = $this->retrieve( $file );
				if ( $filedata )
				{
					$data = array_merge( $data, $filedata );	
				}
			}
		}
		else
		{
			$data = NULL;
		}
		return $data;
	}

	protected function retrieve( $file )
	{
		$parts = pathinfo( $file );
		$data = array();
		
		if ( $cachedData = $this->app['cache']->get( 'data', $file, filemtime($file) ) )
		{
			return $cachedData;
		}
		
		// no cache or cache is outdated
		if ( isset($this->extensions_map[strtolower($parts['extension'])]))
		{
			$parser = 'parse_' . $this->extensions_map[strtolower($parts['extension'])];
			
			if ( method_exists( $this, $parser ) )
			{
				$data = $this->$parser( $file );
			}
	
			$this->app['cache']->set( 'data', $file, $data ); // save to cache
		}
		return $data;
	} 

	protected function parse_csv( $path )
	{
		$config = $this->app['config']['data']['csv'];
		
		try
		{
			$row = 1;
			$data_array = array();
			$headers = array();
			$id_col = FALSE;
			
			if ( strpos($path,'http') === 0 ) {
				// external url
				$handle = fopen('php://temp', 'w+');
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_FILE, $handle);
				curl_exec($curl);
				curl_close($curl);
				rewind($handle);
			} else {
				// local file
				$handle = fopen($path, "r");
			}
			
			if ( $handle !== FALSE )
			{
			    while (($data = fgetcsv($handle, 0, $config['delimiter'], $config['enclosure'], $config['escape'] )) !== FALSE)
				{
					$has_headers = $config['headers'];
					if ( $row == 1 && $has_headers )
					{
						$headers = $data; // set headers
						$id_col = array_search($config['id_header'], $headers );						
					}
					elseif ( $has_headers )
					{
						$row_data = array();
						for ( $i = 0; $i < count( $data ); $i++ )
						{
							$row_data[$headers[$i]] = $data[$i];
						}
						if ( $id_col !== FALSE )
						{
							$data_array[$data[$id_col]] = $row_data;
						}
						else
						{
							$data_array[] = $row_data;
						}
					}
					else
					{
						$data_array[] = $data;
					}
					$row++;
			    }
			    fclose($handle);
			}
			return $data_array;
		}
		catch( \Exception $e )
		{			
            throw new Exception('CSV data format error in ' . $path);
		}
	}
	
	protected function parse_yml( $path )
	{
		try
		{
			if ( strpos($path, 'http') === 0 ) {
				$data = $this->make_external_request($path);
			} else {
				$data = file_get_contents($path);
			}
			$yaml = new Yaml();
			$data = $yaml->parse($data);
			return $data;
		}
		catch( \Exception $e )
		{
            throw new Exception('Yaml data format error in ' . $path);
		}
	}
	
	protected function parse_json( $path )
	{
		try
		{
			if ( strpos($path, 'http') === 0 ) {
				$data = $this->make_external_request($path);
			} else {
				$data = file_get_contents($path);
			}
			$data = json_decode($data, true);
			return $data;
		}
		catch( \Exception $e )
		{
            throw new Exception('JSON data format error in ' . $path);
		}
	}
    
	protected function make_external_request( $url )
	{
		if ( $cachedData = $this->app['cache']->get( 'data', $url, strtotime('- ' . $this->app['config']['request_cache_expiry'] . ' minutes') ) )
		{
			return $cachedData;
		}
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $url);
		$data = curl_exec($ch);
		curl_close($ch);
		
		$this->app['cache']->set( 'data', $url, $data ); // save to cache
		
		return $data;
	}
}

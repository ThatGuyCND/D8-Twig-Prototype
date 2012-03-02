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
		
		if ( $cachedData = $this->app['cache']->get( $file ) )
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
	
			$this->app['cache']->set( $file, $data ); // save to cache
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
			if ( ( $handle = fopen($path, "r") ) !== FALSE )
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
			$yaml = new Yaml();
			$data = $yaml->parse(file_get_contents($path));
			return $data;	
		}
		catch( \Exception $e )
		{
            throw new Exception('Yaml data format error in ' . $path);
		}
	}
    
}

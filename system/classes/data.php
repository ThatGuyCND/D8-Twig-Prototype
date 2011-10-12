<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Data {
	
	protected $data = array();
	
	protected static $instance;
	
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
        /// nothing to see here!
    }

    public function load_all( $data_path, $cache_path = NULL )
    {
		// grab data from the data dir or data cache as appropriate
		
		$datafiles = glob( DATA_PATH . '*' );
		
		if ( count( $datafiles ) )
		{
			foreach( $datafiles as $file )
			{
				$parts = pathinfo( $file );
				$file_mtime = filemtime( $file );
				
				$cache_file = $cache_path . $parts['filename'] . '.cache';
				
				// TODO: Extract caching into standalone class?
				if ( file_exists( $cache_file ) && $file_mtime < filemtime( $cache_file ) )
				{
					// cached version is newer, use that
					$this->data[$parts['filename']] = unserialize( file_get_contents( $cache_file ) );
				}
				else
				{
					// no cache or cache is outdated
					$parser = 'parse_' . $parts['extension'];
				
					if ( method_exists( $this, $parser ) )
					{
						$this->data[$parts['filename']] = $this->$parser( $file );
					}					
				}
			}
			
			if ( $cache_path )
			{
				// save to cache
				foreach( $this->data as $name => $data )
				{
					file_put_contents( $cache_path . $name . '.cache', serialize($data) );
				}
			}
		}
		
        return $this->data;
    }

    public function find( $filename, $key )
    {
        return isset( $this->data[$filename][$key] ) ? $this->data[$filename][$key] : NULL;
    }

	protected function parse_csv( $path )
	{
		try
		{
			$row = 1;
			$data_array = array();
			$headers = array();
			$id_col = FALSE;
			if ( ( $handle = fopen($path, "r") ) !== FALSE )
			{
			    while (($data = fgetcsv($handle, 0, Config::get('csv_delimiter'), Config::get('csv_enclosure'), Config::get('csv_escape') )) !== FALSE)
				{
					$has_headers = Config::get('csv_headers');
					if ( $row == 1 && $has_headers )
					{
						$headers = $data; // set headers
						$id_col = array_search(Config::get('csv_id_header'), $headers );
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
		catch( Exception $e )
		{
            throw new Exception('CSV data format error in ' . $path);
		}
	}
	
	protected function parse_yml( $path )
	{
		try
		{
			$yaml = new sfYamlParser();
			$data = $yaml->parse(file_get_contents($path));
			return $data;	
		}
		catch( Exception $e )
		{
            throw new Exception('Yaml data format error in ' . $path);
		}
	}
    
}
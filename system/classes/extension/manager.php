<?php

Class Extension_manager {	
	
	protected static $instance;
	
	protected $extensions;
	
	protected $extobjs = array();
	
	public static function instance()
	{
		if ( ! isset( self::$instance ) )
		{
			$c = __CLASS__;
			self::$instance = new $c();
			self::$instance->load_extensions();
		}
		return self::$instance;
	}
    
    protected function __construct()
    {
        /// Nothing to see here! Class is acting as a singleton.
    }

	public function load_extensions()
	{
		$extensions = Config::get('extensions');

		if ( count($extensions) )
		{
			foreach( $extensions as $extension )
			{
				if ( file_exists( EXTENSIONS_PATH . $extension . '.php' ) )
				{
					$class_name = ucwords( $extension ) . '_extension';
					include_once( EXTENSIONS_PATH . $extension . '.php' );
					if ( class_exists( $class_name ) )
					{
						$obj = new $class_name();
						if ( $obj instanceof Extension )
						{
							$this->extensions[] = $class_name;
						}
					}
				}
			}
		}
	}
	
	public function run_hook( $name, $args )
	{
		if ( count( $this->extensions) )
		{
			$output = NULL;
			foreach( $this->extensions as $extension )
			{
				$obj = new $extension();
				$method = 'hook_' . $name;
				if ( method_exists( $obj, $method ) )
				{
					$current_args = $args;
					$current_args[] = $output;
					$output = $obj->run_hook($method, $args);
				}
			}
		}
	}
	
	public function get_actions( $request )
	{
		$actions = array();
		if ( count( $this->extensions) )
		{
			foreach( $this->extensions as $extension )
			{
				$actions[strtolower(str_replace('_extension','',$extension))] = new $extension( $request );
			}
		}
		return $actions;
	}
	
}
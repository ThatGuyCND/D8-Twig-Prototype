<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Extension {
		
	public function __call( $name, $args )
	{
		$method = 'action_' . $name;
		if ( method_exists($this, $method ))
		{
			return call_user_func_array(array($this, $method), $args);
		}
	}
	
	public function run_hook( $hook, $args )
	{
		return call_user_func_array(array($this, $hook), $args);
	}

}
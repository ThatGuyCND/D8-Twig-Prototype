<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Example_extension extends Extension {
    
    // Hooks
    
    protected function hook_before_render( $request )
    {
	
    }
    
    protected function hook_before_display( $request )
    {
	
    }

	// Actions

    protected function action_test()
    {
        return 'test';
    }
	
}


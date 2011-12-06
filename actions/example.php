<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Example_actions extends Actions {
	
	// Add methods to be made available to templates here 
	
	public function sayhello()
	{
	    return 'Hello';
	}
	
}


<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

Class Actions {
    
	protected $uri;
	
	protected $view;
		
	protected $store;
	
	protected $pages;
	
	public function __construct( $request )
	{
		$this->request = $request;
		$this->uri = $request->uri;
		$this->view = $request->view;
		$this->store = $request->store;
		$this->pages = $request->pages;
	}

}
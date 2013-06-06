<?php

namespace Prontotype\Service;

class Request {
	
	protected $app;
	
    public function __construct( $app )
    {
        $this->app = $app;
    }
    
    public function method()
    {
        return $this->app['request']->getMethod();
    }
	
	public function query()
	{
		return $this->app['request']->query;
	}
	
	public function post()
	{
		return $this->app['request']->request;
	}
    
}

<?php

namespace Prontotype\Service;

class Request {
	
	protected $app;
	
    public function __construct( $app )
    {
        $this->app = $app;
    }
	
	public function query()
	{
		return $this->app['request']->query;
	}
	
	public function queryString()
	{
		return $_SERVER['QUERY_STRING'];
	}	
	
	public function post()
	{
		return $this->app['request']->request;
	}
	
	public function headers()
	{
		return $this->app['request']->headers;
	}
    
}

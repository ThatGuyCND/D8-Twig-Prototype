<?php

namespace Prontotype\Data;

Class Parser {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getHandledExtensions()
    {
        return array();
    }
    
    public function parse($content)
    {
        
    }
    
}

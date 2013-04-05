<?php

namespace Prontotype\Extension;

class Base {
    
    public function __construct( $app )
    {
        $this->app = $app;
    }
        
    public function globals()
    {
        return array();
    }
    
    public function before()
    {
           
    }
    
    public function after()
    {
        
    }
    
}

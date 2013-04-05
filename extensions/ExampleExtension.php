<?php

class ExampleExtension extends Prontotype\Extension\Base {
    
    public function globals()
    {
        return array(
            'greet' =>'Hello!'
        );
    }
    
    public function before()
    {
        // called before the request is processed
    }
    
    public function after()
    {
        // called after the request is processed
    }
    
}

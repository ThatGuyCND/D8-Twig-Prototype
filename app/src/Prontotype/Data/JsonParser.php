<?php

namespace Prontotype\Data;

Class JsonParser extends Parser {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getHandledExtensions()
    {
        return array(
            'json'
        );
    }
    
    public function parse($content)
    {
        if ( ! is_string($content) ) {
            throw new \Exception('JSON data format error');
        }
        try {
           return json_decode($content, true);
        } catch( \Exception $e ) {
            throw new \Exception('JSON data format error');
        }
    }
    
}

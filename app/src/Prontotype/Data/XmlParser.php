<?php

namespace Prontotype\Data;

Class XmlParser extends Parser {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getHandledExtensions()
    {
        return array(
            'xml'
        );
    }
    
    public function parse($content)
    {
        if ( ! is_string($content) ) {
            throw new \Exception('XML data format error');
        }
        try {
            $data = simplexml_load_string($content);
            return json_decode(json_encode($data),true);
        } catch( \Exception $e ) {
            throw new \Exception('XML data format error');
        }
    }    
}

<?php

namespace Prontotype\Data;

use Symfony\Component\Yaml\Yaml;

Class YamlParser extends Parser {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function getHandledExtensions()
    {
        return array(
            'yml',
            'yaml'
        );
    }
    
    public function parse($content)
    {
        if ( ! is_string($content) ) {
            throw new \Exception('YAML data format error');
        }
        if ( empty($content) ) {
            return array();
        }
        try {
            return Yaml::parse($content);
        } catch( \Exception $e ) {
            throw new \Exception('YAML data format error');
        }
    }
    
}

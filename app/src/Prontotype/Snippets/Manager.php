<?php

namespace Prontotype\Snippets;

Class Manager {

    protected $app;
    
    protected $registry = array();

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function __get($name)
    {
        $name = $this->getClassName($name);
        if ( isset($this->registry[$name]) ) {
            return $this->registry[$name];
        }
        if ( class_exists($name) ) {
            $this->registry[$name] = new $name($this->app);
            return $this->registry[$name];
        }
        return null;
    }
    
    public function __isset($name)
    {
        $name = $this->getClassName($name);
        if ( class_exists($name) ) {
            return true;
        }
        return false;
    }
    
    protected function getClassName($name)
    {
        return 'Prontotype\Snippets\\' . ucfirst(strtolower($name));
    }
    
}
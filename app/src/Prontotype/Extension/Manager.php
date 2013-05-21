<?php

namespace Prontotype\Extension;

class Manager
{
    
    public function __construct($extpath, $app)
    {
        $this->app = $app;
        $this->path = rtrim($extpath,'/');
        $this->extensions = array();
    }
        
    public function loadExtensions($extensions)
    {   
        if ( count($extensions) ) {
            foreach( $extensions as $extensionKey => $extensionFile ) {
                $extPath = $this->path . '/' . $extensionFile;
                if ( file_exists($extPath) ) {
                    require_once $extPath;
                    $pathInfo = pathinfo($extPath);
                    $extName = $pathInfo['filename'];
                    $extension = new $extName($this->app);
                    $this->extensions[$extensionKey] = $extension;
                }
            }
            $this->app['twig']->addGlobal('ext', $this->extensions);
        }
    }
    
    public function before()
    {
        foreach($this->extensions as $extension) {
            $extension->before();
        }
    }
    
    public function after()
    {
        foreach($this->extensions as $extension) {
            $extension->after();
        }
    }
        
}
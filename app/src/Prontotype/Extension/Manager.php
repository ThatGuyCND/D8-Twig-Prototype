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
            $allGlobals = array();
            foreach( $extensions as $extensionKey => $extensionFile ) {
                $extPath =  $this->path . '/' . $extensionFile;
                if ( file_exists($extPath) ) {
                    require_once $extPath;
                    $pathInfo = pathinfo($extPath);
                    $extName = $pathInfo['filename'];
                    $extension = new $extName($this->app);
                    $globals = $extension->globals();
                    if ( $globals || is_array($globals) && count($globals) ) {
                        $this->app['twig']->addGlobal($extensionKey, $globals);
                    }
                    $this->extensions[] = $extension;
                }
            }
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

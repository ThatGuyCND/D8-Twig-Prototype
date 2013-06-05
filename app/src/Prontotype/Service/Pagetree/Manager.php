<?php

namespace Prontotype\Service\PageTree;

Class Manager {
    
    protected $app;
    
    protected $tree;
    
    protected $current = null;
    
    public function __construct( $app )
    {
        $this->app = $app;
        $this->tree = new Directory(new \SPLFileInfo(PAGES_PATH), $app);
    }
    
    public function getCurrent()
    {
        if ( ! $this->current ) {
            $this->current = $this->getByUrlPath($this->app['uri']->string());
        }
        return $this->current;
    }
    
    public function getById( $id )
    {
        foreach( $this->getRecursivePagesIterator() as $page ) {
            if ( $page->getId() == $id ) {
                return $page;
            }
        }
        return null;
    }
    
    public function getByUrlPath( $path )
    {
        foreach( $this->getRecursivePagesIterator() as $page ) {
            if ( $page->matchesUrlPath($path) ) {
                return $page;
            }
        }
        return null;
    }
    
    public function getUrlById( $id )
    {
        if ( $page = $this->getById($id) ) {
            return $page->getUrlPath();
        }
        return '#';
    }
    
    public function getAll()
    {
        return $this->tree->toArray();
    }
    
    public function getAllUnderId( $id )
    {
        if ( $page = $this->getById($id) ) {
            return $this->getAllUnderUrlPath($page->getUrlPath());
        }
        return null;
    }
    
    public function getAllUnderUrlPath( $urlPath )
    {
        $fullTree = new \RecursiveIteratorIterator($this->tree, true);
        foreach( $fullTree as $item ) {
            if ( $item instanceof Directory && $item->matchesUrlPath($urlPath) ) {
                return $item->getItems();
            }
        }
    }
    
    // protected getDirectory()
    
    protected function getRecursivePagesIterator()
    {
        return new \RecursiveIteratorIterator($this->tree, \RecursiveIteratorIterator::LEAVES_ONLY);
    }
    
}
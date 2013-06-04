<?php

namespace Prontotype\Service\PageTree;

use SPLFileInfo;
use DirectoryIterator;
use Exception;

Class Base implements \RecursiveIterator
{
    protected $items = array();
    protected $position = 0;
    protected $app;
    
    public function __construct( SPLFileInfo $file, $basePath, $app )
    {
        $this->app = $app;
        $basePath = '/' . trim($basePath, '/');
        $this->fullPath = $file->getPath() . '/' .  $file->getBasename();
        $this->relPath = str_replace($basePath, '', $this->fullPath);
        $this->pathInfo = pathinfo($this->fullPath);
        // echo '<pre>';
        // print_r($this->pathInfo);
        // echo '</pre>';
    }
    
    public function getFullPath()
    {
        return $this->fullPath;
    }
    
    public function getRelPath()
    {
        return $this->relPath;
    }

    protected function isValidFile( SPLFileInfo $item )
    {
        return ( ! $item->isLink() && ! $item->isDot() && strpos($item->getBasename(), '.') !== 0 );
    }

    public function getItems()
    {
        return $this->items;
    }
    
    public function hasItems()
    {
        return count($this->items);
    }

    public function valid() {
        return isset($this->items[$this->position]);
    }

    public function hasChildren() {
        $current = $this->items[$this->position];
        return ( $current instanceof Directory && $current->hasItems() );
    }
    
    public function getChildren() {
        if ( $this->hasChildren() ) {
            return $this->items[$this->position];            
        }
        return array();
    }

    public function next() {
        $this->position++;
    }
    
    public function current() {
        return $this->items[$this->position];
    }

    public function rewind() {
        $this->position = 0;
    }

    public function key() {
        return $this->position;
    }
    
    public function titleCase( $title )
    { 
        $smallwordsarray = array('of','a','the','and','an','or','nor','but','is','if','then','else','when','at','from','by','on','off','for','in','out','over','to','into','with');
        $words = explode(' ', $title); 
        foreach ($words as $key => $word) { 
            if ($key == 0 or !in_array($word, $smallwordsarray)) {
                $words[$key] = ucwords(strtolower($word)); 
            }
        }
        $newtitle = implode(' ', $words); 
        return $newtitle; 
    }
    
}

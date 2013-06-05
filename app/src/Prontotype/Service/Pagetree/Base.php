<?php

namespace Prontotype\Service\PageTree;

use SPLFileInfo;
use DirectoryIterator;
use Exception;

Class Base implements \RecursiveIterator
{
    protected $app;
    
    protected $urlPath = null;
    
    protected $relPath = null;
    
    protected $fullPath = null;
    
    protected $pathInfo = null;
    
    protected $items = array();
    
    protected $position = 0;
    
    protected $nameFormatRegex = '/^((\d*)[\._\-])?([^\[]*)?(\[([\d\w-_]*?)\][\._\-]?)?(.*?)$/';
    
    protected $nameExtension = 'twig';
    
    public function __construct( SPLFileInfo $file, $app )
    {
        $this->app = $app;
        $this->fullPath = $file->getPath() . '/' .  $file->getBasename();
        $this->relPath = str_replace(PAGES_PATH, '', $this->fullPath);
        $this->templatePath = str_replace(TEMPLATES_PATH, '', $this->fullPath);
        $this->pathInfo = pathinfo($this->fullPath);
    }
    
    public function getFullPath()
    {
        return $this->fullPath;
    }
    
    public function getRelPath()
    {
        return $this->relPath;
    }
    
    public function getTemplatePath()
    {
        return $this->templatePath;
    }
    
    public function getUrlPath()
    {
        if ( $this->urlPath === null ) {
            $segments = explode('/', trim($this->getRelPath(),'/'));
            $cleanSegments = array();
            foreach( $segments as $segment ) {
                preg_match($this->nameFormatRegex, str_replace('.' . $this->nameExtension, '', $segment), $segmentParts);
                $cleanSegments[] = empty($segmentParts[3]) ? $segmentParts[6] : $segmentParts[3];
            }
            if ( $cleanSegments[count($cleanSegments)-1] == 'index' ) {
                unset($cleanSegments[count($cleanSegments)-1]);
            }
            $this->urlPath = $this->prefixUrl('/' . implode('/', $cleanSegments));
        }
        return $this->urlPath;
    }
    
    public function matchesUrlPath($urlPath)
    {
        $urlPath = '/' . trim($urlPath,'/');
        return $this->getUrlPath() == $urlPath;
    }
    
    protected function prefixUrl( $url )
    {
        $prefix = '';
        if ( ! empty($this->app['config']['index']) ) {
            $prefix = '/' . $this->app['config']['index'];
        }
        return $prefix . $url;
    }
    
    protected function unPrefixUrl( $url )
    {
        if ( ! empty($this->app['config']['index']) ) {
            return str_replace('/' . $this->app['config']['index'], '', $url);
        }
        return $url;
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

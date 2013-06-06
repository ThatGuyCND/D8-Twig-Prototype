<?php

namespace Prontotype\PageTree;

use SPLFileInfo;
use DirectoryIterator;
use Exception;

Class Base implements \RecursiveIterator
{
    protected $app;
    
    protected $niceName = null;
    
    protected $cleanName = null;
    
    protected $depth = null;
    
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
        $this->relPath = str_replace($app['pt.prototype.paths.pages'], '', $this->fullPath);
        $this->templatePath = str_replace($app['pt.prototype.paths.templates'], '', $this->fullPath);
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
    
    public function getUnPrefixedUrlPath()
    {
        return $this->unPrefixUrl($this->getUrlPath());
    }
    
    public function matchesUrlPath($urlPath)
    {
        $urlPath = '/' . trim($urlPath,'/');
        return $this->unPrefixUrl($this->getUrlPath()) == $this->unPrefixUrl($urlPath);
    }
    
    public function getDepth()
    {
        if ( ! $this->depth ) {
            $urlPath = $this->unPrefixUrl($this->getUrlPath());
            if ( $urlPath == '/' ) {
                $this->depth = 0;
            } else {
                $this->depth = count(explode('/',trim($urlPath,'/')));
            }
        }
        return $this->depth;
    }
    
    public function getNiceName()
    {
        if ( $this->niceName === null ) {
            $this->makeNiceName();
        }
        return $this->niceName;
    }
    
    public function getCleanName()
    {
        if ( $this->cleanName === null ) {
            $this->parseFileName();
        }
        return $this->cleanName;
    }
    
    protected function prefixUrl($url)
    {
        $prefix = '';
        if ( ! $this->app['config']['clean_urls'] ) {
            $prefix = '/index.php';
        }
        return $prefix . $url;
    }
    
    protected function unPrefixUrl($url)
    {
        if ( ! $this->app['config']['clean_urls'] ) {
            return str_replace('/index.php', '', $url);
        }
        return $url;
    }

    protected function isValidFile(SPLFileInfo $item)
    {
        return ( ! $item->isLink() && ! $item->isDot() && strpos($item->getBasename(), '.') !== 0 );
    }
        
    protected function parseFileName()
    {
        preg_match($this->nameFormatRegex, $this->pathInfo['filename'], $parts);
        $this->id = ! empty($parts[5]) ? $parts[5] : '';
        $this->position = ! empty($parts[2]) ? $parts[2] : 0;
        $cleanName = empty($parts[3]) ? $parts[6] : $parts[3];
        if ( $cleanName == 'index' ) {
            $this->isIndex = true;
            $segments = explode('/',trim($this->getUnPrefixedUrlPath(),'/'));
            if ( isset($segments[count($segments)-1]) ) {
                $cleanName = $segments[count($segments)-1];
            }
        }
        $this->cleanName = $cleanName;
    }
    
    protected function makeNiceName()
    {
        $cleanName = $this->getCleanName();
        
        $name = null;
        if ( count($this->app['config']['name_overrides']) ) {
            foreach( $this->app['config']['name_overrides'] as $path => $niceName ) {
                if ($this->unPrefixUrl($path) == $this->unPrefixUrl($this->getUrlPath())) {
                    $name = $niceName;
                    break;
                }
            }
        }
        
        if ( ! $name ) {
            $name = $cleanName;
        }
        
        $this->niceName = $this->titleCase($name);
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
    
    public function titleCase($title)
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
    
    // public function __get($name)
    // {
    //     $getter = 'get' . ucfirst($name);
    //     if ( method_exists($this, $getter) ) {
    //         return $this->$getter();
    //     }
    // }
    
}

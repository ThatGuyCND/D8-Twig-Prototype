<?php

namespace Prontotype\Service\PageTree;

use SPLFileInfo;
use DirectoryIterator;
use Exception;

Class Page extends Base {
        
    protected $id = null;
    
    protected $position = null;
    
    protected $niceName = null;
    
    protected $cleanName = null;
    
    protected $depth = null;
    
    protected $shortUrl = null;
        
    public function __construct( SPLFileInfo $file, $app )
    {
        if ( ! $file->isFile() ) {
            throw new \Exception('File is not a file');
        }
        parent::__construct($file, $app);
    }
    
    public function getId()
    {
        if ( $this->id === null ) {
            $this->parseFileName();
        }
        return empty($this->id) ? null : $this->id;
    }
    
    public function getShortUrl()
    {
        if ( $id = $this->getId() ) {
            $this->shortUrl = $this->prefixUrl('/' . $this->app['config']['triggers']['shorturl'] . '/' . $id);
        } else {
            $this->shortUrl = $this->getUrlPath();
        }
        return $this->shortUrl;
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
    
    public function isIndex()
    {
        return $this->getCleanName() == 'index';
    }
    
    public function isCurrent()
    {
        return $this->matchesUrlPath($this->app['uri']->string());
    }
    
    public function isParentOfCurrent()
    {
        $uriSegments = $this->app['uri']->segments();
        $urlPathSegments = explode('/', trim($this->unPrefixUrl($this->getUrlPath()),'/'));
        if ( count($urlPathSegments) >= count($uriSegments) ) {
            return false;
        }
        for ( $i = 0; $i < count($urlPathSegments); $i++ ) {
            if ( $uriSegments[$i] !== $urlPathSegments[$i] ) {
                return false;
            }
        }
        return true;
    }
    
    public function toArray()
    {
        return array(
            'id'        => $this->getId(),
            'depth'     => $this->getDepth(),
            'shortUrl'  => $this->getShortUrl(),
            'niceName'  => $this->getNiceName(),
            'name'      => $this->getCleanName(),
            'urlPath'   => $this->getUrlPath(),
            'relPath'   => $this->getRelPath(),
            'fullPath'  => $this->getFullPath(),
            'isCurrent' => $this->isCurrent(),
            'isParentOfCurrent' => $this->isParentOfCurrent(),
            'isPage'    => true,
        );
    }
    
    protected function parseFileName()
    {
        preg_match($this->nameFormatRegex, $this->pathInfo['filename'], $parts);
        $this->id = ! empty($parts[5]) ? $parts[5] : '';
        $this->position = ! empty($parts[2]) ? $parts[2] : 0;
        $this->cleanName = empty($parts[3]) ? $parts[6] : $parts[3];
    }
    
    protected function makeNiceName()
    {
        if ( $this->cleanName === null ) {
            $this->parseFileName();
        }
        if ( $this->cleanName == 'index' ) {
            $name = null;
            if ( count($this->app['config']['nice_names']['specific']) ) {
                foreach( $this->app['config']['nice_names']['specific'] as $path => $niceName ) {
                    if (trim($path,'/') == trim($this->getUrlPath(),'/')) {
                        $name = $niceName;
                        break;
                    }
                }
            }
            if ( ! $name ) {
                $name = $this->app['config']['nice_names']['default'];
            }
        } else {
            $name = $this->cleanName;
        }
        $this->niceName = $this->titleCase($name);
    }
        
}

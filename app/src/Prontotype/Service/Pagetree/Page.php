<?php

namespace Prontotype\Service\PageTree;

use SPLFileInfo;
use DirectoryIterator;
use Exception;

Class Page extends Base {
    
    protected $nameFormatRegex = '/^((\d*)[\._\-])?([^\[]*)?(\[([\d\w-_]*?)\][\._\-]?)?(.*?)$/';
    
    protected $nameExtension = 'twig';
    
    protected $id = null;
    
    protected $position = null;
    
    protected $urlPath = null;
    
    protected $niceName = null;
    
    protected $cleanName = null;
    
    protected $depth = null;
        
    public function __construct( SPLFileInfo $file, $basePath, $app )
    {
        if ( ! $file->isFile() ) {
            throw new \Exception('File is not a file');
        }
        parent::__construct($file, $basePath, $app);
    }
    
    public function getId()
    {
        if ( $this->id === null ) {
            $this->parseFileName();
        }
        return empty($this->id) ? null : $this->id;
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
            $this->urlPath = '/' . implode('/', $cleanSegments);
        }
        return $this->urlPath;
    }
    
    public function getDepth()
    {
        if ( ! $this->depth ) {
            $urlPath = $this->getUrlPath();        
            $this->depth = count(explode('/',trim($urlPath,'/')));            
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

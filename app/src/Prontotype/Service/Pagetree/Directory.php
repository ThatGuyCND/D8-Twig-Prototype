<?php

namespace Prontotype\Service\PageTree;

use SPLFileInfo;
use DirectoryIterator;
use Exception;

Class Directory extends Base {
    
    protected $urlPath;
    
    public function __construct( SPLFileInfo $directory, $app )
    {
        if ( ! $directory->isDir() ) {
            throw new Exception('Not a directory');
        }
        parent::__construct($directory, $app);
        $items = array();
        foreach( new DirectoryIterator($this->fullPath) as $item ) {
            if ( $this->isValidFile($item) ) {
                $path = $item->getPath() . '/' .  $item->getBasename();
                if ( $item->isDir() ) {
                    $items[] = new Directory($item, $app);
                } else {
                    $items[] = new Page($item, $app);
                }
            }
        }
        uasort($items, function( $a, $b ){          
            return strnatcasecmp($a->getRelPath(), $b->getRelPath());
        });
        $this->items = $items;
    }
    
    public function toArray($siblings = null)
    {
        $children = array();
        $output = array(
            "isPage" => false,
            "relPath" => $this->getRelPath(),
            "fullPath" => $this->getFullPath(),
            'urlPath' => $this->getUrlPath()
        );
        $hasIndex = false;
        foreach( $this as $item ) {
            if ( $item instanceof Page && $item->isIndex() ) {
                $hasIndex = true;
                $output = $item->toArray();
            } else {
                $children[] = $item->toArray($this->getItems());
            }
        }
        if ( ! $hasIndex && $siblings ) {
            foreach( $siblings as $sibling ) {
                if ( $sibling instanceof Page && $this->getUrlPath() == $sibling->getUrlPath() ) {
                    $output = $sibling->toArray();
                    break;
                }
            }
        }
        $filteredChildren = array();
        foreach( $children as $child ) {
            if ( ! isset($filteredChildren[$child['urlPath']]) ) {
                $filteredChildren[$child['urlPath']] = $child;
            } else {
                if ( ! isset($filteredChildren[$child['urlPath']]['children']) || ! count($filteredChildren[$child['urlPath']]['children']) ) {
                    $filteredChildren[$child['urlPath']] = $child;
                }
            }
        }
        $children = $filteredChildren;
        uasort($children, function( $a, $b ){          
            return strnatcasecmp($a['relPath'], $b['relPath']);
        });
        $output['children'] = array_values($children);
        return $output;
    }
}

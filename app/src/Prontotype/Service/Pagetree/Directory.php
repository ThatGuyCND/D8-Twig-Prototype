<?php

namespace Prontotype\Service\PageTree;

use SPLFileInfo;
use DirectoryIterator;
use Exception;

Class Directory extends Base {
    
    public function __construct( SPLFileInfo $directory, $basePath, $app )
    {
        if ( ! $directory->isDir() ) {
            throw new Exception('Not a directory');
        }
        parent::__construct($directory, $basePath, $app);
        $items = array();
        foreach( new DirectoryIterator($this->fullPath) as $item ) {
            if ( $this->isValidFile($item) ) {
                $path = $item->getPath() . '/' .  $item->getBasename();
                if ( $item->isDir() ) {
                    $items[] = new Directory($item, $basePath, $app);
                } else {
                    $items[] = new Page($item, $basePath, $app);
                }
            }
        }
        uasort($items, function( $a, $b ){          
            return strnatcasecmp($a->getRelPath(), $b->getRelPath());
        });
        $this->items = $items;
    }
    
    
}

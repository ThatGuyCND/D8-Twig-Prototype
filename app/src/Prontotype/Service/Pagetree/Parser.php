<?php

namespace Prontotype\Service\PageTree;

Class Parser {
    
    public function __construct( $rootPath )
    {
        $pageTree = new Tree($rootPath);
    }
    
}
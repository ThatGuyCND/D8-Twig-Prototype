<?php

namespace Prontotype\Snippets;

Class Navigation extends Base {
    
    protected $templatePath = 'pt/snippets/navigation';
    
    protected $configKey = 'navigation';

    public function pageTree($opts = array(), $tree = null, $level = 0)
    {
        $opts = $this->mergeOpts(array(
            'type'         => 'ul',
            'attrs'        => array(),
            'maxDepth'     => null,
            'currentClass' => 'is-current',
            'parentClass'  => 'is-parent',
        ), $opts);
        
        if ( $tree === null ) {
            $tree = $this->app['pt.pagetree']->getAll();
        }
        if ( ! count($tree) ) {
            return null;
        }
        return $this->renderTemplate('page-tree.twig', array(
            'pages' => $tree,
            'level' => $level,
            'opts' => $opts
        ));
    }
    
}
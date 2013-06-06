<?php

namespace Prontotype\PageTree;

Class Manager {
    
    protected $app;
    
    protected $tree;
    
    protected $treeArray = null;
    
    protected $current = null;
    
    public function __construct($app)
    {
        $this->app = $app;
        $this->tree = new Directory(new \SPLFileInfo($app['pt.prototype.paths.pages']), $app);
    }
    
    public function getCurrent()
    {
        if ( ! $this->current ) {
            $this->current = $this->getByUrlPath($this->app['pt.request']->getUrlPath());
        }
        return $this->current;
    }
    
    public function getById($id)
    {
        foreach( $this->getRecursivePagesIterator() as $page ) {
            if ( $page->getId() == $id ) {
                return $page;
            }
        }
        return null;
    }
    
    public function getByUrlPath($path)
    {
        foreach( $this->getRecursivePagesIterator() as $page ) {
            if ( $page->matchesUrlPath($path) ) {
                return $page;
            }
        }
        return null;
    }
    
    public function getByRoute($route)
    {
        // lets see if we need to do any checking of custom routes
        $routes = $this->app['config']['routes'] ? $this->app['config']['routes'] : array();
        $replacements = array();
        if ( count($routes) ) {
            foreach( $routes as $routeSpec => $endRoute ) {
                // see if there are any page ID placeholders that need parsing out
                $replacements = array();
                if ( preg_match('/\(:id=([^\)]*)\)/', $routeSpec, $matches) ) {
                    if ( $routePage = $this->getById($matches[1]) ) {
                        $replacements[] = trim($routePage->getUnPrefixedUrlPath(),'/');
                        $routeSpec = str_replace(
                            array($matches[0],'index.php'),
                            array($routePage->getUrlPath(),''),
                            $routeSpec
                        );
                    } else {
                        continue;
                    }
                }
                $routeSpec = trim($routeSpec,'/');
                // replace helper placeholders
                $routeSpec = str_replace(
                    array('(:any)','(:num)','(:all)','/'),
                    array('([^/]*)','(\d*)','(.*)','\/'),
                    $routeSpec
                );
                $routeSpec = '/^' . $routeSpec . '$/';
                if ( preg_match( $routeSpec, $route, $matches ) ) {
                    // we have a match!
                    for( $i = 0; $i < count($matches); $i++ ) {
                        if ( $i !== 0) {
                            $replacements[] = $matches[$i];
                        }
                    }
                    $route = $endRoute;
                    break;
                }
            }
                
            // replace and reference tokens in the route
            // '(:id=test)/hello': '$1'
            $replacementTokens = array();
            for ( $j = 0; $j < count($replacements); $j++ ) {
                $replacementTokens['$' . ($j+1)] = $replacements[$j];
            }
            $route = str_replace(array_keys($replacementTokens), array_values($replacementTokens), $route);
            
            // replace any page ID placeholders in the route itself
            if ( preg_match('/\(:id=(.*)\)/', $route, $matches) ) {
                $routePage = $this->getById($matches[1]);
                $route = str_replace(array($matches[0],'index.php'), array($routePage->getUrlPath(),''), $route);
            }
        }
        
        return $this->getByUrlPath(str_replace('//', '/', $route));
    }
    
    public function getUrlById($id)
    {
        if ( $page = $this->getById($id) ) {
            return $page->getUrlPath();
        }
        return '#';
    }
    
    public function getAll()
    {
        if ( $this->treeArray === null ) {
            $this->treeArray = array($this->tree->toArray());
        }
        return $this->treeArray;
    }
    
    public function getChildrenById($id)
    {
        if ( $page = $this->getById($id) ) {
            return $this->getAllUnderUrlPath($page->getUrlPath());
        }
        return null;
    }
    
    public function getChildrenByUrlPath($urlPath)
    {
        if ( $urlPath == '/' || $urlPath == '/index.php/' ) {
            $data = $this->getAll();
            return isset($data[0]['children']) ? $data[0]['children'] : null;
        }
        $fullTree = new \RecursiveIteratorIterator($this->tree, true);
        foreach( $fullTree as $item ) {
            if ( $item instanceof Directory && $item->matchesUrlPath($urlPath) ) {
                $data = $item->toArray();
                return $data['children'];
            }
        }
        return null;
    }
    
    protected function getRecursivePagesIterator()
    {
        return new \RecursiveIteratorIterator($this->tree, \RecursiveIteratorIterator::LEAVES_ONLY);
    }
    
}
<?php

namespace Prontotype;

Class Request {
    
    protected $app;
    
    protected $request;
    
    protected $urlSegments = null;

    public function __construct( $app )
    {
        $this->app = $app;
        $this->request = $app['request'];
        $this->query = $this->request->query;
        $this->post = $this->post->query;
    }
    
    public function getUrlPath()
    {
        return $this->unPrefixUrl($this->getRawUrlPath());
    }
    
    public function getRawUrlPath()
    {
        list($path) = explode('?', $this->getRequestUri());
        return $path;
    }
    
    public function getUrlSegments()
    {
        if ( $this->urlSegments !== null ) {
            return $this->urlSegments;  
        }
        
        $path = trim($this->getUrlPath(),'/');
        
        if ( ! empty($path) ) {
            $this->urlSegments = explode('/', $path);            
        } else {
            $this->urlSegments = array();
        }
        
        return $this->urlSegments;
    }
    
    public function getUrlSegment($pos)
    {
        $segments = $this->getUrlSegments();
        return isset($segments[$pos]) ? $segments[$pos] : null;
    }
    
    public function getQueryString($override = null)
    {
        $qs = $this->request->getQueryString();
        if ( empty($override) || ! is_array($override) ) {
            return $qs;
        }
        
        $qsArray = $this->request->query->all();
        
        foreach ( $override as $name => $value ) {
            $qsArray[$name] = $value;
        }
        
        return http_build_query($qsArray);
    }
    
    public function getQueryParams()
    {
        return $this->request->query;
    }
    
    public function getPostParams()
    {
        return $this->request->request;
    }
    
    public function __call($name, $args)
    {
        if ( method_exists( $this->request, $name ) ) {
            return call_user_func_array(array($this->request, $name), $args);
        }
    }
    
    public function __toString()
    {
        return $this->getUrlPath();
    }
    
    protected function unPrefixUrl($url)
    {
        return str_replace('/index.php', '', $url);
    }
    
}

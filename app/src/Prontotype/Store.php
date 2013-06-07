<?php

namespace Prontotype;

Class Store {
    
    protected $app;
    
    protected $cookiePrefix = '';

    protected $cookieLifetime = '';

    public function __construct( $app )
    {
        $this->app = $app;
        $this->cookiePrefix = $app['pt.config']['cookie']['prefix'];
        $this->cookieLifetime = $app['pt.config']['cookie']['lifetime'];
    }
    
    public function set( $key, $value )
    {
        // raw url encode and set raw cookie used here to prevent issues with spaces encoded as '+'
        $value = rawurlencode(json_encode($value));
        setrawcookie( $this->cookiePrefix . $key, $value, time() + $this->cookieLifetime, '/' );
        $_COOKIE[$this->cookiePrefix . $key] = $value;
    }
    
    public function get( $key )
    {
        return isset($_COOKIE[$this->cookiePrefix . $key]) ? json_decode(rawurldecode(stripslashes($_COOKIE[$this->cookiePrefix . $key])), true) : NULL;
    }
    
    public function clear( $key )
    {
        setcookie( $this->cookiePrefix . $key, '', time() - 3600, '/' );
        unset($_COOKIE[$this->cookiePrefix . $key]);
    }

}

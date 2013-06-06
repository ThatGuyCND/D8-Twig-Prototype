<?php

namespace Prontotype\Service;

Class Store {
    
    protected $cookie_prefix = '';
    
    protected $app;

    public function __construct( $app )
    {
        $this->app = $app;
        $this->cookie_prefix = $app['config']['prefix'];
        $this->cookie_lifetime = $app['config']['cookie_lifetime'];
    }
    
    public function set( $key, $value )
    {
        // raw url encode and set raw cookie used here to prevent issues with spaces encoded as '+'
        $value = rawurlencode(json_encode($value));
        setrawcookie( $this->cookie_prefix . $key, $value, time() + $this->cookie_lifetime, '/' );
        $_COOKIE[$this->cookie_prefix . $key] = $value;
    }
    
    public function get( $key )
    {
        return isset($_COOKIE[$this->cookie_prefix . $key]) ? json_decode(rawurldecode(stripslashes($_COOKIE[$this->cookie_prefix . $key]))) : NULL;
    }
    
    public function clear( $key )
    {
        setcookie( $this->cookie_prefix . $key, '', time() - 3600, '/' );
        unset($_COOKIE[$this->cookie_prefix . $key]);
    }

}

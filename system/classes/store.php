<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

// session/persistent storage handling, uses unencrypted cookies so that they can be read via JS as well

class Store {
	
	protected $cookie_prefix = '';
	    
    public function __construct()
    {
        $this->cookie_prefix = Config::get('prefix');
    }
	
	public function set( $key, $value )
	{
		// raw url encode and set raw cookie used here to prevent issues with spaces encoded as '+'
		$value = rawurlencode(json_encode($value));
		setrawcookie( $this->cookie_prefix . $key, $value, time() + Config::get('cookie_lifetime'), '/' );
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
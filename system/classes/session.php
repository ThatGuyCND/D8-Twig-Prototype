<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

// session handling, uses unencrypted cookies so that they can be read via JS as well

class Session {
	
	protected $cookie_prefix = '';
	
	protected $user_cookie_name = 'user';
	
	protected $userdata = NULL;
    
    public function __construct()
    {
        $this->cookie_prefix = Config::get('cookie_prefix');
    }

    public function set_state()
    {
    	// handle stuff like login in/out as required
		// also load in appropriate user data from cookie store etc
		
		$lit = Config::get('login_trigger');
		$lot = Config::get('logout_trigger');
		
		if ( isset( $_REQUEST[$lit] ) )
		{
			$this->login( $_REQUEST[$lit] );
		}
		elseif ( isset( $_REQUEST[$lot] ) )
		{
			$this->logout();
		}
		
		// check if a user cookie is set
		
		if ( $this->retrieve( $this->user_cookie_name ) )
		{
			$this->userdata = $this->retrieve( $this->user_cookie_name );
		}
    }

	public function login( $user_id )
	{
		$this->store( $this->user_cookie_name, $this->get_user_details( $user_id ) );
	}
	
	public function logout()
	{
		$this->clear( $this->user_cookie_name );
	}
	
    public function userdata()
    {
        return $this->userdata;
    }
	
	public function store( $key, $value )
	{
		// raw url encode and set raw cookie used here to prevent issues with spaces encoded as '+'
		$value = rawurlencode(json_encode($value));
		setrawcookie( $this->cookie_prefix . $key, $value, time() + Config::get('cookie_lifetime'), '/' );
		$_COOKIE[$this->cookie_prefix . $key] = $value;
	}
	
	public function retrieve( $key )
	{
		return isset($_COOKIE[$this->cookie_prefix . $key]) ? json_decode($_COOKIE[$this->cookie_prefix . $key]) : NULL;
	}
	
	public function clear( $key )
	{
		setcookie( $this->cookie_prefix . $key, '', time() - 3600, '/' );
		unset($_COOKIE[$this->cookie_prefix . $key]);
	}
		
	// trys to find a user's details with a specific id. If not found it just returns the ID.
	protected function get_user_details( $user_id )
	{
		$data = Data::instance();
		
		$user = $data->find('users', $user_id);
		
		return $user ? $user : array( 'id' => $user_id );
	}
}
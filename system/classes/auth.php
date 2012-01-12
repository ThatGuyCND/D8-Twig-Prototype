<?php

Class Auth {
	
	protected $request;
	
	protected $salt = '49h3rqihe1uihd109u23eo2oodj2d2';
	
	public function __construct( $request )
	{
		$this->request = $request;
	}
	
	public function authenticate()
	{
		$auth_password = Config::get('auth_password');

		if ( ! $auth_password )
		{
			return true;
		}
		
		$authVal = sha1($this->salt . $auth_password);
		$cookieName = Config::get('prefix') . 'auth';
		if ( isset( $_POST['auth_password'] ) && $_POST['auth_password'] === $auth_password )
		{
			// sumbitted via form, password matches
			setcookie( $cookieName, $authVal, time() + Config::get('cookie_lifetime'), '/' );
			return true;
		}
		elseif ( isset( $_POST['auth_password'] ) )
		{
			$this->request->view->add_global('msg','The password entered was incorrect');
			return false;
		}
		elseif ( ! isset( $_POST['auth_password'] ) && isset($_COOKIE[$cookieName]) )
		{
			if ( $_COOKIE[$cookieName] === $authVal )
			{
				return true;
			}
		}
		
		return false;
	}
	
}

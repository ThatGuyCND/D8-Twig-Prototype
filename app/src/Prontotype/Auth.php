<?php

namespace Prontotype;

Class Auth {
    
    protected $app;
    
    protected $excludePaths = array();
    
    protected $authSessionName;

    public function __construct( $app )
    {
        $this->app = $app;
        $this->excludePaths = array(
            $app['pt.utils']->generateUrlPath('auth.login'),
            $app['pt.utils']->generateUrlPath('auth.check'),
            $app['pt.utils']->generateUrlPath('auth.logout')
        );
        $this->authSessionName = $this->app['config']['cookie']['prefix'] . 'authed';
    }
    
    public function check()
    {        
        if ( in_array($this->app['pt.request']->getRawUrlPath(), $this->excludePaths) ) {
            return true; // no auth required for this URL
        }
        
        return $this->isAuthed();
    }
    
    public function attemptLogin($password)
    {
        if ( $this->app['request']->get('password') === $this->app['config']['authenticate']['password'] ) {
            $this->app['session']->set($this->authSessionName, $this->hashPassword());
            return true;
        }
        
        $this->app['session']->getFlashBag()->set('error', 'Your password was incorrect');
        $this->logout();
        
        return false;
    }
    
    public function logout()
    {
        $this->app['session']->remove($this->authSessionName);
    }
    
    public function isAuthed()
    {
        if ( ! $this->isAuthRequired() || in_array($_SERVER['REMOTE_ADDR'], $this->getWhitelistedIps()) ) {
            return true; // no auth required, or IP is in whitelist
        }
        if ( $this->app['session']->get($this->authSessionName) === $this->hashPassword() ) {
            return true; // user is already logged in
        }
        $this->logout();
        return false;
    }
    
    protected function hashPassword()
    {
        return sha1($this->app['config']['authenticate']['password']);
    }
    
    protected function isAuthRequired()
    {
        return ! empty($this->app['config']['authenticate']['password']);
    }
    
    protected function getWhitelistedIps()
    {
        $ipWhitelist = $this->app['config']['authenticate']['ip_whitelist'];
        if ( is_array($ipWhitelist) ) {
            return $ipWhitelist;
        } elseif ( is_string($ipWhitelist) ) {
            return array($ipWhitelist);
        }
        return array();
    }
    
}
<?php

namespace Prontotype;

Class UserManager {

    protected $app;
    
    protected $users;
    
    protected $identifyBy;
    
    protected $authBy;
    
    protected $currentUser = null;
    
    protected $userCookieName = 'user';

    public function __construct($app)
    {
        $this->app = $app;
        $this->users = $this->app['pt.data']->load('users');
        $this->identifyBy = $this->app['pt.config']['user']['identify'];
        $this->authBy = ! empty($this->app['pt.config']['user']['auth']) ? $this->app['pt.config']['user']['auth'] : null;
    }
    
    public function userIsLoggedIn()
    {
        if ( ! $user = $this->getCurrentUser() ) {
            return false;
        }
        if ( $userData = $this->getUserBy($this->identifyBy, $user[$this->identifyBy]) ) {
            if ( ! $this->authBy || @$user[$this->authBy] == @$userData[$this->authBy] ) {
                return true;
            }
        }
        return false;
    }
    
    public function attemptLogin($identity, $auth = null)
    {
        if ( $userData = $this->getUserBy($this->identifyBy, $identity) ) {
            if ( ! $this->authBy || $auth == @$userData[$this->authBy] ) {
                $this->app['pt.store']->set($this->userCookieName, $userData);
                return true;
            }
        }
        $this->app['pt.notifications']->setFlash('error', $this->app['pt.config']['user']['login']['error']);
        return false;
    }
    
    public function logoutUser()
    {
        $this->currentUser = null;
        $this->app['pt.store']->clear($this->userCookieName);
    }
    
    public function getCurrentUser()
    {
        if ( $this->currentUser === null ) {
            $this->currentUser = $this->app['pt.store']->get($this->userCookieName);
        }
        return $this->currentUser;
    }
    
    public function getUserBy($key, $val)
    {
        foreach( $this->users as $userData ) {
            if ( @$userData[$key] == $val ) {
                return $userData;
            }
        }
        return null;
    }
    
    public function getLoginRedirectUrlPath($override = null)
    {
        if ( $override ) {
            return $override;
        }
        return ! empty($this->app['pt.config']['user']['login']['redirect']) ? $this->app['pt.config']['user']['login']['redirect'] : '/';
    }
    
    public function getLogoutRedirectUrlPath($override = null)
    {
        if ( $override ) {
            return $override;
        }
        return ! empty($this->app['pt.config']['user']['logout']['redirect']) ? $this->app['pt.config']['user']['logout']['redirect'] : '/';
    }
    
}




// $app['store']->clear('user');
//         
// if ( $request->get('redirect') ) {
//     return $app->redirect($request->get('redirect'));
// } else {
//     return $app->redirect('/'); // redirect to homepage
// }
<?php

namespace Prontotype;

Class UserManager {

    protected $app;
    
    protected $users;
    
    protected $identifyBy;
    
    protected $authBy;
    
    protected $userCookieName = 'user';

    public function __construct($app)
    {
        $this->app = $app;
        $this->users = $this->app['pt.data']->load('users');
        // $this->identify = $this->app['pt.config']['']
    }
    
    public function userIsLoggedIn()
    {
        if ( ! $user = $this->app['pt.store']->get($this->userCookieName) ) {
            return false;
        }
        foreach( $this->users as $userData ) {
            
        }
    }
    
    public function attemptLogin($params)
    {
        
    }
    
    public function logoutUser()
    {
        $this->app['pt.store']->clear($this->userCookieName);
    }
    
    public function getCurrentUser()
    {
        return $this->app['pt.store']->get($this->userCookieName);
    }
    
    public function getUserBy($key, $val)
    {
        
    }
    
}


// $user_id = $request->get('user');
// if ( ! $user_id ) {
//     $user = true;
// } else {
//     $user = $app['data']->find('users.' . $user_id);
//     $user = $user ? $user : true;
// }
//         
// $app['store']->set('user', $user);
//         
// if ( $request->get('redirect') ) {
//     return $app->redirect($request->get('redirect'));
// } else {
//     return $app->redirect('/'); // redirect to homepage
// }




// $app['store']->clear('user');
//         
// if ( $request->get('redirect') ) {
//     return $app->redirect($request->get('redirect'));
// } else {
//     return $app->redirect('/'); // redirect to homepage
// }
<?php

namespace Prontotype;

Class UserManager {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function userIsLoggedIn()
    {
        
    }
    
    public function attemptLogin()
    {
        
    }
    
    public function logoutUser()
    {
        
    }
    
    public function getCurrentUser()
    {
        
    }
    
    public function getUserByUsername()
    {
        
    }
    
    public function getUserByEmail()
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
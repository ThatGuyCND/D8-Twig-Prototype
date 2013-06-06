<?php

namespace Prontotype;

Class Auth {
    
    protected $app;

    public function __construct( $app )
    {
        $this->app = $app;
    }
    
    public function check()
    {
            // if ( $app['session']->has($app['config']['prefix'] . 'authed-user') ) {
//                 
//             }
        return true;
    }
    
    public function attemptLogin($userName, $password)
    {
            // if ( $app['request']->get('username') === $app['config']['authenticate']['username'] && $app['request']->get('password') === $app['config']['authenticate']['password'] ) {
//                 $userHash = $userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
//                 $currentUser = $app['session']->set( $app['config']['prefix'] . 'authed-user', $userHash );
//                 return $app->redirect('/');
//             } else {
//                 $app['session']->getFlashBag()->set('error', 'error');
//                 $app['session']->remove( $app['config']['prefix'] . 'authed-user' );
//                 return $app->redirect($app['pt.request']->generateUrlPath('authenticate'));
//             }
    }
    
    public function logout()
    {
        // $app['session']->remove( $app['config']['prefix'] . 'authed-user' );
    }

}


// $authPage = array(
//     $app['uri']->generate('authenticate'),
//     $app['uri']->generate('de_authenticate')
// );
// 
// $ip_whitelist = $app['config']['authenticate']['ip_whitelist'];
// if ( (is_array($ip_whitelist) && in_array($_SERVER['REMOTE_ADDR'], $ip_whitelist)) || is_string($ip_whitelist) && $_SERVER['REMOTE_ADDR'] ===  $ip_whitelist) {
//     $authRequired = false;
// } else {
//     $authRequired = ( ! empty($app['config']['authenticate']) && ! empty($app['config']['authenticate']['username']) && ! empty($app['config']['authenticate']['password']) ) ? true : false;       
// }
// 
// if ( ! in_array($app['request']->getRequestUri(), $authPage) ) {
//     if ( $authRequired ) {
//         $currentUser = $app['session']->get( $app['config']['prefix'] . 'authed-user' );
//         $userHash = sha1($app['config']['authenticate']['username'] . $app['config']['authenticate']['password']);
//         if ( empty( $currentUser ) || $currentUser !== $userHash ) {
//             return $app->redirect($app['uri']->generate('authenticate')); // not logged in, redirect to auth page
//         }
//     }
// } elseif ( ! $authRequired ) {
//     // redirect visits to the auth pages to the homepage if no auth is required.    
//     return $app->redirect('/');
// }
<?php

namespace Prontotype;

Class Notifications {

    protected $app;

    public function __construct($app)
    {
        $this->app = $app;
    }
    
    public function setFlash($name, $content)
    {
        $this->app['session']->getFlashBag()->set('notifications', array(
            $name => $content
        ));
    }
    
    public function get($name)
    {
        $notifications = $this->app['session']->getFlashBag()->get('notifications');
        return isset($notifications[$name]) ? $notifications[$name] : null;
    }
    
    public function getAll()
    {
        $notifications = $this->app['session']->getFlashBag()->set('notifications');
        return $notifications;
    }
}
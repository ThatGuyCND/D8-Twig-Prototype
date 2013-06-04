<?php

namespace Prontotype\Service;

class Exporter {
	
	protected $app;
	
    public function __construct( $app )
    {
        $this->app = $app;
    }
    
    public function generate()
    {
        $pages = $this->app['pages']->getAll();
        echo '<pre>';
        print_r($pages);
        echo '</pre>';
        return;
        foreach( $pages as $page ) {
            echo '<pre>';
            print_r($page['nice_name']);
            echo '</pre>';
            
        }
    }
    
}

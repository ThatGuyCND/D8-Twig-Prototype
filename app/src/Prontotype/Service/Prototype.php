<?php

namespace Prontotype\Service;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\Yaml\Yaml;

class Prototype implements ServiceProviderInterface {
    
    protected $app;

    public function register ( Application $app )
    {
        $this->app = $app;
        
        
        
        // $app['paths.base_path'] = 

        // $config = array();
        // 
        // $filename = DOC_ROOT . "/prototypes.yml";
        // 
        // $default_prototype = array(
        //     'domain' => 'default',
        //     'base_path' => '.',
        //     'config_file' => 'config.yml',
        // );
        // 
        // if (!file_exists($filename)) {
        //     $app['prototype'] = $default_prototype;
        // 
        //     return;
        // }
        // 
        // $config = Yaml::parse($filename);
        // 
        // if (null === $config) {
        //     throw new \InvalidArgumentException(sprintf("The config file '%s' appears to be invalid YAML.", $filename));
        // }
        // 
        // $app['prototypes'] = $config['prototypes'];
        // 
        // $host = $_SERVER['HTTP_HOST'];
        // 
        // // set default prototype
        // $current = $app['prototypes']['default'];
        // 
        // // identify current prototype
        // foreach ($app['prototypes'] as $key => $prototype) {
        //     if ($prototype['domain'] == $host) {
        //         $current = $prototype;
        //         $current['key'] = $key;
        //     }
        // }
        // 
        // // complete current prototype config with default keys if missing
        // foreach (array_keys($default_prototype) as $key) {
        //     if (!isset($current[$key])) {
        //         $current[$key] = $default_prototype[$key];
        //     }
        // }
        // 
        // $app['prototype'] = $current;
    }

    public function boot ( Application $app ) {}

}

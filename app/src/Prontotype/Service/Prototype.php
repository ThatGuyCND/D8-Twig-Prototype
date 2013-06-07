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
        
        $ptDefinitionsPath = DOC_ROOT . '/prototypes.yml';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : $_SERVER['SERVER_NAME'];
        $ptConfig = null;
        
        if ( ! file_exists( $ptDefinitionsPath ) ) {
            throw new \Exception('The require prototypes.yml file does not exist.');
        }
        
        $ptDefinitions = Yaml::parse($ptDefinitionsPath);        
        if (null === $ptDefinitions) {
            throw new \Exception(sprintf("The config file '%s' appears to be invalid YAML.", $filename));
        }
        
        foreach( $ptDefinitions as $label => $definition ) {
            $matches = is_array($definition['matches']) ? $definition['matches'] : array($definition['matches']);
            $regexp = '/^(';
            $regexp .= implode('|', array_map(function($value){
                return str_replace(array('.','*'), array('\.','(.*)'), $value);
            }, $matches));
            $regexp .= ')/';
            if ( preg_match($regexp, $host, $matches) ) {
                $replacements = array_slice($matches, 2);                
                $ptConfig = $definition;
                $replacementTokens = array();
                for ( $j = 0; $j < count($replacements); $j++ ) {
                    $replacementTokens['$' . ($j+1)] = $replacements[$j];
                }
                $ptLabel = $label;
                $ptConfig['prototype'] = str_replace(array_keys($replacementTokens), array_values($replacementTokens), $ptConfig['prototype']);
                break;
            }
        }
        
        if ( ! $ptConfig ) {
            throw new \Exception(sprintf("Could not find matching prototype definition for '%s'.", $host));
        }
        
        $ptDirPath = PROTOYPES_PATH . '/' . $ptConfig['prototype'];
        
        if ( ! file_exists($ptDirPath) ) {
            throw new \Exception(sprintf("Prototype directory '%s' does not exist.", $ptDirPath));
        }
        
        $app['pt.prototype.label'] = $label;
        $app['pt.prototype.environment'] = $ptConfig['environment'];
        
        $app['pt.prototype.paths.root'] = $ptDirPath;
        $app['pt.prototype.paths.templates'] = $app['pt.prototype.paths.root'] . '/templates';
        $app['pt.prototype.paths.pages'] = $app['pt.prototype.paths.templates'] . '/pages';
        $app['pt.prototype.paths.data'] = $app['pt.prototype.paths.root'] . '/data';
        $app['pt.prototype.paths.config'] = $app['pt.prototype.paths.root'] . '/config';
        $app['pt.prototype.paths.extensions'] = $app['pt.prototype.paths.root'] . '/extensions';
        
        $config = array(
            APP_PATH . '/config.yml',
            $app['pt.prototype.paths.config'] . '/common.yml',    
        );
        $envConfig = $app['pt.prototype.paths.config'] . '/' . $app['pt.prototype.environment'] . '.yml';
        if ( file_exists( $envConfig ) ) {
            $config[] = $envConfig;
        }
        $app->register(new \Silextend\Config\YamlConfig($config));
        $app['pt.config'] = $app['config'];
    }

    public function boot ( Application $app ) {}

}

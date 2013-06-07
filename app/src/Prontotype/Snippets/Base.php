<?php

namespace Prontotype\Snippets;

Class Base {

    protected $app;
    
    protected $templatePath = 'pt/snippets';
    
    protected $configKey = null;
    
    protected $config = array();

    public function __construct($app)
    {
        $this->app = $app;
        if ( $this->configKey ) {
            $this->config = isset($this->app['config']['snippets'][$this->configKey]) ? $this->app['config']['snippets'][$this->configKey] : array();
        }
    }
    
    protected function mergeOpts($defaults, $opts)
    {
        if ( ! $opts ) $opts = array();
        return array_merge($defaults, $opts);
    }
    
    protected function renderTemplate($filename, $data = array())
    {
        return $this->app['twig']->render($this->templatePath . '/' . $filename, $data);
    }
    
}
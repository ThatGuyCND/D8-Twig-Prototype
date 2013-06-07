<?php

namespace Prontotype\Snippets;

Class Images extends Base {
    
    protected $templatePath = 'pt/snippets/images';
    
    protected $configKey = 'images';
    
    protected $placeholderUrls = array(
        'dummyimage'  => "http://dummyimage.com/{{ width }}x{{ height }}/{{ bgcolor }}/{{ fgcolor }}/{{ format }}&text={{ text }}",
        'placeholdit' => "http://placehold.it/{{ width }}x{{ height }}/{{ bgcolor }}/{{ fgcolor }}/{{ format }}&text={{ text }}",
        'lorempixel'  => "http://lorempixel.com/{{ width }}/{{ height }}/{{ category }}/{{ text }}",
    );
    
    public function placeholder($opts = array(), $attrs = array())
    {
        $opts = $this->mergeOpts(array(
            'width'    => $this->config['placeholder']['width'],
            'height'   => $this->config['placeholder']['height'],
            'service'  => $this->config['placeholder']['service'],
        ), $opts);
        
        if ( isset($this->placeholderUrls[$this->config['placeholder']['service']]) ) {
            $attrs['src'] = $this->app['twig.stringloader']->render($this->placeholderUrls[$this->config['placeholder']['service']], $opts);
        } else {
            return '';
        }
        
        return $this->image($attrs);
    }
    
    public function image($attrs)
    {
        return $this->renderTemplate('image.twig', array(
            'attrs' => $attrs
        ));
    }
    
}
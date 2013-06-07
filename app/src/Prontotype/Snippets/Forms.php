<?php

namespace Prontotype\Snippets;

Class Forms extends Base {
    
    protected $templatePath = 'pt/snippets/forms';
    
    protected $configKey = 'forms';
    
    public function open($attrs = array())
    {
        $attrs = $this->mergeOpts(array(
            'method' => 'get',
        ), $attrs);
        
        return $this->renderTemplate('open.twig', array(
            'attrs' => $attrs
        ));
    }
    
    public function close()
    {
        return $this->renderTemplate('close.twig');
    }
    
    public function label($text = 'Label', $target = null, $attrs = array())
    {
        $attrs = $this->mergeOpts(array(
            'for'   => $target
        ), $attrs);
        
        return $this->renderTemplate('label.twig', array(
            'text'   => $text,
            'attrs'  => $attrs,
        ));
    }
    
    public function button($text = 'Submit', $attrs = array())
    {
        $attrs = $this->mergeOpts(array(
            'type' => 'submit'
        ), $attrs);
        
        return $this->renderTemplate('button.twig', array(
            'text'   => $text,
            'attrs'  => $attrs,
        ));
    }
    
    public function input($name = null, $value = null, $type = 'text', $attrs = array())
    {
        $attrs = $this->mergeOpts(array(
            'name'  => $name,
            'type'  => $type,
            'value' => $value,
        ), $attrs);
        
        return $this->renderTemplate('input.twig', array(
            'attrs'  => $attrs,
        ));
    }
    
    public function select($name = null, $value = null, $opts = array(), $attrs = array())
    {
        $attrs = $this->mergeOpts(array(
            'name' => $name,
        ), $attrs);
        
        return $this->renderTemplate('select.twig', array(
            'value' => $value,
            'opts'  => $opts,
            'attrs' => $attrs,
        ));
    }
    
    public function textarea($name = null, $value = null, $attrs = array())
    {
        $attrs = $this->mergeOpts(array(
            'name' => $name,
        ), $attrs);
        
        return $this->renderTemplate('textarea.twig', array(
            'value' => $value,
            'attrs' => $attrs,
        ));
    }
    
    // public function control($type = 'input', $label = null, $name = null, $value = null, $opts = array(), $attrs = array())
    // {
    //     return $this->renderTemplate('control.twig', array(
    //         'type'  => $type,
    //         'label' => $label,
    //         'name'  => $name,
    //         'value' => $value,
    //         'opts'  => $opts,
    //         'attrs' => $attrs,
    //     ));
    // }
    
}
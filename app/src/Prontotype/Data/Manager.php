<?php

namespace Prontotype\Data;

Class Manager {

    protected $app;
    
    protected $parsers = array();
    
    protected $parsed = array();

    public function __construct($app, $parsers = array())
    {
        $this->app = $app;
        foreach( $parsers as $parser ) {
            $this->registerParser($parser);
        }
    }
    
    public function get($location, $dataPath = null, $type = null) {
        if ( strpos($location, 'http') !== 0 ) {
            return $this->load($location, $dataPath, $type);
        } else {
            return $this->fetch($location, $dataPath, $type);
        }
    }
    
    public function load($filePath, $dataPath = null, $type = null)
    {
        if ( isset($this->parsed[$filePath]) ) {
            $data = $this->parsed[$filePath];
        } else {
            $filePath = $this->app['pt.prototype.paths.data'] . '/'. trim($filePath,'/');
            if ( file_exists($filePath) ) {
                $dataFiles = array($filePath);
            } else {
                $dataFiles = glob( $filePath . '.*' );
            }
            $data = array();
            if ( count( $dataFiles ) ) {
                foreach( $dataFiles as $file ) {
                    $parts = pathinfo($file);
                    $extension = ! $type ? $parts['extension'] : $type;
                    $contents = file_get_contents($file);
                    try {
                        $newData = $this->parse($contents, $extension);
                        $data = $this->merge($data, $newData);                    
                    } catch ( \Exception $e ) {
                        throw new \Exception(sprintf('Error parsing data file %s', $filePath));
                    }
                }
            }  else {
                $data = null;
            }
            $this->parsed[$filePath] = $data;
        }
        return $this->find($data, $dataPath);
    }
    
    public function fetch($url, $dataPath = null, $type = null)
    {
        if ( isset($this->parsed[$url]) ) {
            $data = $this->parsed[$url];
        } else {
            $data = $this->app['pt.utils']->fetchFromUrl($url);
            if ( !empty($data['body']) ) {
                if ( ! $type ) {
                    $type = $this->getExtensionFromMimeType($data['mime']);
                }
                $data = $this->parse($data['body'], $type);
            } else {
                $data = null;
            }
            $this->parsed[$url] = $data;
        }
        return $this->find($data, $dataPath);
    }
        
    public function registerParser(Parser $parser)
    {
        foreach( $parser->getHandledExtensions() as $extension ) {
            $extension = strtolower($extension);
            if ( ! isset($this->parsers[$extension]) ) {
                $this->parsers[$extension] = array();
            }
            $this->parsers[$extension][] = $parser;
        }
    }
    
    protected function find($data, $path)
    {
        if ( empty($data) ) {
            return null;
        }
        if ( empty($path) ) {
            return $data;
        }
        $pathparts = explode( '.', trim( $path, '.') );
        if ( count( $pathparts) ) {
            foreach ( $pathparts as $key ) {
                if ( isset( $data[$key] ) ) {
                    $data = $data[$key];
                } else {
                    $data = null;
                    break;
                }
            }
        }
        return $data;
    }
    
    protected function parse($contents, $extension)
    {
        $extension = strtolower($extension);
        if ( ! isset($this->parsers[$extension]) ) {
            return $contents;
        }
        foreach( $this->parsers[$extension] as $parser ) {
            try {
                $contents = $parser->parse($contents);
            } catch ( \Exception $e ) {
                throw new \Exception(sprintf('Error parsing file'));
            }
        }
        return $contents;
    }
    
    protected function merge($old, $new)
    {
        if ( gettype($old) !== gettype($new) ) {
            throw new \Exception('Could not merge data');
        }
        if ( is_array($old) ) {
            return array_merge($new,$old);    
        }
        if ( is_string($old) ) {
            return $old . $new;
        }
        throw new \Exception('Could not merge data');
    }
    
    protected function getExtensionFromMimeType($mime)
    {
        if ( strpos($mime, 'json') !== false ) {
            return 'json';
        }
        if ( strpos($mime, 'html') !== false ) {
            return 'html';
        }
        if ( strpos($mime, 'csv') !== false ) {
            return 'csv';
        }
        if ( strpos($mime, 'yml') !== false ) {
            return 'yml';
        }
        if ( strpos($mime, 'xml') !== false ) {
            return 'xml';
        }
        return 'txt';
    }
    
}

<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

class Helpers {
    
    // http://www.php.net/manual/en/function.file-put-contents.php#84180
    public static function file_force_contents($dir, $contents)
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach($parts as $part)
        {
            if(!is_dir($dir .= "/$part"))
            {
                mkdir($dir);  
                chmod($dir, 0771);
            }
        }
        file_put_contents("$dir/$file", $contents);
        chmod("$dir/$file", 0644);
    }

    public static function file_force_contents($dir, $contents)
    {
        $parts = explode('/', $dir);
        $file = array_pop($parts);
        $dir = '';
        foreach($parts as $part)
        {
            if(!is_dir($dir .= "/$part"))
            {
                mkdir($dir);  
                chmod($dir, 0771);
            }
        }
        file_put_contents("$dir/$file", $contents);
        chmod("$dir/$file", 0644);
    }

}
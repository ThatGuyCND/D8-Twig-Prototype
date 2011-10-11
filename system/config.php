<?php defined('SYSTEM_PATH') or exit('No direct script access allowed');

return array(
        
    'debug'                 => FALSE,
	'auto_reload'			=> TRUE,
	'charset'               => 'utf-8',
    'file_extension'    	=> 'html',
    'index_file'            => 'index.php',

	'caching'               => TRUE,
	
	'login_trigger'			=> 'login',
	'logout_trigger'        => 'logout',
	
	'cookie_prefix'        	=> 'prototype_',
	'cookie_lifetime'      	=> 60*60*24*7, // 1 week
	
	'csv_headers'			=> TRUE,
	'csv_delimiter'			=> ',',
	'csv_enclosure'			=> '"',
	'csv_escape'			=> '\\',
	
);

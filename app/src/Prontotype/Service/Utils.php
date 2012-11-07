<?php

namespace Prontotype\Service;

class Utils {
	
	public function random( $min = 0, $max = null )
	{
		if ( $max === null ) $max = getrandmax();
		return rand( $min, $max );
	}
	
	public function list_templates( $dir )
	{
		$dir = TEMPLATES_PATH . '/' . $dir;
		$result = array();
		$dirContents = scandir($dir);
		foreach( $dirContents as $item )
		{
			if ( $item !== '.' && $item !== '..' && ! is_dir($dir . $item) )
			{
				$result[] = pathinfo($item);
			}
		}
		return $result;
	}
	
}
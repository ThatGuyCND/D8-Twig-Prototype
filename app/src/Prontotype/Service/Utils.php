<?php

namespace Prontotype\Service;

class Utils {
	
	public function __construct()
	{
	
	}
	
	public function random( $min = 0, $max = null )
	{
		if ( $max === null ) $max = getrandmax();
		return rand( $min, $max );
	}

}
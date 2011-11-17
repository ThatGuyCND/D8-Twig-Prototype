<?php

Class Utils {
	
	function random( $min = 0, $max = null )
	{
		if ( $max === null ) $max = getrandmax();
		return rand( $min, $max );
	}
		
}
<?php

Class Helpers {	
	
	public static function get_highlighted_source($fileName, $lineNumber, $showLines)
	{
		$lines = @file_get_contents($fileName);
		$lines = highlight_string($lines, true);
		$lines = explode("<br />", $lines);

		$lines = str_replace( array('style="color: #0000BB"','style="color: #007700"','style="color: #DD0000"','style="color: #FF8000"'),	array('class="php-default"','class="php-keyword"','class="php-string"','class="php-comment"'), $lines );

		$offset = max(0, $lineNumber - ceil($showLines / 2));
		$lines = array_slice($lines, $offset, $showLines);
		$html = '';
		
		foreach ($lines as $line)
		{
			$offset++;
			$line = '<em class="line-number">' . sprintf('%4d', $offset) . ' </em>' . $line . '';
			if ($offset == $lineNumber)
			{
				$html .= '<div class="line main">' . $line . '</div>';
			}
			else
			{
				$html .= '<div class="line">' . $line . '</div>';
			}
		}

		return $html;
	}

	public static function title_case( $title )
	{ 
		$smallwordsarray = array('of','a','the','and','an','or','nor','but','is','if','then','else','when','at','from','by','on','off','for','in','out','over','to','into','with');
		
		$words = explode(' ', $title); 
		foreach ($words as $key => $word) 
		{ 
			if ($key == 0 or !in_array($word, $smallwordsarray))
			{
				$words[$key] = ucwords(strtolower($word)); 
			}
		}
		
		$newtitle = implode(' ', $words); 
		return $newtitle; 
	}

}

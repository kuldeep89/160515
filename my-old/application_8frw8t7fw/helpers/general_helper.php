<?php
	
	/*******************************
	**	Neat Trim
	**
	**	Description:
	**	Trims a string to desired length, not splitting words.
	**
	**	@param:		string
	**	@return:	string
	**
	**  Author: Thomas Melvin
	**
	**/
	if( !function_exists('neat_trim') ) {	
		
		function neat_trim($str, $n, $delim='...') {                                                                                                                                                          
			
			$len = strlen($str);
			
			if ($len > $n) {
				preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches);
				return rtrim($matches[1]) . $delim;
			}
			else {
				return $str;
			}
			
		}
		
	}
	
?>
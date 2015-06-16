<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('get_templates')) {

	function get_templates() {
	
		$CI	=& get_instance();
		
		
		
		////////////////
		// Get Directory Listing
		////////////////
		$path			= dirname(dirname(__FILE__)).'/views/backend/pages/pages/templates/';
		$arr_contents	= array();
		
		if ( $handle = opendir($path) ) {

		    while (false !== ($entry = readdir($handle))) {
		        
		        if( preg_match('/^\..*/', $entry) == FALSE ) {
			        $arr_contents[]	= $entry;
		        }
		        
		    }

		    closedir($handle);
			
			return $arr_contents;
		    
		} 
		
	}
	
}
<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Get include JS libs
if (!function_exists('is_active_nav')) {

	function is_active_nav($page) {
		
		$CI	=& get_instance();
		if( $CI->router->class == $page ) {
			return TRUE;
		}
		
	}
	
}
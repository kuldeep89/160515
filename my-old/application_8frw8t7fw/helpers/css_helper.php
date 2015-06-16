<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// Get custom JS
if (!function_exists('page_level_styles')) {

	function page_level_styles($page_type = 'dashboard') {
	

		
	}
		
}

if( !function_exists('category_color') ) {

	function category_color( $category_id ) {
		
		$arr_category_styles	= array('green', 'red', 'purple', 'blue', 'yellow', 'grey');
		return $arr_category_styles[$category_id%6];
		
	}
	
}

if( !function_exists('category_icon') ) {
	
	function category_icon( $category_id ) {
		
		$arr_category_icons	= array('icon-globe', 'icon-beaker', 'icon-bullhorn', 'icon-briefcase', 'icon-trophy');
		return $arr_category_icons[$category_id%5];
		
	}
	
}
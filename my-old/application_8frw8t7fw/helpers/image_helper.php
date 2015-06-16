<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

if(!function_exists('image_thumbnail_path')) {

	function image_thumbnail_path( $image_name ) {

		$arr_pieces	= explode('.', $image_name);
		$ext		= array_pop($arr_pieces);
		
		$name		= implode('.', $arr_pieces);
		$name		= $name.'_thumb.'.$ext;
		
		return image_path($name);
	}
}

if(!function_exists('profile_image_thumbnail')) {

	function profile_image_thumbnail( $image_url ) {

		$thumb_data = pathinfo($image_url);

		return $thumb_data['dirname'].'/'.$thumb_data['filename'].'_thumb.'.$thumb_data['extension'];
		
	}
}

if(!function_exists('remove_cdn_image')) {
	function remove_cdn_image( $image_data ) {

		$CI =& get_instance();

		$CI->load->library('opencloud');

		switch( $image_data['namespace'] ) {
		
			case 'academy':
				$CI->opencloud->set_container('saltsha_academy');
			break;
			case 'profile':
				$CI->opencloud->set_container('saltsha_profile');
			break;
			default:
				$CI->opencloud->set_container('saltsha');
			break;
		}

		// Get file name
		$file_data = pathinfo($image_data['img_url']);
		$file_name = $file_data['basename'];
		$thumb_name = $file_data['filename'].'_thumb.'.$file_data['extension'];

		// Remove image and thumbnail
		if ($CI->opencloud->delete_object($file_name) && $CI->opencloud->delete_object($thumb_name)) {
			return true;
		} else {
			return false;
		}
	}
}
<?php
global $wpdb;

/**
 * Rewrite asset URLs on the fly to pull from CDN
 */
function cfcdn_rewrite_on_fly( $content ){
	// Check if file
	if (@fopen($content,"r")==true) {
		return set_cdn_path($content);
	}
	return $content;
}
add_filter( "the_content", "cfcdn_rewrite_on_fly" );


/**
 * Rewrite attachment URL to pull from CDN
 */
function cfcdn_rewrite_attachment_url( $url ){
	return set_cdn_path($url);
}
add_filter('wp_get_attachment_url', 'cfcdn_rewrite_attachment_url');


/**
 * Save file to cloudfiles when uploading new attachment
 */
function cfcdn_send_to_cdn_on_attachment_post_save( $post_id ){
	// Set global vars
	global $wpdb;
	
	// Update attachment CDN URL
	$attachment_data = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = $post_id LIMIT 1");
	$wpdb->query("UPDATE $wpdb->posts SET guid='".set_cdn_path($attachment_data[0]->guid)."' WHERE ID = $post_id");

	$cdn = new CFCDN_CDN();
	$cdn->upload_file( $attachment_data[0]->guid );

}add_action( 'add_attachment', 'cfcdn_send_to_cdn_on_attachment_post_save');


function upload_thumbnails($meta_id, $post_id, $meta_key='', $meta_value=''){
    if ( $meta_key == '_wp_attachment_metadata') {
		// Set global vars
		global $wpdb;

		// Get attachment metadata so we can delete all attachments associated with this image
		$all_image_sizes = $meta_value;

		// Get image paths and setup upload path
		$img_dir = pathinfo($all_image_sizes['file']);
		$uploads = wp_upload_dir();
		$upload_path = $uploads['basedir'].'/'.$img_dir['dirname'];

		// Create new CDN instance
		$cdn = new CFCDN_CDN();

		// Delete all thumbnails from local server
		foreach ($all_image_sizes['sizes'] as $cur_img_size) {
			// Set file path
			$file_path = $upload_path . '/' . basename( $cur_img_size['file'] );

			// Upload file to CDN, add to local file removal
			if ($cdn->upload_file( $file_path )) {
				$remove_files[] = $file_path;
			}
		}

		// Add main image to be removed from local server
		$remove_files[] = $upload_path . '/' . basename($all_image_sizes['file']);

		// Update post meta
		update_post_meta($post_id, '_wp_attached_file', basename($all_image_sizes['file']));

		// Remove local files
		foreach ($remove_files as $cur_file) {
			@unlink($cur_file);
		}
    }
}
add_action('added_post_meta', 'upload_thumbnails', 10, 4);
add_action('updated_post_meta', 'upload_thumbnails', 10, 4);


/**
 * Save file to cloudfiles when uploading new attachment
 */
function cfcdn_delete_attachment_from_cdn( $post_id ){
	// Set global vars
	global $wpdb;

	// Get attachment metadata so we can delete all attachments associated with this image
	$attachment_metadata = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = '$post_id' AND meta_key='_wp_attachment_metadata'");
	$all_image_sizes = unserialize($attachment_metadata[0]->meta_value);

	// Get attachment CDN URL
	$attachment_data = $wpdb->get_results("SELECT guid FROM $wpdb->posts WHERE ID = $post_id LIMIT 1");

	// Create new CDN instance, delete main image from CDN
	$cdn = new CFCDN_CDN();
	$cdn->delete_file( $attachment_data[0]->guid );

	// Delete all thumbnails from CDN
	foreach ($all_image_sizes['sizes'] as $cur_img_size) {
		$cdn->delete_file( basename( $cur_img_size['file'] ) );
	}

}add_action( 'delete_attachment', 'cfcdn_delete_attachment_from_cdn');


/**
 *	Change filename to make sure it won't be overwritten on CDN
  */
function change_filename($filename, $filename_raw) {
    global $post;
    $current_user = wp_get_current_user();
    $info = pathinfo($filename);
    $new_file_name = date('Y.m.d.H.i.s').'.'.$current_user->ID.'_'.$info['filename'];
    $ext  = empty($info['extension']) ? '' : '.' . $info['extension'];
    return $new_file_name . $ext;
}
add_filter('sanitize_file_name', 'change_filename', 10, 2);


/**
 *	Set CDN path for image
  */
function set_cdn_path($attachment, $cdn_settings = null) {
	$cdn_settings = (isset($cdn_settings)) ? $cdn_settings : CFCDN_CDN::settings();
	$cdn_file_path = pathinfo($attachment);
	return $cdn_settings['public_url'].'/'.$cdn_file_path['basename'];
}
?>

<?php
/*
Plugin Name: Downloadable Resources
Plugin URI: #
Description: Create resources for people to download. List Excel, Word, PowerPoint templates and documents, to name a few! 
Version: 1.0
Author: PayProMedia
Author URI: http://www.paypromedia.com/
License: GPLv2
*/

function create_resource() {
    register_post_type( 'resource',
        array(
            'labels' => array(
                'name' => 'Resources',
                'singular_name' => 'Resource',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Resource',
                'edit' => 'Edit',
                'edit_item' => 'Edit Resource',
                'new_item' => 'New Resource',
                'view' => 'View',
                'view_item' => 'View Resource',
                'search_items' => 'Search Resources',
                'not_found' => 'No Resources found',
                'not_found_in_trash' => 'No Resources found in Trash',
                'parent' => 'Parent Resource'
            ),
 
            'public' => true,
            'menu_position' => 15,
            'supports' => array( 'title', 'editor', 'comments', 'thumbnail' ),
            'taxonomies' => array( 'resource-categories', 'resource-tags' ),
            'menu_icon' => plugins_url( 'images/generic.png', __FILE__ ),
            'has_archive' => true,
            'rewrite' => array( 'slug' => 'resources', 'with_front' => FALSE )
        )
    );
    flush_rewrite_rules();
    
}

add_action( 'init', 'create_resource' );
add_action( 'init', 'create_resource_taxonomies', 0 );

function create_resource_taxonomies() {
	// Add new taxonomy, make it hierarchical (like categories)
	$labels = array(
		'name'              => _x( 'Resource Categories', 'taxonomy general name' ),
		'singular_name'     => _x( 'Resource Category', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Resource Categories' ),
		'all_items'         => __( 'All Resource Categories' ),
		'parent_item'       => __( 'Parent Resource Category' ),
		'parent_item_colon' => __( 'Parent Resource Category:' ),
		'edit_item'         => __( 'Edit Resource Category' ),
		'update_item'       => __( 'Update Resource Category' ),
		'add_new_item'      => __( 'Add New Resource Category' ),
		'new_item_name'     => __( 'New Genre Resource Category' ),
		'menu_name'         => __( 'Resource Categories' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'resource', 'with_front' => FALSE ),
	);
	register_taxonomy( 'resource-categories', array( 'resource-categories' ), $args );

	// Add new taxonomy, NOT hierarchical (like tags)
	$labels = array(
		'name'                       => _x( 'Resource Tags', 'taxonomy general name' ),
		'singular_name'              => _x( 'Resource Tag', 'taxonomy singular name' ),
		'search_items'               => __( 'Search Resource Tags' ),
		'popular_items'              => __( 'Popular Resource Tags' ),
		'all_items'                  => __( 'All Resource Tags' ),
		'parent_item'                => null,
		'parent_item_colon'          => null,
		'edit_item'                  => __( 'Edit resource tag' ),
		'update_item'                => __( 'Update resource tag' ),
		'add_new_item'               => __( 'Add New Resource Tag' ),
		'new_item_name'              => __( 'New Writer Resource Tag' ),
		'separate_items_with_commas' => __( 'Separate tags with commas' ),
		'add_or_remove_items'        => __( 'Add or remove resource tags' ),
		'choose_from_most_used'      => __( 'Choose from the most used resource tags' ),
		'not_found'                  => __( 'No resource tags found.' ),
		'menu_name'                  => __( 'Resource Tags' ),
	);

	$args = array(
		'hierarchical'          => false,
		'labels'                => $labels,
		'show_ui'               => true,
		'show_admin_column'     => true,
		'update_count_callback' => '_update_post_term_count',
		'query_var'             => true,
		'rewrite'               => array( 'slug' => 'resource-tags', 'with_front' => FALSE ),
	);

	register_taxonomy( 'resource-tags', 'resource', $args );
	}

// Let's add the file field to the bottom of these posts

function add_custom_meta_boxes() {

	// Add box to display associated resources.
	add_meta_box(
		'wp_attached_resources',
		'Attached Resources',
		'wp_attached_resources',
		'resource',
		'normal'
	);

	// Define the custom attachment for posts
	add_meta_box(
		'wp_custom_attachment',
		'Attach a File',
		'wp_custom_attachment',
		'resource',
		'normal'
	);

} // end add_custom_meta_boxes
add_action('add_meta_boxes', 'add_custom_meta_boxes');

// That was fun!

// Now, let's actually display the field on the post editor page and get the file.

function wp_custom_attachment() {

	wp_nonce_field(plugin_basename(__FILE__), 'wp_custom_attachment_nonce');

	$html = '<p class="description">';
		$html .= 'Upload your file here.';
	$html .= '</p>';
	$html .= '<input class="wp_custom_attachment" name="wp_custom_attachment0" value="" size="25" type="file">';
	
	$html .= '<br /><br /><input type="button" id="add-resource-uploader" value="Add Another Resource" />';
	
	echo $html;

} // end wp_custom_attachment

function wp_attached_resources() {
	wp_register_style( 'downloadableResourcesPluginStyle', plugins_url('stylesheet.css', __FILE__) );
    wp_enqueue_script( 'downloadbleResourcesPluginScript', plugins_url('script.js', __FILE__), array(), '1.1.'.time(), false );	//Include Stylesheet
	
	wp_enqueue_style('downloadableResourcesPluginStyle');
	wp_enqueue_script('downloadbleResourcesPluginScript');
	
	//Set security nonce field.
	wp_nonce_field(plugin_basename(__FILE__), 'wp_attached_resources_nonce');
	
	//Get post resources.
	global $post, $wpdb;
	
	$arr_rows	= $wpdb->get_results('SELECT * FROM wp_postmeta WHERE post_id='.$post->ID.' AND meta_key="wp_custom_attachment"');
	$arr_resources = array();
	
	foreach( $arr_rows as $key => $arr_row ) {
		
		$arr_resources[$key]	= unserialize($arr_row->meta_value);
		$arr_resources[$key]['meta_id']	= $arr_row->meta_id;
		$arr_resources[$key]['post_id']	= $arr_row->post_id;
		
	}
	
	//Get resources associatd with this image.
	$markup	= '<div class="attached-resources">';
	
	$supported_types = array(
		'application/pdf'					=> 'images/resource-icons/pdf.png',
		'text/plain'						=> 'images/resource-icons/text.png',
		'application/msword'				=> 'images/resource-icons/word.png',
		'application/vnd.ms-excel'			=> 'images/resource-icons/excel.png',
		'application/vnd.ms-powerpoint'		=> 'images/resource-icons/ppt.png',
		'image/png'							=> 'images/resource-icons/png.png',
		'image/jpg'							=> 'images/resource-icons/jpg.png',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'images/resource-icons/ppt.png',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document'	=> 'images/resource-icons/word.png',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'	=> 'images/resource-icons/excel.png'
		);
		
	$arr_supported_types	= array_keys($supported_types);
	$deletion_url			= plugins_url( 'delete-resource.php', __FILE__);
	
	if( sizeof($arr_resources) > 0 ) {

		foreach( $arr_resources as $key => $arr_resource ) {
			
			$arr_type	= wp_check_filetype($arr_resource['file']);
			$resource_icon	= '';
			
			if( in_array($arr_type['type'], $arr_supported_types) ) {
				$resource_icon = plugins_url( $supported_types[$arr_type['type']], __FILE__);
			}
			else {
				$resource_icon = plugins_url( 'images/resource-icons/unknown.png', __FILE__);
			}
	
			$markup .= '<div class="resource"><a href="'.$arr_resource['url'].'"><img src="'.$resource_icon.'"></a>';
			$markup .= '<div class="resource-info"><h4>'.basename($arr_resource['file']).'</h4>';
			$markup .= '<a href="'.$arr_resource['url'].'">View Resource</a>';
			$markup .= '<a href="#" data-metaid="'.$arr_resource['meta_id'].'" data-postid="'.$arr_resource['post_id'].'" class="delete-resource-link">Delete Resource</a>';
			$markup .= '</div></div>';
			
		}
	}
	else {
		$markup .= '<p>No resources available.</p>';
	}
	$markup .= '<div class="clear"></div></div>';

	echo $markup;
	
}

// Guess we should save it, too...
function save_custom_meta_data($id) {

	/* --- security verification --- */
	if(!wp_verify_nonce($_POST['wp_custom_attachment_nonce'], plugin_basename(__FILE__))) {
	  return $id;
	} // end if

	if(defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
	  return $id;
	} // end if

	if('page' == $_POST['post_type']) {
	  if(!current_user_can('edit_page', $id)) {
	    return $id;
	  } // end if
	} else {
   		if(!current_user_can('edit_page', $id)) {
	    	return $id;
	   	} // end if
	} // end if
	/* - end security verification - */

	// Make sure the file array isn't empty
	if(!empty($_FILES['wp_custom_attachment0']['name'])) {

		// Setup the array of supported file types. In this case, it's PDF, txt, .doc, .xls, .ppt, png, jpg.
		$supported_types = array(
		'application/pdf',
		'text/plain',
		'application/msword',
		'application/vnd.ms-excel',
		'application/vnd.ms-powerpoint',
		'image/png',
		'image/jpg',
		'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
		);
		
		$x = 0;
		
		while( isset($_FILES['wp_custom_attachment'.$x]['name']) ) {
		
			// Get the file type of the upload
			$arr_file_type = wp_check_filetype(basename($_FILES['wp_custom_attachment'.$x]['name']));
			$uploaded_type = $arr_file_type['type'];
	
			// Check if the type is supported. If not, throw an error.
			if(in_array($uploaded_type, $supported_types)) {
	
				// Use the WordPress API to upload the file
				$upload = wp_upload_bits($_FILES['wp_custom_attachment'.$x]['name'], null, file_get_contents($_FILES['wp_custom_attachment'.$x]['tmp_name']));
				
				if( $upload['error'] !== FALSE ) {
					wp_die("Problem occurred upload file to the server: ".$upload['error']."<br /><pre>".print_r($_FILES, true).'</pre>');
				}
				
				if( $upload['error'] !== FALSE ) {
					wp_die('There was an error uploading your file. The error is: ' . $upload['error']."<pre>".print_r($_FILES, true).'</pre>');
				} else {
					// Setup CDN
					$cdn = new CFCDN_CDN();
					$cdn->api_settings['container'] = 'saltsha_resource';
					$cdn->api_settings['public_url'] = $cdn->container_object()->SSLURI();

					// Set file information
					$file_location	= $upload['file'];
					$upload['file']	= set_cdn_path('/'.$upload['file'], $cdn->api_settings);
					$upload['url']	= $upload['file'];

					// Upload file to CDN
					$cdn->upload_file( $file_location );
					
					// Remove local file and add attachment to db
					unlink($file_location);
					add_post_meta($id, 'wp_custom_attachment', $upload);
				
				} // end if/else
	
			} else {
				wp_die("The file type that you've uploaded is not valid.");
			} // end if/else
			
			$x++;
			
		}

	} // end if

} // end save_custom_meta_data
add_action('save_post', 'save_custom_meta_data');

function update_edit_form() {  
    echo ' enctype="multipart/form-data"';  
} // end update_edit_form  
add_action('post_edit_form_tag', 'update_edit_form');


?>
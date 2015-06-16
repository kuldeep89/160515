<?php
/*
Plugin Name: Custom User Notes
Plugin URI: http://www.paypromedia.com/
Description: Allows admins to add private custom user notes.
Version: 0.0.1
Contributors: bstump
Author URI: http://www.paypromedia.com/individuals/bobbie-stump/
License: GPLv2
*/

/**
 * Initialize custom post type
 */
function create_custom_user_notes() {
    register_post_type( 'custom_user_notes',
        array(
            'labels' => array(
                'name' => 'User Notes',
                'singular_name' => 'User Note',
                'add_new' => 'New User Note',
                'add_new_item' => 'Add New User Note',
                'edit' => 'Edit',
                'edit_item' => 'Edit User Note',
                'new_item' => 'New User Note',
                'view' => 'View',
                'view_item' => 'View User Note',
                'search_items' => 'Search User Notes',
                'not_found' => 'No user notes found.',
                'not_found_in_trash' => 'No user notes found in trash.'
            ),
            'public' => false,
            'supports' => array( 'title', 'editor', 'custom-fields' ),
            'has_archive' => false
        )
    );

    add_meta_box("custom_user_notes_user", "User Assigned To Note", "custom_user_notes_user", "custom_user_notes", "normal", "high");
}
add_action( 'init', 'create_custom_user_notes' );


/**
 * Update post meta
 */
function save_details() {
	global $post;
	update_post_meta($post->ID, "custom_user_notes_username", $_POST["custom_user_notes_username"]);
}
add_action('save_post', 'save_details');


/**
 * Add custom user note to user's profile
 */
function cu_notes_save_note() {
	// Assign request data
	$the_note = (isset($_REQUEST['add_new_custom_user_note']) && trim($_REQUEST['add_new_custom_user_note']) != '') ? trim($_REQUEST['add_new_custom_user_note']) : null;
	$the_user = addslashes($_REQUEST['add_new_custom_user_note_user_id']);
	date_default_timezone_set('America/Fort_Wayne');
	$the_date = date('M d, Y @ H:i');

	// If not is not empty, add it
	if (!is_null($the_note)) {
		$new_user_note = array('post_title' => 'User Note', 'post_type' => 'custom_user_notes', 'post_content' => $the_note, 'post_status' => 'publish', 'post_author' => get_current_user_id());
		if ($new_post = wp_insert_post($new_user_note)) {
			if (update_post_meta($new_post, 'custom_user_notes_username', $the_user)) {
				$cur_user = wp_get_current_user();
				echo json_encode(array('status' => 'success', 'note_id' => $new_post, 'note_author' => $cur_user->user_login, 'note_created' => $the_date));
			} else {
				echo json_encode(array('status' => 'error', 'message' => 'The note was not added successfully. Please reload the page and try again (Error 001).'));
			}
		} else {
			echo json_encode(array('status' => 'error', 'message' => 'The note was not added successfully. Please reload the page and try again (Error 002).'));
		}
	} else {
		echo json_encode(array('status' => 'error', 'message' => 'Please type some text in the text area to add a note (Error 003).'));
	}
	die();
}
add_action('wp_ajax_cu_notes_save_note', 'cu_notes_save_note');


/**
 * Custom meta field(s)
 */
function custom_user_notes_user() {
	global $post;
	$custom = get_post_meta($post->ID);
	$custom_user_notes_username = $custom["custom_user_notes_username"][0];
	echo '<input type="text" name="custom_user_notes_username" value="'.$custom_user_notes_username.'" />';
}


/**
 * Show notes in user's profile
 */
function show_custom_user_notes( $user ) {
	// Show custom user notes
	//Added PPT Users to this list ~ Curtis
	if (in_array('administrator', wp_get_current_user()->roles) || in_array('ppt-user', wp_get_current_user()->roles)) {
		// Enqueue custom stylesheet
		wp_enqueue_style('custom-user-notes', '/wp-content/plugins/custom-user-notes/style.css');
		wp_enqueue_script('custom-user-notes-js', '/wp-content/plugins/custom-user-notes/js/admin.js');

		// Label and table for section
		echo '<h3>Custom User Notes</h3>';
		echo '<table class="form-table" id="custom_user_notes">';

		// Echo notes table
		echo '<tr>
            <th></th>
            <td>
            	<table id="list_of_custom_notes">
            		<tbody>
            			<tr>
            				<td style="background-color:#f1f1f1 !important;">
            					<textarea id="add_new_custom_user_note" name="add_new_custom_user_note" style="width:100%;" rows="5" placeholder="Add note text here..."></textarea>
            					<input type="button" id="cu_add_note" name="cu_add_note" class="button button-primary" value="Add Note" />
            					<input type="hidden" id="add_new_custom_user_note_user_id" name="add_new_custom_user_note_user_id" value="'.$user->data->ID.'" />
            				</td>
            			</tr>';

		// Build query for retrieving user's notes
		$args = array(
			'post_type' => 'custom_user_notes',
			'meta_query'  => array(
				array(
					'key' => 'custom_user_notes_username',
					'value' => $user->data->ID
				)
			)
		);

		// Send query for user's notes
		$query= new WP_Query( $args );
		while( $query->have_posts() ) {
			$query->the_post();
			$note_owner = get_user_by('id', $query->post->post_author);
			$note_date = date('M d, Y @ H:i', strtotime($query->post->post_date));
			echo '<tr><td style="padding:7px !important;"><strong>'.$note_owner->data->user_login.'</strong> <em>'.$note_date.'</em><br/>' . stripslashes( get_the_content( $query->post->ID ) ) . '</td></tr>';
		}

		// Close the table
        echo '		</tbody>
        		</table>
        	</td>
        <tr>';
	}

	// Close section
	echo '</table>';
}
add_action( 'show_user_profile', 'show_custom_user_notes' );
add_action( 'edit_user_profile', 'show_custom_user_notes' );
?>
<?php
/*
Plugin Name: Rackspace CloudFile CDN
Plugin URI: http://labs.saidigital.co/products/rackspace-cloudfiles-cdn
Description: A plugin that moves attachments to Rackspace Cloudfiles.
Version: 0.0.1
Author: richardroyal
Author URI: http://saidigital.co/about-us/people/richard-royal/
License: GPLv2

Copyright 2013 richardroyal (richard@saidigital.co)
This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*/

defined('WP_PLUGIN_URL') or die('Restricted access');

global $wpdb;

define('CFCDN_PATH', ABSPATH.PLUGINDIR.'/rackspace-cloudfiles-cdn/');
define('CFCDN_URL', WP_PLUGIN_URL.'/rackspace-cloudfiles-cdn/');
define('CFCDN_ROUTE', get_bloginfo('url').'/?cfcdn_routing=');
define('CFCDN_UPLOAD_CURL', CFCDN_ROUTE . "upload_ping" );
define('CFCDN_DELETE_CURL', CFCDN_ROUTE . "delete_ping" );
define('CFCDN_NEEDING_UPLOAD_JSON', CFCDN_ROUTE . "needing_upload.json" );
define('CFCDN_OPTIONS', "wp_cfcdn_settings" );
define('CFCDN_LOADIND_URL', WP_PLUGIN_URL.'/rackspace-cloudfiles-cdn/assets/images/loading.gif');

require_once(ABSPATH.'wp-admin/includes/upgrade.php');
require_once("lib/functions.php");
require_once("admin/functions.php");
require_once("lib/class.cfcdn_cdn.php");
require_once("lib/class.cfcdn_attachments.php");
if( !class_exists("OpenCloud") ){
	require_once("lib/php-opencloud-1.5.10/lib/php-opencloud.php");
}


/**
 *  Register and enqueue frontend JavaScript
 */
function cfcdn_js() {
	if(!is_admin()){
		wp_enqueue_script('jquery');
	}
}
add_action('wp_enqueue_scripts', 'cfcdn_js');


/**
 *  Register and enqueue admin JavaScript
 */
function cfcdn_admin_js() {
	wp_enqueue_script('jquery');
	wp_enqueue_media();
	wp_enqueue_style('thickbox');
	wp_enqueue_script('media-upload');
	wp_enqueue_script('thickbox');
	wp_enqueue_script('rackspace-cloudfiles-cdn-admin-js', CFCDN_URL.'assets/js/rackspace-cloudfiles-cdn-admin.js');
}
add_action('admin_enqueue_scripts', 'cfcdn_admin_js');


/**
 * Parse requests from specific URLs.
 */
function cfcdn_parse_url_requests($wp) {
	if (array_key_exists('cfcdn_routing', $wp->query_vars) ) {
	
		/* Uploads files to Cloudfiles CDN on GET request to "/?cfcdn_routing=upload_ping".*/
		if ( $wp->query_vars['cfcdn_routing'] == 'upload_ping') {
			// CFCDN_Util::upload_all();
		}
		
		/* List of files that need to be uploaded, GET "/?cfcdn_routing=needing_upload.json". */
		if ( $wp->query_vars['cfcdn_routing'] == 'needing_upload.json') {
			$attachments = new CFCDN_Attachments();
			echo $attachments->needing_upload_as_json();
		}
		
		/* Uploads individual file to Cloudfiles CDN on GET request to "/?cfcdn_routing=upload_file&path={PATH_TO_FILE}". */
		if ( $wp->query_vars['cfcdn_routing'] == 'upload_file') {
			$file_path = $_GET['path'];
			$cdn = new CFCDN_CDN();
			if( !empty( $file_path ) ){
			$cdn->upload_file( $file_path );
			echo "Uploading $file_path";
		}
			$cdn->update_setting( "first_upload", "true" );
		}
		
		/* Delete local files that are already pushed to CDN on GET request to "/?cfcdn_routing=delete_ping".*/
		if ( $wp->query_vars['cfcdn_routing'] == 'delete_ping') {
			// CFCDN_Util::delete_local_files();
		}
		die();exit();
	}
}
add_action('parse_request', 'cfcdn_parse_url_requests');


function cfcdn_parse_query_vars($vars) {
	$vars[] = 'cfcdn_routing';
	return $vars;
}
add_filter('query_vars', 'cfcdn_parse_query_vars');
?>

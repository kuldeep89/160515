<?php

/**
 *  Require manage.php
 */
function cfcdn_manage() {
	require_once(CFCDN_PATH . "admin/manage.php");
}


/**
 *  Create admin pages for plugin management.
 */
function cfcdn_admin_pages() {
	if (current_user_can('manage_options')) {
		add_menu_page("Rackspace CloudFiles CDN", "Cloudfiles CDN", "publish_posts", "cfcdn-manage", "cfcdn_manage");
	}
}add_action('admin_menu', 'cfcdn_admin_pages');


/**
 * Save CloudFiles CDN Settings
 */
function cfcdn_save_settings(){
	if( is_admin() && current_user_can('manage_options') && !empty($_POST) && !empty($_POST['cfcdn']) ){
		$settings = $_POST['cfcdn'];
		update_option( CFCDN_OPTIONS, $settings );
	}
}

?>

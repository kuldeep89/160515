<?php
/**
 * Require the admin interface file
 **/
function io_interface() {
	require_once( IO_PATH . 'admin/interface.php' );
}

/**
 * Add 'Import Price Overrides' to the Users menu in the admin interface
 **/
function io_menu_item() {
	add_submenu_page( 'users.php','Import Price Overrides', 'Import Price Overrides', 'create_users', 'import-price-overrides', 'io_interface' );
}
add_action( 'admin_menu','io_menu_item' );


function ipo_scripts(){
	$dir = IO_URL;
	$pluginJsUrl = $dir . 'js/';
	
	wp_register_script('pricing-override-script', $pluginJsUrl.'pricing-override-script.js');
	
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script('pricing-override-script');
}
add_action('admin_enqueue_scripts', 'ipo_scripts');

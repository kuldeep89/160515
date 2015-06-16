<?php
/**
 * Require the admin interface file
 **/
function ulh_interface() {
	require_once( ULH_PATH . 'admin/interface.php' );
}

/**
 * Add 'User Login History' to the Users menu in the admin interface
 **/
function ulh_menu_item() {
	add_submenu_page( 'smrt_merchant_list','User Login History', 'User Login History', 'create_users', 'user-login-history-pro', 'ulh_interface' );
}
add_action( 'admin_menu','ulh_menu_item' );


function ulh_scripts(){
	$pluginJsUrl = plugins_url();
	$pluginJsUrl = $pluginJsUrl.'/user-login-history-pro/js/';
	wp_enqueue_script( 'jquery' );
	wp_register_script('ulh-update-filter', $pluginJsUrl.'ulh-update-filter.js');
	wp_register_script('ulh-datepicker', $pluginJsUrl.'ulh-datepicker.js');
	wp_enqueue_script('ulh-datepicker');
	wp_enqueue_script('ulh-update-filter');
}
add_action('admin_enqueue_scripts', 'ulh_scripts');

function ulh_styles() {
	$pluginCssUrl = plugins_url();
	$pluginCssUrl = $pluginCssUrl.'/user-login-history-pro/css/';
	wp_enqueue_style( 'ulh-style', $pluginCssUrl.'style.css' );	
}
add_action( 'admin_enqueue_scripts', 'ulh_styles' );
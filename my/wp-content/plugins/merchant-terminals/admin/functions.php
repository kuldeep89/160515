<?php
/**
 * Require the admin interface file
 **/
function term_interface() {
	require_once( TERM_PATH . 'admin/interface.php' );
}

/**
 * Add 'Terminals and Paper' to the menu in the admin interface
 **/
function term_menu_item() {
	add_menu_page( 'Terminals and Paper', 'Terminals and Paper', 'terminals_paper', 'merchant-terminals', 'term_interface' );
}
add_action( 'admin_menu','term_menu_item' );


/**
 * Set up styles
 **/
function term_styles() {
	$plugin_css_url = plugins_url();
	$plugin_css_url = $plugin_css_url.'/merchant-terminals/css/';
	wp_register_style( 'term_style', $plugin_css_url.'style.css' );
	wp_enqueue_style( 'term_style' );	
}
add_action( 'admin_enqueue_scripts', 'term_styles' );

/**
 * Set up scripts
 **/
function term_scripts(){
	$plugin_js_url = plugins_url();
	$plugin_js_url = $plugin_js_url.'/merchant-terminals/js/';
	wp_enqueue_script( 'jquery' );
	wp_register_script('merchant_terminals', $plugin_js_url.'merchant-terminals.js');
	wp_enqueue_script('merchant_terminals');
}
add_action('admin_enqueue_scripts', 'term_scripts');
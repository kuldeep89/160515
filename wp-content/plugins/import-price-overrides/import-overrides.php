<?php
/*
Plugin Name: Import Pricing Overrides
Plugin URI: http://www.paypromedia.com/
Description: A plugin that allows you to run the import script.
Version: 1.0.0
Contributors: cwolfenberger1
Author: Curtis Wolfenberger
Author URI: http://www.curtiswolfenberger.com
*/

defined( 'WP_PLUGIN_URL' ) or die( 'Restricted access' );

//Include all required function files
require_once( "includes/functions.php" );
require_once( "admin/functions.php" );

global $wpdb;

/**
 * Define constants
 **/
define( 'IO_PATH', ABSPATH.PLUGINDIR . '/import-price-overrides/' );
define( 'IO_URL', WP_PLUGIN_URL . '/import-price-overrides/' );

/**
 * Installation function
 **/
function io_install(){
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
}
register_activation_hook( __FILE__, 'io_install' );


/**
 * Uninstall function
 **/
function io_uninstall() {

}
register_uninstall_hook( __FILE__, 'io_uninstall' );

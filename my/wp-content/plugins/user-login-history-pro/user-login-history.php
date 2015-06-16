<?php
/*
Plugin Name: User Login History - Pro
Plugin URI: http://www.paypromedia.com/
Description: A plugin that allows you to view your user's login history.
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
define( 'ULH_PATH', ABSPATH.PLUGINDIR . '/user-login-history-pro/' );
define( 'ULH_URL', WP_PLUGIN_URL . '/user-login-history-pro/' );
define( 'ULH_OPTIONS', "wp_ulh_settings" );

/**
 * Installation function
 **/
function ulh_install(){
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	$user_history = $wpdb->prefix . "user_history";
	
	//Create user history table.
	$query = "CREATE TABLE IF NOT EXISTS `" . $user_history . "` (
				`id` int(11) NOT NULL auto_increment,
				`user_id` int(11) NOT NULL,
				`login_date` timestamp NOT NULL default CURRENT_TIMESTAMP,
				PRIMARY KEY  (`id`)
			) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;";
	dbDelta( $query );
}
register_activation_hook( __FILE__, 'ulh_install' );


/**
 * Uninstall function
 **/
function ulh_uninstall() {
	global $wpdb;
	$user_history = $wpdb->prefix . 'user_history';
	
	//Delete user history table.
	$query = "DROP TABLE " . $user_history;
	$wpdb->query( $query );
}
register_uninstall_hook( __FILE__, 'ulh_uninstall' );

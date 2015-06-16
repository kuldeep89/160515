<?php
/**
 * Plugin Name: Merchant Terminals
 * Author: Curtis
 * Description: Allows creation of terminals and paper products in the Wordpress admin interface.
 * Version: 0.0.1
*/

defined( 'WP_PLUGIN_URL' ) or die( 'Restricted access' );
define('TERM_PATH', ABSPATH.PLUGINDIR.'/merchant-terminals/');

//Include all required function files
require_once( "includes/functions.php" );
require_once( "admin/functions.php" );

global $wpdb;

/**
 * Runs when plugin is activated
 */
register_activation_hook( __FILE__,'term_install' ); 
register_uninstall_hook( __FILE__, 'term_uninstall' );
register_deactivation_hook(__FILE__, 'term_uninstall');


/**
 * Install function | Creates new database field(s) associated with plugin
 */
function term_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;
	
	// Set up database table names
	$terminals = $wpdb->prefix . "terminals";
	$merchant_terminals = $wpdb->prefix . "merchant_terminals";
	$terminal_paper = $wpdb->prefix . "terminal_paper";
	$terminal_relationship = $wpdb->prefix . "terminal_relationship";
	
	// Create database tables.
	$install_query	=	"CREATE TABLE IF NOT EXISTS `" . $terminals . "` (
							`id` INT NOT NULL AUTO_INCREMENT,
							`terminal_name` VARCHAR(255)  NOT NULL ,
							PRIMARY KEY (`id`) 
						);";
	dbDelta( $install_query );

	$install_query	=	"CREATE TABLE IF NOT EXISTS `" . $terminal_paper . "` (
							`id` INT NOT NULL AUTO_INCREMENT ,
							`size` VARCHAR(100) NOT NULL ,
							`type` VARCHAR(255) NOT NULL ,
							`transactions`  VARCHAR(100) NOT NULL ,
							PRIMARY KEY (`id`) 
						);";
				
	dbDelta( $install_query );
				
	// This is for future use to add ability to tie terminals to specific merchant IDs
	$install_query	=	"CREATE TABLE IF NOT EXISTS `" . $merchant_terminals . "` (
							`id` INT NOT NULL AUTO_INCREMENT,
							`user_id` INT NOT NULL ,
							`terminal_id` INT NOT NULL ,
							PRIMARY KEY (`id`) 
						);";
	dbDelta( $install_query );
				
	// This is for future use to add ability to tie paper types to specific terminals
	$install_query	=	"CREATE TABLE IF NOT EXISTS `" . $terminal_relationship . "` (
				`			id` INT NOT NULL AUTO_INCREMENT ,
							`terminal_id` INT NOT NULL ,
							`paper_id` INT NOT NULL ,
							PRIMARY KEY (`id`)
						);";
			
	dbDelta( $install_query );

}

/**
 * Uninstall function | Removes database fields upon removal/uninstallation
 **/
function term_uninstall() {
	global $wpdb;
	
	// Set up database table names
	$terminals = $wpdb->prefix . "terminals";
	$merchant_terminals = $wpdb->prefix . "merchant_terminals";
	$terminal_paper = $wpdb->prefix . "terminal_paper";
	$terminal_relationship = $wpdb->prefix . "terminal_relationship";
	
	//Delete user history table.
	$drop_query = "DROP TABLE IF EXISTS " . $terminals . ";";
	$wpdb->query( $drop_query );
	$drop_query .= "DROP TABLE IF EXISTS " . $merchant_terminals . ";";
	$wpdb->query( $drop_query );
	$drop_query .= "DROP TABLE IF EXISTS " . $terminal_paper . ";";
	$wpdb->query( $drop_query );
	$drop_query .= "DROP TABLE IF EXISTS " . $terminal_relationship . ";";
	$wpdb->query( $drop_query );
}

?>
<?php
/**
 * Plugin Name: Merchant Reporting Tool
 * Author: Bobbie Stump
 * Description: Merchant reporting for Saltsha admins.
 * Version: 0.0.1
*/


/**
 * Runs when plugin is activated
 */
register_activation_hook(__FILE__,'mr_install'); 


/**
 * Creates new database field(s) associated with plugin
 */
function mr_install() {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	global $wpdb;

	// Setup db tables
	$mr_mailgun_stats ="CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mailgun_stats` (
        `id` bigint(20) NOT NULL,
        `message_id` varchar(50) NOT NULL,
        `event` varchar(30) NOT NULL,
        `recipient` varchar(50) NOT NULL,
        `domain` varchar(30) NOT NULL,
        `message_headers` text NOT NULL,
        `reason` text NOT NULL,
        `code` varchar(20) NOT NULL,
        `description` text NOT NULL,
        `error` varchar(30) NOT NULL,
        `notification` varchar(150) NOT NULL,
        `ip` varchar(30) NOT NULL,
        `country` varchar(30) NOT NULL,
        `region` varchar(30) NOT NULL,
        `city` varchar(30) NOT NULL,
        `user_agent` varchar(30) NOT NULL,
        `device_type` varchar(30) NOT NULL,
        `client_type` varchar(30) NOT NULL,
        `client_name` varchar(30) NOT NULL,
        `client_os` varchar(30) NOT NULL,
        `campaign_id` varchar(30) NOT NULL,
        `campaign_name` varchar(30) NOT NULL,
        `tag` varchar(128) NOT NULL,
        `mailing_list` varchar(20) NOT NULL,
        `timestamp` varchar(15) NOT NULL,
        `token` varchar(50) NOT NULL,
        `signature` varchar(50) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ;";

	dbDelta( $mr_mailgun_stats );
}


/**
 * Load classes
 */
if( ! class_exists( 'WP_List_Table' ) ) {
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
include 'lib/SMRTMerchantIdsTable.class.php';
include 'lib/SMRTMerchantDataTable.class.php';
include 'lib/SMRTMailgunStatsTable.class.php';
include 'lib/SMRTMerchantRewardsTable.class.php';


/**
 * Include functions
 */
include 'functions/merchant-reporting.php';
include 'functions/email-stats.php';


/**
 * Admin alert page javascript
 */
function mr_enqueue_js() {
    wp_enqueue_script('mr-mdrn-js', '/wp-content/plugins/merchant-reporting/assets/js/MerchantDataReport.js', array('jquery'), null, date('y.m.d'));
    wp_enqueue_script('mr-mir-js', '/wp-content/plugins/merchant-reporting/assets/js/MerchantIdsReport.js', array('jquery'), null, date('y.m.d'));
    wp_enqueue_script('mr-mrr-js', '/wp-content/plugins/merchant-reporting/assets/js/MerchantRewardsReport.js', array('jquery'), null, date('y.m.d'));
    wp_enqueue_script('mr-msr-js', '/wp-content/plugins/merchant-reporting/assets/js/MailgunStatsReport.js', array('jquery'), null, date('y.m.d'));
}
add_action('admin_enqueue_scripts', 'mr_enqueue_js');


/**
 * Add menu item(s) for plugin
 */
function smrt_menu_item(){
    add_menu_page( 'Reporting', 'Reporting', 'activate_plugins', 'smrt_merchant_list', 'smrt_merchant_list' );
    add_submenu_page( 'smrt_merchant_list', 'All Merchant IDs', 'All Merchant IDs', 'activate_plugins', 'smrt_merchant_list');
    add_submenu_page( 'smrt_merchant_list', 'No Merchant Data', 'No Merchant Data', 'smrt_merchant_list', 'smrt_merchant_data_list', 'smrt_merchant_data_list');
    add_submenu_page( 'smrt_merchant_list', 'Merchant Rewards', 'Merchant Rewards', 'smrt_merchant_list', 'smrt_merchant_rewards_list', 'smrt_merchant_rewards_list');
    add_submenu_page( 'smrt_merchant_list', 'Mailgun Stats', 'Mailgun Stats', 'smrt_merchant_list', 'smrt_mail_stats_list', 'smrt_mail_stats_list');
}
add_action( 'admin_menu', 'smrt_menu_item' );
?>
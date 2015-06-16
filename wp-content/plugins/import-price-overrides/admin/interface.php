<?php
/**
 * Only show this page if the user can add users.
 **/
if ( !current_user_can( 'create_users' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
defined('IO_PATH') or die();
global $wpdb;
?>
<div class="wrap">
	<h2>Import Price Overrides</h2>
	<a href="#" class="button alignleft" id="io_run_import" style="margin-left:1em;">Run Import</a>
	<div id="io_response"></div>
</div>
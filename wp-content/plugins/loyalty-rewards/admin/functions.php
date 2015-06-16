<?php
/**
 *  Require manage.php
 */
function rsp_manage() {
	require_once(RSP_PATH."admin/manage.php");
}


/**
 *  Create admin pages for plugin management.
 */
function rsp_admin_pages() {
	if (current_user_can('manage_options')) {
		add_menu_page("Loyalty Rewards", "Loyalty Rewards", "publish_posts", "rsp-manage", "rsp_manage");
	}
}
add_action('admin_menu', 'rsp_admin_pages');
?>
<?php
/**
 * Only show this page if the user can add users.
 **/
if ( !current_user_can( 'create_users' ) )  {
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
defined('ULH_PATH') or die();
global $wpdb;
?>
<div class="wrap">
	<h2>User Login History - Pro</h2>
	<a href="?page=user-login-history-pro" class="button button-primary alignleft" style="margin-bottom:5px;">Clear Results</a>
	<a href="#" class="button alignleft" id="export_ulh_csv" style="margin-left:1em;">Export to CSV</a>
	<?php
		// Create new instance of the ULH_Table class
		$ULH_Table = new ULH_Table();
        $ULH_Table->prepare_items();
	?>
	<!-- Create/display search form -->
	<form method="GET">
		<?php if(isset($_GET['page'])): ?><input type="hidden" name="page" value="<?php echo $_GET['page'] ?>" /><?php endif; ?>
		<?php if(isset($_GET['ulhFilter'])): ?><input type="hidden" name="ulhFilter" value="<?php echo $_GET['ulhFilter'] ?>" /><?php endif; ?>
		<?php $ULH_Table->search_box('Search User', 'ULH'); ?>
		<?php if(isset($_GET['dateFrom'])): ?><input type="hidden" name="dateFrom" value="<?php echo $_GET['dateFrom'] ?>" /> <?php endif; ?>
		<?php if(isset($_GET['dateTo'])): ?><input type="hidden" name="dateTo" value="<?php echo $_GET['dateTo'] ?>" /><?php endif; ?>
	</form>
	<?php
		// Display the table
        $ULH_Table->display();
	?>
</div>
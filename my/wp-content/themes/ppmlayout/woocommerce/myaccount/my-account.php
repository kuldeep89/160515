<?php
/**
 * My Account page
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$woocommerce->show_messages(); ?>

<div class="row-fluid acc_header_text">
	<div class="span12">
		<!--<a class="btn green" href="<?php echo get_permalink( woocommerce_get_page_id( 'change_password' ) ); ?>" >Change Your Password</a>-->
		<a class="btn green" href="#changePassModal" data-toggle="modal">Change Your Password</a>
		<span class="right">To manage your subscriptions, call (574)269-0792 or email <a href="mailto:support@saltsha.com">support@saltsha.com</a></span>
	</div>
</div>

<?php
	$userid = $current_user->ID;
	if(current_user_can( 'administrator' )){
		//IF WORDPRESS ADMIN ACCOUNT
		//do_action( 'woocommerce_before_my_account' ); 
		
		//woocommerce_get_template( 'myaccount/my-downloads.php' ); 
		
		//woocommerce_get_template( 'myaccount/my-orders.php', array( 'order_count' => $order_count ) ); 
	?>
		<div class="row-fluid">
			<div class="span6">
				<?php woocommerce_get_template( 'myaccount/my-address.php' ); ?>
			</div>
				<?php echo do_shortcode('[transactional_data_subscription]'); //do_action( 'woocommerce_after_my_account' ); 
		
		
		
	} else {
		//IF NOT WORDPRESS ADMIN ACCOUNT
		$registered = Groups_Group::read_by_name( 'Registered' );
		$tier0 = Groups_Group::read_by_name( 'Tier 0' );
		$tier1 = Groups_Group::read_by_name( 'Tier 1' );
		$tier0_merchant = Groups_Group::read_by_name( 'Tier 0 Merchant' );
		$tier1_merchant = Groups_Group::read_by_name( 'Tier 1 Merchant' );
		if( Groups_User_Group::read( $userid, $tier1_merchant->group_id ) || Groups_User_Group::read( $userid, $tier0_merchant->group_id ) ) {
			//IF MERCHANT ACCOUNT
			?>
			<div class="row-fluid">
				<div class="span6">
					<?php woocommerce_get_template( 'myaccount/my-address.php' ); ?>
				</div>
					<?php echo do_shortcode('[transactional_data_subscription]'); //do_action( 'woocommerce_after_my_account' ); 
		
		
		} elseif( Groups_User_Group::read( $userid, $tier0->group_id ) || Groups_User_Group::read( $userid, $tier1->group_id ) ||  Groups_User_Group::read( $userid, $registered->group_id )  ){
			//IF NOT MERCHANT ACCOUNT
			?>
			<div class="row-fluid">
				<div class="span12">
					<?php woocommerce_get_template( 'myaccount/my-address.php' ); ?>
				</div>
			</div>
			<?php
		}
	}
?>

<div id="changePassModal" class="modal fade hide" data-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="false">
	<div class="modal-header">
		<h3 id="myModalLabel">Change Password</h3>
	</div>
	
	<div class="modal-body">
		
		<form action="<?php echo esc_url( get_permalink(woocommerce_get_page_id('change_password')) ); ?>" method="post">
		
			<div class="row-fluid">
				<div class="span12">
					<label for="password_1"><?php _e( 'New password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" class="input-text" name="password_1" id="password_1" />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<label for="password_2"><?php _e( 'Re-enter new password', 'woocommerce' ); ?> <span class="required">*</span></label>
					<input type="password" class="input-text" name="password_2" id="password_2" />
				</div>
			</div>
			<div class="row-fluid">
				<div class="span12">
					<input type="submit" class="button" name="change_password" value="<?php _e( 'Save', 'woocommerce' ); ?>" />
					<a href="#" class="btn right 12" id="no-thanks" data-dismiss="modal" aria-hidden="true">Cancel</a>
				</div>
			</div>
			
			<?php $woocommerce->nonce_field('change_password')?>
			<input type="hidden" name="action" value="change_password" />
		
		</form>
	</div>
</div>
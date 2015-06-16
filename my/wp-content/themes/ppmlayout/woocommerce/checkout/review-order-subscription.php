<?php
/**
 * Review order form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $woocommerce;

$available_methods = $woocommerce->shipping->get_available_shipping_methods();
?>
<div id="order_review_container">
<div id="order_review" style="margin-top:-30px;">
	<?php
		// Pre-select whether ther person has chosen monthly or yearly
		$args = array( 'post_type' => 'product', 'post_status' => 'publish', 'posts_per_page' => '-1' );
		$products = new WP_Query( $args );
		foreach ($products->posts as $cur_product) {
			// Get product
			$product_data = get_product($cur_product->ID);

			// Get monthly product
			if (!isset($monthly_product)) {
				if ($product_data->subscription_period == 'month') {
					$monthly_product = array('id' => $cur_product->ID, 'name' => $cur_product->post_title, 'price' => $product_data->subscription_price);
				}
			}

			// Get yearly product
			if (!isset($yearly_product)) {
				if ($product_data->subscription_period == 'year') {
					$yearly_product  =array('id' => $cur_product->ID, 'name' => $cur_product->post_title, 'price' => $product_data->subscription_price);
				}
			}

			// If both monthly and yearly product are set, exit for loop
			if (isset($monthly_product) && isset($yearly_product)) {
				break;
			}
		}

		// If setting billing from $_GET var, make the change
		if (isset($_GET['billing']) && !is_null($_GET['billing']) && trim($_GET['billing']) != '') {
			if ($_GET['billing'] == 'monthly') {
				change_subscription($monthly_product['id'], true);
			} else {
				change_subscription($yearly_product['id'], true);
			}
		}

		// Get cart data
		$cart_data = $woocommerce->cart->get_cart();
		foreach ($cart_data as $product_key => $product_data) {
			$subscription_period = (isset($product_data['data']->subscription_period) && $product_data['data']->subscription_period == 'month') ? 'sub_monthly' : 'sub_yearly';
			break;
		}
	?>
	<?php
		// Get recurring amount
		$coupons = $woocommerce->cart->applied_coupons;
		if (count($woocommerce->cart->applied_coupons) > 0) {
			// Get percentage of full price
			$percent_of_full_price = ($subscription_period == 'sub_monthly') ? ($woocommerce->cart->recurring_total/$monthly_product['price']) : ($woocommerce->cart->recurring_total/$yearly_product['price']);

			// Get yearly and monthly pricing
			$monthly_price = str_replace('.00', '', number_format($monthly_product['price']*$percent_of_full_price, 2));
			$yearly_price = str_replace('.00', '', number_format($yearly_product['price']*$percent_of_full_price, 2));
		} else {
			// Set price for no coupon codes
			$monthly_price = str_replace('.00', '', $monthly_product['price']);
			$yearly_price = str_replace('.00', '', $yearly_product['price']);
		}

		// Make sure font size is good to go
		if (strlen($monthly_price) > 2) {
			$monthly_price = '<span style="font-size:80px;">'.$monthly_price.'</span>';
		}
		if (strlen($yearly_price) > 2) {
			$yearly_price = '<span style="font-size:80px;">'.$yearly_price.'</span>';
		}
	?>
	<div id="pricing">
		<div class="sub_pricing">
			<div id="sub_monthly" subscription_id="<?php echo $monthly_product['id'] ?>" class="<?php echo ((isset($product_data['data']->subscription_period) && $product_data['data']->subscription_period == 'month') || $_GET['billing'] == 'monthly') ? 'choose_sub_active' : 'choose_sub' ?>" onclick="change_subscription('sub_monthly');"></div>
			<div class="sub_label">Monthly Pricing</div>
			<div class="sub_price"><div class="dolla_dolla_bill_yall">$</div><?php echo $monthly_price ?></div>
		</div>
		<div class="pricing_separator"></div>
		<div class="sub_pricing">
			<div id="sub_yearly" subscription_id="<?php echo $yearly_product['id'] ?>" class="<?php echo ((isset($product_data['data']->subscription_period) && $product_data['data']->subscription_period == 'month') || $_GET['billing'] == 'monthly') ? 'choose_sub' : 'choose_sub_active' ?>" onclick="change_subscription('sub_yearly');"></div>
			<div class="sub_label">Yearly Pricing</div>
			<div class="sub_price"><div class="dolla_dolla_bill_yall">$</div><?php echo $yearly_price ?></div>
		</div>
	</div>

	<div id="payment">
		<?php if ($woocommerce->cart->needs_payment()) : ?>
		<ul class="payment_methods methods">
			<?php
				$available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
				if ( ! empty( $available_gateways ) ) {

					// Chosen Method
					if ( isset( $woocommerce->session->chosen_payment_method ) && isset( $available_gateways[ $woocommerce->session->chosen_payment_method ] ) ) {
						$available_gateways[ $woocommerce->session->chosen_payment_method ]->set_current();
					} elseif ( isset( $available_gateways[ get_option( 'woocommerce_default_gateway' ) ] ) ) {
						$available_gateways[ get_option( 'woocommerce_default_gateway' ) ]->set_current();
					} else {
						current( $available_gateways )->set_current();
					}

					foreach ( $available_gateways as $gateway ) {
						?>
						<li>
							<input type="radio" id="payment_method_<?php echo $gateway->id; ?>" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
							<label for="payment_method_<?php echo $gateway->id; ?>"><?php echo $gateway->get_title(); ?> <?php echo $gateway->get_icon(); ?></label>
							<?php
								if ( $gateway->has_fields() || $gateway->get_description() ) :
									echo '<div class="payment_box payment_method_' . $gateway->id . '" ' . ( $gateway->chosen ? '' : 'style="display:none;"' ) . '>';
									$gateway->payment_fields();
									echo '</div>';
								endif;
							?>
						</li>
						<?php
					}
				} else {

					if ( ! $woocommerce->customer->get_country() )
						echo '<p>' . __( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) . '</p>';
					else
						echo '<p>' . __( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) . '</p>';

				}
			?>
		</ul>
		<?php endif; ?>

		<div class="form-row place-order">

			<noscript><?php _e( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the <em>Update Totals</em> button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ); ?><br/><input type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php _e( 'Update totals', 'woocommerce' ); ?>" /></noscript>

			<?php $woocommerce->nonce_field('process_checkout')?>

			<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

			<?php
			$order_button_text = apply_filters('woocommerce_order_button_text', __( 'Place order', 'woocommerce' ));

			echo apply_filters('woocommerce_order_button_html', '<input type="submit" class="button alt" name="woocommerce_checkout_place_order" id="place_order" value="' . $order_button_text . '" />' );
			?>

			<?php if (woocommerce_get_page_id('terms')>0) : ?>
			<p class="form-row terms">
				<label for="terms" class="checkbox"><?php _e( 'I have read and accept the', 'woocommerce' ); ?> <a href="<?php echo esc_url( get_permalink(woocommerce_get_page_id('terms')) ); ?>" target="_blank"><?php _e( 'terms &amp; conditions', 'woocommerce' ); ?></a></label>
				<input type="checkbox" class="input-checkbox" name="terms" <?php checked( isset( $_POST['terms'] ), true ); ?> id="terms" />
			</p>
			<?php endif; ?>

			<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		</div>

		<div class="clear"></div>

	</div>

</div>
</div>
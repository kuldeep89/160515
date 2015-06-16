<?php
/**
 * Checkout billing information form
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $woocommerce;

$product_types = array();
foreach($woocommerce->cart->cart_contents as $cur_item) {
	$product_types[] = $cur_item['data']->product_type;
}
?>

<?php do_action('woocommerce_before_checkout_billing_form', $checkout ); ?>
	<fieldset>
		<legend><span>1</span> Personal Information</legend>
		<div class="row-fluid">
			<div class="span6">
				<input type="text" class="input-text" name="billing_first_name" id="billing_first_name" placeholder="First Name" />
			</div>
			<div class="span6">
				<input type="text" class="input-text" name="billing_last_name" id="billing_last_name" placeholder="Last Name" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<input type="text" class="input-text" name="billing_email" id="billing_email" placeholder="Your Email" />
			</div>
			<div class="span6">
				<input type="email" class="input-text" name="email_confirm" id="email_confirm" placeholder="Confirm Email" />
			</div>
		</div>
		<?php if (!is_user_logged_in() && $checkout->enable_signup): ?>
		<div class="row-fluid">
			<div class="span12">
				<input type="text" class="input-text" name="account_username" id="account_username" placeholder="Username" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<input type="password" class="input-text" name="account_password" id="account_password" placeholder="Password (Case Sensitive)" />
			</div>
			<div class="span6">
				<input type="password" class="input-text" name="account_password-2" id="account_password-2" placeholder="Confirm Password (Case Sensitive)" />
			</div>
		</div>
		<?php endif; ?>
	</fieldset>
	<fieldset>
		<legend><span>2</span> <?php echo (in_array('subscription', $product_types)) ? 'Business' : 'Billing' ?> Information</legend>
		<div class="row-fluid">
			<div class="span6">
				<input type="text" class="input-text" name="billing_company" id="billing_company" placeholder="Business Name" />
			</div>
			<div class="span6">
				<input type="text" class="input-text" name="billing_phone" id="billing_phone" placeholder="Business Phone Number" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<input type="text" class="input-text" name="billing_address_1" id="billing_address_1" placeholder="Business Address" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<?php form_field('billing_country', $checkout); ?>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<input type="text" class="input-text" name="billing_city" id="billing_city" placeholder="City" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<input type="text" class="input-text" name="billing_state" id="billing_state" placeholder="State" />
			</div>
			<div class="span6">
				<input type="text" class="input-text" name="billing_postcode" id="billing_postcode" placeholder="Zip Code" />
			</div>
		</div>
	</fieldset>
	<fieldset>
		<legend><span>3</span> <?php echo (in_array('subscription', $product_types)) ? 'Billing' : 'Shipping' ?> Information</legend>
		<?php if (!in_array('subscription', $product_types)) : ?>
		<div class="row-fluid">
			<div class="span6">
				<input type="text" class="input-text" name="shipping_first_name" id="shipping_first_name" placeholder="First Name" />
			</div>
			<div class="span6">
				<input type="text" class="input-text" name="shipping_last_name" id="shipping_last_name" placeholder="Last Name" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<input type="text" class="input-text" name="shipping_address_1" id="shipping_address_1" placeholder="Business Address" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12" style="margin-top:15px;">
				<p class="form-row form-row-wide address-field update_totals_on_change validate-required woocommerce-invalid woocommerce-invalid-required-field" id="shipping_country_field">
					<select name="shipping_country" id="shipping_country" class="country_to_state country_select">
						<option value="">Select a country...</option>
						<?php $countries = new WC_Countries(); ?>
						<?php foreach($countries->get_allowed_countries() as $key => $value) : ?>
						<option value="<?php echo $key ?>"><?php echo $value ?></option>
						<?php endforeach; ?>
					</select>
				</p>
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12">
				<input type="text" class="input-text" name="shipping_city" id="shipping_city" placeholder="City" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span6">
				<input type="text" class="input-text" name="shipping_state" id="shipping_state" placeholder="State" />
			</div>
			<div class="span6">
				<input type="text" class="input-text" name="shipping_postcode" id="shipping_postcode" placeholder="Zip Code" />
			</div>
		</div>
		<div class="row-fluid">
			<div class="span12" style="margin-top:15px;">
				<select name="shipping_method" id="shipping_method">
					<option value="">Select a shipping method...</option>
				<?php foreach($woocommerce->shipping->get_available_shipping_methods() as $cur_shipping_option) : ?>
					<option value="<?php echo $cur_shipping_option->id ?>"><?php echo $cur_shipping_option->label.' ($'.$cur_shipping_option->cost.')' ?></option>
				<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php endif; ?>
		<div id="coupon_div"></div>
	</fieldset>

<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

<?php
	function form_field( $key, $checkout, $placeholder = 'Fill Out Placeholder', $checkout_form = 'billing', $class = 'form-row-wide') {
		woocommerce_form_field($key, $checkout->checkout_fields[$checkout_form][$key], $checkout->checkout_fields[$checkout_form][$key]['placeholder'] = $placeholder, $checkout->get_value($key));
	}
?>
<?php
/**
 * WooCommerce Authorize.net CIM
 *
 * This source file is subject to the GNU General Public License v3.0
 * that is bundled with this package in the file license.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.gnu.org/licenses/gpl-3.0.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@skyverge.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade WooCommerce Authorize.net CIM to newer
 * versions in the future. If you wish to customize WooCommerce Authorize.net CIM for your
 * needs please refer to http://docs.woothemes.com/document/authorize-net-cim/ for more information.
 *
 * @package     WC-Authorize-Net-CIM/eCheck-Gateway
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Authorize.net CIM eCheck Base Class
 *
 * Adds the eCheck payment method as a separate gateway by extending the base CIM class
 *
 * @since 1.0
 * @extends \WC_Authorize_Net_CIM
 */
class WC_Gateway_Authorize_Net_CIM_eCheck extends WC_Gateway_Authorize_Net_CIM {


	/**
	 * Load payment gateway and related settings
	 *
	 * @since 1.0
	 * @return \WC_Gateway_Authorize_Net_CIM_eCheck
	 */
	public function __construct() {

		// set method information
		$this->id                 = 'authorize_net_cim_echeck';
		$this->method_title       = __( 'Authorize.net CIM eCheck', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		$this->method_description = __( 'Allow customers to securely save their bank account to their account for use with single purchases, pre-orders, and subscriptions.', WC_Authorize_Net_CIM::TEXT_DOMAIN );

		$this->supports( 'products' );

		// allow eCheck icon to be changed
		$this->icon = apply_filters( 'wc_authorize_net_cim_echeck_icon', '' );

		// Init the gateway settings
		$this->init_form_fields();

		// Load the gateway settings
		$this->init_settings();

		// Merge in base gateway settings
		$this->settings = array_merge( parent::$instance->settings, $this->settings );

		// Set settings as class members
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}

		// pay page fallback
		add_action( 'woocommerce_receipt_' . $this->id, create_function( '$order', 'echo "<p>" . __( "Thank you for your order.", WC_Authorize_Net_CIM::TEXT_DOMAIN ) . "</p>";' ) );

		// Save settings
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
	}


	/**
	 * Initialize payment gateway settings fields
	 *
	 * @since 1.0
	 */
	public function init_form_fields() {

		$this->form_fields = array(

			'enabled' => array(
				'title'       => __( 'Enable / Disable', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'label'       => __( 'Enable this gateway.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'default'     => 'no'
			),

			'title' => array(
				'title'       => __( 'Title', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'text',
				'desc_tip'    => __( 'Payment method title that the customer will see during checkout.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => __( 'eCheck', WC_Authorize_Net_CIM::TEXT_DOMAIN )
			),

			'description' => array(
				'title'       => __( 'Description', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'textarea',
				'desc_tip'    => __( 'Payment method description that the customer will see during checkout.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => __( 'Pay securely using your bank account.', WC_Authorize_Net_CIM::TEXT_DOMAIN )
			),
		);
	}


	/**
	 * Checks if the eCheck gateway is available via these conditions:
	 *
	 * 1) the base / eCheck gateway must be enabled
	 * 2) the base gateway must be configured correctly ( determined via parent::is_available() )
	 * 3) all dependencies must be met ( determined via parent::is_available() )
	 *
	 * @since  1.0
	 */
	public function is_available() {

		return ( 'yes' === $this->enabled && 'yes' === parent::$instance->enabled && parent::$instance->is_available() );
	}


	/**
	 * Add the eCheck icon, or display the icon provided by the filter
	 *
	 * @since 1.0
	 */
	public function get_icon() {
		global $wc_authorize_net_cim;

		$icon = '';

		if ( $this->icon ) {

			// use icon provided by filter
			$icon = '<img src="' . esc_url( SV_WC_Plugin_Compatibility::force_https_url( $this->icon ) ) . '" alt="' . esc_attr( $this->title ) . '" />';

		} else {

			if ( is_readable( $wc_authorize_net_cim->get_plugin_path() . '/assets/card-echeck.png' ) ) {
				$icon .= '<img src="' . esc_url( SV_WC_Plugin_Compatibility::force_https_url( $wc_authorize_net_cim->get_plugin_url() ) . '/assets/card-echeck.png' ) . '" alt="' . esc_attr( strtolower( 'echeck' ) ) . '" />';
			}
		}

		return apply_filters( 'woocommerce_gateway_icon', $icon, $this->id );
	}


	/**
	 * Display the payment fields on the checkout page
	 *
	 * @since  1.0
	 */
	public function payment_fields() {

		if ( $this->description ) {
			echo '<p>' . wp_kses_post( $this->description ) . '</p>';
		}

		if ( parent::$instance->is_test_mode() ) {

			echo '<p>' . __( 'TEST MODE ENABLED', WC_Authorize_Net_CIM::TEXT_DOMAIN ) . '</p>';
			echo '<p>' . sprintf( __( 'Use test bank account - routing: %s, account number: %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), '031202084', '8675309' ) . '</p>';
		}
	?>
	<fieldset>
		<?php if ( is_user_logged_in() ) :
			$has_accounts = ( count( $accounts = $this->get_saved_bank_accounts( get_current_user_id() ) ) > 0 ) ? true : false;
			if ( $has_accounts ) : ?>
			  	<p class="form-row form-row-wide">
				<a class="button" style="float:right;" href="<?php echo get_permalink( woocommerce_get_page_id( 'myaccount' ) ); ?>#cim-my-cards"><?php echo wp_filter_kses( parent::$instance->manage_payment_methods_text ); ?></a>
				<?php foreach( $accounts as $profile_id => $card ) : ?>
					<input type="radio" id="authorize-net-cim-echeck-payment-profile-id-<?php echo esc_attr( $profile_id ); ?>" name="authorize-net-cim-echeck-payment-profile-id" style="width:auto;" value="<?php echo esc_attr( $profile_id ); ?>" <?php checked( $card['active'] ); ?>/>
					<label style="display:inline;" for="authorize-net-cim-echeck-payment-profile-id-<?php echo esc_attr( $profile_id ); ?>"><?php printf( __( '%s ending in %s (expires %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( $card['type'] ), esc_html( $card['last_four'] ), esc_html( $card['exp_date'] ) ); ?></label><br />
					<?php endforeach; ?>
				<input type="radio" id="authorize-net-cim-use-new-bank-account" name="authorize-net-cim-echeck-payment-profile-id" <?php checked( $has_accounts, false ); ?> style="width:auto;" value="" /> <label style="display:inline;" for="new-card"><?php echo __( 'Use a new bank account', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></label>
				</p><div class="clear"></div>
			<?php endif; ?>
		<?php endif; ?>

			<div<?php echo ( isset( $has_accounts ) && $has_accounts ) ? ' class="authorize-net-cim-new-saved-bank-account"' : ''; ?>>
				<p class="form-row form-row-first">
					<label for="authorize-net-cim-routing-number"><?php _e( "Bank Routing Number", WC_Authorize_Net_CIM::TEXT_DOMAIN); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" id="authorize-net-cim-routing-number" name="authorize-net-cim-routing-number" maxlength="9" autocomplete="off" />
				</p>
				<p class="form-row form-row-last">
					<label for="authorize-net-cim-account-number"><?php _e( "Bank Account Number", WC_Authorize_Net_CIM::TEXT_DOMAIN); ?> <span class="required">*</span></label>
					<input type="text" class="input-text" id="authorize-net-cim-account-number" name="authorize-net-cim-account-number" maxlength="17" autocomplete="off" />
				</p>
				<div class="clear"></div>
			</div>
	</fieldset>
		<?php
		ob_start();
		?>
			$('body').bind('updated_checkout', function() {

				"use strict";

				$('input[name=authorize-net-cim-echeck-payment-profile-id]:radio').change(function (e) {

					var $savedBankAccountSelected = $(this).filter(':checked').val(),
						$newBankAccountSection = $('div.authorize-net-cim-new-saved-bank-account');

					// if no bank accounts are marked as active (e.g. if a credit card is selected as the active payment method)
					// or a saved bank account is selected, hide the bank account form
					if ('undefined' === typeof $savedBankAccountSelected || '' !== $savedBankAccountSelected) {
						$newBankAccountSection.slideUp(200);
					} else {
						// use new bank account
						$newBankAccountSection.slideDown(200);
					}
				}).change();
			});
		<?php
		SV_WC_Plugin_Compatibility::wc_enqueue_js( ob_get_clean() );
	}


	/**
	 * Validate the payment fields when processing the checkout
	 *
	 * @since 1.0
	 * @return bool true if fields are valid, false otherwise
	 */
	public function validate_fields() {

		// skip validation if using a saved payment method
		if ( $this->get_post( 'authorize-net-cim-echeck-payment-profile-id' ) ) {
			return true;
		}

		$is_valid = true;

		$routing_number = $this->get_post( 'authorize-net-cim-routing-number' );
		$account_number = $this->get_post( 'authorize-net-cim-account-number' );

		// routing number digit check
		if ( ! ctype_digit( $routing_number ) ) {
			SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Routing Number is invalid (only digits are allowed)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
			$is_valid = false;
		}

		// routing number length check
		if ( 9 != strlen( $routing_number ) ) {
			SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Routing number is invalid (must be 9 digits)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
			$is_valid = false;
		}

		// account number digit check
		if ( ! ctype_digit( $account_number ) ) {
			SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Account Number is invalid (only digits are allowed)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
			$is_valid = false;
		}

		// account number length check
		if ( strlen( $account_number ) < 5 || strlen( $account_number ) > 17 ) {
			SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Account number is invalid (must be between 5 and 17 digits)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * Add payment and profile information as class members of \WC_Order instance
	 *
	 * @since 1.0
	 * @param int $order_id order ID being processed
	 * @return \WC_Order instance
	 */
	protected function get_order( $order_id ) {

		// set order defaults
		$order = parent::get_order ( $order_id );

		// add eCheck payment info
		$order->payment = new stdClass();

		$order->payment->routing_number  = $this->get_post( 'authorize-net-cim-routing-number' );
		$order->payment->account_number  = $this->get_post( 'authorize-net-cim-account-number' );
		$order->payment->name_on_account = $order->billing_first_name . ' ' . $order->billing_last_name;
		$order->payment->type            = 'echeck';

		// add selected eCheck payment profile
		$order->customer_payment_profile_id = $this->get_post( 'authorize-net-cim-echeck-payment-profile-id' );

		return $order;
	}


	/**
	 * Get saved bank account payment profiles (excluding any saved credit cards)
	 *
	 * @since 1.0
	 * @param int $user_id
	 * @return array see get_payment_profile() method for array format
	 */
	private function get_saved_bank_accounts( $user_id ) {

		$payment_profiles = $this->get_payment_profiles( $user_id );

		if ( empty( $payment_profiles ) ) {
			return array();
		}

		foreach ( $payment_profiles as $payment_profile_id => $payment_profile ) {

			// remove all non-bank account payment types
			if ( 'Bank Account' !== $payment_profile['type'] ) {
				unset( $payment_profiles[ $payment_profile_id ] );
			}
		}

		return $payment_profiles;
	}


} // end \WC_Gateway_Authorize_Net_CIM_eChecks class

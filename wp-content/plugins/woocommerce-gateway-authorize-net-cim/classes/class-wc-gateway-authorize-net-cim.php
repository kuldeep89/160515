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
 * @package     WC-Authorize-Net-CIM/Gateway
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Authorize.net CIM Base Class
 *
 * Handles all purchases, displaying saved cards, etc
 * Extended by Add-ons class
 *
 * @since 1.0
 * @extends \WC_Payment_Gateway
 */
class WC_Gateway_Authorize_Net_CIM extends WC_Payment_Gateway {


	/** @var string the text used for the "Manage Cards" button on the checkout page */
	public $manage_payment_methods_text;

	/** @var string authorize.net API login ID */
	public $api_login_id;

	/** @var string authorize.net API transaction key */
	public $api_transaction_key;

	/** @var string authorize.net test API login ID */
	public $test_api_login_id;

	/** @var string authorize.net test API transaction key */
	public $test_api_transaction_key;

	/** @var string determines how to process transactions, auth & capture or auth only */
	public $transaction_type;

	/** @var string require the card security code during checkout */
	public $require_cvv;

	/** @var string the location of the merchant's payment processor, determines what fields are required at checkout */
	public $payment_processor_location;

	/** @var array card types to show images for */
	public $card_types;

	/** @var string is test mode enabled */
	public $test_mode;

	/** @var string 4 options for debug mode - off, checkout, log, both */
	public $debug_mode;

	/** @var \WC_Authorize_Net_CIM_API instance */
	protected $api;

	/** @var \WC_Gateway_Authorize_Net_CIM static instance for use by eCheck gateway */
	protected static $instance;


	/**
	 * Load payment gateway and related settings
	 *
	 * @since 1.0
	 * @return \WC_Gateway_Authorize_Net_CIM
	 */
	public function __construct() {

		$this->id                 = 'authorize_net_cim';
		$this->method_title       = __( 'Authorize.net CIM', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		$this->method_description = __( 'Allow customers to securely save their credit card to their account for use with single purchases, pre-orders, and subscriptions.', WC_Authorize_Net_CIM::TEXT_DOMAIN );

		$this->supports = array( 'products' );

		$this->has_fields = true;

		$this->icon = apply_filters( 'wc_authorize_net_cim_icon', '' );

		// Load the form fields
		$this->init_form_fields();

		// Load the settings
		$this->init_settings();

		// Define user set variables
		foreach ( $this->settings as $setting_key => $setting ) {
			$this->$setting_key = $setting;
		}

		// pay page fallback
		add_action( 'woocommerce_receipt_' . $this->id, create_function( '$order', 'echo "<p>" . __( "Thank you for your order.", WC_Authorize_Net_CIM::TEXT_DOMAIN ) . "</p>";' ) );

		// Save settings
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}

		// all billing information is required at checkout for merchants with european payment processors
		if ( 'european' === $this->get_payment_process_location() ) {
			add_filter( 'woocommerce_get_country_locale', array( $this, 'require_billing_fields' ), 100 );
		}

		// set instance for use by eCheck gateway
		self::$instance = $this;
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
				'default'     => __( 'Credit Card', WC_Authorize_Net_CIM::TEXT_DOMAIN )
			),

			'description' => array(
				'title'       => __( 'Description', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'textarea',
				'desc_tip'    => __( 'Payment method description that the customer will see during checkout.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => __( 'Pay securely using your credit card.', WC_Authorize_Net_CIM::TEXT_DOMAIN )
			),

			'manage_payment_methods_text' => array(
				'title'       => __( 'Manage Payment Methods Text', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'text',
				'desc_tip'    => __( 'The text for the "Manage Payment Methods" button on the checkout page.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => __( 'Manage Payment Methods', WC_Authorize_Net_CIM::TEXT_DOMAIN )
			),

			'api_login_id' => array(
				'title'       => __( 'API Login ID', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'text',
				'desc_tip'    => __( 'The API Login ID for your Authorize.net account.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => ''
			),

			'api_transaction_key' => array(
				'title'       => __( 'API Transaction Key', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'text',
				'desc_tip'    => __( 'The API Transaction Key for your Authorize.net account.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => ''
			),

			'test_api_login_id' => array(
				'title'       => __( 'Test Mode API Login ID', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'text',
				'desc_tip'    => __( 'The API Login ID for your Authorize.net test account.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => ''
			),

			'test_api_transaction_key' => array(
				'title'       => __( 'Test Mode API Transaction Key', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'text',
				'desc_tip'    => __( 'The API Transaction Key for your Authorize.net test account.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => ''
			),

			'transaction_type' => array(
				'title'       => __( 'Transaction Type', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'select',
				'desc_tip'    => __( 'Select how transactions should be processed. Authorize and Capture submits all transactions for settlement, Authorize Only simply authorizes the order total for capture later.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => 'auth_capture',
				'options'     => array(
					'auth_capture' => __( 'Authorize & Capture', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
					'auth_only'    => __( 'Authorize Only', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				),
			),

			'require_cvv' => array(
				'title'       => __( 'Card Verification (CV2)', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'label'       => __( 'Require customers to enter credit card verification code (CV2) during checkout.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'default'     => 'no'
			),

			'payment_processor_location' => array(
				'title'    => __( 'Payment Processor Location', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'desc_tip' => __( 'Select the location of your payment processor. Depending on the location, certain checkout fields are required. Read the documentation to learn more.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'     => 'select',
				'options'  => array(
					'north_american' => __( 'North American', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
					'european'      => __( 'European', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				),
				'default' => 'north_american',
			),

			'card_types' => array(
				'title'       => __( 'Accepted Card Logos', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'multiselect',
				'desc_tip'    => __( 'Select which card types you accept to display the logos for on your checkout page.  This is purely cosmetic and optional, and will have no impact on the cards actually accepted by your account.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => array( 'VISA', 'MC', 'AMEX', 'DISC' ),
				'class'       => 'chosen_select',
				'css'         => 'width: 350px;',
				'options'     => apply_filters( 'wc_authorize_net_cim_card_types',
					array(
						'VISA'   => 'Visa',
						'MC'     => 'MasterCard',
						'AMEX'   => 'American Express',
						'DISC'   => 'Discover',
					)
				)
			),

			'test_mode' => array(
				'title'       => __( 'Test Mode', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'label'       => __( 'Enable Test Mode', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'checkbox',
				'description' => sprintf( __( 'Put the gateway in test mode to work with an Authorize.net test account (signup for free %shere%s).', WC_Authorize_Net_CIM::TEXT_DOMAIN ), '<a href="https://developer.authorize.net/testaccount/">', '</a>' ),
				'default'     => 'no'
			),

			'debug_mode' => array(
				'title'       => __( 'Debug Mode', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'type'        => 'select',
				'desc_tip'    => __( 'Show Detailed Error Messages and API requests / responses on the checkout page and/or save them to the log for debugging purposes.', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'default'     => 'off',
				'options' => array(
					'off'      => __( 'Off', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
					'checkout' => __( 'Show on Checkout Page', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
					'log'      => __( 'Save to Log', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
					'both'     => __( 'Both', WC_Authorize_Net_CIM::TEXT_DOMAIN )
				),
			),

		);
	}


	/**
	 * Display settings page with some additional javascript for hiding conditional fields
	 *
	 * @since 1.0
	 */
	public function admin_options() {

		parent::admin_options();

		// add inline javascript
		ob_start();
		?>
			$('#woocommerce_authorize_net_cim_test_mode').change( function() {

				var test_api_login_row = $('#woocommerce_authorize_net_cim_test_api_login_id').closest('tr');
				var test_api_key_row = test_api_login_row.next();

				if ($(this).is(':checked')) {
					test_api_login_row.show();
					test_api_key_row.show();
				} else {
					test_api_login_row.hide();
					test_api_key_row.hide();
				}
			}).change();
		<?php
		SV_WC_Plugin_Compatibility::wc_enqueue_js( ob_get_clean() );
	}


	/**
	 * Checks for proper gateway configuration (required fields populated, etc)
	 * and that there are no missing dependencies
	 *
	 * @since  1.0
	 */
	public function is_available() {
		global $wc_authorize_net_cim;

		// is enabled check
		$is_available = parent::is_available();

		// proper configuration
		if ( ! $this->get_api_login_id() || ! $this->get_api_transaction_key() ) {
			$is_available = false;
		}

		// all dependencies met
		if ( count( $wc_authorize_net_cim->get_missing_dependencies() ) > 0 ) {
			$is_available = false;
		}

		return apply_filters( 'wc_gateway_authorize_net_cim_is_available', $is_available );
	}


	/**
	 * Add selected card icons to payment method label, defaults to Visa/MC/Amex/Discover
	 *
	 * @since 1.0
	 */
	public function get_icon() {
		global $wc_authorize_net_cim;

		$icon = '';

		if ( $this->icon ) {

			// use icon provided by filter
			$icon = '<img src="' . esc_url( SV_WC_Plugin_Compatibility::force_https_url( $this->icon ) ) . '" alt="' . esc_attr( $this->title ) . '" />';

		} elseif ( ! empty( $this->card_types ) ) {

			// display icons for the selected card types
			foreach ( $this->card_types as $card_type ) {

				if ( is_readable( $wc_authorize_net_cim->get_plugin_path() . '/assets/card-' . strtolower( $card_type ) . '.png' ) ) {
					$icon .= '<img src="' . esc_url( SV_WC_Plugin_Compatibility::force_https_url( $wc_authorize_net_cim->get_plugin_url() ) . '/assets/card-' . strtolower( $card_type ) . '.png' ) . '" alt="' . esc_attr( strtolower( $card_type ) ) . '" />';
				}
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

		if ( $this->is_test_mode() ) {

			echo '<p>' . __( 'TEST MODE ENABLED', WC_Authorize_Net_CIM::TEXT_DOMAIN ) . '</p>';
			echo '<p>' . sprintf( __( 'Use test credit cards Visa: %s or Amex: %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), '4007000000027', '370000000000002' ) . '</p>';
		}
		?>
	<style type="text/css">#payment ul.payment_methods li label[for='payment_method_authorize_net_cim'] img:nth-child(n+2) { margin-left:1px; }</style>
	<fieldset>

		<?php if ( is_user_logged_in() ) :
			$has_cards = ( count( $cards = $this->get_saved_credit_cards( get_current_user_id() ) ) > 0 ) ? true : false;
			if ( $has_cards ) : ?>
			  	<p class="form-row form-row-wide">
				<a class="button" style="float:right;" href="<?php echo get_permalink( woocommerce_get_page_id( 'myaccount' ) ); ?>#cim-my-payment-methods"><?php echo wp_kses_post( $this->manage_payment_methods_text ); ?></a>
				<?php foreach( $cards as $profile_id => $card ) : ?>
					<input type="radio" id="authorize-net-cim-payment-profile-id-<?php echo esc_attr( $profile_id ); ?>" name="authorize-net-cim-payment-profile-id" style="width:auto;" value="<?php echo esc_attr( $profile_id ); ?>" <?php checked( $card['active'] ); ?>/>
					<label style="display:inline;" for="authorize-net-cim-payment-profile-id-<?php echo esc_attr( $profile_id ); ?>"><?php printf( __( '%s ending in %s (expires %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( $card['type'] ), esc_html( $card['last_four'] ), esc_html( $card['exp_date'] ) ); ?></label><br />
					<?php endforeach; ?>
				<input type="radio" id="authorize-net-cim-use-new-card" name="authorize-net-cim-payment-profile-id" <?php checked( $has_cards, false ); ?> style="width:auto;" value="" /> <label style="display:inline;" for="new-card"><?php echo __( 'Use a new credit card', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></label>
				</p><div class="clear"></div>
			<?php endif; ?>
		<?php endif; ?>

		<div<?php echo ( isset( $has_cards ) && $has_cards ) ? ' class="authorize-net-cim-new-saved-credit-card"' : ''; ?>>
			<div class="row">
				<div class="<?php echo ( 'yes' == $this->require_cvv ) ? 'span6' : 'span12' ?>">
					<input type="text" class="input-text" id="authorize-net-cim-cc-number" placeholder="Credit Card Number" name="authorize-net-cim-cc-number" maxlength="19" autocomplete="off" />
				<?php if ( 'yes' == $this->require_cvv ) : ?>
				</div>
				<div class="span6">
					<input type="text" class="input-text" id="authorize-net-cim-cc-cvv" name="authorize-net-cim-cc-cvv" placeholder="CVV" maxlength="4" autocomplete="off" />
				</div>
				<?php endif; ?>
			</div>

			<div class="clear"></div>

			<div class="row">
				<div class="span6">
					<select name="authorize-net-cim-cc-exp-month" id="authorize-net-cim-cc-exp-month" class="woocommerce-select woocommerce-cc-month" style="width:auto;">
						<option value=""><?php _e( 'Month', WC_Authorize_Net_CIM::TEXT_DOMAIN ) ?></option>
						<?php foreach ( range( 1, 12 ) as $month ) : ?>
						<option value="<?php printf( '%02d', $month ) ?>"><?php printf( '%02d', $month ) ?></option>
						<?php endforeach; ?>
					</select>
				</div>
				<div class="span6">
					<select name="authorize-net-cim-cc-exp-year" id="authorize-net-cim-cc-exp-year" class="woocommerce-select woocommerce-cc-year" style="width:auto;">
						<option value=""><?php _e( 'Year', WC_Authorize_Net_CIM::TEXT_DOMAIN ) ?></option>
						<?php foreach ( range( date( 'Y' ), date( 'Y' ) + 10 ) as $year ) : ?>
						<option value="<?php echo $year ?>"><?php echo $year ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>

		<div class="clear"></div>

	</fieldset>
	<?php
		ob_start();
		?>

	  		// checkout page
			if ( $( 'form.checkout' ).length ) {

				// handle saved cards, note this is bound to the updated_checkout trigger so it fires even when other parts
				// of the checkout are changed
				$( 'body' ).bind( 'updated_checkout', function() { wcAuthorizeNetCIMHandleSavedCards() } );


			// checkout -> pay page
			} else {

				wcAuthorizeNetCIMHandleSavedCards()
			}

			function wcAuthorizeNetCIMHandleSavedCards() {

				$( 'input[name=authorize-net-cim-payment-profile-id]:radio').change( function  (e ) {

					var $savedCreditCardSelected = $( this ).filter( ':checked' ).val(),
						$newCardSection = $( 'div.authorize-net-cim-new-saved-credit-card' ),
						$cvvField = $( '#authorize-net-cim-cc-cvv-section' );

					// if no cards are marked as active (e.g. if a bank account is selected as the active payment method)
					// or a saved card is selected, hide the credit card form
					if ( 'undefined' === typeof $savedCreditCardSelected || '' !== $savedCreditCardSelected ) {

						$newCardSection.slideUp( 200 );
						$cvvField.removeClass( 'form-row-last' );

					// use new card
					} else {

						$newCardSection.slideDown( 200 );
						$cvvField.addClass( 'form-row-last ');
					}

				}).change();
			}

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

		$is_valid = parent::validate_fields();

		// skip validation if using a saved payment method
		if ( $this->get_post( 'authorize-net-cim-payment-profile-id' ) ) {
			return true;
		}

		$card_number      = preg_replace( '[\D]', '', $this->get_post( 'authorize-net-cim-cc-number' ) );
		$expiration_month = $this->get_post( 'authorize-net-cim-cc-exp-month' );
		$expiration_year  = $this->get_post( 'authorize-net-cim-cc-exp-year' );
		$cvv              = preg_replace( '[\D]', '', $this->get_post( 'authorize-net-cim-cc-cvv' ) );

		if ( 'yes' === $this->require_cvv ) {

			// check security code
			if ( empty( $cvv ) ) {
				SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Card security code is missing', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
				$is_valid = false;
			}

			// digit check
			if ( ! ctype_digit( $cvv ) ) {
				SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Card security code is invalid (only digits are allowed)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
				$is_valid = false;
			}

			// length check
			if ( strlen( $cvv ) < 3 || strlen( $cvv ) > 4 ) {
				SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Card security code is invalid (must be 3 or 4 digits)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
				$is_valid = false;
			}
		}

		// check expiration data
		$current_year  = date( 'Y' );
		$current_month = date( 'n' );

		if ( ! ctype_digit( $expiration_month ) || ! ctype_digit( $expiration_year ) ||
		  $expiration_month > 12 ||
		  $expiration_month < 1 ||
		  $expiration_year < $current_year ||
		  ( $expiration_year == $current_year && $expiration_month < $current_month ) ||
		  $expiration_year > $current_year + 20
		) {
			SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Card expiration date is invalid', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
			$is_valid = false;
		}

		// check card number
		$card_number = str_replace( array( ' ', '-' ), '', $card_number );

		if ( empty( $card_number ) || ! ctype_digit( $card_number ) || ! $this->luhn_check( $card_number ) ) {
			SV_WC_Plugin_Compatibility::wc_add_notice( __( 'Card number is invalid', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
			$is_valid = false;
		}

		return $is_valid;
	}


	/**
	 * Handles payment for guest / registered user checkout, using this logic:
	 *
	 * + If customer is logged in or creating an account, add a customer and payment profile for them, then
	 *   charge the purchase via the payment profile
	 *
	 * + If customer is a guest, create a single transaction and don't create a customer or payment profile
	 *
	 * @since  1.0
	 */
	public function process_payment( $order_id ) {

		// add payment information to order
		$order = $this->get_order( $order_id );

		try {

			// registered customer checkout (already logged in or creating account at checkout)
			if ( is_user_logged_in() || 0 != $order->user_id ) {

				// create new authorize.net customer profile if needed
				if ( ! $order->customer_profile_id ) {
					$order = $this->create_customer( $order );
				}

				// create new payment profile if customer is using new card
				if ( ! $order->customer_payment_profile_id ) {
					$order = $this->create_payment_profile( $order );
				}

				// payment failures are handled internally by do_transaction()
				if ( $this->do_transaction( $order ) ) {

					if ( 'on-hold' == $order->status ) {
						$order->reduce_order_stock(); // reduce stock for held orders, but don't complete payment
					} else {
						$order->payment_complete(); // mark order as having received payment
					}

					SV_WC_Plugin_Compatibility::WC()->cart->empty_cart();

					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
				}

			// guest checkout
			} else {

				if ( $this->do_single_transaction( $order ) ) {

					if ( 'on-hold' == $order->status ) {
						$order->reduce_order_stock(); // reduce stock for held orders, but don't complete payment
					} else {
						$order->payment_complete(); // mark order as having received payment
					}

					SV_WC_Plugin_Compatibility::WC()->cart->empty_cart();

					return array(
						'result'   => 'success',
						'redirect' => $this->get_return_url( $order ),
					);
				}

			}

		} catch ( Exception $e ) {

			// log API requests/responses here too, as exceptions could be thrown before $response object is returned
			$this->log_api();

			$this->mark_order_as_failed( $order, $e->getMessage() );
		}
	}


	/**
	 * Add payment and profile information as class members of \WC_Order instance
	 *
	 * @since 1.0
	 * @param int $order_id order ID being processed
	 * @return \WC_Order instance
	 */
	protected function get_order( $order_id ) {

		$order = new WC_Order( $order_id );

		// add payment info
		$order->payment = new stdClass();

		// paying with credit card
		$order->payment->card_number     = preg_replace( '[\D]', '', $this->get_post( 'authorize-net-cim-cc-number' ) );
		$order->payment->expiration_date = $this->get_post( 'authorize-net-cim-cc-exp-year' ) . '-' . $this->get_post( 'authorize-net-cim-cc-exp-month' );
		$order->payment->cvv             = preg_replace( '[\D]', '', $this->get_post( 'authorize-net-cim-cc-cvv' ) );
		$order->payment->type            = 'credit_card';

		// add customer profile if exists
		$order->customer_profile_id = get_user_meta( $order->user_id, '_wc_authorize_net_cim_profile_id', true );

		// add customer shipping profile if exists
		$order->customer_shipping_profile_id = get_user_meta( $order->user_id, '_wc_authorize_net_cim_shipping_profile_id', true );

		// add selected payment profile
		$order->customer_payment_profile_id = $this->get_post( 'authorize-net-cim-payment-profile-id' );

		// set payment total here so it can be modified for later by add-ons
		$order->payment_total = number_format( $order->get_total(), 2, '.', '' );

		// add order description
		$order->description = apply_filters( 'wc_authorize_net_cim_transaction_description', sprintf( __( '%s - Order - %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( get_bloginfo( 'name') ), $order->get_order_number() ), $order_id, $this );

		// allow a PO Number to be included in the transaction
		$order->po_number = apply_filters( 'wc_authorize_net_cim_transaction_po_number', false, $order_id, $this );

		// set transaction type (default to auth_capture)
		$order->transaction_type = ( 'auth_only' === $this->transaction_type ) ? $this->transaction_type : 'auth_capture';

		return $order;
	}


	/**
	 * Create a new customer and payment profile inside CIM
	 *
	 * @since 1.0
	 * @param \WC_Order $order
	 * @return \WC_Order with customer / payment profile IDs added
	 */
	protected function create_customer( $order ) {

		$response = $this->get_api()->create_new_customer( $order );

		$this->log_api();

		$order->customer_profile_id          = $response->get_customer_profile_id();
		$order->customer_shipping_profile_id = $response->get_customer_shipping_profile_id();
		$order->customer_payment_profile_id  = $response->get_customer_payment_profile_id();

		// add customer profile ID / shipping profile ID to user if logged in or creating account at checkout
		if ( is_user_logged_in() || 0 != $order->user_id ) {

			add_user_meta( $order->user_id, '_wc_authorize_net_cim_profile_id', $order->customer_profile_id );

			// add shipping profile if exists
			if ( $order->customer_shipping_profile_id ) {
				add_user_meta( $order->user_id, '_wc_authorize_net_cim_shipping_profile_id', $order->customer_shipping_profile_id );
			}
		}

		// always add customer profile ID / shipping profile ID to order
		update_post_meta( $order->id, '_wc_authorize_net_cim_customer_profile_id', $order->customer_profile_id );
		update_post_meta( $order->id, '_wc_authorize_net_cim_shipping_profile_id', $order->customer_shipping_profile_id );

		// validate payment profile before saving
		$response = $this->get_api()->validate_payment_profile( $order->customer_profile_id, $order->customer_payment_profile_id );

		$this->log_api();

		// add payment profile ID to order
		update_post_meta( $order->id, '_wc_authorize_net_cim_payment_profile_id', $order->customer_payment_profile_id );

		// return if user is not logged or creating account at checkout
		if ( ! is_user_logged_in() && 0 === $order->user_id ) {
			return $order;
		}

		// get payment profile information for saving
		$payment = $response->get_payment_profile_validation_info();

		// save card to account
		$this->add_payment_profile( $order->user_id, $order->customer_payment_profile_id,
			array(
				'type'      => $payment->card_type,
				'last_four' => $payment->card_last_four,
				'exp_date'  => ( ! empty( $order->payment->expiration_date ) ) ? date( 'm/y', strtotime( $order->payment->expiration_date ) ) : __( 'Never', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'active'    => true
			)
		);

		return $order;
	}


	/**
	 * Create a single payment profile inside CIM, used when existing customer is checking out with new card
	 *
	 * @since 1.0
	 * @param \WC_Order $order
	 * @return \WC_Order with new payment profile ID added
	 */
	protected function create_payment_profile( $order ) {

		$response = $this->get_api()->add_new_payment_profile( $order );

		$this->log_api();

		$order->customer_payment_profile_id = $response->get_customer_payment_profile_id();

		// validate payment profile before saving
		$response = $this->get_api()->validate_payment_profile( $order->customer_profile_id, $order->customer_payment_profile_id );

		$this->log_api();

		// add payment profile ID to order
		update_post_meta( $order->id, '_wc_authorize_net_cim_payment_profile_id', $order->customer_payment_profile_id );

		// get payment profile information for saving
		$payment = $response->get_payment_profile_validation_info();

		// save card to account
		$this->add_payment_profile( $order->user_id, $order->customer_payment_profile_id,
			array(
				'type'      => $payment->card_type,
				'last_four' => $payment->card_last_four,
				'exp_date'  => ( ! empty( $order->payment->expiration_date ) ) ? date( 'm/y', strtotime( $order->payment->expiration_date ) ) : __( 'Never', WC_Authorize_Net_CIM::TEXT_DOMAIN ),
				'active'    => true
			)
		);

		return $order;
	}


	/**
	 * Create a transaction using the customer's profile and payment ID
	 *
	 * @since  1.0
	 * @param \WC_Order $order
	 * @param bool $validate true if the payment method should simply be validated instead of processing the transaction
	 * @return bool true if transaction was successful, false otherwise
	 */
	protected function do_transaction( $order, $validate = false ) {

		if ( $validate ) {
			$response = $this->get_api()->validate_payment_profile( $order->customer_profile_id, $order->customer_payment_profile_id, 'liveMode' ); // perform auth/void
		} else {
			$response = $this->get_api()->create_new_transaction( $order ); // create new transaction
		}

		$this->log_api();

		// success! update order record
		if ( $response->transaction_was_successful() ) {

			// set card info from new card being saved
			if ( ! empty( $order->payment->card_number ) || ! empty( $order->payment->account_number ) ) {

				// set card info
				$card_type      = $response->get_transaction_card_type();
				$card_last_four = $response->get_transaction_card_last_four();
				$card_exp_date  = ( ! empty( $order->payment->expiration_date ) ) ? date( 'm/y', strtotime( $order->payment->expiration_date ) ) : __( 'Never', WC_Authorize_Net_CIM::TEXT_DOMAIN );

			// otherwise use info from existing card that is already saved on file
			} else {

				// get card info from saved payment profile
				if ( count( $payment_profile = $this->get_payment_profile( $order->user_id, $order->customer_payment_profile_id ) ) > 0 ) {

					$card_type      = $payment_profile['type'];
					$card_last_four = $payment_profile['last_four'];
					$card_exp_date  = $payment_profile['exp_date'];

					// set this card as active payment profile
					$this->set_active_payment_profile( $order->user_id, $order->customer_payment_profile_id );
				}
			}

			// process held transactions
			if ( $response->transaction_was_held() ) {

				// mark the order has 'on-hold' and add custom message to be displayed on thank you page
				$this->mark_order_as_held( $order, $response->get_transaction_id(), $response->get_transaction_held_message() );

			} else {

				// not held, just add order note
				if ( $validate ) {
					$message = sprintf( __( 'Authorize.net Authorization Approved: %s ending in %s (expires %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $card_type, $card_last_four, $card_exp_date );
				} else {
					$message = sprintf( __( 'Authorize.net Transaction Approved: %s ending in %s (expires %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $card_type, $card_last_four, $card_exp_date );
				}

				$order->add_order_note( $message );
			}

			// add order meta
			update_post_meta( $order->id, '_wc_authorize_net_cim_trans_id', $response->get_transaction_id() );
			update_post_meta( $order->id, '_wc_authorize_net_cim_card_type', $card_type );
			update_post_meta( $order->id, '_wc_authorize_net_cim_card_last_four', $card_last_four );
			update_post_meta( $order->id, '_wc_authorize_net_cim_card_exp_date', $card_exp_date );
			update_post_meta( $order->id, '_wc_authorize_net_cim_trans_mode', $this->get_api_environment() );

			// add customer profile ID & payment profile ID (this will update them if not set previously)
			update_post_meta( $order->id, '_wc_authorize_net_cim_customer_profile_id', $order->customer_profile_id );
			update_post_meta( $order->id, '_wc_authorize_net_cim_payment_profile_id', $order->customer_payment_profile_id );

			// add shipping profile ID if available
			if ( $order->customer_shipping_profile_id ) {
				update_post_meta( $order->id, '_wc_authorize_net_cim_shipping_profile_id', $order->customer_shipping_profile_id );
			}

			return true;

		// failure
		} else {

			$this->mark_order_as_failed( $order, $response->get_transaction_failure_message() );

			return false;
		}
	}


	/**
	 * Create a transaction using the payment information at checkout. Does not save any profile information, as this
	 * should only be used for guest checkouts
	 *
	 * @since 1.0
	 * @param \WC_Order $order
	 * @return bool true if payment was successful, false otherwise
	 */
	private function do_single_transaction( $order ) {

		$response = $this->get_api()->create_single_transaction( $order );

		$this->log_api();

		// check for held transaction
		if ( $response->single_transaction_was_held() ) {

			// mark the order has 'on-hold' and add custom message to be displayed on thank you page
			$this->mark_order_as_held( $order, $response->get_single_transaction_id(), $response->get_single_transaction_held_message() );

			// add order meta
			add_post_meta( $order->id, '_wc_authorize_net_cim_trans_id', $response->get_single_transaction_id() );
			add_post_meta( $order->id, '_wc_authorize_net_cim_trans_mode', $this->get_api_environment() );

			return true;
		}

		// check for transaction success
		if ( $response->single_transaction_was_successful() ) {

			// set payment info
			$payment_type      = $response->get_single_transaction_payment_type();
			$payment_last_four = $response->get_single_transaction_payment_last_four();
			$payment_exp_date  = ( ! empty( $order->payment->expiration_date ) ) ? date( 'm/y', strtotime( $order->payment->expiration_date ) ) : __( 'Never', WC_Authorize_Net_CIM::TEXT_DOMAIN );

			// add order note
			$order->add_order_note( sprintf( __( 'Authorize.net Transaction Approved: %s ending in %s (expires %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $payment_type, $payment_last_four, $payment_exp_date ) );

			// add order meta
			add_post_meta( $order->id, '_wc_authorize_net_cim_trans_id', $response->get_single_transaction_id() );
			add_post_meta( $order->id, '_wc_authorize_net_cim_card_type', $payment_type );
			add_post_meta( $order->id, '_wc_authorize_net_cim_card_last_four', $payment_last_four );
			add_post_meta( $order->id, '_wc_authorize_net_cim_card_exp_date', $payment_exp_date );
			add_post_meta( $order->id, '_wc_authorize_net_cim_trans_mode', $this->get_api_environment() );

			return true;

		} else {

			// payment failure
			$this->mark_order_as_failed( $order, $response->get_single_transaction_failure_message() );

			return false;
		}
	}


	/**
	 * Mark the given order as 'on-hold' and set a message to display to the customer
	 *
	 * @since  1.0
	 * @param \WC_Order $order the order
	 * @param string $transaction_id the transaction ID from auth.net
	 * @param string $message a message to display on the 'order received' page
	 */
	protected function mark_order_as_held( $order, $transaction_id, $message ) {

		$order_note = sprintf( __( 'Authorize.net Transaction Held for Review (Transaction ID: %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $transaction_id );

		// mark order as held
		if ( 'on-hold' != $order->status ) {
			$order->update_status( 'on-hold', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}

		// add message
		SV_WC_Plugin_Compatibility::wc_add_notice( $message );

		// set message to be displayed on next page load
		SV_WC_Plugin_Compatibility::set_messages();
	}


	/**
	 * Mark the given order as failed and set the order note
	 *
	 * @since  1.0
	 * @param \WC_Order $order the order
	 * @param string $error_message a message to display inside the "Credit Card Payment Failed" order note
	 */
	protected function mark_order_as_failed( $order, $error_message ) {

		$order_note = sprintf( __( 'Authorize.net Payment Failed (%s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $error_message );

		// Mark order as failed if not already set, otherwise, make sure we add the order note so we can detect when someone fails to check out multiple times
		if ( $order->status != 'failed' ) {
			$order->update_status( 'failed', $order_note );
		} else {
			$order->add_order_note( $order_note );
		}

		$this->add_debug_message( $error_message, 'error' );

		SV_WC_Plugin_Compatibility::wc_add_notice( __( 'An error occurred, please try again or try an alternate form of payment.', WC_Authorize_Net_CIM::TEXT_DOMAIN ), 'error' );
	}


	/**
	 * Get the available payment profiles for a user as an associative array in format:
	 * [ payment profile id ] = array with keys:
	 *      type => type of card, e.g. Amex (or Bank Account for eChecks)
	 *      last_four => last four card digits, e.g. 1234
	 *      exp_date => expiration date in MM/YY format
	 *      active => boolean, default card if true
	 *
	 * @since 1.0
	 * @param int $user_id
	 * @return array
	 */
	public function get_payment_profiles( $user_id ) {

		$payment_profiles = get_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles', true );

		return is_array( $payment_profiles ) ? $payment_profiles : array();
	}


	/**
	 * Get the given payment profile for a user as an associative array in format:
	 * [ payment profile id ] = array with keys:
	 *      type => type of card, e.g. Amex (or Bank Account for eChecks)
	 *      last_four => last four card digits, e.g. 1234
	 *      exp_date => expiration date in MM/YY format
	 *      active => boolean, default card if true
	 *
	 * @since 1.0
	 * @param int $profile_id
	 * @param int $user_id
	 * @return array
	 */
	public function get_payment_profile( $user_id, $profile_id ) {

		$payment_profiles = get_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles', true );

		return isset( $payment_profiles[ $profile_id ] ) ? $payment_profiles[ $profile_id ] : array();
	}


	/**
	 * Add a payment method and ID as user meta. Note this does not add the payment profile to CIM, it is only used to
	 * add a payment method to the databases that has already been added to CIM
	 *
	 * @since 1.0
	 * @param int $user_id
	 * @param int $profile_id payment profile ID to add
	 * @param array $payment payment info to add
	 * @return bool|int false if profile not added, user meta ID if added
	 */
	public function add_payment_profile( $user_id, $profile_id, $payment ) {

		$payment_profiles = $this->get_payment_profiles( $user_id );

		// handle updating existing payment profiles
		if ( ! empty( $payment_profiles ) ) {

			// prevent duplicate profiles
			if ( array_key_exists( $profile_id, $payment_profiles ) ) {
				return false;
			}

			// mark new card as active if set
			if ( isset( $payment['active'] ) && true == $payment['active'] ) {

				foreach ( $payment_profiles as $payment_profile_id => $payment_profile ) {

					if ( $profile_id == $payment_profile_id ) {
						$payment_profiles[ $payment_profile_id ]['active'] = true;
					} else {
						$payment_profiles[ $payment_profile_id ]['active'] = false;
					}
				}
			}
		}

		$payment_profiles[ $profile_id ] = $payment;

		return update_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles', $payment_profiles );
	}


	/**
	 * Delete a payment profile from user meta and CIM
	 *
	 * @since 1.0
	 * @param int $user_id
	 * @param int $profile_id payment profile ID to delete
	 * @return bool|int false if not deleted, updated user meta ID if deleted
	 */
	public function delete_payment_profile( $user_id, $profile_id ) {
		global $wc_authorize_net_cim;

		$payment_profiles = $this->get_payment_profiles( $user_id );

		if ( empty( $payment_profiles ) ) {
			return false;
		}

		$customer_profile_id = get_user_meta( $user_id, '_wc_authorize_net_cim_profile_id', true );

		if ( empty( $customer_profile_id ) ) {
			return false;
		}

		try {

			// delete payment profile via API
			$this->get_api()->delete_payment_profile( $customer_profile_id, $profile_id );

			// remove the payment profile
			if ( isset( $payment_profiles[ $profile_id ] ) ) {
				unset( $payment_profiles[ $profile_id ] );
			}

			// set a default card if one isn't already set
			if ( ! empty( $payment_profiles ) ) {

				$has_default = false;

				// if another active card is found, don't set a new active card
				foreach ( $payment_profiles as $payment_profile_id => $payment_profile ) {

					if ( $payment_profile['active'] ) {
						$has_default = true;
					}
				}

				// set first card as default card if none found
				if ( ! $has_default ) {
					reset( $payment_profiles );
					$payment_profiles[ key( $payment_profiles ) ]['active'] = true;
				}
			}

			return update_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles', $payment_profiles );

		} catch ( Exception $e ) {

			if ( 'log' == $this->debug_mode || 'both' == $this->debug_mode ) {
				$wc_authorize_net_cim->log( $e->getMessage() );
			}

			return false;
		}
	}


	/**
	 * Gets the active payment profile ID for a user
	 *
	 * @since  1.0
	 * @param int $user_id
	 * @return string|bool payment profile ID or false if none found
	 */
	public function get_active_payment_profile_id( $user_id ) {

		$payment_profiles = get_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles', true );

		foreach ( $payment_profiles as $payment_profile_id => $payment_profile ) {

			if ( true == $payment_profile['active'] ) {
				return $payment_profile_id;
			}
		}

		return false;
	}


	/**
	 * Set the active payment profile for a user. This is shown as "Default card" in the frontend and will be auto-selected
	 * during checkout
	 *
	 * @since  1.0
	 * @param int $user_id
	 * @param int $profile_id
	 * @return string|bool false if not set, updated user meta ID if set
	 */
	public function set_active_payment_profile( $user_id, $profile_id ) {

		$payment_profiles = $this->get_payment_profiles( $user_id );

		if ( empty( $payment_profiles ) ) {
			return false;
		}

		foreach ( $payment_profiles as $payment_profile_id => $payment_profile ) {

			if ( $profile_id == $payment_profile_id ) {
				$payment_profiles[ $payment_profile_id ]['active'] = true;
			} else {
				$payment_profiles[ $payment_profile_id ]['active'] = false;
			}
		}

		return update_user_meta( $user_id, '_wc_authorize_net_cim_payment_profiles', $payment_profiles );
	}


	/**
	 * Get saved credit card payment profiles (excluding any saved bank accounts)
	 *
	 * @since 1.0
	 * @param int $user_id
	 * @return array see get_payment_profile() method for array format
	 */
	private function get_saved_credit_cards( $user_id ) {

		$payment_profiles = $this->get_payment_profiles( $user_id );

		if ( empty( $payment_profiles ) ) {
			return array();
		}

		foreach ( $payment_profiles as $payment_profile_id => $payment_profile ) {

			// remove all bank account payment types
			if ( 'Bank Account' === $payment_profile['type'] ) {
				unset( $payment_profiles[ $payment_profile_id ] );
			}
		}

		return $payment_profiles;
	}


	/**
	 * Display the 'My Payment Methods' section on the 'My Account' page
	 *
	 * @since  1.0
	 */
	public function show_my_payment_methods() {

		$user_id = get_current_user_id();

		if ( ! $this->is_available() ) {
			return;
		}

		// process payment method actions
		if ( ! empty( $_GET['cim-profile-id'] ) && ! empty( $_GET['cim-action'] ) && ! empty( $_GET['_wpnonce'] ) ) {

			// security check
			if ( false === wp_verify_nonce( $_GET['_wpnonce'], __FILE__ ) ) {
				wp_die( __( 'There was an error with your request, please try again.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
			}

			$profile_id = (int) $_GET['cim-profile-id'];

			if ( ! $profile_id ) {
				wp_die( __( 'Profile ID is invalid, please try again.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
			}

			// handle deletion
			if ( 'delete' === $_GET['cim-action'] ) {
				$this->delete_payment_profile( $user_id, $profile_id );
			}

			// handle active change
			if ( 'make-active' === $_GET['cim-action'] ) {
				$this->set_active_payment_profile( $user_id, $profile_id );
			}
		}

		// get available saved payment methods
		$payment_methods = $this->get_payment_profiles( $user_id );

		?> <h2 id="cim-my-payment-methods" style="margin-top:40px;"><?php _e( 'Manage My Payment Methods', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></h2><?php

		if ( ! empty( $payment_methods ) ) :
			?>
		<a name="cim-my-payment-methods"></a>
		<table class="shop_table my-account-cim-payment-methods">

			<thead>
			<tr>
				<th class="cim-payment-method-label"><span class="nobr"><?php _e( 'Payment Method', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></span></th>
				<th class="cim-payment-method-exp-date"><span class="nobr"><?php _e( 'Expires', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></span></th>
				<th class="cim-payment-method-status"><span class="nobr"><?php _e( 'Status', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></span></th>
				<th class="cim-payment-method-actions"><span class="nobr"><?php _e( 'Actions', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></span></th>
			</tr>
			</thead>

			<tbody>
				<?php foreach ( $payment_methods as $payment_profile_id => $payment_method ) :
				$delete_url      = wp_nonce_url( add_query_arg( array( 'cim-profile-id' => $payment_profile_id, 'cim-action' => 'delete' ) ), __FILE__ );
				$make_active_url = wp_nonce_url( add_query_arg( array( 'cim-profile-id' => $payment_profile_id, 'cim-action' => 'make-active' ) ), __FILE__ );
				?>
			<tr class="cim-payment-method-label">
				<td class="card-label">
					<?php printf( __( '%s ending in %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( $payment_method['type'] ), esc_html( $payment_method['last_four'] ) ); ?>
				</td>
				<td class="cim-payment-method-exp-date">
					<?php echo esc_html( $payment_method['exp_date'] ); ?>
				</td>
				<td class="cim-payment-method-status">
					<?php echo ( $payment_method['active'] ) ? __( 'Default', WC_Authorize_Net_CIM::TEXT_DOMAIN ) : '<a href="' . esc_url( $make_active_url ) . '">' . __( 'Make Default', WC_Authorize_Net_CIM::TEXT_DOMAIN ) . '</a>'; ?>
				</td>
				<td class="cim-payment-method-actions" style="width: 1%; text-align: center;">
					<a href="<?php echo esc_url( $delete_url ); ?>" class="cim-delete-payment-method"><img src="<?php echo esc_attr( $GLOBALS['wc_authorize_net_cim']->get_plugin_url() . '/assets/cross.png' ); ?>" alt="[X]" /></a>
				</td>
			</tr>
				<?php endforeach; ?>
			</tbody>

		</table>
		<?php

		else :

			?><p><?php _e( 'You do not have any saved payment methods.', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></p><?php

		endif;

		// Add confirm javascript when deleting cards
		ob_start();
		?>
			$('a.cim-delete-payment-method').click(function (e) {
				if( ! confirm(' <?php _e( 'Are you sure you want to delete this payment method?', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?> ') ) {
					e.preventDefault();
				}
			});
		<?php
		SV_WC_Plugin_Compatibility::wc_enqueue_js( ob_get_clean() );
	}


	/**
	 * Get the API object
	 *
	 * @since 1.0
	 * @return \WC_Authorize_Net_CIM_API
	 */
	public function get_api() {
		global $wc_authorize_net_cim;

		if ( is_object( $this->api ) ) {
			return $this->api;
		}

		// Authorize.net API
		require( $wc_authorize_net_cim->get_plugin_path() . '/classes/class-wc-authorize-net-cim-api.php' );

		return $this->api = new WC_Authorize_Net_CIM_API( $this->get_api_login_id(), $this->get_api_transaction_key(), $this->get_api_environment() );
	}


	/**
	 * Perform standard luhn check.  Algorithm:
	 *
	 * 1. Double the value of every second digit beginning with the second-last right-hand digit.
	 * 2. Add the individual digits comprising the products obtained in step 1 to each of the other digits in the original number.
	 * 3. Subtract the total obtained in step 2 from the next higher number ending in 0.
	 * 4. This number should be the same as the last digit (the check digit). If the total obtained in step 2 is a number ending in zero (30, 40 etc.), the check digit is 0.
	 *
	 * @since 1.0
	 * @param string : $account_number the credit card number to check
	 * @return bool :true if $account_number passes the check, false otherwise
	 */
	private function luhn_check( $account_number ) {
		$sum = 0;
		for ( $i = 0, $ix = strlen( $account_number ); $i < $ix - 1; $i++) {
			$weight = substr( $account_number, $ix - ( $i + 2 ), 1 ) * ( 2 - ( $i % 2 ) );
			$sum += $weight < 10 ? $weight : $weight - 9;
		}

		return substr( $account_number, $ix - 1 ) == ( ( 10 - $sum % 10 ) % 10 );
	}


	/**
	 * Safely get and trim data from $_POST
	 *
	 * @since 1.0
	 * @param string $key array key to get from $_POST array
	 * @return string value from $_POST or blank string if $_POST[ $key ] is not set
	 */
	protected function get_post( $key ) {

		if ( isset( $_POST[ $key ] ) ) {
			return trim( $_POST[ $key ] );
		}

		return '';
	}


	/**
	 * Get the login ID for use with the API, can be either production or test credentials
	 *
	 * @since 1.0
	 * @return string login ID
	 */
	protected function get_api_login_id() {

		if ( 'production' == $this->get_api_environment() ) {
			return $this->api_login_id;
		} else {
			return $this->test_api_login_id;
		}
	}


	/**
	 * Get the transaction key for use with the API, can be either production or test credentials
	 *
	 * @since  1.0
	 * @return string transaction key
	 */
	protected function get_api_transaction_key() {

		if ( 'production' == $this->get_api_environment() ) {
			return $this->api_transaction_key;
		} else {
			return $this->test_api_transaction_key;
		}
	}


	/**
	 * Get the current API environment, 'test' or 'production'
	 *
	 * @since  1.0
	 * @return string API environment
	 */
	protected function get_api_environment() {

		return ( 'yes' === $this->test_mode ) ? 'test' : 'production';
	}


	/**
	 * Checks if API is in test mode
	 *
	 * @since  1.0
	 * @return bool true if test mode is active, false otherwise
	 */
	protected function is_test_mode() {

		return 'yes' === $this->test_mode;
	}


	/**
	 * Checks if API is in test mode
	 *
	 * @since 1.0.8
	 * @return string the merchant's payment processor location
	 */
	protected function get_payment_process_location() {

		return $this->payment_processor_location;
	}


	/**
	 * Checks if the CIM add-on is enabled for the provided authorize.net account by requesting a token for
	 * the hosted profile page using dummy data. The 'getHostedProfilePageRequest' method was chosen as it's lightweight
	 * and multiple calls to it have no affect on the provided authorize.net account
	 *
	 * @since 1.0.4
	 * @return bool true if CIM feature is enabled on provided authorize.net account, false otherwise
	 */
	public function is_cim_feature_enabled() {

		try {

			$response = $this->get_api()->get_hosted_profile_page_token( get_current_user_id() );

		} catch ( Exception $e ) {

			// 'E00044' is 'Customer Information Manager is not enabled.' error
			if ( false !== strpos( $e->getMessage(), '[E00044]' ) ) {
				return false;
			}
		}

		return true;
	}


	/**
	 * Adds debug messages to the page as a WC message/error, and / or to the WC Error log
	 *
	 * @since 1.0
	 * @param string $message message to add
	 * @param string $type how to add the message, options are:
	 *     'message' (styled as WC message), 'error' (styled as WC Error) or 'xml' (XML formatted for output as HTML)
	 * @param bool $set_message sets any WC messages/errors provided so they appear on the next page load, useful for display messages on the thank you page
	 */
	protected function add_debug_message( $message, $type = 'message', $set_message = false ) {
		global $wc_authorize_net_cim;

		// do nothing when debug mode is off
		if ( 'off' == $this->debug_mode ) {
			return;
		}

		// add debug message to woocommerce->errors/messages if checkout or both is enabled
		if ( 'checkout' === $this->debug_mode || 'both' === $this->debug_mode ) {

			if ( 'message' === $type ) {

				SV_WC_Plugin_Compatibility::wc_add_notice( $message );

			} elseif ( 'xml' === $type ) {

				$dom = new DOMDocument();
				$dom->loadXML( $message );
				$dom->formatOutput = true;
				SV_WC_Plugin_Compatibility::wc_add_notice( "API Request/Response: <br/><pre>" . htmlspecialchars( $dom->saveXML() ) . "</pre>" );

			} else {

				// defaults to error message
				SV_WC_Plugin_Compatibility::wc_add_notice( $message, 'error' );
			}
		}

		// set messages for next page load
		if ( $set_message ) {
			SV_WC_Plugin_Compatibility::set_messages();
		}

		// add log message to WC logger if log/both is enabled
		if ( 'log' === $this->debug_mode || 'both' === $this->debug_mode ) {
			$wc_authorize_net_cim->log( $message );
		}
	}


	/**
	 * Helper to get API response/request XML saved to messages/log. Some functions perform multiple
	 * API calls, so this gets the XML after each request/response to aid with troubleshooting
	 *
	 * @since 1.0
	 */
	protected function log_api() {

		// log request/response XML if enabled
		$this->add_debug_message( $this->get_api()->get_request_xml(), 'xml', true );
		$this->add_debug_message( $this->get_api()->get_response_xml(), 'xml', true );
	}


	/**
	 * Require all billing fields to be entered when the merchant is using a European payment processor
	 *
	 * @since 1.0.8
	 * @param array $locales array of countries and locale-specific address field info
	 * @return array the locales array with billing info required
	 */
	public function require_billing_fields( $locales ) {

		foreach( $locales as $country_code => $fields ) {

			if ( isset( $locales[ $country_code ]['state']['required'] ) ) {
				$locales[ $country_code ]['state']['required'] = true;
			}
		}

		return $locales;
	}


} // end \WC_Gateway_Authorize_Net_CIM class

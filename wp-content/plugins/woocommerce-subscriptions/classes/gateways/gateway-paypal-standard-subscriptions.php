<?php
/**
 * PayPal Standard Subscription Class. 
 * 
 * Filters necessary functions in the WC_Paypal class to allow for subscriptions.
 * 
 * @package		WooCommerce Subscriptions
 * @subpackage	WC_PayPal_Standard_Subscriptions
 * @category	Class
 * @author		Brent Shepherd
 * @since		1.0
 */

/**
 * Needs to be called after init so that $woocommerce global is setup
 **/
function create_paypal_standard_subscriptions() {
	WC_PayPal_Standard_Subscriptions::init();
}
add_action( 'init', 'create_paypal_standard_subscriptions', 10 );


class WC_PayPal_Standard_Subscriptions {

	protected static $log;

	protected static $debug;

	public static $api_username;
	public static $api_password;
	public static $api_signature;

	public static $api_endpoint;

	private static $invoice_prefix;

	private static $paypal_settings;

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 *
	 * @since 1.0
	 */
	public static function init() {
		global $woocommerce;

		self::$paypal_settings = self::get_wc_paypal_settings();

		// Logs
		self::$debug = ( self::$paypal_settings['debug'] == 'yes' ) ? true : false;

		if ( self::$debug ) {
			self::$log = class_exists( 'WC_Logger' ) ? new WC_Logger() : $woocommerce->logger(); // WC <> 2.1
		} else {
			self::$log = '';
		}

		// Set creds
		self::$api_username  = ( isset( self::$paypal_settings['api_username'] ) ) ? self::$paypal_settings['api_username'] : '';
		self::$api_password  = ( isset( self::$paypal_settings['api_password'] ) ) ? self::$paypal_settings['api_password'] : '';
		self::$api_signature = ( isset( self::$paypal_settings['api_signature'] ) ) ? self::$paypal_settings['api_signature'] : '';

		// Invoice prefix added in WC 1.6.3
		self::$invoice_prefix = ( isset( self::$paypal_settings['invoice_prefix'] ) ) ? self::$paypal_settings['invoice_prefix'] : '';

		self::$api_endpoint = ( self::$paypal_settings['testmode'] == 'no' ) ? 'https://api-3t.paypal.com/nvp' :  'https://api-3t.sandbox.paypal.com/nvp';

		// When necessary, set the PayPal args to be for a subscription instead of shopping cart
		add_filter( 'woocommerce_paypal_args', __CLASS__ . '::paypal_standard_subscription_args' );

		// Check a valid PayPal IPN request to see if it's a subscription *before* WC_Paypal::successful_request()
		add_action( 'valid-paypal-standard-ipn-request', __CLASS__ . '::process_paypal_ipn_request', 9 );

		// Set the PayPal Standard gateway to support subscriptions after it is added to the woocommerce_payment_gateways array
		add_filter( 'woocommerce_payment_gateway_supports', __CLASS__ . '::add_paypal_standard_subscription_support', 10, 3 );

		// Add PayPal API fields to PayPal form fields as required
		add_action( 'woocommerce_settings_start', __CLASS__ . '::add_subscription_form_fields', 100 );
		add_action( 'woocommerce_api_wc_gateway_paypal', __CLASS__ . '::add_subscription_form_fields', 100 );

		// Save PayPal settings in WC 2.0+
		add_action( 'woocommerce_update_options_payment_gateways_paypal', __CLASS__ . '::save_subscription_form_fields', 11 );

		// When a subscriber or store manager changes a subscription's status in the store, change the status with PayPal
		add_action( 'cancelled_subscription_paypal', __CLASS__ . '::cancel_subscription_with_paypal', 10, 2 );
		add_action( 'subscription_put_on-hold_paypal', __CLASS__ . '::suspend_subscription_with_paypal', 10, 2 );
		add_action( 'reactivated_subscription_paypal', __CLASS__ . '::reactivate_subscription_with_paypal', 10, 2 );

		// Don't copy over PayPal details to new Parent Orders
		add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', __CLASS__ . '::remove_renewal_order_meta', 10, 4 );

		// Maybe show notice to enter PayPal API credentials
		add_action( 'admin_notices', __CLASS__ . '::maybe_show_admin_notice' );

		// When a payment is due, schedule a special check in one days time to make sure the payment went through
		add_action( 'scheduled_subscription_payment_paypal', __CLASS__ . '::scheduled_subscription_payment', 10, 3 );
		add_action( 'paypal_check_subscription_payment', __CLASS__ . '::check_subscription_payment' );

		// Don't immediately cancel a subscription with PayPal - instead, we'll cancel it ourselves when a success IPN request for a new payment method comes through
		add_action( 'woocommerce_subscriptions_pre_update_recurring_payment_method', __CLASS__ . '::maybe_remove_cancelled_subscription_hook', 10, 4 );
		add_action( 'woocommerce_subscriptions_updated_recurring_payment_method', __CLASS__ . '::maybe_readd_cancelled_subscription_hook', 10, 4 );
	}

	/**
	 * Checks if the PayPal API credentials are set.
	 *
	 * @since 1.0
	 */
	public static function are_credentials_set() {

		$credentials_are_set = false;

		if ( ! empty( self::$api_username ) && ! empty( self::$api_password ) && ! empty( self::$api_signature ) ) {
			$credentials_are_set = true;
		}

		return apply_filters( 'wooocommerce_paypal_credentials_are_set', $credentials_are_set );
	}

	/**
	 * Add subscription support to the PayPal Standard gateway.
	 *
	 * @since 1.0
	 */
	public static function add_paypal_standard_subscription_support( $is_supported, $feature, $gateway ) {

		if ( 'paypal' == $gateway->id ) {
			if ( in_array( $feature, array( 'subscriptions', 'gateway_scheduled_payments', 'subscription_payment_method_change' ) ) ) {
				$is_supported = true;
			} elseif ( in_array( $feature, array( 'subscription_cancellation', 'subscription_suspension', 'subscription_reactivation' ) ) && self::are_credentials_set() ) {
				$is_supported = true;
			}
		}

		return $is_supported;
	}

	/**
	 * When a PayPal IPN messaged is received for a subscription transaction, 
	 * check the transaction details and 
	 *
	 * @since 1.0
	 */
	public static function process_paypal_ipn_request( $transaction_details ) {
		global $wpdb;

		$transaction_details = stripslashes_deep( $transaction_details );

		if ( ! in_array( $transaction_details['txn_type'], array( 'subscr_signup', 'subscr_payment', 'subscr_cancel', 'subscr_eot', 'subscr_failed', 'subscr_modify' ) ) ) {
			return;
		}

		// Get the $order_id & $order_key with backward compatibility
		extract( self::get_order_id_and_key( $transaction_details ) );

		$transaction_details['txn_type'] = strtolower( $transaction_details['txn_type'] );

		if ( self::$debug ) {
			self::$log->add( 'paypal', 'Subscription Transaction Type: ' . $transaction_details['txn_type'] );
		}

		if ( self::$debug ) {
			self::$log->add( 'paypal', 'Subscription transaction details: ' . print_r( $transaction_details, true ) );
		}

		$order = new WC_Order( $order_id );

		// We have an invalid $order_id, probably because invoice_prefix has changed since the subscription was first created, so get the order by order key
		if ( ! isset( $order->id ) ) {
			$order_id = ( function_exists( 'woocommerce_get_order_id_by_order_key' ) ) ? woocommerce_get_order_id_by_order_key( $order_key ) : $wpdb->get_var( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_order_key' AND meta_value = '{$order_key}'" );
			$order = new WC_Order( $order_id );
		}

		if ( $order->order_key !== $order_key ) {
			if ( self::$debug ) {
				self::$log->add( 'paypal', 'Subscription IPN Error: Order Key does not match invoice.' );
			}
			exit;
		}

		if ( 'paypal' != $order->recurring_payment_method ) {
			if ( self::$debug ) {
				self::$log->add( 'paypal', 'IPN ignored, recurring payment method has changed.' );
			}
			exit;
		}

		if ( isset( $transaction_details['ipn_track_id'] ) ) {

			// Make sure the IPN request has not already been handled
			$handled_ipn_requests = get_post_meta( $order_id, '_paypal_ipn_tracking_ids', true );

			if ( empty ( $handled_ipn_requests ) ) {
				$handled_ipn_requests = array();
			}

			// The 'ipn_track_id' is not a unique ID and is shared between different transaction types, so create a unique ID by prepending the transaction type
			$ipn_id = $transaction_details['txn_type'] . '_' . $transaction_details['ipn_track_id'];

			if ( in_array( $ipn_id, $handled_ipn_requests ) ) {
				if ( self::$debug ) {
					self::$log->add( 'paypal', 'Subscription IPN Error: This IPN message has already been correctly handled.' );
				}
				exit;
			}
		}

		if ( isset( $transaction_details['txn_id'] ) ) {

			// Make sure the IPN request has not already been handled
			$handled_transactions = get_post_meta( $order_id, '_paypal_transaction_ids', true );

			if ( empty ( $handled_transactions ) ) {
				$handled_transactions = array();
			}

			$transaction_id = $transaction_details['txn_id'];

			if ( isset( $transaction_details['txn_type'] ) ) {
				$transaction_id .= '_' . $transaction_details['txn_type'];
			}

			// The same transaction ID is used for different payment statuses, so make sure we handle it only once. See: http://stackoverflow.com/questions/9240235/paypal-ipn-unique-identifier
			if ( isset( $transaction_details['payment_status'] ) ) {
				$transaction_id .= '_' . $transaction_details['payment_status'];
			}

			if ( in_array( $transaction_id, $handled_transactions ) ) {
				if ( self::$debug ) {
					self::$log->add( 'paypal', 'Subscription IPN Error: This transaction has already been correctly handled.' );
				}
				exit;
			}
		}

		// Save the profile ID if it's not a cancellation/expiration request
		if ( isset( $transaction_details['subscr_id'] ) && ! in_array( $transaction_details['txn_type'], array( 'subscr_cancel', 'subscr_eot' ) ) ) {
			update_post_meta( $order_id, 'PayPal Subscriber ID', $transaction_details['subscr_id'] );
		}

		// Get the subscription this IPN message relates to
		$subscriptions_in_order = WC_Subscriptions_Order::get_recurring_items( $order );
		$subscription_item      = array_pop( $subscriptions_in_order );
		$product_id             = WC_Subscriptions_Order::get_items_product_id( $subscription_item );
		$subscription_key       = WC_Subscriptions_Manager::get_subscription_key( $order->id, $product_id );
		$subscription           = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		$is_first_payment       = empty( $subscription['completed_payments'] ) ? true : false;

		if ( 'switched' === $subscription['status'] ) {
			if ( self::$debug ) {
				self::$log->add( 'paypal', 'IPN ignored, subscription has been switched.' );
			}
			exit;
		}

		switch( $transaction_details['txn_type'] ) {
			case 'subscr_signup':

				// Store PayPal Details
				update_post_meta( $order_id, 'Payer PayPal address', $transaction_details['payer_email']);
				update_post_meta( $order_id, 'Payer PayPal first name', $transaction_details['first_name']);
				update_post_meta( $order_id, 'Payer PayPal last name', $transaction_details['last_name']);

				$default_invoice_string = self::$paypal_settings['invoice_prefix'] . ltrim( $order->get_order_number(), '#' );

				// If the invoice ID doesn't match the default invoice ID and contains the string '-wcscpm-', the IPN is for a subscription payment method change
				if ( $default_invoice_string != $transaction_details['invoice'] && false !== strpos( $transaction_details['invoice'], '-wcscpm-' ) ) {
					$is_payment_change = true;
				} else {
					$is_payment_change = false;
				}

				$switched_subscription_key = get_post_meta( $order_id, '_switched_subscription_key', true );
				$no_initial_payment = ( 0 == WC_Subscriptions_Order::get_total_initial_payment( $order ) && WC_Subscriptions_Order::get_subscription_trial_length( $order ) > 0 ) ? true : false;

				// When there is a free trial & no initial payment amount, we need to mark the order as paid and activate the subscription
				if ( ! $is_payment_change && ( ! empty( $switched_subscription_key ) || $no_initial_payment ) ) {
					$order->payment_complete();
				}

				// Payment completed
				if ( $is_payment_change ) {

					$old_payment_method = get_post_meta( $order->id, '_old_recurring_payment_method', true );

					// We need to cancel the subscription now that the method has been changed successfully
					if ( 'paypal' == $old_payment_method ) {

						$profile_id = get_post_meta( $order->id, '_old_paypal_subscriber_id', true );

						self::cancel_subscription_with_paypal( $order, $product_id, $profile_id );

					}

					$order->add_order_note( __( 'IPN subscription payment method changed.', 'woocommerce-subscriptions' ) );

				} else {

					$order->add_order_note( __( 'IPN subscription sign up completed.', 'woocommerce-subscriptions' ) );

				}

				if ( self::$debug ){
					if ( $is_payment_change ) {
						self::$log->add( 'paypal', 'IPN subscription payment method changed for order ' . $order_id );
					} else {
						self::$log->add( 'paypal', 'IPN subscription sign up completed for order ' . $order_id );
					}
				}

				break;

			case 'subscr_payment':

				if ( 'completed' == strtolower( $transaction_details['payment_status'] ) ) {
					// Store PayPal Details
					update_post_meta( $order_id, 'PayPal Transaction ID', $transaction_details['txn_id'] );
					update_post_meta( $order_id, 'Payer PayPal first name', $transaction_details['first_name'] );
					update_post_meta( $order_id, 'Payer PayPal last name', $transaction_details['last_name'] );
					update_post_meta( $order_id, 'PayPal Payment type', $transaction_details['payment_type'] ); 

					// Subscription Payment completed
					$order->add_order_note( __( 'IPN subscription payment completed.', 'woocommerce-subscriptions' ) );

					if ( self::$debug ) {
						self::$log->add( 'paypal', 'IPN subscription payment completed for order ' . $order_id );
					}

					// First payment on order, process payment & activate subscription
					if ( $is_first_payment ) {

						$order->payment_complete();

						WC_Subscriptions_Manager::activate_subscriptions_for_order( $order );

					} elseif ( ! in_array( $subscription['status'], array( 'cancelled', 'expired', 'switched', 'trash' ) ) ) {

						// We don't need to reactivate the subscription because Subs didn't suspend it
						remove_action( 'reactivated_subscription_paypal', __CLASS__ . '::reactivate_subscription_with_paypal', 10, 2 );

						WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );

						// Make sure the next payment date is sync with when PayPal processes the payments
						WC_Subscriptions_Manager::set_next_payment_date( $subscription_key, $order->customer_user );

						add_action( 'reactivated_subscription_paypal', __CLASS__ . '::reactivate_subscription_with_paypal', 10, 2 );

					}

				} elseif ( 'failed' == strtolower( $transaction_details['payment_status'] ) ) {

					// Subscription Payment completed
					$order->add_order_note( __( 'IPN subscription payment failed.', 'woocommerce-subscriptions' ) );

					if ( self::$debug ) {
						self::$log->add( 'paypal', 'IPN subscription payment failed for order ' . $order_id );
					}

					// First payment on order, don't generate a renewal order
					if ( $is_first_payment ) {
						remove_action( 'processed_subscription_payment_failure', 'WC_Subscriptions_Renewal_Order::generate_failed_payment_renewal_order', 10, 2 );
					}

					WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order );

				} else {

					if ( self::$debug ) {
						self::$log->add( 'paypal', 'IPN subscription payment notification received for order ' . $order_id  . ' with status ' . $transaction_details['payment_status'] );
					}

				}

				break;

			case 'subscr_cancel':

				// Make sure the subscription hasn't been linked to a new payment method
				if ( $transaction_details['subscr_id'] != self::get_subscriptions_paypal_id( $order ) ) {

					if ( self::$debug ) {
						self::$log->add( 'paypal', 'IPN subscription cancellation request ignored - new PayPal Profile ID linked to this subscription, for order ' . $order_id );
					}

				} else {

					WC_Subscriptions_Manager::cancel_subscriptions_for_order( $order );

					// Subscription Cancellation Completed
					$order->add_order_note( __( 'IPN subscription cancelled for order.', 'woocommerce-subscriptions' ) );

					if ( self::$debug ) {
						self::$log->add( 'paypal', 'IPN subscription cancelled for order ' . $order_id );
					}

				}

				break;

			case 'subscr_eot': // Subscription ended, either due to failed payments or expiration

				if ( self::$debug ) {
					self::$log->add( 'paypal', 'IPN EOT request ignored for order ' . $order_id );
				}
				break;

			case 'subscr_failed': // Subscription sign up failed

				if ( self::$debug ) {
					self::$log->add( 'paypal', 'IPN subscription payment failure for order ' . $order_id );
				}

				// Subscription Payment completed
				$order->add_order_note( __( 'IPN subscription payment failure.', 'woocommerce-subscriptions' ) );

				// First payment on order, don't generate a renewal order
				if ( $is_first_payment ) {
					remove_action( 'processed_subscription_payment_failure', 'WC_Subscriptions_Renewal_Order::generate_failed_payment_renewal_order', 10, 2 );
				}

				WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order );

				break;
		}

		// Store the transaction IDs to avoid handling requests duplicated by PayPal
		if ( isset( $transaction_details['ipn_track_id'] ) ) {
			$handled_ipn_requests[] = $ipn_id;
			update_post_meta( $order_id, '_paypal_ipn_tracking_ids', $handled_ipn_requests );
		}

		if ( isset( $transaction_details['txn_id'] ) ) {
			$handled_transactions[] = $transaction_id;
			update_post_meta( $order_id, '_paypal_transaction_ids', $handled_transactions );
		}

		// Prevent default IPN handling for subscription txn_types
		exit;
	}

	/**
	 * Override the default PayPal standard args in WooCommerce for subscription purchases when
	 * automatic payments are enabled and when the recurring order totals is over $0.00 (because
	 * PayPal doesn't support subscriptions with a $0 recurring total, we need to circumvent it and
	 * manage it entirely ourselves.)
	 *
	 * Based on the HTML Variables documented here: https://developer.paypal.com/webapps/developer/docs/classic/paypal-payments-standard/integration-guide/Appx_websitestandard_htmlvariables/#id08A6HI00JQU
	 *
	 * @since 1.0
	 */
	public static function paypal_standard_subscription_args( $paypal_args ) {

		extract( self::get_order_id_and_key( $paypal_args ) );

		$order = new WC_Order( $order_id );

		if ( WC_Subscriptions_Order::order_contains_subscription( $order ) && WC_Subscriptions_Order::get_recurring_total( $order ) > 0 && 'yes' !== get_option( WC_Subscriptions_Admin::$option_prefix . '_turn_off_automatic_payments', 'no' ) ) {

			$order_items = $order->get_items();

			// Only one subscription allowed in the cart when PayPal Standard is active
			$product = $order->get_product_from_item( array_pop( $order_items ) );

			// It's a subscription
			$paypal_args['cmd'] = '_xclick-subscriptions';

			if ( count( $order->get_items() ) > 1 ) {

				foreach ( $order->get_items() as $item ) {
					if ( $item['qty'] > 1 ) {
						$item_names[] = $item['qty'] . ' x ' . $item['name'];
					} elseif ( $item['qty'] > 0 ) {
						$item_names[] = $item['name'];
					}
				}

				$paypal_args['item_name'] = sprintf( __( 'Order %s', 'woocommerce-subscriptions' ), $order->get_order_number() );

			} else {

				$paypal_args['item_name'] = $product->get_title();

			}

			$unconverted_periods = array(
				'billing_period' => WC_Subscriptions_Order::get_subscription_period( $order ),
				'trial_period'   => WC_Subscriptions_Order::get_subscription_trial_period( $order )
			);

			$converted_periods = array();

			// Convert period strings into PayPay's format
			foreach ( $unconverted_periods as $key => $period ) {
				switch( strtolower( $period ) ) {
					case 'day':
						$converted_periods[ $key ] = 'D';
						break;
					case 'week':
						$converted_periods[ $key ] = 'W';
						break;
					case 'year':
						$converted_periods[ $key ] = 'Y';
						break;
					case 'month':
					default:
						$converted_periods[ $key ] = 'M';
						break;
				}
			}

			$price_per_period      = WC_Subscriptions_Order::get_recurring_total( $order );
			$subscription_interval = WC_Subscriptions_Order::get_subscription_interval( $order );
			$subscription_length   = WC_Subscriptions_Order::get_subscription_length( $order );

			$subscription_installments = $subscription_length / $subscription_interval;

			$is_payment_change = WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment;
			$is_switch_order   = WC_Subscriptions_Switcher::order_contains_subscription_switch( $order->id );

			$sign_up_fee     = ( $is_payment_change ) ? 0 : WC_Subscriptions_Order::get_sign_up_fee( $order );
			$initial_payment = ( $is_payment_change ) ? 0 : WC_Subscriptions_Order::get_total_initial_payment( $order );

			if ( $is_payment_change) {

				// Add a nonce to the order ID to avoid "This invoice has already been paid" error when changing payment method to PayPal when it was previously PayPal
				$paypal_args['invoice'] = $paypal_args['invoice'] . '-wcscpm-' . wp_create_nonce();

			}

			// If we're changing the payment date or switching subs, we need to set the trial period to the next payment date & installments to be the number of installments left
			if ( $is_payment_change || $is_switch_order ) {

				$subscription_key = WC_Subscriptions_Manager::get_subscription_key( $order_id, $product->id );

				// Give a free trial until the next payment date
				if ( $is_switch_order ) {
					$next_payment_timestamp = WC_Subscriptions_Manager::get_next_payment_date( $subscription_key, $order->user_id, 'timestamp' );
				} else {
					$next_payment_timestamp = WC_Subscriptions_Manager::get_next_payment_date( $subscription_key, $order->user_id, 'timestamp' );
				}

				// When the subscription is on hold
				if ( $next_payment_timestamp != false && ! empty( $next_payment_timestamp ) ) {

					$trial_until = self::calculate_trial_periods_until( $next_payment_timestamp );

					$subscription_trial_length = $trial_until['first_trial_length'];
					$converted_periods['trial_period'] = $trial_until['first_trial_period'];

					$second_trial_length = $trial_until['second_trial_length'];
					$second_trial_period = $trial_until['second_trial_period'];

				} else {

					$subscription_trial_length = 0;

				}

				// If is a payment change, we need to account for completed payments on the number of installments owing
				if ( $is_payment_change && $subscription_length > 0 ) {
					$subscription_installments -= WC_Subscriptions_Manager::get_subscriptions_completed_payment_count( $subscription_key );
				}

			} else {

				$subscription_trial_length = WC_Subscriptions_Order::get_subscription_trial_length( $order );

			}

			if ( $subscription_trial_length > 0 ) { // Specify a free trial period

				if ( $is_switch_order )
					$paypal_args['a1'] = ( $initial_payment > 0 ) ? $initial_payment : 0; // Include any gap payments
				else
					$paypal_args['a1'] = ( $sign_up_fee > 0 ) ? $sign_up_fee : 0; // Maybe add the sign up fee to the free trial period

				// Trial period length
				$paypal_args['p1'] = $subscription_trial_length;

				// Trial period
				$paypal_args['t1'] = $converted_periods['trial_period'];

				// We need to use a second trial period before we have more than 90 days until the next payment
				if ( WC_Subscriptions_Change_Payment_Gateway::$is_request_to_change_payment && $second_trial_length > 0 ) {
					$paypal_args['a2'] = 0;
					$paypal_args['p2'] = $second_trial_length;
					$paypal_args['t2'] = $second_trial_period;
				}

			} elseif ( $sign_up_fee > 0 || $initial_payment !== $price_per_period ) { // No trial period, so charge sign up fee and per period price for the first period

				if ( $subscription_installments == 1 )
					$param_number = 3;
				else
					$param_number = 1;

				$paypal_args['a'.$param_number] = $initial_payment;

				// Sign Up interval
				$paypal_args['p'.$param_number] = $subscription_interval;

				// Sign Up unit of duration
				$paypal_args['t'.$param_number] = $converted_periods['billing_period'];

			}

			// We have a recurring payment
			if ( ! isset( $param_number ) || $param_number == 1 ) {

				// Subscription price
				$paypal_args['a3'] = $price_per_period;

				// Subscription duration
				$paypal_args['p3'] = $subscription_interval;

				// Subscription period
				$paypal_args['t3'] = $converted_periods['billing_period'];

			}

			// Recurring payments
			if ( $subscription_installments == 1 || ( $sign_up_fee > 0 && $subscription_trial_length == 0 && $subscription_installments == 2 ) ) {

				// Non-recurring payments
				$paypal_args['src'] = 0;

			} else {

				$paypal_args['src'] = 1;

				if ( $subscription_installments > 0 ) {
					if ( $sign_up_fee > 0 && $subscription_trial_length == 0 ) // An initial period is being used to charge a sign-up fee
						$subscription_installments--;

					$paypal_args['srt'] = $subscription_installments;

				}
			}

			// Don't reattempt failed payments, instead let Subscriptions handle the failed payment
			$paypal_args['sra'] = 0;

			// Force return URL so that order description & instructions display
			$paypal_args['rm'] = 2;

		}

		return $paypal_args;
	}

	/**
	 * Adds extra PayPal credential fields required to manage subscriptions.
	 *
	 * @since 1.0
	 */
	public static function add_subscription_form_fields(){
		global $woocommerce;

		foreach ( $woocommerce->payment_gateways->payment_gateways as $key => $gateway ) {

			if ( $woocommerce->payment_gateways->payment_gateways[ $key ]->id !== 'paypal' ) 
				continue;

			// Warn store managers not to change their PayPal Email address as it can break existing Subscriptions in WC2.0+
			$woocommerce->payment_gateways->payment_gateways[ $key ]->form_fields['receiver_email']['desc_tip'] = false;
			$woocommerce->payment_gateways->payment_gateways[ $key ]->form_fields['receiver_email']['description'] .= ' </p><p class="description">' . __( 'It is <strong>strongly recommended you do not change the Receiver Email address</strong> if you have active subscriptions with PayPal. Doing so can break existing subscriptions.', 'woocommerce-subscriptions' );

			$woocommerce->payment_gateways->payment_gateways[ $key ]->form_fields += array(

				'api_credentials' => array(
					'title'       => __( 'API Credentials', 'woocommerce-subscriptions' ), 
					'type'        => 'title', 
					'description' => sprintf( __( 'Enter your PayPal API credentials to unlock subscription suspension and cancellation features. %sLearn More &raquo;%s', 'woocommerce-subscriptions' ), '<a href="http://docs.woothemes.com/document/subscriptions/store-manager-guide/#section-4" target="_blank" tabindex="-1">', '</a>' ),
				),

				'api_username' => array(
					'title'       => __( 'API Username', 'woocommerce-subscriptions' ), 
					'type'        => 'text', 
					'description' => '',
					'default'     => ''
				),

				'api_password' => array(
					'title'       => __( 'API Password', 'woocommerce-subscriptions' ), 
					'type'        => 'text', 
					'description' => '',
					'default'     => ''
				),

				'api_signature' => array(
					'title'       => __( 'API Signature', 'woocommerce-subscriptions' ), 
					'type'        => 'text', 
					'description' => '',
					'default'     => ''
				)
			);
		}

	}

	/**
	 * In WC 2.0, settings are saved on a new instance of the PayPalpayment gateway, not
	 * the global instance, so our admin fields are not set (nor saved). As a result, we
	 * need to run the save routine @see WC_Settings_API::process_admin_options() again
	 * to save our fields.
	 *
	 * @since 1.2.5
	 */
	public static function save_subscription_form_fields() {

		$paypal_gateway = WC_Subscriptions_Payment_Gateways::get_payment_gateway( 'paypal' );

		$paypal_gateway->process_admin_options();
	}

	/**
	 * When a store manager or user cancels a subscription in the store, also cancel the subscription with PayPal. 
	 *
	 * @since 1.1
	 */
	public static function cancel_subscription_with_paypal( $order, $product_id = '', $profile_id = '' ) {

		if ( empty( $profile_id ) ) {
			$profile_id = self::get_subscriptions_paypal_id( $order, $product_id );
		}

		// Make sure a subscriptions status is active with PayPal
		$response = self::change_subscription_status( $profile_id, 'Cancel' );

		$item = WC_Subscriptions_Order::get_item_by_product_id( $order, $product_id );

		if ( isset( $response['ACK'] ) && $response['ACK'] == 'Success' )
			$order->add_order_note( sprintf( __( 'Subscription "%s" cancelled with PayPal', 'woocommerce-subscriptions' ), $item['name'] ) );
	}

	/**
	 * When a store manager or user suspends a subscription in the store, also suspend the subscription with PayPal. 
	 *
	 * @since 1.1
	 */
	public static function suspend_subscription_with_paypal( $order, $product_id ) {

		$profile_id = self::get_subscriptions_paypal_id( $order, $product_id );

		// Make sure a subscriptions status is active with PayPal
		$response = self::change_subscription_status( $profile_id, 'Suspend' );

		if ( isset( $response['ACK'] ) && $response['ACK'] == 'Success' ) {

			$item = WC_Subscriptions_Order::get_item_by_product_id( $order, $product_id );

			$order->add_order_note( sprintf( __( 'Subscription "%s" suspended with PayPal', 'woocommerce-subscriptions' ), $item['name'] ) );
		}
	}

	/**
	 * When a store manager or user reactivates a subscription in the store, also reactivate the subscription with PayPal. 
	 *
	 * How PayPal Handles suspension is discussed here: https://www.x.com/developers/paypal/forums/nvp/reactivate-recurring-profile
	 *
	 * @since 1.1
	 */
	public static function reactivate_subscription_with_paypal( $order, $product_id ) {

		$profile_id = self::get_subscriptions_paypal_id( $order, $product_id );

		// Make sure a subscriptions status is active with PayPal
		$response = self::change_subscription_status( $profile_id, 'Reactivate' );

		$item = WC_Subscriptions_Order::get_item_by_product_id( $order, $product_id );

		if ( isset( $response['ACK'] ) && $response['ACK'] == 'Success' )
			$order->add_order_note( sprintf( __( 'Subscription "%s" reactivated with PayPal', 'woocommerce-subscriptions' ), $item['name'] ) );
	}

	/**
	 * Returns a PayPal Subscription ID/Recurring Payment Profile ID based on a user ID and subscription key
	 *
	 * @since 1.1
	 */
	public static function get_subscriptions_paypal_id( $order, $product_id = '' ) {

		$profile_id = get_post_meta( $order->id, 'PayPal Subscriber ID', true );

		return $profile_id;
	}

	/**
	 * Performs an Express Checkout NVP API operation as passed in $api_method.
	 *
	 * Although the PayPal Standard API provides no facility for cancelling a subscription, the PayPal
	 * Express Checkout  NVP API can be used.
	 *
	 * @since 1.1
	 */
	public static function change_subscription_status( $profile_id, $new_status ) {

		switch( $new_status ) {
			case 'Cancel' :
				$new_status_string = __( 'cancelled', 'woocommerce-subscriptions' );
				break;
			case 'Suspend' :
				$new_status_string = __( 'suspended', 'woocommerce-subscriptions' );
				break;
			case 'Reactivate' :
				$new_status_string = __( 'reactivated', 'woocommerce-subscriptions' );
				break;
		}

		$api_request = 'USER=' . urlencode( self::$api_username )
					.  '&PWD=' . urlencode( self::$api_password )
					.  '&SIGNATURE=' . urlencode( self::$api_signature )
					.  '&VERSION=76.0'
					.  '&METHOD=ManageRecurringPaymentsProfileStatus'
					.  '&PROFILEID=' . urlencode( $profile_id )
					.  '&ACTION=' . urlencode( $new_status )
					.  '&NOTE=' . urlencode( sprintf( __( 'Subscription %s at %s', 'woocommerce-subscriptions' ), $new_status_string, get_bloginfo( 'name' ) ) );

		$ch = curl_init();
		curl_setopt( $ch, CURLOPT_URL, self::$api_endpoint );
		curl_setopt( $ch, CURLOPT_VERBOSE, 1 );

		// Turn off server and peer verification
		curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, FALSE );
		curl_setopt( $ch, CURLOPT_SSL_VERIFYHOST, FALSE );
		curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt( $ch, CURLOPT_POST, 1 );

		// Set the API parameters for this transaction
		curl_setopt( $ch, CURLOPT_POSTFIELDS, $api_request );

		// Request response from PayPal
		$response = curl_exec( $ch );

		// If no response was received from PayPal there is no point parsing the response
		if( ! $response && self::$debug )
			self::$log->add( 'paypal', 'Calling PayPal to change_subscription_status failed: ' . curl_error( $ch ) . '(' . curl_errno( $ch ) . ')' );

		curl_close( $ch );

		// An associative array is more usable than a parameter string
		parse_str( $response, $parsed_response );

		if( ( 0 == sizeof( $parsed_response ) || ! array_key_exists( 'ACK', $parsed_response ) ) && self::$debug )
			self::$log->add( 'paypal', "Invalid HTTP Response for change_subscription_status POST request($api_request) to " . self::$api_endpoint );

		if( $parsed_response['ACK'] == 'Failure' && self::$debug )
			self::$log->add( 'paypal', "Calling PayPal to change_subscription_status has Failed: " . $parsed_response['L_LONGMESSAGE0'] );

		return $parsed_response;
	}

	/**
	 * Checks a set of args and derives an Order ID with backward compatibility for WC < 1.7 where 'custom' was the Order ID.
	 *
	 * @since 1.2
	 */
	private static function get_order_id_and_key( $args ) {

		// First try and get the order ID by the subscr_id
		if ( isset( $args['subscr_id'] ) ){
			$posts = get_posts( array(
				'numberposts'      => 1,
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'meta_key'         => 'PayPal Subscriber ID',
				'meta_value'       => $args['subscr_id'],
				'post_type'        => 'shop_order',
				'post_parent'      => 0,
				'suppress_filters' => true,
			));

			if ( ! empty( $posts ) ) {
				$order_id  = $posts[0]->ID;
				$order_key = get_post_meta( $order_id, '_order_key', true );
			}
		}

		// Couldn't find the order ID by subscr_id, so it's either not set on the order yet or the $args doesn't have a subscr_id, either way, let's get it from the args
		if ( ! isset( $order_id ) ) {
			// WC < 1.6.5
			if ( is_numeric( $args['custom'] ) ) {
				$order_id  = $args['custom'];
				$order_key = $args['invoice'];
			} else {
				$args['custom'] = maybe_unserialize( $args['custom'] );
				if ( is_array( $args['custom'] ) ) { // WC 2.0+
					$order_id  = (int) $args['custom'][0];
					$order_key = $args['custom'][1];
				} else { // WC 1.6.5 = WC 2.0
					$order_id  = (int) str_replace( self::$invoice_prefix, '', $args['invoice'] );
					$order_key = $args['custom'];
				}
			}
		}

		return array( 'order_id' => $order_id, 'order_key' => $order_key );
	}

	/**
	 * Return the default WC PayPal gateway's settings.
	 *
	 * @since 1.2
	 */
	private static function get_wc_paypal_settings() {

		if ( ! isset( self::$paypal_settings ) )
			self::$paypal_settings = get_option( 'woocommerce_paypal_settings' );

		return self::$paypal_settings;
	}

	/**
	 * Don't transfer PayPal customer/token meta when creating a parent renewal order.
	 *
	 * @access public
	 * @param array $order_meta_query MySQL query for pulling the metadata
	 * @param int $original_order_id Post ID of the order being used to purchased the subscription being renewed
	 * @param int $renewal_order_id Post ID of the order created for renewing the subscription
	 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'
	 * @return void
	 */
	public static function remove_renewal_order_meta( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {

		if ( 'parent' == $new_order_role )
			$order_meta_query .= " AND `meta_key` NOT IN ("
							  .		"'Transaction ID', "
							  .		"'Payer first name', "
							  .		"'Payer last name', "
							  .		"'Payment type', "
							  .		"'Payer PayPal address', "
							  .		"'Payer PayPal first name', "
							  .		"'Payer PayPal last name', " 
							  .		"'PayPal Subscriber ID' )";

		return $order_meta_query;
	}

	/**
	 * Prompt the store manager to enter their PayPal API credentials if they are using 
	 * PayPal and have yet not entered their API credentials.
	 *
	 * @return void
	 */
	public static function maybe_show_admin_notice() {

		self::get_wc_paypal_settings();

		if ( ! in_array( get_woocommerce_currency(), apply_filters( 'woocommerce_paypal_supported_currencies', array( 'AUD', 'BRL', 'CAD', 'MXN', 'NZD', 'HKD', 'SGD', 'USD', 'EUR', 'JPY', 'TRY', 'NOK', 'CZK', 'DKK', 'HUF', 'ILS', 'MYR', 'PHP', 'PLN', 'SEK', 'CHF', 'TWD', 'THB', 'GBP', 'RMB' ) ) ) )
			$valid_for_use = false;
		else
			$valid_for_use = true;

		if ( WC_Subscriptions::is_woocommerce_pre_2_1() ) {
			$payment_gateway_tab_url = admin_url( 'admin.php?page=woocommerce_settings&tab=payment_gateways&section=WC_Gateway_Paypal' );
		} else {
			$payment_gateway_tab_url = admin_url( 'admin.php?page=wc-settings&tab=checkout&section=wc_gateway_paypal' );
		}

		if ( ! self::are_credentials_set() && $valid_for_use && 'yes' == self::$paypal_settings['enabled'] && ! has_action( 'admin_notices', 'WC_Subscriptions_Admin::admin_installed_notice' ) && current_user_can( 'manage_options' ) ) { ?>
<div id="message" class="updated warning">
	<p>
		<?php 
		printf( __( 'Just a few more steps to configure PayPal to sell subscriptions. Please %sset up the PayPal IPN%s and %senter your API credentials%s.', 'woocommerce-subscriptions' ),
				'<a href="http://docs.woothemes.com/document/subscriptions/store-manager-guide/#section-4" target="_blank">',
				'</a>',
				'<a href="' . $payment_gateway_tab_url . '">',
				'</a>'
		); 
		?>
	</p>
</div>
<?php
		}
	}

	/**
	 * Takes a timestamp for a date in the future and calculates the number of days between now and then
	 *
	 * @since 1.4
	 */
	public static function calculate_trial_periods_until( $future_timestamp ) {

		$seconds_until_next_payment = $future_timestamp - gmdate( 'U' );
		$days_until_next_payment    = ceil( $seconds_until_next_payment / ( 60 * 60 * 24 ) );

		if ( $days_until_next_payment <= 90 ) { // Can't be more than 90 days free trial

			$first_trial_length = $days_until_next_payment;
			$first_trial_period = 'D';

			$second_trial_length = 0;
			$second_trial_period = 'D';

		} else { // We need to use a second trial period

			if ( $days_until_next_payment > 365 * 2 ) { // We need to use years because PayPal has a maximum of 24 months

				$first_trial_length = floor( $days_until_next_payment / 365 );
				$first_trial_period = 'Y';

				$days_remaining = $days_until_next_payment % 365;

				if ( $days_remaining <= 90 ) { // We can use days
					$second_trial_length = $days_remaining;
					$second_trial_period = 'D';
				} else { // We need to use weeks
					$second_trial_length = floor( $days_remaining / 7 );
					$second_trial_period = 'W';
				}

			} elseif ( $days_until_next_payment > 365 ) { // Less than two years but more than one, use months

				$first_trial_length = floor( $days_until_next_payment / 30 );
				$first_trial_period = 'M';

				$days_remaining = $days_until_next_payment % 30;

				if ( $days_remaining <= 90 ) { // We can use days
					$second_trial_length = $days_remaining;
					$second_trial_period = 'D';
				} else { // We need to use weeks
					$second_trial_length = floor( $days_remaining / 7 );
					$second_trial_period = 'W';
				}

			} else {  // We need to use weeks

				$first_trial_length = floor( $days_until_next_payment / 7 );
				$first_trial_period = 'W';

				$second_trial_length = $days_until_next_payment % 30;
				$second_trial_period = 'D';

			}

		}

		return array(
			'first_trial_length'  => $first_trial_length,
			'first_trial_period'  => $first_trial_period,
			'second_trial_length' => $second_trial_length,
			'second_trial_period' => $second_trial_period,
		);
	}

	/**
	 * In typical PayPal style, there are a couple of important limitations we need to work around:
	 *
	 * 1. PayPal does not support subscriptions with a $0 recurring total. As a result, we treat it
	 * as a normal purchase and then handle the subscription renewals here.
	 *
	 * 2. PayPal make no guarantee about when a recurring payment will be charged. This creates issues for
	 * suspending a subscription until the payment is processed. Specifically, if PayPal processed a payment
	 * *before* it was due, we can't suspend the subscription when it is due because it will remain suspended
	 * until the next payment. As a result, subscriptions for PayPal are not suspended. However, if there was
	 * an issue with the subscription sign-up or payment that was not correctly reported to the store, then the
	 * subscription would remain active. No renewal order would be generated, because no payments are completed,
	 * so physical subscriptions would not be affected, however, subscriptions to digital goods would be affected.
	 *
	 * @since 1.4.3
	 */
	public static function scheduled_subscription_payment( $amount_to_charge, $order, $product_id ) {

		if ( 0 == $amount_to_charge ) {

			WC_Subscriptions_Manager::process_subscription_payments_on_order( $order );

		} else {

			$hook_args = array(
				'subscription_key' => WC_Subscriptions_Manager::get_subscription_key( $order->id, $product_id )
			);

			$one_day_from_now = gmdate( 'U' ) + ( 60 * 60 * 24 );

			wp_schedule_single_event( $one_day_from_now, 'paypal_check_subscription_payment', $hook_args );
		}
	}

	/**
	 * If changing a subscriptions payment method from and to PayPal, wait until an appropriate IPN message
	 * has come in before deciding to cancel the old subscription.
	 *
	 * @since 1.4.6
	 */
	public static function maybe_remove_cancelled_subscription_hook( $order, $subscription_key, $new_payment_method, $old_payment_method ) {
		if ( 'paypal' == $new_payment_method && 'paypal' == $old_payment_method ) {
			remove_action( 'cancelled_subscription_paypal', __CLASS__ . '::cancel_subscription_with_paypal', 10, 2 );

			// Keep a record of current profile ID
			update_post_meta( $order->id, '_old_paypal_subscriber_id', self::get_subscriptions_paypal_id( $order ) );
		}
	}

	/**
	 * If changing a subscriptions payment method from and to PayPal, the cancelled subscription hook was removed in
	 * @see self::maybe_remove_cancelled_subscription_hook() so we want to add it again for other subscriptions.
	 *
	 * @since 1.4.6
	 */
	public static function maybe_readd_cancelled_subscription_hook( $order, $subscription_key, $new_payment_method, $old_payment_method ) {
		if ( 'paypal' == $new_payment_method && 'paypal' == $old_payment_method ) {
			add_action( 'cancelled_subscription_paypal', __CLASS__ . '::cancel_subscription_with_paypal', 10, 2 );
		}
	}

	/**
	 * If a subscription has the status of active, this function checks to make sure that a payment was completed
	 * within the last couple of days. It is hooked to the 'paypal_check_subscription_payment' action triggered by
	 * WP-Cron and scheduled with @see self::scheduled_subscription_payment() to run 24 hours after the payment was due.
	 *
	 * @since 1.4.3
	 */
	public static function check_subscription_payment( $subscription_key ) {

		$subscription = WC_Subscriptions_Manager::get_subscription( $subscription_key );
		$order        = new WC_Order( $subscription['order_id'] );

		if ( 'active' === $subscription['status'] ) {

			$last_payment_plus_two_days = ( 60 * 60 * 24 * 2 ) + WC_Subscriptions_Manager::get_last_payment_date( $subscription_key, $order->user_id, 'timestamp' );

			// If the last payment was more than 2 days ago, then we're missing a payment because this function is called 24 hours after payment was due
			if ( $last_payment_plus_two_days < gmdate( 'U' ) ) {

				// @TODO process a subscription payment failure when this has been reliably tested

				//remove_action( 'subscription_put_on-hold_paypal', __CLASS__ . '::suspend_subscription_with_paypal', 10, 2 );

				//WC_Subscriptions_Manager::process_subscription_payment_failure( $order->user_id, $subscription_key );

				//$order->add_order_note( __( 'Subscription payment failure triggered as no recurring payment has been processed by PayPal.', 'woocommerce-subscriptions' ) );

				//add_action( 'subscription_put_on-hold_paypal', __CLASS__ . '::suspend_subscription_with_paypal', 10, 2 );
			}

		}

	}
}

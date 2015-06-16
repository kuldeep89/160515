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
 * @package     WC-Authorize-Net-CIM/Gateway-Addons
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Authorize.net CIM Addons Class
 *
 * Extends the base CIM gateway to provide support for WC Add-ons -- Subscriptions and Pre-Orders
 *
 * @since 1.0
 * @extends \WC_Gateway_Authorize_Net_CIM
 */
class WC_Gateway_Authorize_Net_CIM_Addons extends WC_Gateway_Authorize_Net_CIM {


	/**
	 * Load parent gateway and add-on specific hooks
	 *
	 * @since  1.0
	 * @return \WC_Gateway_Authorize_Net_CIM_Addons
	 */
	public function __construct() {
		global $wc_authorize_net_cim;

		// load parent gateway
		parent::__construct();

		// add subscription support if active
		if ( $wc_authorize_net_cim->is_subscriptions_active() ) {

			$this->supports = array_merge( $this->supports,
				array(
					'subscriptions',
					'subscription_suspension',
					'subscription_cancellation',
					'subscription_reactivation',
					'subscription_amount_changes',
					'subscription_date_changes',
					'subscription_payment_method_change',
				)
			);

			// process scheduled subscription payments
			add_action( 'scheduled_subscription_payment_' . $this->id, array( $this, 'process_scheduled_subscription_payment' ), 10, 3 );

			// prevent unnecessary order meta from polluting parent renewal orders
			add_filter( 'woocommerce_subscriptions_renewal_order_meta_query', array( $this, 'remove_subscriptions_renewal_order_meta' ), 10, 4 );

			// update the customer payment profile ID on the original order when making payment for a failed automatic renewal order
			add_action( 'woocommerce_subscriptions_changed_failing_payment_method_' . $this->id, array( $this, 'update_failing_payment_method' ), 10, 2 );

			// display the current payment method used for a subscription in the "My Subscriptions" table
			add_filter( 'woocommerce_my_subscriptions_recurring_payment_method', array( $this, 'maybe_render_subscription_payment_method' ), 10, 3 );
		}

		// add pre-orders support if active
		if ( $wc_authorize_net_cim->is_pre_orders_active() ) {

			$this->supports = array_merge( $this->supports,
				array(
					'pre-orders',
				)
			);

			// process batch pre-order payments
			add_action( 'wc_pre_orders_process_pre_order_completion_payment_' . $this->id, array( $this, 'process_pre_order_payment' ) );
		}
	}


	/**
	 * Process payment for an order:
	 * 1) If the order contains a subscription, process the initial subscription payment (could be $0 if a free trial exists)
	 * 2) If the order contains a pre-order, process the pre-order total (could be $0 if the pre-order is charged upon release)
	 * 3) Otherwise use the parent::process_payment() method for regular product purchases
	 *
	 * @since 1.0
	 * @param int $order_id
	 * @return array
	 */
	public function process_payment( $order_id ) {
		global $wc_authorize_net_cim;

		/* processing subscription */
		if ( $wc_authorize_net_cim->is_subscriptions_active() && WC_Subscriptions_Order::order_contains_subscription( $order_id ) ) {

			$order = $this->get_order( $order_id );

			// set subscription-specific order description
			$order->description = apply_filters( 'wc_authorize_net_cim_transaction_description', sprintf( __( '%s - Subscription Order %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), $order_id, $this );

			// get subscription amount
			$order->payment_total = number_format( WC_Subscriptions_Order::get_total_initial_payment( $order ), 2, '.', '' );

			try {

				// create new authorize.net customer profile if needed
				if ( ! $order->customer_profile_id ) {
					$order = $this->create_customer( $order );
				}

				// create new payment profile if customer is using new card
				if ( ! $order->customer_payment_profile_id ) {
					$order = $this->create_payment_profile( $order );
				}

				// process transaction
				if ( 0 == $order->payment_total || $this->do_transaction( $order ) ) {

					// manually add customer profile ID & payment profile ID to the order because it wasn't done in the do_transaction() call
					if ( 0 == $order->payment_total ) {

						update_post_meta( $order->id, '_wc_authorize_net_cim_customer_profile_id', $order->customer_profile_id );
						update_post_meta( $order->id, '_wc_authorize_net_cim_payment_profile_id', $order->customer_payment_profile_id );

						if ( $order->customer_shipping_profile_id ) {
							update_post_meta( $order->id, '_wc_authorize_net_cim_shipping_profile_id', $order->customer_shipping_profile_id );
						}
					}

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

			} catch ( Exception $e ) {

				// log API requests/responses here too, as exceptions could be thrown before $response object is returned
				$this->log_api();

				$this->mark_order_as_failed( $order, $e->getMessage() );
			}

		/* processing pre-order */
		} elseif ( $wc_authorize_net_cim->is_pre_orders_active() && WC_Pre_Orders_Order::order_contains_pre_order( $order_id ) ) {

			// do pre-authorization
			if ( WC_Pre_Orders_Order::order_requires_payment_tokenization( $order_id ) ) {

				$order = $this->get_order( $order_id );

				// set pre-order-specific order description
				$order->description = apply_filters( 'wc_authorize_net_cim_transaction_description', sprintf( __( '%s - Pre-Order Authorization %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), $order_id, $this );

				try {

					// create new authorize.net customer profile if needed
					if ( ! $order->customer_profile_id ) {
						$order = $this->create_customer( $order );
					}

					// create new payment profile if customer is using new card
					if ( ! $order->customer_payment_profile_id ) {
						$order = $this->create_payment_profile( $order );
					}

					// validate the payment method by running a $1 auth/void
					if ( $this->do_transaction( $order, true ) ) {

						// set a flag to remark the order as on-hold after the pre-order is processed if the order was placed on-hold due
						// to the transaction returning as "pending review" from auth.net
						if ( 'on-hold' == $order->status ) {
							$remark_as_on_hold = true;
						} else {
							$remark_as_on_hold = false;
						}

						// mark order as pre-ordered / reduce order stock
						WC_Pre_Orders_Order::mark_order_as_pre_ordered( $order );

						if ( $remark_as_on_hold ) {
							$order->update_status( 'on-hold' );
						}

						// empty cart
						SV_WC_Plugin_Compatibility::WC()->cart->empty_cart();

						// redirect to thank you page
						return array(
							'result'   => 'success',
							'redirect' => $this->get_return_url( $order ),
						);
					}

				} catch ( Exception $e ) {

					// log API requests/responses here too, as exceptions could be thrown before $response object is returned
					$this->log_api();

					$this->mark_order_as_failed( $order, $e->getMessage() );
				}

			} else {

				// charged upfront (or paying for a newly-released pre-order with the gateway), process just like regular product
				return parent::process_payment( $order_id );
			}

		// processing regular product
		} else {

			return parent::process_payment( $order_id );
		}
	}


	/**
	 * Process a pre-order payment when the pre-order is released
	 *
	 * @since 1.0
	 * @param \WC_Order $order original order containing the pre-order
	 * @throws Exception
	 */
	public function process_pre_order_payment( $order ) {

		// set order defaults
		$order = $this->get_order( $order->id );

		// get order total
		$order->payment_total = number_format( $order->get_total(), 2, '.', '' );

		// order description
		$order->description = apply_filters( 'wc_authorize_net_cim_transaction_description', sprintf( __( '%s - Pre-Order Release Payment for Order %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), $order->id, $this );

		// get customer profile ID
		$order->customer_profile_id = get_post_meta( $order->id, '_wc_authorize_net_cim_customer_profile_id', true );

		// get customer payment profile ID that was used to auth the pre-order
		$order->customer_payment_profile_id = get_post_meta( $order->id, '_wc_authorize_net_cim_payment_profile_id', true );

		try {

			// customer profile ID and custom payment profile ID are required
			if ( ! $order->customer_profile_id || ! $order->customer_payment_profile_id ) {
				throw new Exception( __( 'Pre-Order Release: Customer or Payment Profile is missing.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
			}

			$response = $this->get_api()->create_new_transaction( $order );

			$this->log_api();

			// success! update order record
			if ( $response->transaction_was_successful() ) {

				// add order note
				$order->add_order_note( sprintf( __( 'Authorize.net Pre-Order Release Payment Approved (Transaction ID: %s) ', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $response->get_transaction_id() ) );

				// complete the order
				$order->payment_complete();

			} else {

				// failure
				throw new Exception( $response->get_transaction_failure_message() );
			}

		} catch ( Exception $e ) {

			// Mark order as failed
			$message = sprintf( __( 'Authorize.net Pre-Order Release Payment Failed (Result: %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $e->getMessage() );

			if ( $order->status != 'failed' ) {
				$order->update_status( 'failed', $message );
			} else {
				$order->add_order_note( $message );
			}

			$this->add_debug_message( $e->getMessage(), 'error' );
		}
	}


	/**
	 * Process subscription renewal
	 *
	 * @since 1.0
	 * @param float $amount_to_charge subscription amount to charge, could include multiple renewals if they've previously failed and the admin has enabled it
	 * @param \WC_Order $order original order containing the subscription
	 * @param int $product_id the ID of the subscription product
	 * @throws Exception
	 */
	public function process_scheduled_subscription_payment( $amount_to_charge, $order, $product_id ) {

		try {

			// set order defaults
			$order = $this->get_order( $order->id );

			// set custom class members used by API ( @see WC_Gateway_Authorize_Net_CIM::get_order() )
			$order->payment_total       = $amount_to_charge;
			$order->description         = apply_filters( 'wc_authorize_net_cim_transaction_description', sprintf( __( '%s - Renewal for Subscription Order %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), esc_html( get_bloginfo( 'name' ) ), $order->get_order_number() ), $order->id, $this );
			$order->customer_profile_id = get_user_meta( $order->user_id, '_wc_authorize_net_cim_profile_id', true );

			/* Could use get_active_payment_profile_id() to charge the active card on file for renewals, but this is confusing for customers on sites that sell both subscriptions and products
			 * instead use the same payment profile that was used to purchase the subscription
			 */
			$order->customer_payment_profile_id = get_post_meta( $order->id, '_wc_authorize_net_cim_payment_profile_id', true );

			// required for profile transactions
			if ( ! $order->customer_profile_id || ! $order->customer_payment_profile_id ) {
				throw new Exception( __( 'Subscription Renewal: Customer or Payment Profile is missing.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
			}

			$response = $this->get_api()->create_new_transaction( $order );

			$this->log_api();

			// success! update order record
			if ( $response->transaction_was_successful() ) {

				// add order note
				$order->add_order_note( sprintf( __( 'Authorize.net Subscription Renewal Payment Approved (Transaction ID: %s) ', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $response->get_transaction_id() ) );

				// update subscription
				WC_Subscriptions_Manager::process_subscription_payments_on_order( $order, $product_id );

			} else {
				// failure
				throw new Exception( $response->get_transaction_failure_message() );
			}

		} catch ( Exception $e ) {

			$order->add_order_note( sprintf( __( 'Authorize.net Subscription Renewal Payment Failed (Result: %s)', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $e->getMessage() ) );

			$this->add_debug_message( $e->getMessage(), 'error' );

			// update subscription
			WC_Subscriptions_Manager::process_subscription_payment_failure_on_order( $order, $product_id );
		}
	}


	/**
	 * Don't copy over profile/payment meta when creating a parent renewal order
	 *
	 * @since 1.0
	 * @param array $order_meta_query MySQL query for pulling the metadata
	 * @param int $original_order_id Post ID of the order being used to purchased the subscription being renewed
	 * @param int $renewal_order_id Post ID of the order created for renewing the subscription
	 * @param string $new_order_role The role the renewal order is taking, one of 'parent' or 'child'
	 * @return string
	 */
	public function remove_subscriptions_renewal_order_meta( $order_meta_query, $original_order_id, $renewal_order_id, $new_order_role ) {

		if ( 'parent' == $new_order_role ) {
			$order_meta_query .= " AND `meta_key` NOT IN ("
							  . "'_wc_authorize_net_cim_trans_id', "
							  . "'_wc_authorize_net_cim_payment_profile_id', "
							  . "'_wc_authorize_net_cim_card_type', "
							  . "'_wc_authorize_net_cim_card_last_four', "
							  . "'_wc_authorize_net_cim_card_exp_date', "
							  . "'_wc_authorize_net_cim_trans_mode' )";
		}

		return $order_meta_query;
	}


	/**
	 * Update the profile IDs for a subscription after a customer used Auth.net to successfully complete the payment
	 * for an automatic renewal payment which had previously failed
	 *
	 * @since 1.0.9
	 * @param WC_Order $original_order The original order in which the subscription was purchased.
	 * @param WC_Order $renewal_order The order which recorded the successful payment (to make up for the failed automatic payment).
	 */
	public function update_failing_payment_method( WC_Order $original_order, WC_Order $renewal_order ) {

		update_post_meta( $original_order->id, '_wc_authorize_net_cim_customer_profile_id', get_post_meta( $renewal_order->id, '_wc_authorize_net_cim_customer_profile_id', true ) );
		update_post_meta( $original_order->id, '_wc_authorize_net_cim_shipping_profile_id', get_post_meta( $renewal_order->id, '_wc_authorize_net_cim_shipping_profile_id', true ) );
		update_post_meta( $original_order->id, '_wc_authorize_net_cim_payment_profile_id',  get_post_meta( $renewal_order->id, '_wc_authorize_net_cim_payment_profile_id', true ) );
	}


	/**
	 * Render the payment method used for a subscription in the "My Subscriptions" table
	 *
	 * @since 1.0.9
	 * @param string $payment_method_to_display the default payment method text to display
	 * @param array $subscription_details the subscription details
	 * @param WC_Order $order the order containing the subscription
	 * @return string the subscription payment method
	 */
	public function maybe_render_subscription_payment_method( $payment_method_to_display, $subscription_details, WC_Order $order ) {

		// bail for other payment methods
		if ( $this->id !== $order->recurring_payment_method ) {
			return $payment_method_to_display;
		}

		$payment_method = $this->get_payment_profile( $order->user_id, get_post_meta( $order->id, '_wc_authorize_net_cim_payment_profile_id', true ) );

		if ( ! empty( $payment_method ) ) {
			$payment_method_to_display = sprintf( 'Via %s ending in %s', $payment_method['type'], $payment_method['last_four'] );
		}

		return $payment_method_to_display;
	}


} // end \WC_Gateway_Authorize_Net_CIM_Addons class

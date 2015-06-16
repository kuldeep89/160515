<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * New Order Email
 *
 * An email sent to the admin when a new order is received/paid for.
 *
 * @class 		WC_Email_New_Order
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class WCS_Email_New_Renewal_Order extends WC_Email_New_Order {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id             = 'new_renewal_order';
		$this->title          = __( 'New Renewal Order', 'woocommerce-subscriptions' );
		$this->description    = __( 'New renewal order emails are sent when a subscription renewal payment is processed.', 'woocommerce-subscriptions' );

		$this->heading        = __( 'New subscription renewal order', 'woocommerce-subscriptions' );
		$this->subject        = __( '[{blogname}] New subscription renewal order ({order_number}) - {order_date}', 'woocommerce-subscriptions' );

		$this->template_html  = 'emails/admin-new-renewal-order.php';
		$this->template_plain = 'emails/plain/admin-new-renewal-order.php';
		$this->template_base  = plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/';

		// Triggers for this email
		add_action( 'woocommerce_order_status_pending_to_processing_renewal_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_pending_to_completed_renewal_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_pending_to_on-hold_renewal_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_failed_to_processing_renewal_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_failed_to_completed_renewal_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_failed_to_on-hold_renewal_notification', array( $this, 'trigger' ) );

		// We want all the parent's methods, with none of its properties, so call its parent's constructor, rather than my parent constructor
		WC_Email::__construct();

		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		woocommerce_get_template(
			$this->template_html,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading()
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		woocommerce_get_template(
			$this->template_plain,
			array(
				'order'         => $this->object,
				'email_heading' => $this->get_heading()
			),
			'',
			$this->template_base
		);
		return ob_get_clean();
	}
}
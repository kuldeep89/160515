<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Customer Invoice
 *
 * An email sent to the customer via admin.
 *
 * @class 		WC_Email_Customer_Invoice
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class WCS_Email_Customer_Renewal_Invoice extends WC_Email_Customer_Invoice {

	var $find;
	var $replace;

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id             = 'customer_renewal_invoice';
		$this->title          = __( 'Customer Renewal Invoice', 'woocommerce-subscriptions' );
		$this->description    = __( 'Customer renewal invoice emails can be sent to the user containing order info and payment links. If a subscription is using manual renewals, it will be sent to the customer when a renewal payment is due.', 'woocommerce-subscriptions' );

		$this->template_html  = 'emails/customer-renewal-invoice.php';
		$this->template_plain = 'emails/plain/customer-renewal-invoice.php';
		$this->template_base  = plugin_dir_path( WC_Subscriptions::$plugin_file ) . 'templates/';

		$this->subject        = __( 'Invoice for renewal order {order_number} from {order_date}', 'woocommerce-subscriptions');
		$this->heading        = __( 'Invoice for renewal order {order_number}', 'woocommerce-subscriptions');

		$this->subject_paid   = __( 'Your {blogname} renewal order from {order_date}', 'woocommerce-subscriptions');
		$this->heading_paid   = __( 'Renewal order {order_number} details', 'woocommerce-subscriptions');

		// Triggers for this email
		add_action( 'woocommerce_generated_manual_renewal_order_renewal_notification', array( $this, 'trigger' ) );
		add_action( 'woocommerce_order_status_failed_renewal_notification', array( $this, 'trigger' ) );

		// We want all the parent's methods, with none of its properties, so call its parent's constructor, rather than my parent constructor
		WC_Email::__construct();
	}

	/**
	 * get_subject function.
	 *
	 * @access public
	 * @return string
	 */
	function get_subject() {
		return apply_filters( 'woocommerce_subscriptions_email_subject_new_renewal_order', parent::get_subject(), $this->object );
	}

	/**
	 * get_heading function.
	 *
	 * @access public
	 * @return string
	 */
	function get_heading() {
		return apply_filters( 'woocommerce_email_heading_customer_renewal_order', parent::get_heading(), $this->object );
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
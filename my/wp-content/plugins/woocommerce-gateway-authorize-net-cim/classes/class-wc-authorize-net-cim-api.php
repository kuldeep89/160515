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
 * @package     WC-Authorize-Net-CIM/API
 * @author      SkyVerge
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Authorize.net CIM API Class
 *
 * Handles sending/receiving/parsing of CIM XML
 *
 * @since 1.0
 */
class WC_Authorize_Net_CIM_API {


	/** string API production endpoint */
	const PRODUCTION_ENDPOINT = 'https://api.authorize.net/xml/v1/request.api';

	/** string API test endpoint */
	const TEST_ENDPOINT = 'https://apitest.authorize.net/xml/v1/request.api';

	/** @var string API endpoint */
	public $endpoint;

	/** @var \WC_Authorize_Net_CIM_API_Request instance */
	public $request;

	/** @var string generated request XML */
	private $request_xml;

	/** @var \WC_Authorize_Net_CIM_API_Response instance */
	public $response;

	/** @var string retrieved response XML */
	private $response_xml;


	/**
	 * Constructor - setup request object and set endpoint
	 *
	 * @since 1.0
	 * @param string $api_user_id
	 * @param string $api_transaction_key
	 * @param string $environment API environment to POST transactions to
	 * @return \WC_Authorize_Net_CIM_API
	 */
	public function __construct( $api_user_id, $api_transaction_key, $environment ) {

		// setup request object
		$this->request = new WC_Authorize_Net_CIM_API_Request( $api_user_id, $api_transaction_key );

		// set endpoint
		$this->endpoint = ( 'production' === $environment ) ? WC_Authorize_Net_CIM_API::PRODUCTION_ENDPOINT : WC_Authorize_Net_CIM_API::TEST_ENDPOINT;
	}


	/**
	 * Create a single transaction using AIM XML API
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	public function create_single_transaction( $order ) {

		$this->perform_request( $this->request->get_transaction_request_xml( $order ) );

		return $this->parse_response();
	}


	/**
	 * Create a new customer / payment profile using CIM XML API
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	public function create_new_customer( $order ) {

		$this->perform_request( $this->request->get_create_customer_profile_xml( $order ) );

		return $this->parse_response();
	}


	/**
	 * Create a new profile transaction using CIM XML API
	 *
	 * @since  1.0
	 * @param \WC_Order instance
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	public function create_new_transaction( $order ) {

		$this->perform_request( $this->request->get_create_customer_profile_transaction_request_xml( $order ) );

		return $this->parse_response();
	}


	/**
	 * Adds a new customer payment profile using CIM XML API
	 *
	 * @since  1.0
	 * @param \WC_Order instance
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	public function add_new_payment_profile( $order ) {

		$this->perform_request( $this->request->get_create_customer_payment_profile_xml( $order ) );

		return $this->parse_response();
	}


	/**
	 * Validate's a customer's payment profile from CIM and retrieves payment information
	 * Mainly used for getting information about a credit card/bank account, like last 4, exp date, etc
	 *
	 * @since 1.0
	 * @param int $customer_profile_id the customer profile ID provided returned by CIM when the profile was created
	 * @param int $payment_profile_id the payment profile ID provided returned by CIM when the profile was created
	 * @param string $mode 'testMode' runs a luhn check and test transaction, 'liveMode' processes a $1 auth/void to actually
	 * verify the card instead of a test transaction. Uses 'testMode' by default
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	public function validate_payment_profile( $customer_profile_id, $payment_profile_id, $mode = 'testMode' ) {

		$this->perform_request( $this->request->get_validate_customer_payment_profile_xml( $customer_profile_id, $payment_profile_id, $mode ) );

		return $this->parse_response();
	}


	/**
	 * Deletes a customer payment profile using CIM XML API
	 *
	 * @since  1.0
	 * @param string $customer_profile_id
	 * @param string $customer_payment_profile_id
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	public function delete_payment_profile( $customer_profile_id, $customer_payment_profile_id ) {

		$this->perform_request( $this->request->get_delete_customer_payment_profile_request_xml( $customer_profile_id, $customer_payment_profile_id ) );

		return $this->parse_response();
	}


	/**
	 * Gets the token required for the redirect to the hosted profile page, currently only used to determine if the
	 * provided authorize.net account has the CIM add-on enabled
	 *
	 * @since 1.0.4
	 * @param int $customer_profile_id the customer profile ID to retrieve the hosted profile page for
	 * @throws Exception any API error
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	public function get_hosted_profile_page_token( $customer_profile_id ) {

		$this->perform_request( $this->request->get_hosted_profile_page_xml( $customer_profile_id ) );

		return $this->parse_response();
	}


	/**
	 * Remove XML namespace and instantiate a new WC_Authorize_Net_CIM_API_Response object from the response XML
	 *
	 * @since 1.0
	 * @throws Exception any API error
	 * @return \WC_Authorize_Net_CIM_API_Response instance
	 */
	private function parse_response() {

		// Remove namespace as SimpleXML throws warnings with invalid namespace URI provided by Authorize.net
		$this->response = preg_replace( '/[[:space:]]xmlns[^=]*="[^"]*"/i', '', $this->response );

		// LIBXML_NOCDATA ensures that any XML fields wrapped in [CDATA] will be included as text nodes
		$response = new WC_Authorize_Net_CIM_API_Response( $this->response, LIBXML_NOCDATA );

		// Throw exception for true API errors, 'E00027' is a generic 'Declined' message which is handled by response class
		if ( $response->has_api_error() && 'E00027' != $response->get_api_error_code() ) {
			throw new Exception( sprintf( __( 'Error: [%s] - %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $response->get_api_error_code(), $response->get_api_error_message() ) );
		}

		return $response;
	}


	/**
	 * HTTP POST request XML to active endpoint using wp_remote_post() and set response XML
	 *
	 * @since 1.0
	 * @throws Exception network timeouts, etc
	 */
	private function perform_request( $xml ) {

		$this->request_xml = $xml;

		$wp_http_args = array(
			'timeout'     => apply_filters( 'wc_authorize_net_cim_api_timeout', 45 ), // default to 45 seconds
			'redirection' => 0,
			'httpversion' => '1.0',
			'sslverify'   => false,
			'blocking'    => true,
			'headers'     => array(
				'accept'       => 'application/xml',
				'content-type' => 'application/xml' ),
			'body'        => $xml,
			'cookies'     => array(),
			'user-agent'  => "PHP " . PHP_VERSION
		);

		$this->response = wp_remote_post( $this->endpoint, $wp_http_args );

		// Check for Network timeout, etc.
		if ( is_wp_error( $this->response ) ) {
			throw new Exception( $this->response->get_error_message() );
		}

		// return blank XML document if response body doesn't exist
		$this->response = ( isset( $this->response[ 'body' ] ) ) ? $this->response[ 'body' ] : '<?xml version="1.0" encoding="utf-8"?>';

		$this->response_xml = $this->response;
	}


	/**
	 * Get the request XML stripped of namespace and confidential information (merchant auth, card number, CVV code)
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_request_xml() {

		// Remove namespace
		$this->request_xml = preg_replace( '/[[:space:]]xmlns[^=]*="[^"]*"/i', '', $this->request_xml );

		// replace merchant authentication
		$this->request_xml = preg_replace( '/<name>[a-zA-Z0-9]*<\/name>/', '<name>123456789</name>', $this->request_xml );
		$this->request_xml = preg_replace( '/<transactionKey>[a-zA-Z0-9]*<\/transactionKey>/', '<transactionKey>123456789</transactionKey>', $this->request_xml );

		// replace real card number
		$this->request_xml = preg_replace( '/<cardNumber>[0-9]*<\/cardNumber>/', '<cardNumber>0000000000000000</cardNumber>', $this->request_xml );

		// replace real CVV code
		$this->request_xml = preg_replace( '/<cardCode>[0-9]*<\/cardCode>/', '<cardCode>000</cardCode>', $this->request_xml );

		return $this->request_xml;
	}


	/**
	 * Get the response XML stripped of namespace
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_response_xml() {

		// Remove namespace
		$this->response_xml = preg_replace( '/[[:space:]]xmlns[^=]*="[^"]*"/i', '', $this->response_xml );

		return $this->response_xml;
	}


} // end \WC_Authorize_Net_CIM_API class


/**
 * Authorize.net CIM API Request Class
 *
 * Generates XML required by AIM/CIM API specs
 *
 * @link http://www.authorize.net/support/AIM_guide_XML.pdf
 * @link http://www.authorize.net/support/CIM_XML_guide.pdf
 *
 * @since 1.0
 * @extends XMLWriter
 */
class WC_Authorize_Net_CIM_API_Request extends XMLWriter {


	/** @var string API login ID */
	private $api_login_id;

	/** @var string API transaction key */
	private $api_transaction_key;


	/**
	 * Open XML document in memory, set version/encoding, and auth information
	 *
	 * @since 1.0
	 * @param string $api_login_id required
	 * @param string $api_transaction_key required
	 * @return \WC_Authorize_Net_CIM_API_Request
	 */
	public function __construct( $api_login_id, $api_transaction_key ) {

		// Create XML document in memory
		$this->openMemory();

		// Set XML version & encoding
		$this->startDocument( '1.0', 'UTF-8' );

		$this->api_login_id        = $api_login_id;
		$this->api_transaction_key = $api_transaction_key;
	}


	/**
	 * Create XML for adding a customer profile, including the payment profile
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 * @return string
	 */
	public function get_create_customer_profile_xml( $order ) {

		// root element is unique to each request
		$this->startElementNs( null, 'createCustomerProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		// <profile>
		$this->startElement( 'profile' );

		$this->add_profile_info_xml( $order );

		// <paymentProfiles>
		$this->startElement( 'paymentProfiles' );

		// <billTo>
		$this->startElement( 'billTo' );

		$this->add_address_xml( $order, 'billing' );

		// </billTo>
		$this->endElement();

		$this->add_payment_info_xml( $order->payment );

		// </paymentProfiles>
		$this->endElement();

		// add shipToList if shipping address is populated
		if ( $order->shipping_country ) {

			// <shipToList>
			$this->startElement( 'shipToList' );

			$this->add_address_xml( $order, 'shipping' );

			// </shipToList>
			$this->endElement();
		}

		// </profile>
		$this->endElement();

		$this->add_validation_mode_xml();

		// </createCustomerProfileRequest>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Create XML for creating a transaction from a customer profile ID and payment profile ID
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 * @return string
	 */
	public function get_create_customer_profile_transaction_request_xml( $order ) {

		// root element is unique to each request
		$this->startElementNs( null, 'createCustomerProfileTransactionRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		// <transaction>
		$this->startElement( 'transaction' );

		// <profileTransAuthCapture> or <profileTransAuthOnly>
		$this->add_profile_transaction_type_xml( $order );

		$this->writeElement( 'amount', $order->payment_total );

		$this->writeElement( 'customerProfileId', $order->customer_profile_id );

		$this->writeElement( 'customerPaymentProfileId', $order->customer_payment_profile_id );

		// add shipping profile if available
		if ( $order->customer_shipping_profile_id ) {
			$this->writeElement( 'customerShippingAddressId', $order->customer_shipping_profile_id );
		}

		// <order>
		$this->startElement( 'order' );

		$this->writeElement( 'invoiceNumber', ltrim( $order->get_order_number(), _x( '#', 'hash before the order number', WC_Authorize_Net_CIM::TEXT_DOMAIN ) ) );

		$this->writeElement( 'description', $order->description );

		if ( $order->po_number ) {
			$this->writeElement( 'purchaseOrderNumber', $order->po_number );
		}

		// </order>
		$this->endElement();

		// add CVV if available
		if ( ! empty( $order->payment->cvv ) ) {
			$this->writeElement( 'cardCode', $order->payment->cvv );
		}

		// </profileTransAuthCapture> or </profileTransAuthOnly>
		$this->endElement();

		// </transaction>
		$this->endElement();

		// @see set_duplicate_window() for explanation
		if ( ! empty( $order->payment->cvv ) ) {
			$this->set_duplicate_window( 'cim' );
		}

		return $this->get_xml();
	}


	/**
	 * Create XML for adding a new customer payment profile
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 * @return string
	 */
	public function get_create_customer_payment_profile_xml( $order ) {

		// root element is unique to each request
		$this->startElementNs( null, 'createCustomerPaymentProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		$this->writeElement( 'customerProfileId', $order->customer_profile_id );

		// <paymentProfile>
		$this->startElement( 'paymentProfile' );

		// <billTo>
		$this->startElement( 'billTo' );

		$this->add_address_xml( $order, 'billing' );

		// </billTo>
		$this->endElement();

		$this->add_payment_info_xml( $order->payment );

		// </paymentProfile>
		$this->endElement();

		$this->add_validation_mode_xml();

		// </createCustomerPaymentProfileRequest>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Get XML for creating a single transaction via the AIM API
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 * @return string
	 */
	public function get_transaction_request_xml( $order ) {

		$this->startElementNs( null, 'createTransactionRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		// <transactionRequest>
		$this->startElement( 'transactionRequest' );

		// <transactionType>
		$this->add_transaction_type_xml( $order );

		$this->writeElement( 'amount', $order->payment_total );

		$this->add_payment_info_xml( $order->payment );

		// <order>
		$this->startElement( 'order' );

		$this->writeElement( 'invoiceNumber', ltrim( $order->get_order_number(), _x( '#', 'hash before the order number', WC_Authorize_Net_CIM::TEXT_DOMAIN ) ) );

		$this->writeElement( 'description', $order->description );

		// </order>
		$this->endElement();

		// <poNumber>
		if ( $order->po_number ) {
			$this->writeElement( 'poNumber', $order->po_number );
		}

		// <customer>
		$this->startElement( 'customer' );

		$this->writeElement( 'id', $order->user_id );

		if ( $order->billing_email ) {
			$this->writeElement( 'email', $order->billing_email );
		}

		// </customer>
		$this->endElement();

		// <billTo>
		$this->startElement( 'billTo' );

		$this->add_address_xml( $order, 'billing' );

		// </billTo>
		$this->endElement();

		// <shipTo>
		$this->startElement( 'shipTo' );

		$this->add_address_xml( $order, 'shipping' );

		// </shipTo>
		$this->endElement();

		// @see set_duplicate_window() for explanation
		if ( ! empty( $order->payment->cvv ) ) {
			$this->set_duplicate_window( 'aim' );
		}

		// </transactionRequest>
		$this->endElement();

		// </createTransactionRequest>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Get XML for validating a customer's payment profile information
	 *
	 * @since 1.0
	 * @param int $customer_profile_id the customer profile ID provided returned by CIM when the profile was created
	 * @param int $payment_profile_id the payment profile ID provided returned by CIM when the profile was created
	 * @param string $mode the mode to validate the payment profile in
	 * @return string
	 */
	public function get_validate_customer_payment_profile_xml( $customer_profile_id, $payment_profile_id, $mode ) {

		$this->startElementNs( null, 'validateCustomerPaymentProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		// <customerProfileId>
		$this->writeElement( 'customerProfileId', $customer_profile_id );

		// <customerPaymentProfileId>
		$this->writeElement( 'customerPaymentProfileId', $payment_profile_id );

		// see specifics on testMode parameter in add_validation_mode_xml() below
		$this->writeElement( 'validationMode', ( 'liveMode' === $mode ) ? 'liveMode' : 'testMode' );

		// </getCustomerPaymentProfileRequest>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Create XML for deleting an existing payment profile
	 *
	 * @since 1.0
	 * @param string $customer_profile_id
	 * @param string $customer_payment_profile_id
	 * @return string
	 */
	public function get_delete_customer_payment_profile_request_xml( $customer_profile_id, $customer_payment_profile_id ) {

		$this->startElementNs( null, 'deleteCustomerPaymentProfileRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		$this->writeElement( 'customerProfileId', $customer_profile_id );

		$this->writeElement( 'customerPaymentProfileId', $customer_payment_profile_id );

		// </deleteCustomerPaymentProfileRequest>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Create XML for retrieving the hosted profile page token
	 *
	 * @since 1.0.4
	 * @param string $customer_profile_id
	 * @return string
	 */
	public function get_hosted_profile_page_xml( $customer_profile_id ) {

		// <getHostedProfilePageRequest>
		$this->startElementNs( null, 'getHostedProfilePageRequest', 'AnetApi/xml/v1/schema/AnetApiSchema.xsd' );

		$this->add_auth_xml();

		$this->writeElement( 'customerProfileId', $customer_profile_id );

		// <hostedProfileSettings>
		$this->startElement( 'hostedProfileSettings' );

		// <setting>
		$this->startElement( 'setting' );

		$this->writeElement( 'settingName', 'hostedProfileReturnUrl' );

		$this->writeElement( 'settingValue', get_bloginfo( 'url' ) );

		// </setting>
		$this->endElement();

		// </hostedProfileSettings>
		$this->endElement();

		// </getHostedProfilePageRequest>
		$this->endElement();

		return $this->get_xml();
	}


	/**
	 * Adds customer profile XML
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 */
	private function add_profile_info_xml( $order ) {

		// customer ID or email is required
		if ( 0 != $order->user_id ) {
			$this->writeElement( 'merchantCustomerId', $order->user_id );
		}

		if ( $order->billing_email ) {
			$this->writeElement( 'email', $order->billing_email );
		}
	}


	/**
	 * Adds payment XML
	 *
	 * @since  1.0
	 * @param \WC_Order instance
	 */
	private function add_payment_info_xml( $payment ) {

		// <payment>
		$this->startElement( 'payment' );

		if ( 'echeck' === $payment->type ) {

			// <bankAccount>
			$this->startElement( 'bankAccount' );

			$this->writeElement( 'routingNumber', $payment->routing_number );

			$this->writeElement( 'accountNumber', $payment->account_number );

			$this->writeElement( 'nameOnAccount', $payment->name_on_account );

			$this->writeElement( 'echeckType', 'WEB' );

			// </bankAccount>
			$this->endElement();

		} else {

			// <creditCard>
			$this->startElement( 'creditCard' );

			$this->writeElement( 'cardNumber', $payment->card_number );

			$this->writeElement( 'expirationDate', $payment->expiration_date );

			if ( ! empty( $payment->cvv ) ) {
				$this->writeElement( 'cardCode', $payment->cvv );
			}

			// </creditCard>
			$this->endElement();
		}

		// </payment>
		$this->endElement();
	}


	/**
	 * Adds customer address XML
	 *
	 * @since 1.0
	 * @param \WC_Order instance
	 * @param $type : address information to add, either 'billing' or 'shipping'
	 */
	private function add_address_xml( $order, $type ) {

		$fields = array(
			'first_name' => 'firstName',
			'last_name'  => 'lastName',
			'company'    => 'company',
			'address_1'  => 'address',
			'city'       => 'city',
			'state'      => 'state',
			'postcode'   => 'zip',
			'country'    => 'country',
			'phone'      => 'phoneNumber'
		);

		foreach ( $fields as $wc_field_name => $cim_field_name ) {

			$field_name = $type . '_' . $wc_field_name;

			// WC doesn't provide shipping phone
			if ( 'shipping' == $type && 'phone' == $wc_field_name ) {
				continue;
			}

			// only one field for address, so combine them
			if ( 'billing' == $type && 'address_1' == $wc_field_name ) {
				$order->billing_address_1 = $order->billing_address_1 . ' ' . $order->billing_address_2;
			}

			if ( 'shipping' == $type && 'address_1' == $wc_field_name ) {
				$order->shipping_address_1 = $order->shipping_address_1 . ' ' . $order->shipping_address_2;
			}

			// write field if it's populated
			if ( $order->$field_name ) {
				$this->writeElement( $cim_field_name, $order->$field_name );
			}
		}
	}


	/**
	 * Sets the time window for duplicate checking to 0 when the CVV is required. This is important because of this use case:
	 *
	 * 1) Customer enters payment info and accidentally enters an incorrect CVV
	 * 2) Auth.net properly declines the transaction
	 * 3) Customer notices the CVV was incorrect, re-enters the correct CVV and tries to submit order
	 * 4) Auth.net rejects this second transaction attempt as a "duplicate transaction"
	 *
	 * For some reason, Auth.net doesn't consider the CVV changing evidence of a non-duplicate transaction and recommends
	 * changing the `x_duplicate_window` transaction option between transactions (https://support.authorize.net/authkb/index?page=content&id=A425&actp=search&viewlocale=en_US&searchid=1375994496602)
	 * to avoid this error. However, simply changing the `x_duplicate_window` between transactions *does not* prevent
	 * the "duplicate transaction" error.
	 *
	 * The `x_duplicate_window` must actually be set to 0 to suppress this error. However, this has the side affect of
	 * potentially allowing duplicate transactions through. The better option will be to catch the CVV decline error and
	 * mark the order so that this duplicate window can be set to 0 on a per-order basis.
	 *
	 * @since 1.0.8
	 * @param string $transaction_type whether the transaction is using the CIM or AIM API
	 */
	private function set_duplicate_window( $transaction_type ) {

		$window = 0;

		if ( 'cim' === $transaction_type ) {

			$this->writeElement( 'extraOptions', "x_duplicate_window={$window}" );

		} elseif ( 'aim' == $transaction_type ) {

			$this->startElement( 'transactionSettings' );

				$this->startElement( 'setting' );

					$this->writeElement( 'settingName', 'duplicateWindow' );

					$this->writeElement( 'settingValue', $window );

				$this->endElement();

			$this->endElement();
		}
	}


	/**
	 * Generates authorization XML that is included with every request
	 *
	 * @since 1.0
	 */
	private function add_auth_xml() {

		// <merchantAuthentication>
		$this->startElement( 'merchantAuthentication' );

		// <name>{api_login_id}</name>
		$this->writeElement( 'name', $this->api_login_id );

		// <transactionKey>{api_transaction_key}</transactionKey>
		$this->writeElement( 'transactionKey', $this->api_transaction_key );

		// </merchantAuthentication>
		$this->endElement();
	}


	/**
	 * Adds validation XML required for certain API calls. This is set to 'none' currently, as the two other modes available aren't useful:
	 * 'testMode' - performs a simple Luhn check, which is already done in validate_fields()
	 * 'liveMode' - performs a $0 authorization, but complicates error checking logic while providing no clear benefit - the payment method
	 *  added will be declined by the processor when trying to complete the transaction anyway, however this is can be used to retrieve
	 *  payment information for storage (e.g. last four, card type, exp date, etc)
	 *
	 * @since 1.0
	 */
	private function add_validation_mode_xml() {

		$this->writeElement( 'validationMode', 'none' );
	}


	/**
	 * Helper to set the transaction type for single transactions, either Authorize only or Authorize and capture
	 *
	 * @since 1.0
	 * @param object $order
	 * @return string XML
	 */
	private function add_transaction_type_xml( $order ) {

		if ( 'auth_only' === $order->transaction_type ) {
			$this->writeElement( 'transactionType', 'authOnlyTransaction' );
		} else {
			$this->writeElement( 'transactionType', 'authCaptureTransaction' );
		}
	}


	/**
	 * Helper to set the transaction type for profile transactions, either Authorize only or Authorize and capture
	 *
	 * @since 1.0
	 * @param object $order
	 * @return string XML
	 */
	private function add_profile_transaction_type_xml( $order ) {

		if ( 'auth_only' === $order->transaction_type ) {
			$this->startElement( 'profileTransAuthOnly' );
		} else {
			$this->startElement( 'profileTransAuthCapture' );
		}
	}

	/**
	 * Helper to return completed XML document
	 *
	 * @since 1.0
	 * @return string XML
	 */
	private function get_xml() {

		$this->endDocument();

		return $this->outputMemory();
	}


} // end \WC_Authorize_Net_CIM_API_Request class


/**
 * Authorize.net CIM API Request Class
 *
 * Parses XML received by AIM/CIM API
 *
 * @link http://www.authorize.net/support/AIM_guide_XML.pdf
 * @link http://www.authorize.net/support/CIM_XML_guide.pdf
 *
 * @since 1.0
 * @extends SimpleXMLElement
 */
class WC_Authorize_Net_CIM_API_Response extends SimpleXMLElement {

	/* Cannot override __construct when extending SimpleXMLElement */

	/** @var string processor response code */
	private $_response_code;

	/** @var string processor decline reason code */
	private $_response_reason_code;

	/** @var string processor decline reason */
	private $_response_reason_text;

	/** @var string transaction ID */
	private $_transaction_id;

	/** @var string credit card last four digits */
	private $_card_last_four;

	/** @var string credit card type */
	private $_card_type;


	/**
	 * Checks if response contains an API error code
	 *
	 * @since 1.0
	 * @return bool true if has API error, false otherwise
	 */
	public function has_api_error() {

		if ( ! isset( $this->messages->resultCode ) ) {
			return true;
		}

		return 'error' == strtolower( (string) $this->messages->resultCode );
	}


	/**
	 * Gets the API error code
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_api_error_code() {

		if ( ! isset( $this->messages->message->code ) ) {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}

		return (string) $this->messages->message->code;
	}


	/**
	 * Gets the API error message
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_api_error_message() {

		if ( ! isset( $this->messages->message->text ) ) {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}

		return (string) $this->messages->message->text;
	}


	/**
	 * Loads the response from a comma separated string into class members
	 *
	 * @link page 40 of http://www.authorize.net/support/AIM_guide.pdf for format details
	 * @link http://www.authorize.net/support/merchant/Transaction_Response/Response_Reason_Codes_and_Response_Reason_Text.htm
	 *
	 * @since 1.0
	 * @throws Exception if response array not present or CSV -> array conversion ( explode() ) fails
	 */
	private function load_response() {

		if ( empty( $this->directResponse ) ) {
			throw new Exception( __( 'Response Error: array not present.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
		}

		$_response = explode( ',', (string) $this->directResponse );

		if ( ! is_array( $_response ) || empty( $_response ) ) {
			throw new Exception( __( 'Response Error: array conversion failed.' ) );
		}

		$this->_response_code        = ( isset( $_response[0] ) )  ? $_response[0] : '';
		$this->_response_reason_code = ( isset( $_response[2] ) )  ? $_response[2] : '';
		$this->_response_reason_text = ( isset( $_response[3] ) )  ? $_response[3] : '';
		$this->_transaction_id       = ( isset( $_response[6] ) )  ? $_response[6] : '';
		$this->_card_last_four       = ( isset( $_response[50] ) ) ? $_response[50] : '';
		$this->_card_type            = ( isset( $_response[51] ) ) ? $_response[51] : '';
	}


	/**
	 * Checks if the transaction was successful
	 *
	 * @since 1.0
	 * @return bool true if successful, false otherwise
	 */
	public function transaction_was_successful() {

		$this->load_response();

		return ( '1' == $this->_response_code || '4' == $this->_response_code );
	}


	/**
	 * Checks if the transaction was held
	 *
	 * @since 1.0
	 * @return bool true if held, false otherwise
	 */
	public function transaction_was_held() {

		$this->load_response();

		return '4' == $this->_response_code;
	}


	/**
	 * Gets the transaction failure message
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_transaction_failure_message() {

		$code_description = __( 'Declined', WC_Authorize_Net_CIM::TEXT_DOMAIN );

		if ( '3' == $this->_response_code ) {
			$code_description = __( 'Error', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}

		return sprintf( __( 'Response: [Code %s] - %s, Reason: [Code %s] - %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), $this->_response_code, $code_description, $this->_response_reason_code, $this->_response_reason_text );
	}


	/**
	 * Gets the transaction held message
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_transaction_held_message() {

		// this assumes the transaction has already been checked for code 4 - held, prior to calling this method
		return (string) $this->_response_reason_text;
	}


	/**
	 * Gets the generated customer profile ID
	 *
	 * @since 1.0
	 * @throws Exception if customer profile ID not present in response
	 * @return string
	 */
	public function get_customer_profile_id() {

		if ( ! isset( $this->customerProfileId ) ) {
			throw new Exception( __( 'Response Error: Customer Profile ID not present in response.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
		}

		return (string) $this->customerProfileId;
	}


	/**
	 * Gets the generated customer shipping profile ID
	 *
	 * @since 1.0
	 * @return string empty string if shipping profile ID is not available, or the shipping profile ID otherwise
	 */
	public function get_customer_shipping_profile_id() {

		return ( isset( $this->customerShippingAddressIdList->numericString ) ) ? (string) $this->customerShippingAddressIdList->numericString : '';
	}


	/**
	 * Gets the generated customer payment profile ID
	 *
	 * @since 1.0
	 * @throws Exception if customer payment profile ID not present in response
	 * @return string
	 */
	public function get_customer_payment_profile_id() {

		if ( ! isset( $this->customerPaymentProfileIdList->numericString ) && ! isset( $this->customerPaymentProfileId ) ) {
			throw new Exception( __( 'Response Error: Customer Payment Profile ID not present in response.', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
		}

		// from createCustomerProfileRequest
		if ( isset( $this->customerPaymentProfileIdList->numericString ) ) {
			return (string) $this->customerPaymentProfileIdList->numericString;
		}

		// from createCustomerPaymentProfileRequest
		if ( isset( $this->customerPaymentProfileId ) ) {
			return (string) $this->customerPaymentProfileId;
		}
	}


	/**
	 * Gets payment profile validation info (card type, exp date, etc)
	 *
	 * @since 1.0
	 * @throws Exception if validation information is missing
	 * @return object
	 */
	public function get_payment_profile_validation_info() {

		if ( ! isset( $this->directResponse ) ) {
			throw new Exception( __( 'Response Error: Validation Info Missing', WC_Authorize_Net_CIM::TEXT_DOMAIN ) );
		}

		$_response = explode( ',', (string) $this->directResponse );

		if ( ! is_array( $_response ) || empty( $_response ) ) {
			throw new Exception( __( 'Response Error: array conversion failed.' ) );
		}

		$info = new stdClass();

		$info->card_type      = ( isset( $_response[51] ) ) ? $_response[51] : '';
		$info->card_last_four = ( isset( $_response[50] ) ) ? $_response[50] : '';
		$info->card_last_four = ltrim( $info->card_last_four, 'X' );

		return $info;
	}


	/**
	 * Gets the transaction ID
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_transaction_id() {

		if ( $this->_transaction_id ) {
			return (string) $this->_transaction_id;
		} else {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}
	}


	/**
	 * Gets the transaction card type (Visa, MC, Bank Account, etc)
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_transaction_card_type() {

		if ( $this->_card_type ) {
			return (string) $this->_card_type;
		} else {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}
	}


	/**
	 * Gets the last four digits of the card used for the transaction
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_transaction_card_last_four() {

		if ( $this->_card_last_four ) {
			return ltrim( (string) $this->_card_last_four, 'X' );
		} else {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}
	}


	/* Functions below are used exclusively for single transactions for guest checkouts */

	/**
	 * Check if the single transaction was successful
	 *
	 * @since 1.0
	 * @return bool true if successful, false otherwise
	 */
	public function single_transaction_was_successful() {

		if ( ! isset( $this->transactionResponse->responseCode ) ) {
			return false;
		}

		return '1' == strtolower( (string) $this->transactionResponse->responseCode );
	}


	/**
	 * Check if the single transaction was held for merchant review
	 *
	 * @since 1.0
	 * @return bool true if transaction was held for merchant review, false otherwise
	 */
	public function single_transaction_was_held() {

		if ( ! isset( $this->transactionResponse->responseCode ) ) {
			return false;
		}

		return '4' == strtolower( (string) $this->transactionResponse->responseCode );
	}


	/**
	 * Gets the single transaction card type
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_single_transaction_payment_type() {

		if ( ! isset( $this->transactionResponse->accountType ) ) {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}

		return (string) $this->transactionResponse->accountType;
	}


	/**
	 * Gets the last four digits of the card used for the single transaction
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_single_transaction_payment_last_four() {

		if ( ! isset( $this->transactionResponse->accountNumber ) ) {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}

		return ltrim( (string) $this->transactionResponse->accountNumber, 'X' );
	}


	/**
	 * Gets the single transaction ID
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_single_transaction_id() {

		if ( ! isset( $this->transactionResponse->transId ) ) {
			return __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}

		return (string) $this->transactionResponse->transId;
	}


	/**
	 * Gets the single transaction failure message
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_single_transaction_failure_message() {

		$failure_message = '';

		// response code
		if ( isset( $this->transactionResponse->responseCode ) ) {
			$failure_message .= sprintf( __( 'Response Error: [Code %s]', WC_Authorize_Net_CIM::TEXT_DOMAIN ), (string) $this->transactionResponse->responseCode );
		}

		// messages
		if ( isset( $this->transactionResponse->messages->message ) ) {

			foreach ( $this->transactionResponse->messages->message as $message ) {

				$failure_message .= sprintf( __( ' Message: [Code %s] - %s.', WC_Authorize_Net_CIM::TEXT_DOMAIN ), (string) $message->code, (string) $message->description );
			}
		}

		// errors
		if ( isset( $this->transactionResponse->errors->error ) ) {

			foreach ( $this->transactionResponse->errors->error as $error ) {

				$failure_message .= sprintf( __( ' Error [Code %s] - %s', WC_Authorize_Net_CIM::TEXT_DOMAIN ), (string) $error->errorCode, (string) $error->errorText );
			}
		}

		return ( ! empty( $failure_message ) ) ? $failure_message : __( 'N/A', WC_Authorize_Net_CIM::TEXT_DOMAIN );
	}


	/**
	 * Gets the single transaction held message
	 *
	 * @since 1.0
	 * @return string
	 */
	public function get_single_transaction_held_message() {

		if ( isset( $this->transactionResponse->messages->message ) ) {
			return (string) $this->transactionResponse->messages->message->description;
		} else {
			return __( 'Your order has been received and is being reviewed. Thank you for your business.', WC_Authorize_Net_CIM::TEXT_DOMAIN );
		}
	}


} // end \WC_Authorize_Net_CIM_API_Response class


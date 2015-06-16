<?php
/**
 * Plugin Name: WooCommerce Authorize.net CIM Gateway
 * Plugin URI: http://www.woothemes.com/products/authorize-net-cim/
 * Description: Adds the Authorize.net CIM Payment Gateway to your WooCommerce site, allowing customers to securely save their credit card to their account for use with single purchases, pre-orders, subscriptions, and more!
 * Author: SkyVerge
 * Author URI: http://www.skyverge.com
 * Version: 1.1
 * Text Domain: woocommerce-authorize-net-cim
 * Domain Path: /i18n/languages/
 *
 * Copyright: (c) 2013-2014 SkyVerge, Inc. (info@skyverge.com)
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package     WC-Authorize-Net-CIM
 * @author      SkyVerge
 * @Category    Payment-Gateways
 * @copyright   Copyright (c) 2013-2014, SkyVerge, Inc.
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU General Public License v3.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// Required functions
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

// Plugin updates
woothemes_queue_update( plugin_basename( __FILE__ ), '8b61524fe53add7fdd1a8d1b00b9327d', '178481' );

// WC active check
if ( ! is_woocommerce_active() ) {
	return;
}

// Required library class
if ( ! class_exists( 'SV_WC_Framework_Bootstrap' ) ) {
	require_once( 'lib/skyverge/woocommerce/class-sv-wc-framework-bootstrap.php' );
}

SV_WC_Framework_Bootstrap::instance()->register_plugin( '2.0', __( 'WooCommerce Authorize.net CIM Gateway', 'woocommerce-gateway-authorize-net-cim' ), __FILE__, 'init_woocommerce_gateway_authorize_net_cim' );

function init_woocommerce_gateway_authorize_net_cim() {

/**
 * The main class for the Authorize.net CIM Gateway.  This class handles all the
 * non-gateway tasks such as verifying dependencies are met, loading the text
 * domain, etc.  It also loads the Authorize.net CIM Gateway when needed now that the
 * gateway is only created on the checkout & settings pages / api hook.  The gateway is
 * also loaded in the following instances:
 *
 * + On the My Account page to display / change saved payment methods
 *
 * Prefixes used :
 *  + 'wc_authorize_net_cim_' for option keys and actions/filters
 *  + '_wc_authorize_net_cim_' for meta keys
 */
class WC_Authorize_Net_CIM extends SV_WC_Plugin {


	/** string version number */
	const VERSION = '1.1';

	/** plugin id */
	const PLUGIN_ID = 'authorize_net_cim';

	/** string plugin text domain */
	const TEXT_DOMAIN = 'woocommerce-authorize-net-cim';

	/** @var string class to load as gateway, can be base or add-ons class */
	public $gateway_class_name = 'WC_Gateway_Authorize_Net_CIM';

	/** @var string class to load as eCheck gateway, can be base or add-ons class */
	public $echeck_gateway_class_name = 'WC_Gateway_Authorize_Net_Cim_eCheck';


	/**
	 * Setup main plugin class
	 *
	 * @since 1.0
	 * @return \WC_Authorize_Net_CIM
	 */
	public function __construct() {

		parent::__construct(
			self::PLUGIN_ID,
			self::VERSION,
			self::TEXT_DOMAIN,
			array( 'dependencies' => array( 'SimpleXML', 'xmlwriter', 'dom' ) )
		);

		// include required files
		add_action( 'sv_wc_framework_plugins_loaded', array( $this, 'includes' ) );

		// Add the 'Manage My Payment Methods' on the 'My Account' page
		add_action( 'woocommerce_after_my_account', array( $this, 'add_my_payment_methods' ) );

		// Admin
		if( is_admin() && ! defined( 'DOING_AJAX' ) ) {

			// show CIM customer profile ID field on edit user pages
			add_action( 'show_user_profile', array( $this, 'add_cim_customer_profile_id_meta_field' ) );
			add_action( 'edit_user_profile', array( $this, 'add_cim_customer_profile_id_meta_field' ) );

			// save CIM customer profile ID field
			add_action( 'personal_options_update',  array( $this, 'save_cim_customer_profile_id_meta_field' ) );
			add_action( 'edit_user_profile_update', array( $this, 'save_cim_customer_profile_id_meta_field' ) );
		}
	}


	/**
	 * Loads API and Gateway classes
	 *
	 * @since 1.0
	 */
	public function includes() {

		// Base gateway class
		require_once( 'classes/class-wc-gateway-authorize-net-cim.php' );

		// eCheck gateway class
		require_once( 'classes/class-wc-gateway-authorize-net-cim-echeck.php' );

		// load add-ons class if subscriptions and/or pre-orders are active
		if ( $this->is_subscriptions_active() || $this->is_pre_orders_active() ) {

			require_once( 'classes/class-wc-gateway-authorize-net-cim-addons.php' );
			require_once( 'classes/class-wc-gateway-authorize-net-cim-echeck-addons.php' );

			$this->gateway_class_name = 'WC_Gateway_Authorize_Net_CIM_Addons';
			$this->echeck_gateway_class_name = 'WC_Gateway_Authorize_Net_CIM_eCheck_Addons';
		}

		// Add classes to WC Payment Methods
		add_filter( 'woocommerce_payment_gateways', array( $this, 'load_gateway' ) );

		// help require the state/province field if a european processor is used
		// note that this filter *must* be added before WooCommerce::init() is called on `init`
		$settings = get_option( 'woocommerce_authorize_net_cim_settings' );

		if ( isset( $settings['payment_processor_location'] ) && 'european' === $settings['payment_processor_location'] ) {
			add_action( 'woocommerce_states', array( $this, 'add_states' ), 1 );
		}
	}


	/**
	 * Adds Authorize.net CIM to the list of available payment gateways
	 *
	 * @since 1.0
	 * @param array $gateways
	 * @return array $gateways
	 */
	public function load_gateway( $gateways ) {

		$gateways[] = $this->gateway_class_name;
		$gateways[] = $this->echeck_gateway_class_name;

		return $gateways;
	}


	/**
	 * Handle localization, WPML compatible
	 *
	 * @since 1.0
	 * @see SV_WC_Plugin::load_translation()
	 */
	public function load_translation() {

		load_plugin_textdomain( 'woocommerce-gateway-authorize-net-cim', false, dirname( plugin_basename( $this->get_file() ) ) . '/i18n/languages' );
	}


	/** Frontend methods ******************************************************/


	/**
	 * Before requiring the state/province field, the state array has to be removed of blank arrays, otherwise
	 * the field is hidden
	 *
	 * @see WC_Countries::__construct()
	 *
	 * @since 1.0.9
	 * @param array $countries the available countries
	 * @return array the available countries
	 */
	public function add_states( $countries ) {

		foreach ( $countries as $country_code => $states ) {

			if ( is_array( $countries[ $country_code ] ) && empty( $countries[ $country_code ] ) )
				$countries[ $country_code ] = null;
		}

		return $countries;
	}


	/**
	 * Helper to add the 'My Cards' section to the 'My Account' page
	 *
	 * @since 1.0
	 */
	public function add_my_payment_methods() {

		$gateway = new WC_Gateway_Authorize_Net_CIM();

		$gateway->show_my_payment_methods();
	}


	/** Admin methods ******************************************************/


	/**
	 * Render a notice for the user to select their desired export format
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::render_admin_notices()
	 */
	public function render_admin_notices() {

		// show any dependency notices
		parent::render_admin_notices();

		$settings = get_option( 'woocommerce_authorize_net_cim_settings' );

		// install notice
		if ( empty( $settings) && ! $this->is_message_dismissed( 'install-notice' ) ) {

			$this->add_dismissible_notice(
				sprintf( __( 'Thanks for installing the WooCommerce Authorize.net CIM Gateway! To start accepting payments, %sset your Authorize.net API credentials%s. Need help? See the %sdocumentation%s. ', self::TEXT_DOMAIN ),
					'<a href="' . $this->get_settings_url() . '">', '</a>',
					'<a target="_blank" href="' . $this->get_documentation_url() . '">', '</a>'
				), 'install-notice'
			);
		}

		// SSL check (only when enabled in production mode)
		if ( isset( $settings['enabled'] ) && 'yes' == $settings['enabled'] ) {
			if ( isset( $settings['test_mode'] ) && 'no' == $settings['test_mode'] ) {

				if ( 'no' === get_option( 'woocommerce_force_ssl_checkout' ) && ! $this->is_message_dismissed( 'ssl-required-notice' ) ) {

					$this->add_dismissible_notice( __( 'WooCommerce is not being forced over SSL -- your customer\'s credit card data is at risk. ', self::TEXT_DOMAIN ), 'ssl-required-notice' );
				}
			}
		}

		// check if CIM feature is enabled on customer's authorize.net account
		if ( ! get_option( 'wc_authorize_net_cim_feature_enabled' ) ) {

			$gateway = new WC_Gateway_Authorize_Net_CIM();

			// don't check if gateway is not available, as proper credentials are needed first
			if ( ! $gateway->is_available() ) {
				return;
			}

			if ( $gateway->is_cim_feature_enabled() ) {
				update_option( 'wc_authorize_net_cim_feature_enabled', true );
			} else {

				if ( ! $this->is_message_dismissed( 'cim-add-on-notice' ) ) {
					$this->add_dismissible_notice(
						sprintf( __( 'The CIM Add-On is not enabled on your Authorize.net account. Please %scontact Authorize.net%s to enable CIM. You will be unable to process transactions until CIM is enabled. ', WC_Authorize_Net_CIM::TEXT_DOMAIN ), '<a href="http://support.authorize.net" target="_blank">', '</a>' ),
						'cim-add-on-notice' );
				}
			}
		}
	}


	/**
	 * Display a field for the CIM Customer Profile ID meta on the view/edit user page
	 *
	 * @since 1.0.2
	 * @param WP_User $user user object for the current edit page
	 */
	public function add_cim_customer_profile_id_meta_field( $user ) {

		// bail if the current user is not allowed to manage woocommerce
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		?>
		<h3><?php _e( 'Authorize.net CIM Customer Details', self::TEXT_DOMAIN ) ?></h3>
		<table class="form-table">
			<tr>
				<th><label for="_wc_authorize_net_cim_profile_id"><?php _e( 'Customer Profile ID', self::TEXT_DOMAIN ); ?></label></th>
				<td>
					<input type="text" name="_wc_authorize_net_cim_profile_id" id="_wc_authorize_net_cim_profile_id" value="<?php echo esc_attr( get_user_meta( $user->ID, '_wc_authorize_net_cim_profile_id', true ) ); ?>" class="regular-text" /><br/>
					<span class="description"><?php _e( 'The CIM customer profile ID for the user. Only edit this if necessary.', WC_Authorize_Net_CIM::TEXT_DOMAIN ); ?></span>
				</td>
			</tr>
		</table>
	<?php
	}


	/**
	 * Display a field for the CIM Customer Profile ID meta on the view/edit user page
	 *
	 * @since 1.0.2
	 * @param int $user_id identifies the user to save the settings for
	 */
	public function save_cim_customer_profile_id_meta_field( $user_id ) {

		// bail if the current user is not allowed to manage woocommerce
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			return;
		}

		if ( ! empty( $_POST['_wc_authorize_net_cim_profile_id'] ) ) {
			update_user_meta( $user_id, '_wc_authorize_net_cim_profile_id', trim( $_POST['_wc_authorize_net_cim_profile_id'] ) );
		} else {
			delete_user_meta( $user_id, '_wc_authorize_net_cim_profile_id' );
		}
	}


	/** Helper methods ******************************************************/


	/**
	 * Checks is WooCommerce Subscriptions is active
	 *
	 * @since 1.0
	 * @return bool true if WCS is active, false if not active
	 */
	public function is_subscriptions_active() {

		return $this->is_plugin_active( 'woocommerce-subscriptions/woocommerce-subscriptions.php' );
	}


	/**
	 * Checks is WooCommerce Pre-Orders is active
	 *
	 * @since 1.0
	 * @return bool true if WC Pre-Orders is active, false if not active
	 */
	public function is_pre_orders_active() {

		return $this->is_plugin_active( 'woocommerce-pre-orders/woocommerce-pre-orders.php' );
	}


	/**
	 * Returns the plugin name, localized
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::get_plugin_name()
	 * @return string the plugin name
	 */
	public function get_plugin_name() {
		return __( 'WooCommerce Authorize.net CIM Gateway', self::TEXT_DOMAIN );
	}


	/**
	 * Returns __FILE__
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::get_file()
	 * @return string the full path and filename of the plugin file
	 */
	protected function get_file() {
		return __FILE__;
	}


	/**
	 * Gets the plugin documentation url
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::get_documentation_url()
	 * @return string documentation URL
	 */
	public function get_documentation_url() {
		return 'http://docs.woothemes.com/document/authorize-net-cim/';
	}


	/**
	 * Gets the gateway configuration URL
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::get_settings_url()
	 * @param string $_ unused
	 * @return string plugin settings URL
	 */
	public function get_settings_url( $_ = null ) {

		return SV_WC_Plugin_Compatibility::get_payment_gateway_configuration_url( $this->gateway_class_name );
	}


	/**
	 * Returns true if on the gateway settings page
	 *
	 * @since 1.1
	 * @see SV_WC_Plugin::is_plugin_settings()
	 * @return boolean true if on the admin gateway settings page
	 */
	public function is_plugin_settings() {

		return SV_WC_Plugin_Compatibility::is_payment_gateway_configuration_page( $this->gateway_class_name );
	}


} // end \WC_Authorize_Net_CIM class


/**
 * The WC_Authorize_Net_CIM global object
 * @name $wc_authorize_net_cim
 * @global WC_Authorize_Net_CIM $GLOBALS['wc_authorize_net_cim']
 */
$GLOBALS['wc_authorize_net_cim'] = new WC_Authorize_Net_CIM();

} // init_woocommerce_gateway_authorize_net_cim()

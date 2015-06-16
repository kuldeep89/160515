<?php
/*
Plugin Name: WooCommerce Name Your Price
Plugin URI: http://woothemes.com/woocommerce
Description: WooCommerce Name Your Price allows customers to set their own price for products or donations.
Version: 1.2.6
Author: Kathy Darling
Author URI: http://kathyisawesome.com
Requires at least: 3.5
Tested up to: 3.6

	Copyright: © 2012 Kathy Darling.
	License: GNU General Public License v3.0
	License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

/**
 * Required functions
 */
if ( ! function_exists( 'woothemes_queue_update' ) )
	require_once( 'woo-includes/woo-functions.php' );

/**
 * Plugin updates
 */
woothemes_queue_update( plugin_basename( __FILE__ ), '31b4e11696cd99a3c0572975a84f1c08', '18738' );

// Quit right now if WooCommerce is not active
if ( ! is_woocommerce_active() )
	return;

/**
 * Localisation
 **/
load_plugin_textdomain( 'wc_name_your_price' , false , dirname( plugin_basename( __FILE__ ) ) .  '/languages/' );

/**
 * wc_name_your_price class
 **/
if ( ! class_exists( 'wc_name_your_price' ) ) :

	class wc_name_your_price {

		/** Versions ***************************************************************/

		public $min_woo = '1.6.2';		//minimum WooCommerce Needed

		/** URLS ***************************************************************/

		var $plugin_path;

		/**
		 * wc_name_your_price Constructor
		 * lets get warmed up
		 * @since 1.0
		 */

		public function __construct() {
			global $woocommerce;

			// define the plugin path
			$this->plugin_path = untrailingslashit( plugin_dir_path( __FILE__ ) );

			// check wordpress and woocommerce versions (do not switch this to init hook!)
			add_action( 'plugins_loaded', array( $this, 'version_check' ), 10 );
	    }

	    /**
		 * Checks for minimum woocommerce version
		 * @since 1.0
		 */
		public function version_check() {
			global $woocommerce;

			// if woo version above require version proceed, otherwise show admin notice and stop in your tracks
			if ( version_compare( $woocommerce->version, $this->min_woo ) >= 0 ) {

				// woo is good to go
				$this->init();

			} else {

				add_action( 'admin_notices', array( $this, 'admin_notice' ) );

			}

		}

	    /**
		 * Display a warning message if version check is not met.
		 * @since 1.0
		 */

		public function admin_notice(){
		    echo '<div class="error">
		       <p>' . sprintf( __( 'WooCommerce <strong>Name Your Price</strong> extension requires at least WooCommerce %s in order to function. Please upgrade your WooCommerce.', 'wc_name_your_price'), $this->min_woo ) . '</p>
				    </div>';
		}

		/*-----------------------------------------------------------------------------------*/
		/* Launch the NYP actions */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * boostrap constructor
		 * @since 1.0
		 */

		public function init(){

			// Setup Product Data
			add_action( 'the_post', array( $this, 'setup_product' ), 20 );

			// Single Product Display
			add_action( 'wp_enqueue_scripts', array( $this, 'nyp_style' ) );
			add_action( 'woocommerce_single_product_summary', array( $this, 'display_minimum_price' ) );
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'display_price_input' ) );
			add_filter( 'single_add_to_cart_text', array( $this, 'add_to_cart_text' ), 20 );

			// Loop Display
			add_filter( 'woocommerce_get_price_html', array( $this, 'filter_suggested_price'), 10, 2 );
			add_filter( 'add_to_cart_text', array( $this, 'add_to_cart_text' ), 20 );
			add_filter( 'woocommerce_add_to_cart_url', array( $this, 'add_to_cart_url' ) );

			// Functions for cart actions - ensure they have a priority before addons (10)
			add_filter( 'woocommerce_is_purchasable', array( $this, 'is_purchasable' ), 5, 2 );
			add_filter( 'woocommerce_add_cart_item_data', array( $this, 'add_cart_item_data' ), 5, 2 );
			add_filter( 'woocommerce_get_cart_item_from_session', array( $this, 'get_cart_item_from_session' ), 5, 2 );
			add_filter( 'woocommerce_add_cart_item', array( $this, 'add_cart_item' ), 5, 1 );
			add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_add_cart_item' ), 5, 3 );

			// Settings Link for Plugin page
			add_filter( 'plugin_action_links', array( $this, 'add_action_link' ), 9, 2);

			// require admin class to handle all backend functions
			require_once( 'classes/class-wc-name-your-price-admin.php' );
		}

		/*-----------------------------------------------------------------------------------*/
		/* Add to the global $product object */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * setup product
		 * @since 1.0
		 */
		function setup_product(){
			global $product;

			if ( ! $product || ! is_object( $product ) )
				return;

			$val = $this->is_nyp( $product->id );

			// add our data to the $product variable
			$product->nyp = ( $val ) ? $val['nyp'] : FALSE;
			$product->suggested = ( $val ) ? $val['suggested'] : FALSE;
			$product->minimum = ( $val ) ? $val['minimum'] : FALSE;
		}

		/*-----------------------------------------------------------------------------------*/
		/* Single Product Display Functions */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Load a little stylesheet
		 * @since 1.0
		 */
		public function nyp_style(){
			global $post;

			if( ! is_product() || ! $this->is_nyp( $post->ID ) ) return;

			wp_enqueue_style( 'name-your-price', plugins_url( 'css/name-your-price.css', __FILE__ ) );
		}

		/**
		 * Call the Minimum Price Template
		 * @since 1.0
		 */
		function display_minimum_price(){
			global $post;

			// if not a nyp product quit right now
			if( ! ( $attributes = $this->is_nyp( $post->ID ) ) ) return;

			// Product does not exist yet so load values from is_nyp array
			extract( $attributes );

			if ( $minimum )
				woocommerce_get_template( 'single-product/minimum-price.php', FALSE, FALSE, $this->plugin_path . '/templates/');
		}

		/**
		 * Call the Price Input Template
		 * @since 1.0
		 */
		function display_price_input(){
			global $post;

			// if not a nyp product quit right now
			if( ! ( $attributes = $this->is_nyp( $post->ID ) ) ) return;

			woocommerce_get_template( 'single-product/price-input.php', FALSE, FALSE, $this->plugin_path . '/templates/');
		}

		/*
		 * Price Formatting Helper
		 * similar to woocommerce_price() but returns a text input instead with formatted number
		 * @since 1.0
		 */
		public function price_input_helper( $price ) {
		    global $woocommerce, $product;

		    $num_decimals = ( int ) get_option( 'woocommerce_price_num_decimals' );
		    $currency_pos = get_option( 'woocommerce_currency_pos' );
		    $currency_symbol = get_woocommerce_currency_symbol();

		    if ( '' != $price ) {

		    	$price = apply_filters( 'raw_woocommerce_price', ( double ) $price );

		 		$price = number_format( $price, $num_decimals, stripslashes( get_option( 'woocommerce_price_decimal_sep' ) ), stripslashes( get_option( 'woocommerce_price_thousand_sep' ) ) );

			    if ( 'yes' == get_option( 'woocommerce_price_trim_zeros' ) && $num_decimals > 0 )
			    	$price = woocommerce_trim_zeros( $price);
			}

		    $input = sprintf( '<input id="nyp" name="nyp" value="%s" size="6" title="nyp" class="input-text amount nyp text" />', $price);

		    if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) && class_exists( 'WC_Subscriptions_Manager' ) ) {

		    	$include = array();

		    	if ( $product->suggested || $product->minimum )
		    		$include = array( 'price' => woocommerce_price( max( $product->suggested, $product->minimum ) ) );

		    	$subscription_string = WC_Subscriptions_Product::get_price_string( $product, $include );

		    	$subscription_string = '<span class="subscription-terms">' . preg_replace ( "#<span.*?>(.*?)</span>#", '', $subscription_string, 1 ) . '</span>';

		    	$input .= $subscription_string;

		    }

			return $input;
		}

		/*-----------------------------------------------------------------------------------*/
		/* Loop Display Functions */
		/*-----------------------------------------------------------------------------------*/

		/**
		 * Filter the Suggested Price
		 * @since 1.0
		 */
		function filter_suggested_price( $price, $product ){

			// if not a nyp product quit right now ( can't check the $product because of conflict with Sensei )
			if( ! ( $attributes = $this->is_nyp( $product->id ) ) )
				return $price;

			// load values from is_nyp array
			extract( $attributes );

			// Hide the Suggested Price on archive pages and on single products if not set :: @since 1.1.2
			if( is_shop() || is_product_category() || is_product_tag() || ( is_product() && ! $suggested ) ) {

				$price = FALSE;

			} else {

				if( class_exists( 'WC_Subscriptions_Product' ) && WC_Subscriptions_Product::is_subscription( $product ) ) {
				$include = array(
									'price' => woocommerce_price( $this->standardize_number( $suggested ) ),
									'subscription_length' => false,
									'sign_up_fee'         => false,
									'trial_length'        => false
								);
					$suggested = WC_Subscriptions_Product::get_price_string( $product, $include );
				} else {
					$suggested = woocommerce_price( $this->standardize_number( $suggested ) );
				}

				$price = sprintf( _x( '%s: %s', 'In case you need to change the order of Suggested Price: $suggested', 'wc_name_your_price', 'wc_name_your_price' ), stripslashes ( get_option( 'woocommerce_nyp_suggested_text', __('Suggested Price', 'wc_name_your_price' ) ) ), $suggested );
			}

			return $price;
		}

		/*
		 * if NYP change the loop's add to cart button text
		 * @since 1.0
		 */
		public function add_to_cart_text( $text ) {
			global $product;

			if ( empty( $product->nyp ) ) return $text;

			if ( is_archive() ) {
				$product->product_type = 'nyp';
				$text = get_option( 'woocommerce_nyp_button_text', __( 'Set Price', 'wc_name_your_price' ) );
			} else {
				$text = get_option( 'woocommerce_nyp_button_text_single', __( 'Add to Cart', 'wc_name_your_price' ) );
			}

			return $text;
		}

		/*
		 * if NYP change the loop's add to cart button URL
		 * disable ajax add to cart and redirect to product page
		 * @since 1.0
		 */
		public function add_to_cart_url( $url ) {
			global $product;

			if ( is_archive() && $this->is_nyp( $product->id ) ) {
					$product->product_type = 'nyp';
					$url = get_permalink( $product->id );
			}

			return $url;
		}

		/*-----------------------------------------------------------------------------------*/
		/* Cart Filters */
		/*-----------------------------------------------------------------------------------*/

		/*
		 * override woo's is_purchasable in cases of nyp products
		 * @since 1.0
		 */
		public function is_purchasable( $purchasable , $product ) {
			if( $this->is_nyp( $product->id) ) {
				$purchasable = true;
			}
			return $purchasable;
		}

		/*
		 * add cart session data
		 * @since 1.0
		 */
		public function add_cart_item_data( $cart_item_meta, $product_id ) {
			global $woocommerce;

			//no need to check is_nyp b/c this has already been validated by validate_add_cart_item()
			if( isset( $_POST['nyp'] ) ) {
				$cart_item_meta['nyp'] = $this->standardize_number( $_POST['nyp'] );
			}
			return $cart_item_meta;
		}

		/*
		 * adjust the product based on cart session data
		 * @since 1.0
		 */
		function get_cart_item_from_session( $cart_item, $values ) {

			//no need to check is_nyp b/c this has already been validated by validate_add_cart_item()
			if ( isset( $values['nyp'] ) ) {
				$cart_item['nyp'] = woocommerce_format_total( $values['nyp'] );
				$cart_item = $this->add_cart_item( $cart_item );
			}
			return $cart_item;

		}

		/*
		 * change the price of the item in the cart
		 * @since 1.0
		 */
		public function add_cart_item( $cart_item ) {

			// Adjust price in cart if nyp is set
			if ( $this->is_nyp( $cart_item['data']->id ) && isset( $cart_item['nyp'] ) ) {
				$cart_item['data']->price = $cart_item['nyp'];
				$cart_item['data']->sale_price =  $cart_item['nyp'];
				$cart_item['data']->regular_price = $cart_item['nyp'];
			}

			return $cart_item;
		}

		/*
		 * check this is a NYP product before adding to cart
		 * @since 1.0
		 */
		public function validate_add_cart_item( $passed, $product_id, $qty ) {
			global $woocommerce;

			// skip if not a nyp product - send original status back
			$val = $this->is_nyp( $product_id );

			if ( ! $val )
				return $passed;

			$input = $_POST['nyp'];

			// set a null string to 0
			if ( ! isset( $_POST['nyp'] ) || empty( $_POST['nyp'] ) )
				$input = 0;

			$input = $this->standardize_number( $input );

			$minimum = ! empty( $val[ 'minimum'] ) ? $this->standardize_number( $val[ 'minimum'] ) : '';

			// check that it is a numeric value
			if ( ! is_numeric( $input ) ) {
				$passed = false;
				$woocommerce->add_error( __( 'Please enter a valid number.', 'wc_name_your_price' ) );
			// check that it is not negative
			} elseif ( floatval( $input ) < 0 ) {
				$passed = false;
				$woocommerce->add_error( __( 'You cannot enter a negative value.', 'wc_name_your_price' ) );
			// check that it is greater than minimum price
			} elseif ( $minimum && floatval( $input ) < floatval( $minimum ) ) {
				$passed = false;
				$woocommerce->add_error( sprintf(__( 'Please enter at least %s.', 'wc_name_your_price' ), woocommerce_price( $minimum ) ) );
			}

			return $passed;
		}


		/*
		 * 'Settings' link on plugin page
		 * @since 1.0
		 */

		public function add_action_link( $links, $file ) {

		    if ( $file == plugin_basename( __FILE__ ) ) {
		      $settings_link = '<a href="'.admin_url('admin.php?page=woocommerce&tab=nyp').'" title="'.__('Go to the settings page', 'wc_name_your_price').'">'.__('Settings', 'wc_shipworks').'</a>';
				      // make the 'Settings' link appear first
		      array_unshift( $links, $settings_link );
		    }

		    return $links;
		  }



	    /*-----------------------------------------------------------------------------------*/
		/* Helper Functions */
		/*-----------------------------------------------------------------------------------*/

		/*
		 * Verify this is a Name Your Price product
		 *
		 * right now only available on simple products and subscriptions
		 *
		 * @since 	1.0
		 * @access 	public
		 * @return 	return array() or FALSE
		 */

		public function is_nyp( $id ){
			if ( get_post_type( $id ) == 'product' && has_term( array( 'simple', 'subscription' ), 'product_type', $id ) && get_post_meta( $id , '_nyp', true ) == 'yes' ) {

				$num_decimals = ( int ) get_option( 'woocommerce_price_num_decimals' );

				// filter the raw suggested price @since 1.2
				$suggested = apply_filters ( 'woocommerce_raw_suggested_price', get_post_meta( $id , '_suggested_price', true ), $id );

				// filter the raw minimum price @since 1.2
				$minimum = apply_filters ( 'woocommerce_raw_minimum_price', get_post_meta( $id , '_min_price', true ), $id );

				return array (
						'nyp' => TRUE,
						'suggested' => $suggested,
						'minimum' => $minimum,
					) ;
			} else {
				return FALSE;
			}
		}


		/*
		 * Standardize number
		 *
		 * Switch the configured decimal and thousands separators to PHP default
		 *
		 * @since 	1.2.2
		 * @access 	public
		 * @return 	return string
		 */
		public function standardize_number( $value ){

			//using NYP as a temp holder so that decimals and thousands don't end up the same
			$value = str_replace(
			  array( get_option( 'woocommerce_price_thousand_sep' ), get_option( 'woocommerce_price_decimal_sep' ), 'NYP' ),
			  array( 'NYP', '.', '' ),
			  $value
			);

			return woocommerce_format_total ( $value );

		}

	} //end class: do not remove or there will be no more guacamole for you

endif; // end class_exists check

// boot it up
$wc_name_your_price = new wc_name_your_price();

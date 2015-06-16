<?php
/**
 * Name Your Price Admin Class
 *
 * Adds a name your price setting tab and saves name your price meta data.
 *
 * @package		WooCommerce Name Your Price
 * @subpackage	WC_Name_Your_Price_Admin
 * @category	Class
 * @author		Kathy Darling
 * @since		1.0
 */
class WC_Name_Your_Price_Admin {

	/**
	 * Bootstraps the class and hooks required actions & filters.
	 *
	 * @since 1.0
	 */
	public static function init() {

		// Product Meta boxes
		add_filter( 'product_type_options', array( __CLASS__, 'product_type_options' ) );
		add_action( 'woocommerce_product_options_general_product_data', array( __CLASS__, 'add_to_metabox' ) );
		add_action( 'woocommerce_process_product_meta', array( __CLASS__, 'process_meta_box' ), 1, 2 );
		add_action( 'admin_print_footer_scripts', array( __CLASS__, 'meta_box_script' ) );
		add_action( 'admin_print_styles', array( __CLASS__, 'add_help_tab' ), 20 );

		// Edit Products screen
		add_filter( 'manage_edit-product_columns', array( __CLASS__, 'edit_product_columns'), 20 );
		add_action( 'manage_product_posts_custom_column', array( __CLASS__, 'column_display'), 10, 2 );
		add_filter( 'manage_edit-product_sortable_columns', array( __CLASS__, 'column_register_sortable' ) );
		add_filter( 'request', array( __CLASS__, 'column_orderby' ) );

		// Quick Edit
		add_action( 'quick_edit_custom_box',  array( __CLASS__, 'quick_edit'), 20, 2 );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'quick_edit_scripts'), 20 );
		add_action( 'save_post', array( __CLASS__, 'quick_edit_save'), 10, 2 );

		// Admin Settings
		add_filter( 'woocommerce_settings_tabs_array', array( __CLASS__, 'admin_tabs' ) );
		add_action( 'woocommerce_settings_tabs_nyp', array( __CLASS__, 'admin_panel') );
		add_action( 'woocommerce_update_options_nyp' , array( __CLASS__, 'process_admin_options' ) );
	}

		    /*-----------------------------------------------------------------------------------*/
			/* Write Panel / metabox */
			/*-----------------------------------------------------------------------------------*/

			/*
			 * Add checkbox to product data metabox title
			 * @since 1.0
			 */
			public static function product_type_options( $options ){

			  $options['nyp'] = array(
			      'id' => '_nyp',
			      'wrapper_class' => 'show_if_nyp',
			      'label' => __( 'Name Your Price', 'wc_name_your_price'),
			      'description' => __( 'Customers are allowed to determine their own price.', 'wc_name_your_price')
			    );

			  return $options;
			}

			/*
			 * Add text inputs to product metabox
			 * @since 1.0
			 */
			public static function add_to_metabox(){
				global $post;

				echo '<div class="options_group show_if_nyp">';

				// Suggested Price
				echo woocommerce_wp_text_input( array(
					'id' => '_suggested_price',
					'class' => 'wc_input_price short',
					'label' => __( 'Suggested Price', 'wc_name_your_price') . ' ('.get_woocommerce_currency_symbol().')' ,
					'desc_tip' => 'true',
					'description' => __( 'Price to pre-fill for customers.  Leave blank to not suggest a price.', 'wc_name_your_price' ) )
				);

				// Minimum Price
				echo woocommerce_wp_text_input( array(
					'id' => '_min_price',
					'class' => 'wc_input_price short',
					'label' => __( 'Minimum Price', 'wc_name_your_price') . ' ('.get_woocommerce_currency_symbol().')',
					'desc_tip' => 'true',
					'description' =>  __( 'Lowest acceptable price for product. Leave blank to not enforce a minimum. Must be less than or equal to the set suggested price.', 'wc_name_your_price') . '</a>' )
				);

				do_action( 'woocommerce_name_your_price_options_pricing' );

				echo '</div>';

			  }


			/*
			 * Save extra meta info
			 * @since 1.0
			 */
		    public static function process_meta_box( $post_id, $post ) {

				if ( isset( $_POST['_nyp'] ) ) {
					update_post_meta( $post_id, '_nyp', 'yes' );
				} else {
					update_post_meta( $post_id, '_nyp', 'no' );
				}

 				$num_decimals = ( int ) get_option( 'woocommerce_price_num_decimals' );
 				$sep = stripslashes( get_option( 'woocommerce_price_decimal_sep' ) );

				if ( isset( $_POST['_suggested_price'] ) && $_POST['_suggested_price'] !== '' ) {
					$suggested = woocommerce_clean( $_POST['_suggested_price'] );
					update_post_meta( $post_id, '_suggested_price', $suggested );
				} else {
					delete_post_meta( $post_id, '_suggested_price' );
				}

				if ( isset( $_POST['_min_price'] ) && $_POST['_min_price'] !== '' ) {
					$min = woocommerce_clean( $_POST['_min_price'] );
					//if $min is less than $suggested, set $min = $suggested @todo: warning/notice for this?
					$min = ( isset( $suggested ) && wc_name_your_price::standardize_number( $min ) > wc_name_your_price::standardize_number( $suggested ) ) ? $suggested : $min;
					update_post_meta( $post_id, '_min_price', $min );
				} else {
					delete_post_meta( $post_id, '_min_price' );
				}

		    }

			/*
			 * Javascript to hide/show the suggested price inputs only if NYP checkbox is checked
			 * @since 1.0
			 */
		    public static function meta_box_script(){

		    	if ( ! function_exists( 'get_current_screen' ) )
		    		return;

				$screen = get_current_screen();

				// Product/Coupon/Orders
				if ( $screen->id != 'product' ) return; ?>

				<script type="text/javascript">
					jQuery(document).ready( function( $ ) {

						var allowed_types = [ 'simple', 'subscription' ];

						$('.options_group.show_if_nyp').insertAfter('.options_group.pricing');

					   //show the nyp text inputs and hide the regular price inputs
						var woocommerce_nyp_admin = function() {

							if( 0 <= $.inArray( $( '#product-type' ).val(), allowed_types ) ) {
								$( 'label.show_if_nyp' ).show();
								if ( $( '#_nyp' ).is( ':checked' ) ) {
									$( '.options_group.show_if_nyp' ).show();
									$( '.options_group.pricing' ).hide();
									//disable the subcription price
									$( '#_subscription_price' ).prop( 'disabled', true ).css( 'background','#CCC' );
								} else {
									$( '.options_group.show_if_nyp' ).hide();
									$( '.options_group.pricing' ).show();
									$( '#_subscription_price' ).prop( 'disabled', false ).css( 'background', '#FFF' );
								}
							} else {
								$( '.show_if_nyp' ).hide();
							}


						};

						//run on page load
						woocommerce_nyp_admin();

						//run again any time product type or nyp status changes
						$( '#_nyp, #product-type' ).change(function() {
							woocommerce_nyp_admin();
						});

					});

				</script>

			<?php

			}

			/*
			 * Add help tab for product meta
			 * @since 1.0
			 */
		    public static function add_help_tab(){

		    	if ( ! function_exists( 'get_current_screen' ) )
		    		return;

				$screen = get_current_screen();

				// Product/Coupon/Orders
				if ( ! in_array( $screen->id, array( 'product', 'edit-product' ) ) ) return;

				$screen->add_help_tab( array(
			    'id'	=> 'woocommerce_nyp_tab',
			    'title'	=> __('Name Your Price', 'wc_name_your_price'),
			    'content'	=>

			    	'<h4>' . __( 'Name Your Price', 'wc_name_your_price' ) . '</h4>' .

			    	'<p>' . __( 'In the "Product Meta" metabox, check the Name Your Price checkbox to allow your customers to enter their own price.', 'wc_name_your_price' ) . '</p>' .

			    	'<p>' . __( 'Current this ability is only available for "Simple" Products.', 'wc_name_your_price' ) . '</p>' .

			    	'<h4>' . __( 'Suggested Price', 'wc_name_your_price' ) . '</h4>' .

			    	'<p>' . __( 'This is the price you\'d like to suggest to your customers.  The Name Your Price input will be prefilled with this value.  To not suggest a price at all, you may leave this field blank.', 'wc_name_your_price' ) . '</p>' .

			    	'<p>' . __( 'This value must be a positive number.', 'wc_name_your_price' ) . '</p>' .

			    	'<h4>' . __( 'Minimum Price', 'wc_name_your_price' ) . '</h4>' .

			    	'<p>' . __( 'This is the lowest price you are willing to accept for this product.  To not enforce a minimum (ie: to accept any price, including zero), you may leave this field blank.', 'wc_name_your_price' ) . '</p>' .

			    	'<p>' . __( 'This value must be a positive number that is less than or equal to the set suggested price.', 'wc_name_your_price' ) . '</p>' .

			    	'<h4>' . __( 'Subscriptions', 'wc_name_your_price' ) . '</h4>' .

			    	'<p>' . __( 'If you have a name your price subscription product, the subscription time period fields are still needed, but the price will be disabled in lieu of the Name Your Price suggested and minimum prices.', 'wc_name_your_price' ) . '</p>'

			    ) );

			}

		    /*-----------------------------------------------------------------------------------*/
			/* Product Overview - edit columns */
			/*-----------------------------------------------------------------------------------*/

			/*
			 * Add columns to edit screen
			 * @since 1.0
			 */

			public static function edit_product_columns ( $columns ) {

				$new_columns = array();

				foreach( $columns as $key => $value ) {
					$new_columns[ $key ] = $value;
					if ( $key == 'price' ) {
						$new_columns[ 'nyp' ] = '<span class="tips" data-tip="' . __('Sort by suggested price', 'woocommerce_nyp') . '">' . __( 'Suggested', "woocommerce_nyp" ) . ' / <br>' . __( 'Minimum' , "woocommerce_nyp" ) . '</span>';
					}
				}

				return $new_columns;
			}


			/*
			 * Display the column content
			 * @since 1.0
			 */
			public static function column_display( $column_name, $post_id ) {

				switch ( $column_name ) {

					case 'nyp' :

						/* Custom inline data for nyp */
						$nyp = get_post_meta( $post_id, '_nyp', true );
						$suggested = get_post_meta( $post_id, '_suggested_price', true );
						$min = get_post_meta( $post_id, '_min_price', true );
						$product_type = has_term( 'simple', 'product_type', $post_id ) ? 'simple' : 'not';

						echo '
							<div class="hidden" id="nyp_inline_' . $post_id . '">
								<div class="nyp">' . $nyp . '</div>
								<div class="suggested_price">' . $suggested . '</div>
								<div class="min_price">' . $min . '</div>
								<div class="product_type">' . $product_type . '</div>
							</div>
						';

						if ( wc_name_your_price::is_nyp( $post_id ) && $suggested )
							echo woocommerce_price( $suggested ) ;

						if ( wc_name_your_price::is_nyp( $post_id ) && $min = get_post_meta( $post_id, '_min_price', true ) )
							echo ' / ' . woocommerce_price( $min ) ;

					break;


				}

			}

			/*
			 * Register the column as sortable
			 * @since 1.0
			 */

			public static function column_register_sortable( $columns ) {
				$columns['nyp'] = 'suggested';

				return $columns;
			}

			/*
			 * Sort the columns
			 * @since 1.0
			 */

			public static function column_orderby( $vars ) {
				if ( isset( $vars['orderby'] ) && 'suggested' == $vars['orderby'] ) {
					$vars = array_merge( $vars, array(
						'meta_key' => '_suggested_price',
						'orderby' => 'meta_value_num'
					) );
				}

				return $vars;
			}

		    /*-----------------------------------------------------------------------------------*/
			/* Quick Edit */
			/*-----------------------------------------------------------------------------------*/

			/*
			 * Add quick edit fields
			 * @since 1.0
			 */

			public static function quick_edit( $column_name, $post_type ) {

				if ( $column_name != 'price' || $post_type != 'product' ) return;  ?>

			    <fieldset class="inline-edit-col-left">

				    <div id="nyp-fields" class="inline-edit-col">

				    	<br class="clear" />

					   	<h4><?php _e('Name Your Price', 'wc_name_your_price'); ?>  <input type="checkbox" name="_nyp" id="#nyp" value="1" /></h4>

					    <div class="nyp_prices">
					    	<label>
					            <span class="checkbox-title"><?php _e( 'Suggested Price', 'wc_name_your_price' ); ?></span>
					            <span>
					            	<input type="text" name="_suggested_price" class="inline-edit-password-input suggested_price" placeholder="<?php _e( 'Suggested Price', 'wc_name_your_price' ); ?>" value="">
					            </span>
					        </label>
					        <label>
					            <span class="checkbox-title"><?php _e( 'Minimum Price', 'wc_name_your_price' ); ?></span>
					            <span>
					            	<input type="text" name="_min_price" class="inline-edit-password-input min_price" placeholder="<?php _e( 'Minimum price', 'wc_name_your_price' ); ?>" value="">
					        	</span>
					        </label>
					    </div>

					    <input type="hidden" name="nyp_quick_edit_nonce" value="<?php echo wp_create_nonce( 'nyp_quick_edit_nonce' ); ?>" />
				    </div>
				</fieldset>
			  <?php
			}

			/*
			 * Load the scripts for dealing with the quick edit
			 * @since 1.0
			 */

			public static function quick_edit_scripts( $hook ) {
			  global $post_type;

			  if ( $hook == 'edit.php' && $post_type == 'product' )
			      wp_enqueue_script( 'nyp_quick-edit', plugins_url( 'js/quick-edit.js', dirname( __FILE__ ) ), array('jquery'), '1.2.3', true );
			}

			/*
			 * Save quick edit changes
			 * @since 1.0
			 */

			public static function quick_edit_save( $post_id, $post ) {
				global $woocommerce, $wpdb;

				if ( !$_POST ) return $post_id;
				if ( is_int( wp_is_post_revision( $post_id ) ) ) return;
				if( is_int( wp_is_post_autosave( $post_id ) ) ) return;
				if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return $post_id;
				if ( !isset($_POST['nyp_quick_edit_nonce']) || (isset($_POST['nyp_quick_edit_nonce']) && !wp_verify_nonce( $_POST['nyp_quick_edit_nonce'], 'nyp_quick_edit_nonce' ))) return $post_id;
				if ( !current_user_can( 'edit_post', $post_id )) return $post_id;
				if ( $post->post_type != 'product' ) return $post_id;

				// Save fields

				if(isset($_POST['_nyp'])) update_post_meta( $post_id, '_nyp', 'yes' ); else update_post_meta( $post_id, '_nyp', 'no' );

				if(isset($_POST['_suggested_price'])) {
					$suggested = round( abs ( floatval( $_POST['_suggested_price'] ) ), $num_decimals );
					update_post_meta( $post_id, '_suggested_price', $suggested );
				}

			    if(isset($_POST['_min_price'])) {
					$min = round( abs ( floatval( $_POST['_min_price'] ) ), $num_decimals );
					//don't save if $min is less than $suggested
					$min = isset( $suggested ) && $min > $sugested ? '' : $min;
					update_post_meta( $post_id, '_min_price', $min );
			    }

			  // Clear transient
			  $woocommerce->clear_product_transients( $post_id );
			}

			/*-----------------------------------------------------------------------------------*/
			/* Admin Settings */
			/*-----------------------------------------------------------------------------------*/

			/*
			 * Add tab to settings
			 * @since 1.0
			 */

			public static function admin_tabs( $tabs ) {
				$tabs['nyp'] = __( 'Name Your Price', 'wc_name_your_price' );
				return $tabs;
			}


			 /**
			  * add_settings_fields
			  *
			  * Add settings fields for the nyp tab.
			  *
			  * @since 1.0
			  */
			public static function add_settings_fields () {
			  	global $woocommerce_settings;

			  	$woocommerce_settings['nyp'] = apply_filters('woocommerce_nyp_settings', array(

						array( 'name' => __( 'Name Your Price Setup', 'wc_name_your_price' ), 'type' => 'title', 'desc' =>  __( 'Modify the text strings used by the Name Your Own Price extension.', 'wc_name_your_price' ), 'id' => 'woocommerce_nyp_options' ),

						array(
							'name' => __( 'Suggested Price Text', 'wc_name_your_price' ),
							'desc' 		=> __( 'This is the text to display before the suggested price.', 'wc_name_your_price' ),
							'id' 		=> 'woocommerce_nyp_suggested_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'std' 		=> __( 'Suggested Price', 'wc_name_your_price' ),
							'desc_tip'	=>  true,
						),

						array(
							'name' => __( 'Minimum Price Text', 'wc_name_your_price' ),
							'desc' 		=> __( 'This is the text to display before the minimum accepted price.', 'wc_name_your_price' ),
							'id' 		=> 'woocommerce_nyp_minimum_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'std' 		=> __( 'Minimum Price', 'wc_name_your_price' ),
							'desc_tip'	=>  true,
						),

						array(
							'name' => __( 'Name Your Price Text', 'wc_name_your_price' ),
							'desc' 		=> __( 'This is the text that appears above the Name Your Price input field.', 'wc_name_your_price' ),
							'id' 		=> 'woocommerce_nyp_label_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'std' 		=> __( 'Name Your Price', 'wc_name_your_price' ),
							'desc_tip'	=>  true,
						),

						array(
							'name' => __( 'Add to Cart Button Text for Shop', 'wc_name_your_price' ),
							'desc' 		=> __( 'This is the text that appears on the Add to Cart buttons on the Shop Pages.', 'wc_name_your_price' ),
							'id' 		=> 'woocommerce_nyp_button_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'std' 		=> __( 'Set Price', 'wc_name_your_price' ),
							'desc_tip'	=>  true,
						),

						array(
							'name' => __( 'Add to Cart Button Text for Single Product', 'wc_name_your_price' ),
							'desc' 		=> __( 'This is the text that appears on the Add to Cart buttons on the Single Product Pages.', 'wc_name_your_price' ),
							'id' 		=> 'woocommerce_nyp_button_text_single',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'std' 		=> __( 'Add to Cart', 'wc_name_your_price' ),
							'desc_tip'	=>  true,
						),

						array( 'type' => 'sectionend', 'id' => 'woocommerce_nyp_options' )

					)); // End nyp settings

			} // End add_settings_fields()


			/*
			 * Display the plugin's options
			 * @since 1.0
			 */
			public static function admin_panel() {

				if( ! current_user_can('manage_options') ){

					echo '<p>'. __('You do not have sufficient permissions to access this page.', 'wc_name_your_price') . '</p>';

				} else {

					global $woocommerce_settings;

					self::add_settings_fields();

					woocommerce_admin_fields( $woocommerce_settings['nyp'] );

				}
			}

			/*
			 * Save the plugin's options
			 * @since 1.0
			 */
			public static function process_admin_options(){
				global $woocommerce_settings;

				// Make sure our settings fields are recognised.
	  			self::add_settings_fields();

				// Save settings
				woocommerce_update_options( $woocommerce_settings['nyp'] );

			}
}

WC_Name_Your_Price_Admin::init();

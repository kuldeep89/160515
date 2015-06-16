<?php
/*
Plugin Name: Fancy Product Designer - WooCommerce
Plugin URI: http://fancyproductdesigner.com/woocommerce-plugin/
Description: Integrate Fancy Product Designer in WooCommerce and sell custom designed products.
Version: 1.0.26
Author: radykal.de
Author URI: http://radykal.de
*/

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if (!defined('FPD_PLUGIN_DIR'))
    define( 'FPD_PLUGIN_DIR', dirname(__FILE__) );

if (!defined('FPD_ORDER_DIR'))
    define( 'FPD_ORDER_DIR', WP_CONTENT_DIR . '/fancy_products_orders/' );


if(!class_exists('Fancy_Product_Designer')) {

	class Fancy_Product_Designer {

		private $version_field_name = 'fancyproductdesigner_version';
		public static $views_table_name;
		public static $add_script = false;

		const VERSION = '1.0.26';
		const FPD_VERSION = '2.0.6';
		const CAPABILITY = "edit_fancy_product_desiger";
		const DEMO = false;

		public function __construct() {

			global $wpdb;

			self::$views_table_name = $wpdb->prefix . "fpd_views";

			require_once('admin/admin.php');



            //********** WORDPRESS HOOKS *******************

            //activation and actions
            register_activation_hook(  __FILE__, array( &$this, 'activate_plugin' ) );
            //Uncomment this line to delete all database tables when deactivating the plugin
            //register_deactivation_hook( __FILE__, array( &$this,'deactive_plugin' ) );

			add_filter( 'body_class', array( &$this, 'add_fancy_product_class') );
			add_action( 'wpmu_new_blog', array( &$this, 'new_blog'), 10, 6);
			add_action( 'init', array( &$this,'init' ) );
			add_action( 'plugins_loaded', array( &$this,'plugins_loaded' ) );
			add_action( 'wp_enqueue_scripts',array( &$this,'enqueue_styles' ) );
			add_action( 'wp_footer', array(&$this, 'enqueue_scripts') );

			//********** WOOCOMMERCE HOOKS *******************

			//CATEGORY
			add_filter( 'woocommerce_loop_add_to_cart_link', array(&$this, 'add_to_cart_cat_text'), 10, 2 );


			//SINGLE FANCY PRODUCT
			//load custom template for fancy products
			add_filter( 'template_include', array( &$this, 'use_custom_template'), 99 );
			//add product designer
			if( get_option('fpd_placement')  == 'fpd-replace-image')
				add_action( 'woocommerce_before_single_product_summary', array( &$this, 'add_product_designer'), 15 );
			else
				add_action( 'fpd_product_designer', array( &$this, 'add_product_designer') );
			//add additional form fields to cart form
			add_action( 'woocommerce_before_add_to_cart_button', array( &$this, 'add_product_designer_form') );

			//CART
			add_filter( 'woocommerce_add_cart_item', array(&$this, 'add_cart_item'), 10 );
			//get cart item from session
			add_filter( 'woocommerce_get_cart_item_from_session', array(&$this, 'get_cart_item_from_session'), 10, 2 );
			//add additional [fpd_data]([fpd_product],[fpd_price]) to cart item
			add_filter( 'woocommerce_add_cart_item_data', array(&$this, 'add_cart_item_data'), 10, 2 );
			//add
			add_filter( 'woocommerce_get_item_data', array(&$this, 'get_item_data'), 10, 2 );


		}

		//install when a new network site is added
		public function new_blog( $blog_id, $user_id, $domain, $path, $site_id, $meta ) {

			if ( ! function_exists( 'is_plugin_active_for_network' ) )
				require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

		    global $wpdb;

		    if ( is_plugin_active_for_network('fancy-product-designer/fancy-product-designer.php') ) {
		        $old_blog = $wpdb->blogid;
		        switch_to_blog($blog_id);
		        $this->activate_plugin();
		        switch_to_blog($old_blog);
		    }

		}

		//frontend init
		public function init() {

			load_plugin_textdomain( 'radykal', false, dirname( plugin_basename( __FILE__ ) ). '/languages/' );

			$tax_design_cat_labels = array(
			  'name' => _x( 'Fancy Design Categories', 'taxonomy general name', 'radykal' ),
			  'singular_name' => _x( 'Fancy Design Category', 'taxonomy singular name', 'radykal' ),
			  'search_items' =>  __( 'Search Design Categories', 'radykal' ),
			  'all_items' => __( 'All Design Categories', 'radykal' ),
			  'parent_item' => __( 'Parent Design Category', 'radykal' ),
			  'parent_item_colon' => __( 'Parent Design Category:', 'radykal' ),
			  'edit_item' => __( 'Edit Design Category', 'radykal' ),
			  'update_item' => __( 'Update Design Category', 'radykal' ),
			  'add_new_item' => __( 'Add New Design Category', 'radykal' ),
			  'new_item_name' => __( 'New Design Category Name', 'radykal' ),
			  'menu_name' => __( 'Fancy Design Categories', 'radykal' ),
			);

			register_taxonomy( 'fpd_design_category', 'attachment', array(
				'public' => true,
				'labels' => $tax_design_cat_labels,
				'hierarchical' => false,
				'sort' => true,
				'show_tagcloud' => false,
				'capabilities' => array(self::CAPABILITY)
			));

			wp_register_style( 'font-awesome-4', plugins_url('/font-awesome/css/font-awesome.min.css', __FILE__), false, '4.0.3' );
			wp_register_style( 'fpd-plugins', plugins_url('/css/plugins.min.css', __FILE__), false, self::FPD_VERSION );
			wp_register_style( 'fpd-fonts', plugins_url('/css/jquery.fancyProductDesigner-fonts.css', __FILE__), false, self::FPD_VERSION );
			wp_register_style( 'jquery-fpd', plugins_url('/css/jquery.fancyProductDesigner.css', __FILE__), array(
				'font-awesome-4',
				'fpd-plugins',
				'fpd-fonts'
			), self::FPD_VERSION );

			wp_register_script( 'fabric', plugins_url('/js/fabric.js', __FILE__), false, '1.4.3' );
			wp_register_script( 'fpd-plugins', plugins_url('/js/plugins.min.js', __FILE__), false, self::FPD_VERSION );
			wp_register_script( 'google-webfont', plugins_url('/js/webfont.js', __FILE__), false, '1.4.7' );
			wp_register_script( 'fpd-jspdf', plugins_url('/jspdf/jspdf.min.js', __FILE__), false, self::FPD_VERSION );
			wp_register_script( 'fpd-jquery-form', plugins_url('/js/jquery.form.min.js', __FILE__) );
			wp_register_script( 'jquery-fpd', plugins_url('/js/jquery.fancyProductDesigner.min.js', __FILE__), array(
				'jquery',
				'fabric',
				'fpd-plugins',
				'google-webfont'
			), self::FPD_VERSION );

			add_action( 'wp_ajax_fpduploadimage', array( &$this, 'upload_image' ) );
			if( get_option('fpd_upload_designs_php_logged_in') == 'no' )
				add_action( 'wp_ajax_nopriv_fpduploadimage', array( &$this, 'upload_image' ) );

		}

		//define some hooks and check version
		public function plugins_loaded() {

			global $woocommerce;

			if( get_option($this->version_field_name) != self::VERSION)
			    $this->upgrade();

			//ORDER
			add_action( 'woocommerce_add_order_item_meta', array( &$this, 'add_order_item_meta'), 10, 2 );
			if(floatval($woocommerce->version) < 2.1) {
				add_filter( 'woocommerce_order_table_product_title', array(&$this, 'add_edit_link_to_order_item') , 10, 2 );
			}
			else {
				add_filter( 'woocommerce_order_item_name', array(&$this, 'add_edit_link_to_order_item') , 10, 2 );
			}

		}

		//includes scripts and styles in the frontend
		public function enqueue_styles() {

			global $post;

			//only enqueue css and js files when necessary
			if( self::is_fancy_product($post->ID) ) {

				wp_enqueue_style( 'fpd-single-product', plugins_url('/css/fancy-product.css', __FILE__), array('jquery-fpd'), self::FPD_VERSION );

				$google_webfonts = get_option( 'fpd_google_webfonts' );
				if( !empty($google_webfonts) ) {
					echo '<link href="http://fonts.googleapis.com/css?family='.implode ("|", $google_webfonts).'" rel="stylesheet" type="text/css">';
				}

				$selector = 'fancy-product-designer-'.$post->ID.'';

				?>
				<style type="text/css">

					/* Styling */
					.fancy-product .fpd-container .fpd-main-color,
					.fancy-product .fpd-container .fpd-menu-bar > h3,
					.fancy-product .fpd-container .fpd-upload-progess-bar > div {
						background-color: <?php echo get_option('fpd_frontend_primary'); ?>;
						color: <?php echo get_option('fpd_frontend_primary_elements'); ?>;
					}

					.fancy-product .fpd-container .fpd-content-color,
					.fancy-product .fpd-container .fpd-content-color h3,
					.fancy-product .fpd-container .fpd-tooltip-theme, {
						background-color: <?php echo get_option('fpd_frontend_secondary'); ?>;
						color: <?php echo get_option('fpd_frontend_primary'); ?>;
					}

					.fancy-product .fpd-container .fpd-border-color,
					.fancy-product .fpd-container .fpd-content > div > ul > li,
					.fancy-product .fpd-container .fpd-saved-products > ol > li,
					.fancy-product .fpd-container .fpd-text-input,
					.fancy-product .fpd-container .fpd-edit-elements select,
					.fancy-product .fpd-container .fpd-views-selection li,
					.fancy-product .fpd-container .fpd-tooltip-theme {
						border-color: <?php echo get_option('fpd_frontend_border'); ?>;
					}

					.fancy-product .fpd-container .fpd-content > div > textarea:focus,
					.fancy-product .fpd-container .fpd-text-input:focus,
					.fancy-product .fpd-container .fpd-edit-elements select:focus,
					.fancy-product .fpd-container .fpd-views-selection li:hover {
						border-color: <?php echo get_option('fpd_frontend_border_highlight'); ?>;
					}

					.fancy-product .fpd-container .fpd-saved-products > ol > li > button {
						color: <?php echo get_option('fpd_frontend_primary_elements'); ?>;
					}

					.fancy-product .fpd-container .fpd-button,
					.fancy-product .fpd-container .fpd-color-picker .sp-replacer {
						background-color: <?php echo get_option('fpd_frontend_primary_elements'); ?>;
						color: <?php echo get_option('fpd_frontend_text_button'); ?>;
					}

					.fancy-product .fpd-container .fpd-button-danger {
						background-color: <?php echo get_option('fpd_frontend_danger_button'); ?>;
					}

					.fancy-product .fpd-container .fpd-button-submit {
						background-color: <?php echo get_option('fpd_frontend_submit_button'); ?>;
					}

				</style>
				<?php
			}

		}

		public function enqueue_scripts() {

			if( self::$add_script ) {
				if( get_option('fpd_pdf_button') == 'yes' )
					wp_enqueue_script( 'fpd-jspdf' );
				wp_enqueue_script( 'fpd-jquery-form' );
				wp_enqueue_script( 'jquery-fpd' );
			}

		}

		//loads a custom template for single fancy product pages
		public function use_custom_template( $template ) {

			global $post;

			$template_slug = basename(rtrim( $template, '.php' ));
			if($template_slug == 'single-product' && self::is_fancy_product($post->ID) && get_option('fpd_template_full') == 'yes') {
				//set ptoduct title above product designer
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
				add_action( 'woocommerce_before_single_product_summary', 'woocommerce_template_single_title', 10 );

				$template = FPD_PLUGIN_DIR . '/single-fancy-product.php';
			}

			return $template;

		}

		//the actual product designer will be added
		public function add_product_designer() {

			global $post, $wpdb, $product, $woocommerce;

			if( self::is_fancy_product( $post->ID ) ) {

				require_once('admin/settings-init.php');

				//remove product image, there you gonna see the product designer
				if(get_option('fpd_template_product_image') != 'yes')
					remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );

				self::$add_script = true;

				$selector = 'fancy-product-designer-'.$post->ID.'';

				$load_from_cart_or_order = 0;
				if( isset($_GET['cart_item_key']) ) {
					//load from cart item
					$load_from_cart_or_order = 1;
					$cart = $woocommerce->cart->get_cart();
					$cart_item = $cart[$_GET['cart_item_key']];
					if($cart_item) {
						if( isset($cart_item['fpd_data']) ) {
							$views = $cart_item['fpd_data']['fpd_product'];
						}
					}
					else {
						//cart item could not be found
						echo '<p><strong>';
						_e('Sorry, but the cart item could not be found!', 'radykal');
						echo '</strong></p>';
						return;
					}

				}
				else if( isset($_GET['order']) && isset($_GET['item_id']) ) {
					//load ordered product in designer
					$load_from_cart_or_order = 1;
					$order = new WC_Order( $_GET['order'] );
					$item_meta = $order->get_item_meta( $_GET['item_id'], 'fpd_data' );
					$views = $item_meta[0]["fpd_product"];

				}
				else {
					//load product view(s) from database
					$tableCheck = $wpdb->query("SHOW TABLES LIKE '".self::$views_table_name."'");
					if($tableCheck == 0)
						return;
					$views = $wpdb->get_results("SELECT * FROM ".(self::$views_table_name)." WHERE product_id={$post->ID} ORDER BY ID ASC");

				}

				//check if views is not empty
				if(empty($views))
					return;

				?>
				<div id="<?php echo $selector; ?>" class="fpd-container">

					<?php if(!$load_from_cart_or_order) {
						$first_view = $views[0];
					?>
					<div class="fpd-product" title="<?php echo $first_view->title; ?>" data-thumbnail="<?php echo $first_view->thumbnail; ?>">
						<?php
							echo $this->get_element_anchors_from_view($first_view->elements);
						?>
						<?php
						if(sizeof($views) > 1) {
							for($i = 1; $i <  sizeof($views); $i++) {
								$sub_view = $views[$i];
								?>
								<div class="fpd-product" title="<?php echo $sub_view->title; ?>" data-thumbnail="<?php echo $sub_view->thumbnail; ?>"><?php echo $sub_view->title; ?>
									<?php
									echo $this->get_element_anchors_from_view($sub_view->elements);
									?>
								</div>
								<?php
							}
						}
						?>
					</div>
					<?php } ?>

					<div class="fpd-design">
						<?php

						//get all category terms
						$category_terms = get_terms( 'fpd_design_category', array(
							'hide_empty' => false
						));

						//loop through all categories
						if(is_array($category_terms)) {
							foreach($category_terms as $category_term) {

								//get attachments from fancy design category
								$args = array(
									 'posts_per_page' => -1,
									 'post_type' => 'attachment',
									 'orderby' => 'menu_order',
									 'order' => 'ASC',
									 'fpd_design_category' => $category_term->slug
								);
								$designs = get_posts( $args );

								if( !empty($designs) ) :
								?>
								<div class="fpd-category" title="<?php echo $category_term->name; ?>">
									<?php

									$general_design_parameters_str = $this->get_designs_parameters_str();

									if(is_array($designs)) {
										foreach( $designs as $design ) {
											$design_parameters_str = $general_design_parameters_str;
											$parameters = get_post_meta($design->ID, 'fpd_parameters', true);
											if (strpos($parameters,'enabled') !== false) {
												$parameters_array = array();
												parse_str($parameters, $parameters_array);
												$design_parameters_str = $this->convert_parameters_to_string(array_merge($this->get_designs_parameters_as_array(), $parameters_array));
											}

											echo "<img src='{$design->guid}' title='{$design->post_title}' data-parameters='$design_parameters_str' />";
										}
									}

									?>
								</div>
								<?php
								endif;
							}
						}
						?>
					</div>

				</div>
				<p class="fpd-not-supported-device-info">
					<strong><?php echo get_option('fpd_not_supported_device_info'); ?></strong>
				</p>

				<script type="text/javascript">

					jQuery(document).ready(function() {

						$ = jQuery.noConflict();
						//return;
						<?php
						//paramaters for all text elements
						$custom_text_parameters['x'] = get_option('fpd_custom_texts_parameter_x');
						$custom_text_parameters['y'] = get_option('fpd_custom_texts_parameter_y');
						$custom_text_parameters['z'] = get_option('fpd_custom_texts_parameter_z');
						$custom_text_parameters['colors'] = get_option('fpd_custom_texts_parameter_colors');
						$custom_text_parameters['price'] = get_option('fpd_custom_texts_parameter_price');
						$custom_text_parameters['auto_center'] = get_option('fpd_custom_texts_parameter_auto_center') == 'yes';
						$custom_text_parameters['draggable'] = get_option('fpd_custom_texts_parameter_draggable') == 'yes';
						$custom_text_parameters['rotatable'] = get_option('fpd_custom_texts_parameter_rotatable') == 'yes';
						$custom_text_parameters['resizable'] = get_option('fpd_custom_texts_parameter_resizable') == 'yes';
						$custom_text_parameters['zChangeable'] = get_option('fpd_custom_texts_parameter_zchangeable') == 'yes';
						$custom_text_parameters['patternable'] = get_option('fpd_custom_texts_parameter_patternable') == 'yes';
						$custom_text_parameters['removable'] = 1;
						$custom_text_parameters['bounding_box_control'] = get_option('fpd_custom_texts_parameter_bounding_box_control') == 'yes';
						$custom_text_parameters['bounding_box_by_other'] = get_option('fpd_custom_texts_parameter_bounding_box_target');
						$custom_text_parameters['bounding_box_x'] = get_option('fpd_custom_texts_parameter_bounding_box_x');
						$custom_text_parameters['bounding_box_y'] = get_option('fpd_custom_texts_parameter_bounding_box_y');
						$custom_text_parameters['bounding_box_width'] = get_option('fpd_custom_texts_parameter_bounding_box_width');
						$custom_text_parameters['bounding_box_height'] = get_option('fpd_custom_texts_parameter_bounding_box_height');
						$custom_text_parameters['textSize'] = get_option('fpd_default_text_size');

						$custom_text_parameters_string = $this->convert_parameters_to_string($custom_text_parameters);
						$general_design_parameters_str = $this->get_designs_parameters_str();

						?>

						//merge default designs parameters with the additioanl parameters for uploaded designs
						var customImagesParams = $.extend(<?php echo $general_design_parameters_str; ?>, {
							minW: <?php echo get_option('fpd_uploaded_designs_parameters_min_w'); ?>,
							minH: <?php echo get_option('fpd_uploaded_designs_parameters_min_h'); ?>,
							maxW: <?php echo get_option('fpd_uploaded_designs_parameters_max_w'); ?>,
							maxH: <?php echo get_option('fpd_uploaded_designs_parameters_max_h'); ?>,
							resizeToW: <?php echo get_option('fpd_uploaded_designs_parameters_resize_to_w'); ?>,
							resizeToH: <?php echo get_option('fpd_uploaded_designs_parameters_resize_to_h'); ?>
							}
						);

						//call fancy product designer plugin
						var fancyProductDesigner = $('#<?php echo $selector; ?>').fancyProductDesigner({
							fontDropdown: <?php echo intval(get_option('fpd_fonts_dropdown') == 'yes') ?>,
							allowProductSaving: <?php echo intval(get_option('fpd_allow_product_saving') == 'yes') ?>,
							centerInBoundingbox: <?php echo intval(get_option('fpd_center_in_bounding_box') == 'yes')  ?>,
							customTexts: <?php echo intval(get_option('fpd_custom_texts') == 'yes') ?>,
							customTextParameters: <?php echo $custom_text_parameters_string; ?>,
							fonts: ["<?php echo implode('", "', self::get_active_fonts()); ?>"],
							templatesDirectory: "<?php echo plugins_url('/templates/', __FILE__); ?>",
							labels: <?php echo self::get_labels_string(); ?>,
							saveAsPdf: <?php echo intval(get_option('fpd_pdf_button') == 'yes') ?>,
							uploadDesigns: <?php echo intval(get_option('fpd_upload_designs') == 'yes') ?>,
							customImagesParameters: customImagesParams,
							defaultFont: <?php echo get_option('fpd_default_font') ? '"'. get_option('fpd_default_font') . '"' : 'false'; ?>,
							dimensions: {
								sidebarNavWidth: <?php echo get_option('fpd_sidebar_nav_width'); ?>,
								sidebarContentWidth: <?php echo get_option('fpd_sidebar_content_width'); ?>,
								sidebarHeight: <?php echo get_option('fpd_sidebar_height'); ?>,
								productStageWidth: <?php echo get_option('fpd_stage_width'); ?>,
								productStageHeight: <?php echo get_option('fpd_stage_height'); ?>
							},
							facebookAppId: "<?php echo get_option('fpd_facebook_app_id'); ?>",
							phpDirectory: "<?php echo plugins_url('/inc/', __FILE__); ?>",
							patterns: ["<?php echo implode('", "', $this->get_pattern_urls()); ?>"],
							layout: "<?php echo get_option('fpd_layout'); ?>"
						}).data('fancy-product-designer');

						var $selector = $('#<?php echo $selector; ?>'),
							productCreated = false,
							wcPrice = <?php echo $product->get_price() ? $product->get_price() : 0; ?>,
							fpdPrice = 0,
							currencySymbol = "<?php echo get_woocommerce_currency_symbol(); ?>",
							decimalSeparator = "<?php echo get_option('woocommerce_price_decimal_sep'); ?>",
							thousandSeparator = "<?php echo get_option('woocommerce_price_thousand_sep'); ?>",
							numberOfDecimals = <?php echo get_option('woocommerce_price_num_decimals'); ?>,
							currencyPos = "<?php echo get_option('woocommerce_currency_pos'); ?>";

						//when load from cart or order, use loadProduct
						$selector.on('ready', function() {

							if(<?php echo $load_from_cart_or_order; ?>) {
								var views = <?php echo $load_from_cart_or_order ? stripslashes($views) : 0; ?>;
								fancyProductDesigner.clear();
								fancyProductDesigner.loadProduct(views);
							}

							//replace filereader uploader with php uploader
							if("<?php echo get_option('fpd_type_of_uploader'); ?>" == 'php') {

								$selector.find('.fpd-upload-form').off('change').change(function() {

									$selector.find('.fpd-upload-form').ajaxSubmit({
										url: "<?php echo admin_url('admin-ajax.php'); ?>",
										dataType: 'json',
										data: {action: 'fpduploadimage'},
										type: 'post',
										beforeSubmit: function(arr, $form, options) {
											$phpUploaderInfo.show().children('p:first').text(arr[0].value.name);
											$progressBar.children('.fpd-progress-bar-move').css('width', 0);
										},
										success: function(responseText, statusText) {

											if(responseText.code == 200) {
												//successfully uploaded

												var image = new Image();
												image.src = responseText.url;

												image.onload = function() {

													var imageH = this.height,
									    				imageW = this.width,
									    				scaling = 1;

													//check if its too big
													if(imageW > imageH) {
														if(imageW > customImagesParams.resizeToW) { scaling = customImagesParams.resizeToW / imageW; }
														if(scaling * imageH > customImagesParams.resizeToH) { scaling = customImagesParams.resizeToH / imageH; }
													}
													else {
														if(imageH > customImagesParams.resizeToH) { scaling = customImagesParams.resizeToH / imageH; }
														if(scaling * imageW > customImagesParams.resizeToW) { scaling = customImagesParams.resizeToW / imageW; }
													}

													customImagesParams.scale = scaling;
													fancyProductDesigner.addElement('image', this.src, responseText.filename, customImagesParams);

													$phpUploaderInfo.hide();

												};

											}
											else if(responseText.code == 500) {
												//failed
												alert(responseText.message);
											}
											else {
												//failed
												alert("<?php echo _e('You need to be logged in to upload images!', 'radykal'); ?>");
											}

										},
										error: function() {
											alert("<?php echo _e('Server error: Image could not be uploaded, please try again!', 'radykal'); ?>");
										},
										uploadProgress: function(evt, pos, total, percentComplete) {
											$progressBar.children('.fpd-progress-bar-move').css('width', percentComplete+'%');
										}
									});

								}).after('<div class="fpd-php-uploader-info"><p></p><div class="fpd-upload-progess-bar"><div class="fpd-progress-bar-bg"></div><div class="fpd-progress-bar-move"></div></div></div>');

								$phpUploaderInfo = $selector.find('.fpd-php-uploader-info');
								$progressBar = $phpUploaderInfo.children('.fpd-upload-progess-bar');

							}


						});

						//calculate initial price
						$selector.on('productCreate', function() {

							productCreated = true;
							_setTotalPrice();

						});

						//check when variation has been selected
						$(document).on('found_variation', '.variations_form', function(evt, variation) {

							if(variation.price_html) {
								wcPrice = $(variation.price_html).text().replace(thousandSeparator, '').replace(decimalSeparator, '.').replace(/[^\d.]/g,'');
								_setTotalPrice();
							}

						});

						//listen when price changes
						$selector.on('priceChange', function(evt, sp, tp) {

							fpdPrice = tp;
							_setTotalPrice();

						});

						//listen when a template is loaded
						$selector.on('templateLoad', function(evt, templateUrl) {

							if(templateUrl.lastIndexOf('productstage.php') != -1 && <?php echo intval(get_option('fpd_product_title_menu_bar') == 'yes'); ?>) {
								$selector.find('.fpd-menu-bar > h3').text("<?php echo html_entity_decode(get_the_title($post->ID)); ?>");
							}

						});

						//fill custom form with values and then submit
						$('.fancy-product form.cart').on('click', ':submit', function(evt) {

							evt.preventDefault();

							if(!productCreated) { return false; }

							var product = fancyProductDesigner.getProduct();
							if(product != false) {
								$('input[name="fpd_product"]').val(JSON.stringify(product));
								_setTotalPrice();
								$(this).parents('form.cart').submit();
							}

						});

						//set total price depending from wc and fpd price
						function _setTotalPrice() {

							var totalPrice = parseFloat(wcPrice) + parseFloat(fpdPrice),
								htmlPrice;

							totalPrice = totalPrice.toFixed(numberOfDecimals);
							htmlPrice = totalPrice.toString().replace('.', decimalSeparator);
							if(thousandSeparator.length > 0) {
								htmlPrice = _addThousandSep(htmlPrice);
							}

							if(currencyPos == 'right') {
								htmlPrice = htmlPrice + currencySymbol;
							}
							else if(currencyPos == 'right_space') {
								htmlPrice = htmlPrice + ' ' + currencySymbol;
							}
							else if(currencyPos == 'left_space') {
								htmlPrice = currencySymbol + ' ' + htmlPrice;
							}
							else {
								htmlPrice = currencySymbol + htmlPrice;
							}

							//check if variations are used
							if($('.variations_form').size() > 0) {
								//check if a variations are used with the same price
								if($('.variations_form').data('product_variations').length > 1) {
									//different prices
									$('.single_variation .price .amount').html(htmlPrice);
								}
								else {
									//same price
									$('.price .amount').html(htmlPrice);
								}

							}
							else {
								$('.price .amount').html(htmlPrice);
							}

							$('input[name="fpd_product_price"]').val(totalPrice);

						};

						function _addThousandSep(n){
						    var rx=  /(\d+)(\d{3})/;
						    return String(n).replace(/^\d+/, function(w){
						        while(rx.test(w)){
						            w= w.replace(rx, '$1'+thousandSeparator+'$2');
						        }
						        return w;
						    });
						};
					});

				</script>

				<?php
			}

		}

		//the additional form fields
		public function add_product_designer_form() {

			global $post;

			if(self::is_fancy_product($post->ID)) {
				?>
				<input type="hidden" value="" name="fpd_product" />
				<input type="hidden" value="" name="fpd_product_price" />
				<?php
			}

		}

		//store values from additional form fields
		public function add_cart_item_data( $cart_item_meta, $product_id ) {

			if( self::is_fancy_product($product_id) ) {
				$cart_item_meta['fpd_data']['fpd_product'] = $_POST['fpd_product'];
				$cart_item_meta['fpd_data']['fpd_product_price'] = $_POST['fpd_product_price'];
			}

		    return $cart_item_meta;
		}

		public function get_cart_item_from_session( $cart_item, $values ) {

	        //check for fpd data in session
	        if (isset($values['fpd_data'])) {
	            $cart_item['fpd_data'] = $values['fpd_data'];
	        }

			//check of cart item is fancy product
	        if (isset($cart_item['fpd_data'])) {
	        	//add fpd data to cart item
	            $this->add_cart_item($cart_item);
	        }

	        return $cart_item;
	    }

		public function get_item_data( $other_data, $cart_item ) {

			//get fpd data
			$fpd_data = $cart_item['fpd_data'];
			//check if data contains a product
	        if (isset($fpd_data['fpd_product']) && $fpd_data['fpd_product']) {
	            if (isset($cart_item['fpd_data'])) {

	            	global $woocommerce;
	            	//get cart item key
	            	foreach($woocommerce->cart->get_cart() as $cart_item_key => $values) {
		            	if($values === $cart_item) {
							$cik = $cart_item_key;
		            	}
	            	}

	                $fpd_data = $cart_item['fpd_data'];
					array_push($other_data, array(
						'name' => __('Customized Product', 'radykal'),
						'value' => '<a href="'.add_query_arg( array('cart_item_key' => $cik), get_permalink($cart_item['product_id']) ).'">'.__('Edit', 'radykal').'</a>'
					));
	            }
	        }

	        return $other_data;
		}

		//hook into the cart
		public function add_cart_item( $cart_item ) {

			global $woocommerce;

			$fpd_data = $cart_item['fpd_data'];
			//check if data contains a product
	        if (isset($fpd_data) && $fpd_data) {
	            if (isset($fpd_data['fpd_product_price'])) {
					$cart_item['data']->set_price($fpd_data['fpd_product_price']);
	            }

	        }

		    return $cart_item;

		}

		//custom text for the add-to-cart button in catalog
		public function add_to_cart_cat_text( $handler, $product ) {

			if( self::is_fancy_product( $product->id ) ) {
				return sprintf( '<a href="%s" rel="nofollow" data-product_id="%s" data-product_sku="%s" class="button product_type_%s">%s</a>',
					esc_url( get_permalink($product->ID) ),
					esc_attr( $product->id ),
					esc_attr( $product->get_sku() ),
					esc_attr( $product->product_type ),
					esc_html( get_option( 'fpd_add_to_cart_text' ) )
				);
			}

			return $handler;

		}

		//add order meta from the cart
		public function add_order_item_meta( $item_id, $values ) {

			if( isset( $values['fpd_data']) )
				woocommerce_add_order_item_meta( $item_id, 'fpd_data', $values['fpd_data'] );

		}

		//change url of the order link so the ordered product comes up in designer
		public function add_edit_link_to_order_item( $link, $item ) {

			global $woocommerce, $postid;

			if( isset($item['fpd_data']) ) {
				//check if on view order page, +2.1
				if(isset($_GET['view-order'])) {
					$order_url_param = 'view-order';
				}
				else {
					//checkout page, +2.1
					$order_url_param = 'order-received';
				}

				if(floatval($woocommerce->version) < 2.1) {
					$order_url_param = 'order';
				}

				$order = new WC_Order( $_GET[$order_url_param] );
				foreach($order->get_items() as $key => $value) {
					if($value === $item) {
						$link = '<a href="' . add_query_arg( array('order' => $_GET[$order_url_param], 'item_id' => $key), get_permalink( $item['product_id'] ) ) .'">' . $item['name'] . '</a>';
					}
				}
			}
			else {
				$link = '<a href="' . get_permalink( $item['product_id'] ) . '">' . $item['name'] . '</a>';
			}

			return $link;

		}

		//activate plugin hook
		public function activate_plugin( $networkwide ) {

		   if(version_compare(PHP_VERSION, '5.2.0', '<')) {
			  deactivate_plugins(plugin_basename(__FILE__)); // Deactivate plugin
			  wp_die("Sorry, but you can't run this plugin, it requires PHP 5.2 or higher.");
			  return;
			}

			global $wpdb;

			if ( is_multisite() ) {
	    		if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
	                $current_blog = $wpdb->blogid;
	    			// Get all blog ids
	    			$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
	    			foreach ($blogids as $blog_id) {
	    				switch_to_blog($blog_id);
	    				$this->install();
	    			}
	    			switch_to_blog($current_blog);
	    			return;
	    		}
	    	}

			$this->install();

		}

		//deactivate plugin hook
		public function deactive_plugin($networkwide) {

			global $wpdb;

		    if (is_multisite()) {
		        if ($networkwide) {
		            $old_blog = $wpdb->blogid;
		            // Get all blog ids
		            $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
		            foreach ($blogids as $blog_id) {
		                switch_to_blog($blog_id);
		                $this->deinstall();
		            }
		            switch_to_blog($old_blog);
		            return;
		        }
		    }

		    $this->deinstall();

		}
		 //create a string with a js object for the labels option
		public static function get_labels_string() {

			$labels_object_str = '{';
			$labels_object_str .= 'outOfContainmentAlert: "'. get_option('fpd_out_of_containment_alert') .'", ';
			$labels_object_str .= 'uploadedDesignSizeAlert: "'. get_option('fpd_uploaded_design_size_alert') .'", ';
			$labels_object_str .= 'confirmProductDelete: "'. get_option('fpd_confirm_product_delete') .'", ';
			$labels_object_str .= 'modificationTooltips: {x: "'. get_option('fpd_modification_tooltip_x') .'", y: " '. get_option('fpd_modification_tooltip_y') .'", width: "'. get_option('fpd_modification_tooltip_width') .'", height: " '. get_option('fpd_modification_tooltip_height') .'", angle: "'. get_option('fpd_modification_tooltip_angle') .'"}, ';
			$labels_object_str .= 'colorpicker : {cancel: "'. get_option('fpd_colorpicker_cancel') .'", change: "'. get_option('fpd_colorpicker_choose') .'"}';
			$labels_object_str .= '}';

			return $labels_object_str;

		}

		//returns an array with all active fonts
		public static function get_active_fonts() {

			$all_fonts = array();

			$common_fonts = get_option( 'fpd_common_fonts' );
			if( !empty($common_fonts) ) {
				$all_fonts = explode(",", $common_fonts);
			}

			$google_webfonts = get_option( 'fpd_google_webfonts' );
			if( !empty($google_webfonts) ) {
				foreach($google_webfonts as $google_webfont) {
					$all_fonts[] = str_replace('+', ' ', $google_webfont);
				}
			}

			$directory_fonts = get_option( 'fpd_fonts_directory' );
			if( !empty($directory_fonts) ) {
				foreach($directory_fonts as $directory_font) {
					$all_fonts[] = preg_replace("/\\.[^.\\s]{3,4}$/", "", $directory_font);
				}
			}

			asort($all_fonts);

			return $all_fonts;

		}

		//check if a value is not empty, but 0 is allowed
		public function not_empty( $value ) {

			return $value == '0' || !empty($value);

		}

		//static function to check if a page belongs to a fancy product
		public static function is_fancy_product( $product_id ) {

			return get_post_meta( $product_id, '_fancy_product', true ) == 'yes';

		}

		//add fancy-product class in body
		public function add_fancy_product_class( $classes ) {

			global $post;
			if( self::is_fancy_product( $post->ID ) ) {

				$classes[] = 'fancy-product';

				//check if tablets are supported
				if( get_option( 'fpd_disable_on_tablets' ) == 'yes' )
					$classes[] = 'fpd-hidden-tablets';


				//check if smartphones are supported
				if( get_option( 'fpd_disable_on_smartphones' ) == 'yes' )
					$classes[] = 'fpd-hidden-mobile';

			}

			return $classes;

		}

		//ajax image upload handler
		public function upload_image() {

			$mb_size =  intval(get_option('fpd_max_image_size'));
			$maximum_filesize = $mb_size * 1024 * 1000;

			foreach($_FILES as $fieldName => $file) {

				$filename = $file['name'];

				//check if its an image
				if(!getimagesize($file['tmp_name'])) {
					echo json_encode(array('code' => 500, 'message' => __('File is not an image!', 'radykal'), 'filename' => $file['name']));
					die;
				}

				//check for php errors
				if($file['error'] !== UPLOAD_ERR_OK) {
					echo json_encode(array('code' => 500, 'message' => file_upload_error_message($file['error']), 'filename' => $filename));
					die;
				}

				//check for maximum upload size
				if($file['size'] > $maximum_filesize) {
					echo json_encode(array('code' => 500, 'message' => sprintf(__('Uploaded image is too big! Maximum image size is %d MB!', 'radykal'), $mb_size), 'filename' => $filename));
					die;
				}

				//check dimensions
				$image_dimensions = getimagesize($file['tmp_name']);
				$image_w = $image_dimensions[0];
				$image_h = $image_dimensions[1];

				if( $image_w < floatval(get_option('fpd_uploaded_designs_parameters_min_w')) ||  $image_w > floatval(get_option('fpd_uploaded_designs_parameters_max_w')) ||
				 	$image_h < floatval(get_option('fpd_uploaded_designs_parameters_min_h')) ||  $image_h > floatval(get_option('fpd_uploaded_designs_parameters_max_h'))
				 ){
					echo json_encode(array('code' => 500, 'message' => get_option('fpd_uploaded_design_size_alert'), 'filename' => $filename));
					die;
				}



				$upload_path = WP_CONTENT_DIR . '/uploads/fancy_products_uploads/';

				if(!file_exists($upload_path))
					mkdir($upload_path);

				$upload_path = $upload_path . '/'. date('Y') . '/';
				if(!file_exists($upload_path))
					mkdir($upload_path);

				$upload_path = $upload_path . '/'. date('m') . '/';
				if(!file_exists($upload_path))
					mkdir($upload_path);

				$upload_path = $upload_path . '/'. date('d') . '/';
				if(!file_exists($upload_path))
					mkdir($upload_path);

				$filename = sanitize_file_name($filename);

				if( @move_uploaded_file($file['tmp_name'], $upload_path.$filename) ) {
					$img_url = WP_CONTENT_URL . '/uploads/fancy_products_uploads/' . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $filename;
					echo json_encode(array('code' => 200, 'url' => $img_url, 'filename' => preg_replace("/\\.[^.\\s]{3,4}$/", "", $filename), 'dim' => $image_dimensions));
				}
				else {
					echo json_encode(array('error' => 2, 'message' => 'PHP Issue - move_uploaed_file failed', 'filename' => $filename));
				}

			}

			die;

		}

		//return the markup with all elements of a view
		private function get_element_anchors_from_view($elements) {

			//unserialize when necessary
			if( @unserialize($elements) !== false ) {
				$elements = unserialize($elements);
			}

			$view_html = '';
			if(is_array($elements)) {
				foreach($elements as $element) {
					$element = (array) $element;
					$view_html .= $this->get_element_anchor($element['type'], $element['title'], $element['source'], (array) $element['parameters']);
				}
			}

			return $view_html;

		}

		//return a single element markup
		private function get_element_anchor($type, $title, $source, $parameters) {
			$parameters_string = $this->convert_parameters_to_string($parameters);

			if($type == 'image') {
				return "<img src='$source' title='$title' data-parameters='$parameters_string' />";
			}
			else {
				$source = stripslashes($source);
				return "<span title='$title' data-parameters='$parameters_string'>$source</span>";
			}

		}

		//returns a string with element parameters
		private function convert_parameters_to_string( $parameters ) {

			if(empty($parameters))
				return '{}';

			$params_object = '{';
			foreach($parameters as $key => $value) {
				if($this->not_empty($value)) {
					if(is_bool($value))
						$value = (int) $value;
					switch($key) {
						case 'x':
							$params_object .= '"x":'. $value .',';
						break;
						case 'y':
							$params_object .= '"y":'. $value .',';
						break;
						case 'z':
							$params_object .= '"z":'. $value .',';
						break;
						case 'colors':
							$params_object .= '"colors":"'. (is_array($value) ? implode (", ", $value) : $value) .'",';
						break;
						case 'removable':
							$params_object .= '"removable":'. $value .',';
						break;
						case 'draggable':
							$params_object .= '"draggable":'. $value .',';
						break;
						case 'rotatable':
							$params_object .= '"rotatable":'. $value .',';
						break;
						case 'resizable':
							$params_object .= '"resizable":'. $value .',';
						break;
						case 'removable':
							$params_object .= '"removable":'. $value .',';
						break;
						case 'zChangeable':
							$params_object .= '"zChangeable":'. $value .',';
						break;
						case 'scale':
							$params_object .= '"scale":'. $value .',';
						break;
						case 'angle':
							$params_object .= '"degree":'. $value .',';
						break;
						case 'price':
							$params_object .= '"price":'. $value .',';
						break;
						case 'auto_center':
							$params_object .= '"autoCenter":'. $value .',';
						break;
						case 'font':
							$params_object .= '"font":"'. str_replace('_', ' ', $value) .'",';
						break;
						case 'patternable':
							$params_object .= '"patternable":'. $value .',';
						break;
						case 'textSize':
							$params_object .= '"textSize":'. $value .',';
						break;
						case 'textEditable':
							$params_object .= '"textEditable":'. $value .',';
						break;
					}
				}
			}

			//bounding box
			if( empty($parameters['bounding_box_control']) ) {
				//use custom bounding box
				if( $this->not_empty($parameters['bounding_box_x']) && $this->not_empty($parameters['bounding_box_y']) && $this->not_empty($parameters['bounding_box_width']) && $this->not_empty($parameters['bounding_box_height']) ) {
					$params_object .= '"boundingBox": { "x":'. $parameters['bounding_box_x'] .', "y":'. $parameters['bounding_box_y'] .', "width":'. $parameters['bounding_box_width'] .', "height":'. $parameters['bounding_box_height'] .'}';
				}
			}
			else {
				if( $this->not_empty($parameters['bounding_box_by_other']) )
					$params_object .= '"boundingBox": "'. $parameters['bounding_box_by_other'] .'"';
			}

			$params_object = trim($params_object, ',');
			$params_object .= '}';

			return $params_object;

		}

		private function get_designs_parameters_as_array() {

			$design_parameters['x'] = get_option('fpd_designs_parameter_x');
			$design_parameters['y'] = get_option('fpd_designs_parameter_y');
			$design_parameters['z'] = get_option('fpd_designs_parameter_z');
			$design_parameters['colors'] = get_option('fpd_designs_parameter_colors');
			$design_parameters['price'] = get_option('fpd_designs_parameter_price');
			$design_parameters['auto_center'] = get_option('fpd_designs_parameter_auto_center') == 'yes';
			$design_parameters['draggable'] = get_option('fpd_designs_parameter_draggable') == 'yes';
			$design_parameters['rotatable'] = get_option('fpd_designs_parameter_rotatable') == 'yes';
			$design_parameters['resizable'] = get_option('fpd_designs_parameter_resizable') == 'yes';
			$design_parameters['zChangeable'] = get_option('fpd_designs_parameter_zchangeable') == 'yes';
			$design_parameters['removable'] = 1;
			$design_parameters['bounding_box_control'] = get_option('fpd_designs_parameter_bounding_box_control') == 'yes';
			$design_parameters['bounding_box_by_other'] = get_option('fpd_designs_parameter_bounding_box_target');
			$design_parameters['bounding_box_x'] = get_option('fpd_designs_parameter_bounding_box_x');
			$design_parameters['bounding_box_y'] = get_option('fpd_designs_parameter_bounding_box_y');
			$design_parameters['bounding_box_width'] = get_option('fpd_designs_parameter_bounding_box_width');
			$design_parameters['bounding_box_height'] = get_option('fpd_designs_parameter_bounding_box_height');

			return $design_parameters;

		}

		//returns a string with all design parameters
		private function get_designs_parameters_str() {

			$design_parameters_string = $this->convert_parameters_to_string($this->get_designs_parameters_as_array());

			return $design_parameters_string;

		}

		private function get_pattern_urls() {

			$urls = array();

			$path = FPD_PLUGIN_DIR . '/patterns/';
		  	$folder = opendir($path);

			$pic_types = array("jpg", "jpeg", "png");

			while ($file = readdir ($folder)) {

			  if(in_array(substr(strtolower($file), strrpos($file,".") + 1),$pic_types)) {
				  $urls[] = plugins_url('/patterns/'.$file, __FILE__);
			  }
			}

			closedir($folder);

			return $urls;

		}

		//all things that need to be installed on activation
		private function install() {

			require_once('admin/settings-init.php');

			//install options
			foreach(Fancy_Product_Designer_Settings::$options as $key => $value) {
				if(is_array(Fancy_Product_Designer_Settings::$options[$key])) {
					$option_section = Fancy_Product_Designer_Settings::$options[$key];
					foreach($option_section as $value) {
						if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
				        	add_option( $value['id'], $value['default'] );
				        }
					}
				}
			}

	        foreach( Fancy_Product_Designer_Settings::$styling_colors as $key => $value ) {
		        add_option( $key, $value );
	        }

		}

		private function deinstall() {

			global $wpdb;
			require_once('admin/settings-init.php');

			$wpdb->query("SET FOREIGN_KEY_CHECKS=0;");
			$wpdb->query("DROP TABLE ".self::$views_table_name."");
			$wpdb->query("SET FOREIGN_KEY_CHECKS=1;");

			foreach(Fancy_Product_Designer_Settings::$options as $key => $value) {
				if(is_array(Fancy_Product_Designer_Settings::$options[$key])) {
					$option_section = Fancy_Product_Designer_Settings::$options[$key];
					foreach($option_section as $value) {
						if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
				        	delete_option( $value['id'] );
				        }
					}
				}
			}

	        foreach( Fancy_Product_Designer_Settings::$styling_colors as $key => $value ) {
		        delete_option( $key );
	        }

		}

		private function upgrade() {

			global $wpdb;

			//upgrade to V1.0.1
			if( get_option($this->version_field_name) == '1.0.0' || !get_option($this->version_field_name) ) {
				add_option( 'fpd_stage_menu_bar_reset', 'Reset Product');
		   		update_option($this->version_field_name, '1.0.1');
			}

			//upgrade to V1.0.11
			if( get_option($this->version_field_name) == '1.0.1' ) {
		   		update_option($this->version_field_name, '1.0.11');
			}

			//upgrade to V1.0.2
			if( get_option($this->version_field_name) == '1.0.11' ) {
				update_option( 'fpd_fb_headline', 'Add Facebook Photos' );
				update_option( 'fpd_fb_select_friend', 'Select a friend' );
				update_option( 'fpd_select_album', 'Select an album' );
				update_option( 'fpd_navigation_tab_facebook', 'Add Photos From Facebook' );
				update_option( 'fpd_layout', 'fpd-vertical' );
				update_option( 'fpd_sidebar_height', 600 );
				update_option( 'fpd_stage_height', 600 );
				update_option( 'fpd_designs_parameter_z', -1 );
				update_option( 'fpd_custom_texts_parameter_patternable', 'no' );
		   		update_option( $this->version_field_name, '1.0.2' );
			}

			//upgrade to V1.0.21
			if( get_option($this->version_field_name) == '1.0.2' ) {
				update_option( 'fpd_custom_texts_parameter_z', -1 );
		   		update_option($this->version_field_name, '1.0.21');
			}

			//upgrade to V1.0.22
			if( get_option($this->version_field_name) == '1.0.21' ) {
		   		update_option($this->version_field_name, '1.0.22');
			}

			//upgrade to V1.0.23
			if( get_option($this->version_field_name) == '1.0.22' ) {
		   		update_option($this->version_field_name, '1.0.23');
			}

			//upgrade to V1.0.24
			if( get_option($this->version_field_name) == '1.0.23' ) {
		   		update_option($this->version_field_name, '1.0.24');
			}

			//upgrade to V1.0.25
			if( get_option($this->version_field_name) == '1.0.24' ) {
		   		update_option($this->version_field_name, '1.0.25');
			}

			//upgrade to V1.0.26
			if( get_option($this->version_field_name) == '1.0.25' ) {
				if( get_option('fpd_layout') == 'fpd-vertical' ) {
					update_option( 'fpd_layout', 'icon-sb-left' );
				}
				else {
					update_option( 'fpd_layout', 'icon-sb-top' );
				}

				update_option( 'fpd_placement', 'fpd-replace-image' );
		   		update_option($this->version_field_name, '1.0.26');
			}

		}

		private function file_upload_error_message($error_code) {

		    switch ($error_code) {
		        case UPLOAD_ERR_INI_SIZE:
		            return __('The uploaded file exceeds the upload_max_filesize directive in php.ini', 'radykal');
		        case UPLOAD_ERR_FORM_SIZE:
		            return __('The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form', 'radykal');
		        case UPLOAD_ERR_PARTIAL:
		            return __('The uploaded file was only partially uploaded', 'radykal');
		        case UPLOAD_ERR_NO_FILE:
		            return __('No file was uploaded', 'radykal');
		        case UPLOAD_ERR_NO_TMP_DIR:
		            return __('Missing a temporary folder', 'radykal');
		        case UPLOAD_ERR_CANT_WRITE:
		            return __('Failed to write file to disk', 'radykal');
		        case UPLOAD_ERR_EXTENSION:
		            return __('File upload stopped by extension', 'radykal');
		        default:
		            return __('Unknown upload error', 'radykal');
		    }

		}

	}
}

//check if woocommerce is activated
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

	//init Fancy Product Designer
	if(class_exists('Fancy_Product_Designer'))
		new Fancy_Product_Designer();

}
else {

	add_action( 'admin_notices',  'fpd_admin_notices' );

}

//let user know that he needs the woocommerce plugin
function fpd_admin_notices() {

    ?>
    <div class="updated">
        <p><?php _e( 'Please activate the woocommerce plugin, otherwise you can not use Fancy Product Designer for woocommerce!', 'radykal' ); ?></p>
    </div>
    <?php

}
?>
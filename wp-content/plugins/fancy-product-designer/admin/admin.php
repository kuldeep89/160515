<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if(!class_exists('Fancy_Product_designer_Admin')) {

	class Fancy_Product_designer_Admin {

		private $ajax_nonce;

		public function __construct() {


			//********** WORDPRESS ADMIN HOOKS *******************

			add_action( 'admin_init', array( &$this, 'init_admin' ) );
			add_action( 'admin_menu', array( &$this, 'add_menu_pages' ) );
			add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_styles_scripts' ) );

		}

		public function init_admin() {

			global $woocommerce;

			require_once('settings-init.php');


			//********** WOOCOMMERCE ADMIN HOOKS *******************

			//product post type
			add_filter( 'product_type_options', array( &$this, 'add_product_type_option' ) );
			add_action( 'woocommerce_product_write_panel_tabs', array( &$this, 'add_product_data_tab' ) );
			add_action( 'woocommerce_product_write_panels', array( &$this, 'add_product_data_panel' ) );
			add_action( 'woocommerce_process_product_meta', array( &$this, 'save_custom_fields' ), 10, 2 );
			add_action( 'woocommerce_duplicate_product', array( &$this, 'duplicate_fancy_product' ), 10, 2 );

			//settings
			add_filter( 'woocommerce_settings_tabs_array', array( &$this, 'add_settings_tab' ), 25 );
			add_action( 'woocommerce_settings_tabs_fancy_product_designer', array( &$this, 'add_settings_panel' ) );
			add_action( 'woocommerce_update_options_fancy_product_designer', array( &$this, 'update_options' ) );
			if(floatval($woocommerce->version) < 2.1) {
				add_action( 'woocommerce_update_options_fancy_product_designer_default_parameters', array( &$this, 'update_default_parameters_options' ) );
				add_action( 'woocommerce_update_options_fancy_product_designer_labels', array( &$this, 'update_label_options' ) );
				add_action( 'woocommerce_update_options_fancy_product_designer_fonts', array( &$this, 'update_fonts_options' ) );
			}
			add_action( 'woocommerce_admin_field_fpd_styling', array( &$this, 'styling_color_picker' ) );

			//order post type
			add_action( 'add_meta_boxes', array( &$this, 'add_meta_boxes' ) );
			add_action( 'woocommerce_admin_order_item_headers', array( &$this, 'add_order_item_header' ) );
			add_action( 'woocommerce_admin_order_item_values', array( &$this, 'admin_order_item_values' ), 10, 3 );

			$this->ajax_nonce = wp_create_nonce( 'fpd_ajax_nonce' );

			//add capability to administrator
			$role = get_role( 'administrator' );
			$role->add_cap( Fancy_Product_designer::CAPABILITY );

			//ajax handlers
			add_action( 'wp_ajax_fpd_newview', array( &$this, 'new_view' ) );
			add_action( 'wp_ajax_fpd_editview', array( &$this, 'edit_view' ) );
			add_action( 'wp_ajax_fpd_removeview', array( &$this, 'remove_view' ) );
			add_action( 'wp_ajax_fpd_imagefromdataurl', array( &$this, 'create_image_from_dataurl' ) );
			add_action( 'wp_ajax_fpd_pdffromdataurl', array( &$this, 'create_pdf_from_dataurl' ) );
			add_action( 'wp_ajax_fpd_loadorderitemimages', array( &$this, 'load_order_item_images' ) );

			//when product gets deleted, delete also the asscociated views in the database
			if ( current_user_can( 'delete_posts' ) )
	        	add_action( 'delete_post', array( &$this, 'delete_views_on_post_delete' ), 10 );

		}

		public function add_menu_pages() {

			//add fancy products sub menu page to products menu
			add_submenu_page(
				'edit.php?post_type=product',
				 __('Fancy Products', 'radykal'),
				 __('Fancy Products', 'radykal'),
				 Fancy_Product_Designer::CAPABILITY,
				 'edit_fancy_products',
				 array($this, 'edit_fancy_products')
			);

			//add fancy designs sub menu page to products menu
			add_submenu_page(
				'edit.php?post_type=product',
				__('Manage Fancy Designs', 'radykal'),
				__('Fancy Designs', 'radykal'),
				Fancy_Product_Designer::CAPABILITY,
				'manage_designs',
				array($this, 'manage_designs_page')
			);

		}

		//include edit-fancy-products.php
		public function edit_fancy_products() {

			global $wpdb;
			require_once("edit-fancy-products.php");

		}

		//include manage-designs.php
		public function manage_designs_page() {

			require_once("manage-designs.php");

		}

		//includes the necessarry css and js files
		public function enqueue_styles_scripts( $hook ) {

			global $post, $woocommerce;

			$wc_settings_page = 'wc-settings';
			if(floatval($woocommerce->version) < 2.1) {
				$wc_settings_page = 'woocommerce_settings';
			}

			//woocommerce settings
			if( $hook == 'woocommerce_page_'.$wc_settings_page.'' ) {

				wp_enqueue_script( 'fpd-admin', plugins_url('/js/admin.js', __FILE__), false, Fancy_Product_Designer::VERSION );

			}

			//woocommerce post types
		    if ( $hook == 'post-new.php' || $hook == 'post.php' ) {

		    	//product
		        if ( 'product' === $post->post_type ) {

		        	wp_enqueue_style( 'fpd-admin', plugins_url('/css/admin.css', __FILE__) );

		        }
		        //order
		        else if( 'shop_order' === $post->post_type ) {

		        	$google_webfonts = get_option( 'fpd_google_webfonts' );
					if( !empty($google_webfonts) ) {
						echo '<link href="http://fonts.googleapis.com/css?family='.implode ("|", $google_webfonts).'" rel="stylesheet" type="text/css">';
					}

					wp_enqueue_style( 'jquery-fpd' );
					wp_enqueue_style( 'fpd-admin', plugins_url('/css/admin.css', __FILE__) );
					wp_enqueue_script( 'fpd-jspdf' );
					wp_enqueue_script( 'jquery-fpd' );

		        }
		    }

			//edit fancy products
		    if( $hook == 'product_page_edit_fancy_products') {

		    	wp_enqueue_media();

		    	wp_enqueue_style( 'jquery-fpd' );
		    	wp_enqueue_style( 'woocommerce_admin_styles', $woocommerce->plugin_url() . '/assets/css/admin.css' );
		    	wp_enqueue_style( 'fpd-admin', plugins_url('/css/admin.css', __FILE__) );

		    	$google_webfonts = get_option( 'fpd_google_webfonts' );
				if( !empty($google_webfonts) ) {
					echo '<link href="http://fonts.googleapis.com/css?family='.implode ("|", $google_webfonts).'" rel="stylesheet" type="text/css">';
				}

				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-mouse' );
				wp_enqueue_script( 'jquery-ui-sortable' );
				wp_enqueue_script( 'jquery-ui-spinner' );
				wp_enqueue_script( 'jquery-ui-widget' );
				wp_enqueue_script( 'jquery-fpd' );
				wp_enqueue_script( 'jquery-tiptip', $woocommerce->plugin_url() . '/assets/js/jquery-tiptip/jquery.tipTip.min.js'  );

		    }

			//manage designs
		    if( $hook == 'product_page_manage_designs') {

		    	wp_enqueue_media();
		    	wp_enqueue_style( 'font-awesome-4' );
		    	wp_enqueue_style( 'fpd-admin', plugins_url('/css/admin.css', __FILE__) );
		    	wp_enqueue_script( 'fpd-admin', plugins_url('/js/admin.js', __FILE__), false, Fancy_Product_Designer::VERSION );

		    }

		}


		/************************************ PRODUCT POST ************************************************/

		//add checkbox to enable fancy product for a product
		public function add_product_type_option( $types ) {

			$types['fancy_product'] = array(
				'id' => '_fancy_product',
				'wrapper_class' => 'show_if_fancy_product',
				'label' => __( 'Fancy Product', 'radykal' ),
				'description' => __( 'A product for the Fancy Product Designer?', 'radykal' )
			);

			return $types;

		}

		//the checkbox to start the magic
		public function add_product_data_tab() {

			?>
			<li class="fancy_product_tab hide_if_fancy_product fancy_product_options"><a href="#fancy_product_data"><?php _e( 'Fancy Product', 'radykal' ); ?></a></li>
			<?php

		}

		//custom panel in the product post to add/edit/remove views
		public function add_product_data_panel() {

			global $wpdb, $post;

			?>

			<div id="fancy_product_data" class="panel woocommerce_options_panel">
				<div class="options_group">
					<ul id="fpd-view-list">
						<?php
						$tableCheck = $wpdb->query("SHOW TABLES LIKE '".Fancy_Product_Designer::$views_table_name."'");
						if($tableCheck == 1) {

							//get views of a fancy product
							$views = $wpdb->get_results("SELECT * FROM ".Fancy_Product_Designer::$views_table_name." WHERE product_id='{$post->ID}' ORDER BY ID ASC");
							if(is_array($views)) {
								foreach($views as $view) {
									echo $this->get_view_list_item($view->ID, $view->title, $view->thumbnail);
								}
							}
						}
						?>
					</ul>
					<button type="button" class="button button-primary" id="fpd-add-view"><?php _e( 'Add View', 'radykal' ); ?></button>
				</div>
			</div>

			<script type="text/javascript">

				jQuery(document).ready(function($) {

					var mediaUploader = $currentMediaInput = null;

					$viewsList = $( "#fpd-view-list" );

					//fancy product checkbox handler
					$('#_fancy_product').change(function() {
						if($(this).is(':checked')) {
							$('.hide_if_fancy_product').show();
						}
						else {
							$('.hide_if_fancy_product').hide();
						}
					}).change();

					//add or edit view
					$('#fpd-add-view').click(_addOrEditView);
					$('a.fpd-edit-view').on('click', _addOrEditView);
					$viewsList.on('click', 'a.fpd-edit-view', _addOrEditView);

					//remove view
					$('a.fpd-remove-view').on('click', _removeView);
					$viewsList.on('click', 'a.fpd-remove-view', _removeView);

					function _addOrEditView() {

						var $this = $(this),
							$listItem = $this.parent().parent(),
							editView = $this.hasClass('fpd-edit-view');

						var viewTitle = prompt("<?php _e( 'Enter a title for the view:', 'radykal' ); ?>", editView ? $this.prevAll('span:first').text() : "");
						if(viewTitle == null) {
							return false;
						}
						else if(viewTitle.length == 0) {
							alert("<?php _e( 'Please enter a title!', 'radykal' ); ?>");
							return false;
						}

						var viewThumbnail = editView ? $this.prevAll('img:first').attr('src') : "";

						if (mediaUploader) {
							mediaUploader.viewTitle = viewTitle;
							mediaUploader.editView = editView;
				            mediaUploader.open();
				            return;
				        }

				        mediaUploader = wp.media({
				            title: '<?php _e( 'Choose a view thumbnail', 'radykal' ); ?>',
				            button: {
				                text: '<?php _e( 'Choose a view thumbnail', 'radykal' ); ?>'
				            },
				            multiple: false
				        });

				        mediaUploader.viewTitle = viewTitle;
				        mediaUploader.editView = editView;


				        mediaUploader.on('select', function() {

							viewThumbnail = mediaUploader.state().get('selection').toJSON()[0].url;;

				        	if(viewThumbnail.length > 4) {
					        	if(mediaUploader.editView) {

				        			var viewId = $listItem.data('id');
									$.ajax({
										url: "<?php echo admin_url('admin-ajax.php'); ?>",
										data: { action: 'fpd_editview', _ajax_nonce: "<?php echo $this->ajax_nonce; ?>", title: mediaUploader.viewTitle, thumbnail: viewThumbnail, id: viewId },
										type: 'post',
										dataType: 'json',
										success: function(data) {
											if(data == 0) {
												alert("<?php _e( 'Could not change view. Please try again', 'radykal' ); ?>");
											}
											else {
												$listItem.children('h3').children('img').attr('src', data.thumbnail).next('span').text(data.title);
											}

										}
									});
				        		}
				        		else {
					        		$.ajax({
										url: "<?php echo admin_url('admin-ajax.php'); ?>",
										data: { action: 'fpd_newview', _ajax_nonce: "<?php echo $this->ajax_nonce; ?>", title: mediaUploader.viewTitle, thumbnail: viewThumbnail, product_id: <?php echo $post->ID; ?> },
										type: 'post',
										dataType: 'json',
										success: function(data) {
											if(data == 0) {
												alert("<?php _e( 'Could not create view. Please try again', 'radykal' ); ?>");
											}
											else {
												$viewsList.append(data.html);
											}

										}
									});
				        		}

				        	}
				        });

						mediaUploader.open();

						return false;
					};

					function _removeView() {
						var $listItem = $(this).parent().parent(),
							viewId = $listItem.data('id');

						var c = confirm("<?php _e( 'Remove this view?', 'radykal' ); ?>");

						if(c) {
							$.ajax({
								url: "<?php echo admin_url('admin-ajax.php'); ?>",
								data: { action: 'fpd_removeview', _ajax_nonce: "<?php echo $this->ajax_nonce; ?>", id: viewId },
								type: 'post',
								dataType: 'json',
								success: function(data) {
									if(data == 0) {
										alert("<?php _e( 'Could not delete view. Please try again', 'radykal' ); ?>");
									}
									else {
										$listItem.remove();
									}

								}
							});
						}

						return false;
					};

				});

			</script>

			<?php

		}

		//be sure to save the checkbox value (product post)
		public function save_custom_fields( $post_id, $post ) {

			update_post_meta( $post_id, '_fancy_product', isset( $_POST['_fancy_product'] ) ? 'yes' : 'no' );

		}

		//duplicate fancy products, all views will be available in the duplicated product
		public function duplicate_fancy_product( $new_id, $post ) {

			if( Fancy_Product_Designer::is_fancy_product($post->ID) ) {

				global $wpdb;

				$views = $wpdb->get_results("SELECT title, thumbnail, elements FROM ".Fancy_Product_Designer::$views_table_name." WHERE product_id=".$post->ID."");

				foreach( $views as $view ) {

					$wpdb->insert(
						Fancy_Product_Designer::$views_table_name,
						array( 'product_id' => $new_id, 'title' => $view->title, 'thumbnail' => $view->thumbnail, 'elements' => $view->elements ),
						array( '%d', '%s', '%s', '%s')
					);

				}

			}

		}

		//delete also the views from db when associated post is deleted
		public function delete_views_on_post_delete( $pid ) {

			global $wpdb;
			$wpdb->query( $wpdb->prepare("DELETE FROM ".Fancy_Product_Designer::$views_table_name." WHERE product_id=%d", $pid) );

		}



		/************************************ SETTINGS ************************************************/

		//add custom tab (product post)
		public function add_settings_tab( $tabs ) {

			$tabs['fancy_product_designer'] = __( 'Fancy Product Designer', 'radykal' );
			return $tabs;

		}

		//the custom settings page (settings)
		public function add_settings_panel(  ) {

			global $woocommerce, $current_tab, $current_section;

			$wc_settings_page = 'wc-settings';
			if(floatval($woocommerce->version) < 2.1) {
				include_once( $woocommerce->plugin_path().'/admin/settings/settings-frontend-styles.php' );
				$wc_settings_page = 'woocommerce_settings';
			}

			//section links
			$links = array(
			'<a href="' . admin_url( 'admin.php?page='.$wc_settings_page.'&tab=fancy_product_designer' ) . '" class="' . ( $current_section == '' ? 'current' : '' ) . '">' . __( 'General', 'woocommerce' ) . '</a>',
			'<a href="' . admin_url( 'admin.php?page='.$wc_settings_page.'&tab=fancy_product_designer&section=default_parameters' ) . '" class="' . ( $current_section == 'default_parameters' ? 'current' : '' ) . '">' . __( 'Default Parameters', 'radykal' ) . '</a>',
			'<a href="' . admin_url( 'admin.php?page='.$wc_settings_page.'&tab=fancy_product_designer&section=labels' ) . '" class="' . ( $current_section == 'labels' ? 'current' : '' ) . '">' . __( 'Labels', 'radykal' ) . '</a>',
			'<a href="' . admin_url( 'admin.php?page='.$wc_settings_page.'&tab=fancy_product_designer&section=fonts' ) . '" class="' . ( $current_section == 'fonts' ? 'current' : '' ) . '">' . __( 'Fonts', 'radykal' ) . '</a>',
			);

			echo '<ul class="subsubsub"><li>' . implode( ' | </li><li>', $links ) . '</li></ul><br class="fpd-clear" />';

			$current_options = 'general';
			if( $current_section != '' )
				$current_options = $current_section;

			woocommerce_admin_fields( Fancy_Product_Designer_Settings::$options[$current_options] );

		}

		public function update_options() {

			global $woocommerce, $current_section;

	        $current_options = 'general';
			if( $current_section != '' )
				$current_options = $current_section;

			if($current_options == 'general') {
				foreach ( Fancy_Product_Designer_Settings::$styling_colors as $key => $value ) {
			        if( isset($_POST[$key]) ) {
						update_option( $key, woocommerce_format_hex($_POST[$key]) );
					}
		        }
			}
			else if($current_options == 'fonts') {
				$fonts_css = FPD_PLUGIN_DIR.'/css/jquery.fancyProductDesigner-fonts.css';
				$handle = @fopen($fonts_css, 'w') or print('Cannot open file:  '.$fonts_css);
				$files = scandir(FPD_PLUGIN_DIR.'/fonts');
				$data = '';
				if(is_array($files)) {
					foreach($files as $file) {
						if(preg_match("/.woff/", $file)) {
							$data .= '@font-face {'."\n";
							$data .= '  font-family: "'.preg_replace("/\\.[^.\\s]{3,4}$/", "", $file).'";'."\n";
							$data .= '  src: local("#"), url(../fonts/'.$file.') format("woff");'."\n";
							$data .= '  font-weight: normal;'."\n";
							$data .= '  font-style: normal;'."\n";
							$data .= '}'."\n\n\n";
						}
					}
				}

				fwrite($handle, $data);
				fclose($handle);
			}

			woocommerce_update_options( Fancy_Product_Designer_Settings::$options[$current_options] );

		}

		//for 2.0
		public function update_default_parameters_options(  ) {

			global $woocommerce;

			include_once( $woocommerce->plugin_path().'/admin/settings/settings-save.php' );

			woocommerce_update_options( Fancy_Product_Designer_Settings::$options['default_parameters'] );

		}

		//for 2.0
		public function update_label_options(  ) {

			global $woocommerce;

			include_once( $woocommerce->plugin_path().'/admin/settings/settings-save.php' );

			woocommerce_update_options( Fancy_Product_Designer_Settings::$options['labels'] );

		}

		//for 2.0
		public function update_fonts_options() {

			global $woocommerce;

			include_once( $woocommerce->plugin_path().'/admin/settings/settings-save.php' );

			$fonts_css = FPD_PLUGIN_DIR.'/css/jquery.fancyProductDesigner-fonts.css';
			$handle = @fopen($fonts_css, 'w') or print('Cannot open file:  '.$fonts_css);
			$files = scandir(FPD_PLUGIN_DIR.'/fonts');
			$data = '';
			if(is_array($files)) {
				foreach($files as $file) {
					if(preg_match("/.woff/", $file)) {
						$data .= '@font-face {'."\n";
						$data .= '  font-family: "'.preg_replace("/\\.[^.\\s]{3,4}$/", "", $file).'";'."\n";
						$data .= '  src: local("#"), url(../fonts/'.$file.') format("woff");'."\n";
						$data .= '  font-weight: normal;'."\n";
						$data .= '  font-style: normal;'."\n";
						$data .= '}'."\n\n\n";
					}
				}
			}

			fwrite($handle, $data);
			fclose($handle);

			woocommerce_update_options( Fancy_Product_Designer_Settings::$options['fonts'] );

		}

		//add colorpicker to settings for styling
		public function styling_color_picker() {

			global $woocommerce;

			?><tr valign="top" class="fpd_frontend_css_colors">
			<th scope="row" class="titledesc">
				<label><?php _e( 'Styles', 'woocommerce' ); ?></label>
			</th>
		    <td class="forminp"><?php

            if(floatval($woocommerce->version) < 2.1) {
	            woocommerce_frontend_css_color_picker( __( 'Primary', 'radykal' ), 'fpd_frontend_primary', get_option('fpd_frontend_primary'), __( 'Sidebar Navigation, Product Stage Header', 'radykal' ) );
				woocommerce_frontend_css_color_picker( __( 'Secondary', 'radykal' ), 'fpd_frontend_secondary', get_option('fpd_frontend_secondary'), __( 'Sidebar Content Background, Tooltip Background', 'radykal' ) );
				woocommerce_frontend_css_color_picker( __( 'Border', 'radykal' ), 'fpd_frontend_border', get_option('fpd_frontend_border'), __( 'Border Color', 'radykal' ) );
				woocommerce_frontend_css_color_picker( __( 'Highlight', 'radykal' ), 'fpd_frontend_border_highlight', get_option('fpd_frontend_border_highlight'), __( 'Highlight for Textareas, Inputs, Dropdowns, View Selection', 'radykal' ) );
				woocommerce_frontend_css_color_picker( __( 'Elements', 'radykal' ), 'fpd_frontend_primary_elements', get_option('fpd_frontend_primary_elements'), __( 'Primary Button Background, Text Color', 'radykal' ) );
				woocommerce_frontend_css_color_picker( __( 'Submit Btn', 'radykal' ), 'fpd_frontend_submit_button', get_option('fpd_frontend_submit_button'), __( 'Submit Button Background', 'radykal' ) );
				woocommerce_frontend_css_color_picker( __( 'Danger Btn', 'radykal' ), 'fpd_frontend_danger_button', get_option('fpd_frontend_danger_button'), __( 'Trash Button Background', 'radykal' ) );
				woocommerce_frontend_css_color_picker( __( 'Text Btn', 'radykal' ), 'fpd_frontend_text_button', get_option('fpd_frontend_text_button'), __( 'Text Button Color', 'radykal' ) );
            }
            else {
	            WC_Settings_General::color_picker( __( 'Primary', 'radykal' ), 'fpd_frontend_primary', get_option('fpd_frontend_primary'), __( 'Sidebar Navigation, Product Stage Header', 'radykal' ) );
	            WC_Settings_General::color_picker( __( 'Secondary', 'radykal' ), 'fpd_frontend_secondary', get_option('fpd_frontend_secondary'), __( 'Sidebar Content Background, Tooltip Background', 'radykal' ) );
				WC_Settings_General::color_picker( __( 'Border', 'radykal' ), 'fpd_frontend_border', get_option('fpd_frontend_border'), __( 'Border Color', 'radykal' ) );
				WC_Settings_General::color_picker( __( 'Highlight', 'radykal' ), 'fpd_frontend_border_highlight', get_option('fpd_frontend_border_highlight'), __( 'Highlight for Textareas, Inputs, Dropdowns, View Selection', 'radykal' ) );
				WC_Settings_General::color_picker( __( 'Elements', 'radykal' ), 'fpd_frontend_primary_elements', get_option('fpd_frontend_primary_elements'), __( 'Primary Button Background, Text Color', 'radykal' ) );
				WC_Settings_General::color_picker( __( 'Submit Btn', 'radykal' ), 'fpd_frontend_submit_button', get_option('fpd_frontend_submit_button'), __( 'Submit Button Background', 'radykal' ) );
				WC_Settings_General::color_picker( __( 'Danger Btn', 'radykal' ), 'fpd_frontend_danger_button', get_option('fpd_frontend_danger_button'), __( 'Trash Button Background', 'radykal' ) );
				WC_Settings_General::color_picker( __( 'Text Btn', 'radykal' ), 'fpd_frontend_text_button', get_option('fpd_frontend_text_button'), __( 'Text Button Color', 'radykal' ) );
            }

			?></td>
			</tr>
			<?php

		}



		/************************************ ORDER ************************************************/

		public function add_meta_boxes() {

			add_meta_box( 'fpd-order', __( 'Fancy Product - Order Viewer', 'radykal' ), array( &$this, 'fancy_product_order'), 'shop_order', 'normal', 'default' );

		}

		public function add_order_item_header() {

			?>
			<th class="fancy-product"><?php _e( 'Fancy Product', 'radykal' ); ?></th>
			<?php

		}

		//add a button to the ordered fancy product
		public function admin_order_item_values( $_product, $item, $item_id ) {

			$product = unserialize($item['fpd_data']);
			?>
			<td class="fancy-product" width="100px">
				<?php if( isset($item['fpd_data']) ) : ?>
					<button class='button button-secondary fpd-show-order-item' data-order='<?php echo str_replace("'", "’", $product['fpd_product']); ?>' id='<?php echo $item_id; ?>'><?php _e( 'Open', 'radykal' ); ?></button>
				<?php endif; ?>
			</td>
			<?php

		}

		//add fancy product panel to order post
		public function fancy_product_order( $post ) {

			global $post, $woocommerce, $thepostid;

			?>

			<div id="fpd-order-panel" class="fpd-clearfix">
				<div id="fpd-order-designer-wrapper" class="fpd-clearfix">
					<p class="description"><strong><?php _e( 'To load an ordered product, you need to click the "Open" button next to the ordered item in the "Order Items" panel!', 'radykal' ); ?></strong></p>
					<div id="fpd-order-designer" class="<?php echo get_option('fpd_layout'); ?>"></div>
					<div id="fpd-export-tools" class="fpd-clear fpd-clearfix" style="float: none;">
						<h2><?php _e( 'Export', 'radykal' ); ?></h2>
						<p>
							<span style="font-weight: bold;"><?php _e('Image format:', 'radykal' ); ?></span>
							<label><input type="radio" name="fpd_image_format" value="png" checked="checked" /> PNG</label>
							<label style="margin-left: 20px;"><input type="radio" name="fpd_image_format" value="jpeg" /> JPEG</label>
							<br /><br />
							<span  style="font-weight: bold;"><?php _e('Scale Factor:', 'radykal' ); ?></span><input type="text" value="" name="fpd_scale" size="4" placeholder="1" />
						</p>
						<div>
							<h4><?php _e('PDF', 'radykal' ); ?></h4>
							<div>
								<div>
									<h5><?php _e('Orientation', 'radykal' ); ?></h5>
									<label style="margin-right: 20px;"><input type="radio" name="fpd_pdf_orientation" value="P" checked="checked" /> <?php _e('Portrait', 'radykal' ); ?></label>
									<label><input type="radio" name="fpd_pdf_orientation" value="L" /> <?php _e('Landscape', 'radykal' ); ?></label>
								</div>
								<br />
								<div>
									<h5><?php _e('Size', 'radykal' ); ?></h5>
									<label>
										<span><?php _e('Width in mm', 'radykal' ); ?>:</span></label>
										<input type="number" value="210" id="fpd-pdf-width" size="6" />
									<br />
									<label>
										<span><?php _e('Height in mm', 'radykal' ); ?>:</span>
										<input type="number" value="297" id="fpd-pdf-height" size="6" />
									</label>
									<p><a href="http://www.hdri.at/dpirechner/dpirechner_en.htm" target="_blank" style="font-size: 11px;"><?php _e('DPI - Pixel Converter', 'radykal' ); ?></a></p>
								</div>
							</div>
							<br />
							<button id="fpd-create-pdf" class="button button-secondary"><?php _e( 'Create PDF', 'radykal' ); ?></button>
							<button id="fpd-create-view-pdf" class="button button-secondary"><?php _e( 'Create PDF Of Current View', 'radykal' ); ?></button>
							<img class="help_tip" data-tip="<?php _e( 'The created pdfs will be stored: ', 'radykal' ); echo content_url('/fancy_products_orders/pdfs'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" />
							<div class="fpd-ajax-loader" id="fpd-ajax-loader-pdf"></div>
						</div>
						<div>
							<h4><?php _e( 'Image', 'radykal' ); ?></h4>
							<button id="fpd-create-image" class="button button-secondary"><?php _e( 'Create Full Product Image', 'radykal' ); ?></button>
							<button id="fpd-create-view-image" class="button button-secondary"><?php _e( 'Create Image Of Current View', 'radykal' ); ?></button>
							<img class="help_tip" data-tip="<?php _e( 'To save the image on your computer, just right-click on it in the new opened tab and select <i>Save Image As</i> from the context-menu.', 'radykal' ); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" />
							<div>
								<h5><?php _e( 'Element Images', 'radykal' ); ?><img class="help_tip" data-tip="<?php _e( 'Element Images will be stored in: ', 'radykal' ); echo content_url('/fancy_products_orders/images'); ?>" src="<?php echo $woocommerce->plugin_url() . '/assets/images/help.png'; ?>" height="16" width="16" /></h5>
								<label>
									<input type="checkbox" id="fpd-restore-oring-size" />
									<?php _e( 'Use origin size, that will set the scaling to 1, when exporting the image.', 'radykal' ); ?>
								</label>
								<br /><br />
								<button id="fpd-save-element-as-image" class="button button-secondary"><?php _e( 'Create Image From Element', 'radykal' ); ?></button>
								<div class="fpd-ajax-loader" id="fpd-ajax-loader-image"></div>
							</div>
							<ul id="fpd-order-image-list"></ul>
						</div>
					</div>
				</div>
			</div>

			<script type="text/javascript">

				jQuery(document).ready(function($) {

					var $fancyProductDesigner = $('#fpd-order-designer'),
						$orderImageList = $('#fpd-order-image-list'),
						currentItemId = null,
						isReady = false,
						stageWidth = <?php echo get_option('fpd_stage_width'); ?>,
						stageHeight = <?php echo get_option('fpd_stage_height'); ?>;

					var fancyProductDesigner = $fancyProductDesigner.fancyProductDesigner({
						_useChosen: false,
						centerInBoundingbox: false,
						allowProductSaving: false,
						customTexts: false,
						editorMode: true,
						fonts: ["<?php echo implode('", "', Fancy_Product_Designer::get_active_fonts()); ?>"],
						templatesDirectory: "<?php echo plugins_url('/templates/', dirname(__FILE__)); ?>",
						dimensions: {
							sidebarNavWidth: <?php echo get_option('fpd_sidebar_nav_width'); ?>,
							sidebarContentWidth: <?php echo get_option('fpd_sidebar_content_width'); ?>,
							sidebarHeight: <?php echo get_option('fpd_sidebar_height'); ?>,
							productStageWidth: stageWidth,
							productStageHeight: stageHeight
						}
					}).data('fancy-product-designer');

					//api buttons first available when
					$fancyProductDesigner.on('ready', function() {
						isReady = true;
					});

					$('.fancy-product').on('click', '.fpd-show-order-item', function(evt) {

						evt.preventDefault();
						var $this = $(this),
							order = $(this).data('order');

						$orderImageList.empty();

						currentItemId = $this.attr('id');

						if(typeof order == 'object') {
							fancyProductDesigner.loadProduct(order);

							$.ajax({
								url: "<?php echo admin_url('admin-ajax.php'); ?>",
								data: {
									action: 'fpd_loadorderitemimages',
									_ajax_nonce: "<?php echo $this->ajax_nonce; ?>",
									order_id: <?php echo $thepostid; ?>,
									item_id: currentItemId
								},
								type: 'post',
								dataType: 'json',
								success: function(data) {
									if(data == undefined || data.code == 500) {
										alert("<?php _e( 'Could not load order item image. Please try again', 'radykal' ); ?>");
									}
									//append order item images to list
									else if( data.code == 200 ) {
										for (var i=0; i < data.images.length; ++i) {
											var title = data.images[i].substr(data.images[i].lastIndexOf('/')+1);
											$orderImageList.append('<li><a href="'+data.images[i]+'" title="'+data.images[i]+'" target="_blank" >'+title+'</a></li>');
										}
									}
								}
							});
						}

					});

					$('#fpd-create-image, #fpd-create-view-image').click(function(evt) {

						evt.preventDefault();

						if(_checkAPI()) {

							var stage = fancyProductDesigner.getStage(),
								objects = stage.getObjects(),
								$this = $(this),
								tempViewIndex = fancyProductDesigner.getViewIndex(),
								onlyCurrentView = $this.attr('id') == 'fpd-create-view-image';

							_scaleStage(onlyCurrentView ? tempViewIndex : false, 'image', function(dataUrls) {

								var image = new Image();

								image.src = dataUrls[0];

								image.onload = function() {

									var popup = window.open('','_blank');
									popup.document.title = $this.attr('id') == 'fpd-create-image' ? "Product Image" : "Image of the current view";
									$(popup.document.body).append('<img src="'+this.src+'" title="Product" />');

								}

								_resetObjects(tempViewIndex);

							});

						}

					});

					$('#fpd-save-element-as-image').click(function(evt) {

						evt.preventDefault();

						if(_checkAPI()) {

							var stage = fancyProductDesigner.getStage();
							if(stage.getActiveObject()) {

								var $this = $(this).prop('disabled', true),
									element = stage.getActiveObject();

								//check if origin size should be rendered
								if($('#fpd-restore-oring-size').is(':checked')) {
									element.setScaleX(1);
									element.setScaleY(1);
								}
								var paddingTemp = element.padding;
								element.padding = 0;
								element.setCoords();

								$('#fpd-ajax-loader-image').show();

								$.ajax({
									url: "<?php echo admin_url('admin-ajax.php'); ?>",
									data: {
										action: 'fpd_imagefromdataurl',
										_ajax_nonce: "<?php echo $this->ajax_nonce; ?>",
										order_id: <?php echo $thepostid; ?>,
										item_id: currentItemId,
										data_url: element.toDataURL(),
										title: element.title
									},
									type: 'post',
									dataType: 'json',
									complete: function(data) {

										var json = data.responseJSON;
										if(data.status != 200 || json.code == 500) {
											alert("<?php _e( 'Could not create image. Please try again', 'radykal' ); ?>");
										}
										else if( json.code == 201 ) {
											$orderImageList.append('<li><a href="'+json.url+'" title="'+json.url+'" target="_blank">'+json.title+'.png</a></li>');
										}
										else {
											//prevent caching
											$orderImageList.find('a[title="'+json.url+'"]').attr('href', json.url+'?t='+new Date().getTime());
										}
										$('#fpd-ajax-loader-image').hide();
										$this.prop('disabled', false);

									}
								});

								element.setScaleX(element.params.scale);
								element.setScaleY(element.params.scale);
								element.padding = paddingTemp;
								element.setCoords();
								stage.renderAll();

							}
							else {
								alert("<?php _e('No element selected!', 'radykal'); ?>");
							}
						}

					});

					$('#fpd-create-pdf, #fpd-create-view-pdf').click(function(evt) {

						evt.preventDefault();

						if(_checkAPI()) {

							var stage = fancyProductDesigner.getStage(),
								objects = stage.getObjects(),
								$this = $(this),
								tempViewIndex = fancyProductDesigner.getViewIndex(),
								onlyCurrentView = $this.attr('id') == 'fpd-create-view-pdf';

							if($('#fpd-pdf-width').val() == '') {
								alert("<?php _e( 'No width is set. Please set one!', 'radykal' ); ?>");
								return false;
							}
							else if($('#fpd-pdf-height').val() == '') {
								alert("<?php _e( 'No width is set. Please set one!', 'radykal' ); ?>");
								return false;
							}

							$('#fpd-create-pdf, #fpd-create-view-pdf').prop('disabled', true);
							$('#fpd-ajax-loader-pdf').show();

							_scaleStage(onlyCurrentView ? tempViewIndex : false, 'pdf', function(dataURLs) {

								$.ajax({
									url: "<?php echo admin_url('admin-ajax.php'); ?>",
									data: {
										action: 'fpd_pdffromdataurl',
										_ajax_nonce: "<?php echo $this->ajax_nonce; ?>",
										order_id: <?php echo $thepostid; ?>,
										data_urls: JSON.stringify(dataURLs),
										width: $('#fpd-pdf-width').val(),
										height: $('#fpd-pdf-height').val(),
										image_format: $('input[name="fpd_image_format"]:checked').val(),
										orientation: $('input[name="fpd_pdf_orientation"]:checked').val()
									},
									type: 'post',
									dataType: 'json',
									complete: function(data) {

										if(data == undefined || data.status != 200) {
											alert("<?php _e( 'Could not create pdf. The sended data could be too large. Please try to export only a single view or try jpeg as image format!', 'radykal' ); ?>");
										}
										else {
											var json = data.responseJSON;
											window.open(json.url, '_blank');
										}

										$('#fpd-create-pdf, #fpd-create-view-pdf').prop('disabled', false);
										$('#fpd-ajax-loader-pdf').hide();

									}
								});

								_resetObjects(tempViewIndex);
							});

						}

					});

					$('input[name="fpd_scale"]').keyup(function() {

						var scale = !isNaN(this.value) && this.value.length > 0 ? this.value : 1,
							mmInPx = 3.779528;

						$('#fpd-pdf-width').val(Math.round((stageWidth * scale) / mmInPx));
						$('#fpd-pdf-height').val(Math.round((stageHeight * scale) / mmInPx));

					}).keyup();

					function _checkAPI() {

						if(fancyProductDesigner.getStage().getObjects().length > 0 && isReady) {
							return true;
						}
						else {
							alert("<?php _e( 'No Fancy Product is selected. Please open one from the Order Items!', 'radykal' ); ?>");
							return false;
						}

					};

					function _scaleStage(onlyCurrentView, type, callback) {

						var stage = fancyProductDesigner.getStage(),
							objects = stage.getObjects(),
							imageFormat = $('input[name="fpd_image_format"]:checked').val();
							backgroundColor = $('input[name="fpd_image_format"]:checked').val() == 'jpeg' ? '#ffffff' : 'transparent',
							viewsLength = $('.fpd-views-selection').children('li').size(),
							scaleFactor = $('input[name="fpd_scale"]').val().length == 0 ? 1 : Number($('input[name="fpd_scale"]').val()),
							viewsDataUrl = [];

						stage.setBackgroundColor(backgroundColor, function() {

							//scale stage so it fits for all views for image type
							if(onlyCurrentView === false && type == 'image') {
								stage.setHeight((stageHeight * scaleFactor) * viewsLength);
						    }
						    else {
								stage.setHeight(stageHeight * scaleFactor);
						    }

							stage.setWidth(stageWidth * scaleFactor);

							if(type == 'pdf') {
								for(var i=0; i<viewsLength;++i) {
									for(var j=0; j<objects.length; ++j) {

										var object = objects[j];
										if(object.viewIndex == i) {
											var	scaleX = object.scaleX,
												scaleY = object.scaleY,
												left = object.left,
												top = object.top;

											object.scaleX = scaleX * scaleFactor;
									        object.scaleY = scaleY * scaleFactor;
									        object.left = left * scaleFactor;
									        object.top = top * scaleFactor;
											object.visible = true;
										}
										else {
											object.visible = false;
										}

									}

									if(onlyCurrentView === false) {
										viewsDataUrl.push(stage.toDataURL({format: imageFormat}));
									}
									else {
										if(onlyCurrentView == i) {
											viewsDataUrl.push(stage.toDataURL({format: imageFormat}));
										}
									}
								}

							}
							else {
								for (var i=0; i < objects.length; ++i) {
							    	var object = objects[i],
							        	scaleX = object.scaleX,
										scaleY = object.scaleY,
										left = object.left,
										top = object.top;

							        object.scaleX = scaleX * scaleFactor;
							        object.scaleY = scaleY * scaleFactor;
							        object.left = left * scaleFactor;
							        object.top = (top * scaleFactor) + (object.viewIndex * (stageHeight * scaleFactor));
							        object.visible = true;
							        if(onlyCurrentView !== false) {
										object.top = (top * scaleFactor);
										 if(onlyCurrentView != object.viewIndex) {
											 object.visible = false;
								        }
							        }

							        object.setCoords();
							    }
							    viewsDataUrl.push(stage.toDataURL({format: imageFormat}));
							}
							callback(viewsDataUrl);

						});

					};

					function _resetObjects(tempViewIndex) {

						var stage = fancyProductDesigner.getStage(),
							objects = stage.getObjects();

						stage.setHeight(stageHeight);
						stage.setWidth(stageWidth);

						for(var i=0; i < objects.length; ++i) {
							var object = objects[i];
							object.setScaleX(object.params.scale);
							object.setScaleY(object.params.scale);
							object.setLeft(object.params.x);
							object.setTop(object.params.y);
							object.visible = object.viewIndex == tempViewIndex;
							object.setCoords();
						}
						stage.setBackgroundColor('transparent').renderAll();

					};

					// Convert dataURL to Blob object
					function _dataURLtoBlob(dataURL, imageFormat) {
					  var binary = atob(dataURL.split(',')[1]);
					  var array = [];
					  for(var i = 0; i < binary.length; i++) {
					      array.push(binary.charCodeAt(i));
					  }
					  // Return Blob object
					  return new Blob([new Uint8Array(array)], {type: 'image/'+imageFormat+''});
					}

				});

			</script>

			<?php

		}



		/************************************ AJAX ************************************************/

		//add a new view to a fancy product
		public function new_view() {

			if ( !isset($_POST['title']) || !isset($_POST['product_id']) || !isset($_POST['thumbnail']) )
			    exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			$title = trim($_POST['title']);
			$thumbnail = trim($_POST['thumbnail']);
			$product_id = trim($_POST['product_id']);

			global $wpdb, $charset_collate;

			$tableCheck = $wpdb->query("SHOW TABLES LIKE '".Fancy_Product_Designer::$views_table_name."'");

			//create views table if necessary
			if($tableCheck == 0) {
				require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
				//create table
				$views_sql = "ID BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
							  product_id BIGINT(20) UNSIGNED NOT NULL DEFAULT '0',
				              title TEXT COLLATE utf8_general_ci NOT NULL,
				              thumbnail TEXT COLLATE utf8_general_ci NOT NULL,
				              elements LONGTEXT COLLATE utf8_general_ci NULL,
							  PRIMARY KEY (ID)";

				$sql = "CREATE TABLE ".Fancy_Product_Designer::$views_table_name." ($views_sql) $charset_collate;";

				dbDelta($sql);
			}

			$inserted = $wpdb->insert(
				Fancy_Product_Designer::$views_table_name,
				array( 'product_id' => $product_id, 'title' => $title, 'thumbnail' => $thumbnail ),
				array( '%d', '%s', '%s')
			);

			if($inserted) {
				echo json_encode(array('html' => $this->get_view_list_item($wpdb->insert_id, $title, $thumbnail)));
			}
			else {
				echo json_encode(0);
			}

			die;

		}

		//edit title and thumbnail of a view
		public function edit_view() {

			if ( !isset($_POST['title']) || !isset($_POST['id']) || !isset($_POST['thumbnail']) )
			    exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			$title = trim($_POST['title']);
			$thumbnail = trim($_POST['thumbnail']);
			$id = trim($_POST['id']);

			global $wpdb;

			$success = $wpdb->update(
			 	Fancy_Product_Designer::$views_table_name,
			 	array('title' => $title, 'thumbnail' => $thumbnail), //what
			 	array('ID' => $id), //where
			 	array('%s', '%s'), //format what
			 	array('%d') //format where
			 );

			if($success) {
				echo json_encode(array('title' => $title, 'thumbnail' => $thumbnail, 'id' => $success));
			}
			else {
				echo json_encode(0);
			}

			die;

		}

		//remove a view from a fancy product
		public function remove_view() {

			if ( !isset($_POST['id']) )
			    exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			$id = trim($_POST['id']);

			global $wpdb;

			try {
				$wpdb->query( $wpdb->prepare("DELETE FROM ".Fancy_Product_Designer::$views_table_name." WHERE ID=%d", $id) );
				echo json_encode(1);
			}
			catch(Exception $e) {
				echo json_encode(0);
			}

			die;

		}

		//creates an image from a data url
		public function create_image_from_dataurl() {

			if ( !isset($_POST['order_id']) || !isset($_POST['item_id']) || !isset($_POST['data_url']) || !isset($_POST['title']) )
			    exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			$order_id = trim($_POST['order_id']);
			$item_id = trim($_POST['item_id']);
			$data_url = trim($_POST['data_url']);
			$title = sanitize_title( trim($_POST['title']) );

			//create fancy product orders directory
			if( !file_exists(FPD_ORDER_DIR) )
				mkdir(FPD_ORDER_DIR);

			//create uploads dir
			$images_dir = FPD_ORDER_DIR.'images/';
			if( !file_exists($images_dir) )
				mkdir($images_dir);

			//create order dir
			$order_dir = $images_dir . $order_id . '/';
			if( !file_exists($order_dir) )
				mkdir($order_dir);

			//create item dir
			$item_dir = $order_dir . $item_id . '/';
			if( !file_exists($item_dir) )
				mkdir($item_dir);

			$png_path = $item_dir.$title.'.png';

			$image_exist = file_exists($png_path);

			//get the base-64 from data
			$base64_str = substr($data_url, strpos($data_url, ",")+1);
			//decode base64 string
			$decoded = base64_decode($base64_str);
			$result = file_put_contents($png_path, $decoded);

			if( $result ) {
				$png_url = content_url( substr($png_path, strrpos($png_path, '/fancy_products_orders/')) );
				echo json_encode( array('code' => $image_exist ? 302 : 201, 'url' => $png_url, 'title' => $title) );
			}
			else {
				echo json_encode( array('code' => 500) );
			}

			die;

		}

		//creates a pdf from a data url
		public function create_pdf_from_dataurl() {

			if ( !isset($_POST['order_id']) || !isset($_POST['data_urls']) )
			    exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			if(!class_exists('FPDF'))
				require(dirname(__FILE__).'/classes/fpdf.php');

			$order_id = trim($_POST['order_id']);
			$data_urls = json_decode(stripslashes($_POST['data_urls']));
			$width = trim($_POST['width']);
			$height = trim($_POST['height']);
			$image_format = trim($_POST['image_format']);
			$orientation = trim($_POST['orientation']);

			//create fancy product orders directory
			if( !file_exists(FPD_ORDER_DIR) )
				mkdir(FPD_ORDER_DIR);

			//create pdf dir
			$pdf_dir = FPD_ORDER_DIR.'pdfs/';
			if( !file_exists($pdf_dir) )
				mkdir($pdf_dir);

			$pdf_path = $pdf_dir.$order_id.'.pdf';

			$pdf = new FPDF($orientation, 'mm', array($width, $height));
			foreach($data_urls as $data_url) {
				$pdf->AddPage();
				$pdf->Image($data_url,0,0,0,0,$image_format);
			}
			$pdf->Output($pdf_path, 'F');

			$pdf_url = content_url( substr($pdf_path, strrpos($pdf_path, '/wp-content/')+12) );
			echo json_encode( array('code' => 201, 'url' => $pdf_url) );

			die;

		}

		//load all images to an order based on order id and item id
		public function load_order_item_images() {

			if ( !isset($_POST['order_id']) || !isset($_POST['item_id']) )
			    exit;

			check_ajax_referer( 'fpd_ajax_nonce', '_ajax_nonce' );

			$order_id = trim($_POST['order_id']);
			$item_id = trim($_POST['item_id']);

			$pic_types = array("jpg", "jpeg", "png");

			$item_dir = FPD_ORDER_DIR . 'images/' . $order_id . '/' . $item_id;
			$folder = opendir($item_dir);

			$images = array();
			$item_dir_url = substr($item_dir, strrpos($item_dir, '/fancy_products_orders/'));
			while ($file = readdir($folder) ) {
				if(in_array(substr(strtolower($file), strrpos($file,".") + 1),$pic_types)) {
					$images[] = content_url( $item_dir_url ) . '/' . $file;
				}
			}
			closedir($folder);

			echo json_encode( array( 'code' => 200, images =>  $images) );

			die;

		}


		/************************************ PRIVATE ************************************************/

		private function get_view_list_item($id, $title, $thumbnail) {

			return '<li data-id="'.$id.'"><h3 class="fpd-clearfix"><img src="'.$thumbnail.'" /><span>'.$title.'</span><a href="#" class="button fpd-remove-view">'. __( 'Remove', 'radykal' ).'</a><a href="#" class="button fpd-edit-view">'.__( 'Edit', 'radykal' ).'</a><a href="'.(admin_url().'edit.php?post_type=product&page=edit_fancy_products&view_id='.$id.'').'" class="button">'.__( 'Edit Elements', 'radykal' ).'</a></h3></li>';

		}

		private function get_element_list_item($index_id, $type, $title, $source, $parameters) {

			$type_identifier = $type == 'image' ? '<img src="'.$source.'" />' : '<i class="fa fa-font"></i>';

			return '<li id="'.$index_id.'"><input type="text" name="element_titles[]" value="'.$title.'"/><div class="fpd-element-identifier">'.$type_identifier.'</div><div class="fpd-clearfix"><span class="fa fa-unlock fpd-lock-element"></span><span class="fa fa-times fpd-trash-element"></span></div><input type="hidden" name="element_sources[]" value="'.$source.'"/><input type="hidden" name="element_types[]" value="'.$type.'"/><input type="hidden" name="element_parameters[]" value="'.$parameters.'"/></li>';

		}
	}

}


//init Fancy Product Designer Admin
if(class_exists('Fancy_Product_designer_Admin')) {
	new Fancy_Product_designer_Admin();
}
?>
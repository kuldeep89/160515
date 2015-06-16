<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly


if( !class_exists('Fancy_Product_Designer_Settings') ) {
	class Fancy_Product_Designer_Settings {

		//frontend colors
		public static $styling_colors = array(
			'fpd_frontend_primary' => '#2C3E50',
			'fpd_frontend_secondary' => '#F6F6F6',
			'fpd_frontend_border' => '#DAE4EB',
			'fpd_frontend_border_highlight' => '#9EB2C0',
			'fpd_frontend_primary_elements' => '#c1cfd9',
			'fpd_frontend_submit_button' => '#a8bd44',
			'fpd_frontend_danger_button' => '#f97e76',
			'fpd_frontend_text_button' => '#FFFFFF'
		);
		public static $options;

		public function __construct() {

			//load fonts from google webfonts
			$optimised_google_webfonts = get_transient( 'fpd_google_webfonts' );
			if ( empty( $optimised_google_webfonts ) )	{
				$google_webfonts = unserialize(file_get_contents('http://phat-reaction.com/googlefonts.php?format=php'));
				$optimised_google_webfonts = array();
				foreach($google_webfonts as $google_webfont) {
					$optimised_google_webfonts[$google_webfont['css-name']] = $google_webfont['font-name'];
				}
				//cache google webfonts for one week
				set_transient('fpd_google_webfonts', $optimised_google_webfonts, 604800 );
			}

			//load woff fonts from fonts directory
			$files = scandir(FPD_PLUGIN_DIR.'/fonts');
			$woff_files = array();
			foreach($files as $file) {
				if(preg_match("/.woff/", $file)) {
					$woff_files[$file] = preg_replace("/\\.[^.\\s]{3,4}$/", "", $file);
				}
			}

			self::$options = array(
				//general
				'general' => apply_filters('fancy_product_designer_general_options', array(
						array(
							'title' => __( 'Layout & Dimensions', 'radykal' ),
							'type' => 'title',
							'desc' => __('Set here the layout and dimensions for the product designer.', 'radykal')
						),

						array(
							'title' 	=> __( 'Layout', 'radykal' ),
							'id' 		=> 'fpd_layout',
							'css' 		=> 'min-width:350px;',
							'default'	=> 'icon-sb-left',
							'type' 		=> 'select',
							'class'		=> 'chosen_select',
							'options'   => array(
								'icon-sb-left'	 => __( 'Left Sidebar', 'radykal' ),
								'icon-sb-top' => __( 'Top Sidebar', 'radykal' ),
								'icon-sb-right'	 => __( 'Right Sidebar', 'radykal' ),
								'icon-sb-bottom' => __( 'Bottom Sidebar', 'radykal' ),
							)
						),

						array(
							'title' => __( 'Sidebar Navigation Width/Height', 'radykal' ),
							'desc' 		=> __( 'The size for the navigation in the sidebar. Vertical layout = Width, Horizontal Layout = Height', 'radykal' ),
							'id' 		=> 'fpd_sidebar_nav_width',
							'css' 		=> 'width:70px;',
							'default'	=> '50',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Sidebar Content Width', 'radykal' ),
							'desc' 		=> __( 'The width for the content box in the sidebar. Only necessary for the vertical layout. If using the horizontal layout, a fixed height of 250px will be used.', 'radykal' ),
							'id' 		=> 'fpd_sidebar_content_width',
							'css' 		=> 'width:70px;',
							'default'	=> '200',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Sidebar Height/Width', 'radykal' ),
							'desc' 		=> __( 'The size for the sidebar. Vertical layout = Height, Horizontal Layout = Width', 'radykal' ),
							'id' 		=> 'fpd_sidebar_height',
							'css' 		=> 'width:70px;',
							'default'	=> '600',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Product Stage Width', 'radykal' ),
							'desc' 		=> __( 'The width for the product stage.', 'radykal' ),
							'id' 		=> 'fpd_stage_width',
							'css' 		=> 'width:70px;',
							'default'	=> '550',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Product Stage Height', 'radykal' ),
							'desc' 		=> __( 'The height for the product stage', 'radykal' ),
							'id' 		=> 'fpd_stage_height',
							'css' 		=> 'width:70px;',
							'default'	=> '600',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array( 'type' => 'sectionend'),

						array(	'title' => __( 'Styling', 'woocommerce' ), 'type' => 'title','desc' => __('Set here the colors for the product designer.', 'radykal') ),

						array( 'type' => 'fpd_styling'),

						array( 'type' => 'sectionend'),

						array(	'title' => __( 'Template', 'radykal' ), 'type' => 'title','desc' => '' ),

						array(
							'title' 	=> __( 'No Sidebar', 'radykal' ),
							'desc'	 	=> __( 'A template without sidebar and the product title will be placed over the product designer.', 'radykal' ),
							'id' 		=> 'fpd_template_full',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Show Product Image', 'radykal' ),
							'desc' 		=> __( 'Show the product image as well, this could cause that the product designer is not aligned correctly.', 'radykal' ),
							'id' 		=> 'fpd_template_product_image',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array( 'type' => 'sectionend'),

						array(	'title' => __( 'Product Designer', 'radykal' ), 'type' => 'title','desc' => __('Set here the plugin options for the Product Designer.', 'radykal') ),

						array(
							'title' 	=> __( 'Placement', 'radykal' ),
							'desc' 		=> __( 'By default the product designer will replace the product image. You can only use a custom hook to place it anywhere in the product page. Check out the documentation how to use the custom hook!', 'radykal' ),
							'desc_tip'	=>  true,
							'id' 		=> 'fpd_placement',
							'css' 		=> 'min-width:350px;',
							'default'	=> 'fpd-replace-image',
							'type' 		=> 'select',
							'class'		=> 'chosen_select',
							'options'   => array(
								'fpd-replace-image'	 => __( 'Replace Product Image', 'radykal' ),
								'fpd-custom-hook' => __( 'Use Custom Hook', 'radykal' ),
							)
						),

						array(
							'title' => __( 'Default Text Size', 'radykal' ),
							'desc' 		=> __( 'The default text size for all text elements.', 'radykal' ),
							'id' 		=> 'fpd_default_text_size',
							'css' 		=> 'width:70px;',
							'default'	=> '18',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Show Fonts Dropdown', 'radykal' ),
							'desc' 		=> __( 'Let the customers select a font for text elements', 'radykal' ),
							'id' 		=> 'fpd_fonts_dropdown',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Allow Product Saving', 'radykal' ),
							'desc' 		=> __( 'Allow customers to save their customized products', 'radykal' ),
							'id' 		=> 'fpd_allow_product_saving',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'PDF Button', 'radykal' ),
							'desc' 		=> __( 'Show the PDF button in the menu bar of the product stage', 'radykal' ),
							'id' 		=> 'fpd_pdf_button',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Center Elements In Bounding Box', 'radykal' ),
							'desc' 		=> __( 'Center elements, that have a bounding box, automatically in it', 'radykal' ),
							'id' 		=> 'fpd_center_in_bounding_box',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' 	=> __( 'Custom Text Tab', 'radykal' ),
							'desc'	 	=> __( 'Can customers add own text elements to the product?', 'radykal' ),
							'id' 		=> 'fpd_custom_texts',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Default Font', 'radykal' ),
							'desc' 		=> __( 'Enter the default font. If you leave it empty, the first font from the fonts dropdown will be used.', 'radykal' ),
							'id' 		=> 'fpd_default_font',
							'css' 		=> 'width:300px;',
							'default'	=> '',
							'desc_tip'	=>  true,
							'type' 		=> 'text'
						),

						array(
							'title' 	=> __( 'Upload Designs Tab', 'radykal' ),
							'desc'	 	=> __( 'Can customers upload own designs to the product?', 'radykal' ),
							'id' 		=> 'fpd_upload_designs',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Image Uploader', 'radykal' ),
							'id' 		=> 'fpd_type_of_uploader',
							'default'	=> 'filereader',
							'type' 		=> 'radio',
							'desc_tip'	=>  __( 'When customers can upload own images, you can choose between 2 different uploaders.', 'radykal' ),
							'options'	=> array(
								'filereader' => __( 'Filereader Uploader', 'radykal' ),
								'php' => __( 'PHP Uploader', 'radykal' )
							),
						),

						array(
							'title' 	=> __( 'Maximum Image Size (MB)', 'radykal' ),
							'desc' 		=> __( 'The maximum image size in Megabytes, when using the PHP uploader.', 'radykal' ),
							'id' 		=> 'fpd_max_image_size',
							'css' 		=> 'width:70px;',
							'default'	=> '1',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Only logged-in users can upload images?', 'radykal' ),
							'desc'	 	=> __( 'Because the PHP uploader uploads the image to your web server, you can allow the image upload for logged-in users only.', 'radykal' ),
							'id' 		=> 'fpd_upload_designs_php_logged_in',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Facebook App-ID', 'radykal' ),
							'desc' 		=> __( 'To allow users to add photos from facebook you have to enter a Facebook App-Id.', 'radykal' ),
							'id' 		=> 'fpd_facebook_app_id',
							'css' 		=> 'width:300px;',
							'default'	=> '',
							'desc_tip'	=>  true,
							'type' 		=> 'text'
						),

						array(
							'title' 	=> __( 'Disable On Smartphones', 'radykal' ),
							'desc'	 	=> __( 'Disable product designer on smartphones and display an information instead!', 'radykal' ),
							'id' 		=> 'fpd_disable_on_smartphones',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' 	=> __( 'Disable On Tablets', 'radykal' ),
							'desc'	 	=> __( 'Disable product designer on tablets and display an information instead!', 'radykal' ),
							'id' 		=> 'fpd_disable_on_tablets',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' 	=> __( 'Product Title In Menu Bar', 'radykal' ),
							'desc'	 	=> __( 'Display product title in the menu bar instead the text you set in the labels settings.', 'radykal' ),
							'id' 		=> 'fpd_product_title_menu_bar',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array( 'type' => 'sectionend'),
				)),
				//default parameters
				'default_parameters' => apply_filters('fancy_product_designer_default_parameters_options', array(

						array(
							'title' => __( 'Design & Uploaded Images Parameters', 'radykal' ),
							'type' => 'title',
							'desc' => __('The default parameters for the designs in the sidebar and the uploaded images by the customers.', 'radykal')
						),

						array(
							'title' => __( 'X-Position', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_x',
							'css' 		=> 'width:70px;',
							'default'	=> '0',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Y-Position', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_y',
							'css' 		=> 'width:70px;',
							'default'	=> '0',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Z-Position', 'radykal' ),
							'desc' 		=> __( '-1 means that the element will be added at the top. An value higher than that, will add the element to that z-position.', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_z',
							'css' 		=> 'width:70px;',
							'default'	=> '-1',
							'desc_tip'	=>  true,
							'type' 		=> 'text',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Colors', 'radykal' ),
							'desc' 		=> __( 'Enter hex color(s) separated by comma.', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_colors',
							'css' 		=> 'width:300px;',
							'default'	=> '',
							'desc_tip'	=>  true,
							'type' 		=> 'text'
						),

						array(
							'title' => __( 'Price', 'radykal' ),
							'desc' 		=> __( 'Enter the additional price for a design element.', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_price',
							'css' 		=> 'width:70px;',
							'default'	=> '0',
							'desc_tip'	=>  true,
							'type' 		=> 'text',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Auto-Center', 'radykal' ),
							'desc' 		=> __( 'Will center a new added design automtically in stage or in bounding box.', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_auto_center',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Draggable', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_draggable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Rotatable', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_rotatable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Resizable', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_resizable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Z-Changeable', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_zchangeable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Use another element as bounding box?', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_bounding_box_control',
							'class'		=> 'fpd-bounding-box-control',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' 	=> __( 'Bounding Box Target', 'radykal' ),
							'desc' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_bounding_box_target',
							'css' 		=> 'width:150px;',
							'class'		=> 'fpd-bounding-box-target-input',
							'default'	=> '',
							'desc_tip'	=>  true,
							'type' 		=> 'text'
						),

						array(
							'title'		=> __( 'Bounding Box X-Position', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_bounding_box_x',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Bounding Box Y-Position', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_bounding_box_y',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Bounding Box Width', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_bounding_box_width',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Bounding Box Height', 'radykal' ),
							'id' 		=> 'fpd_designs_parameter_bounding_box_height',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Minimum Width', 'radykal' ),
							'desc' 		=> __( 'The minimum image width for uploaded designs from the customers.', 'radykal' ),
							'id' 		=> 'fpd_uploaded_designs_parameters_min_w',
							'css' 		=> 'width:70px;',
							'default'	=> '100',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Minimum Height', 'radykal' ),
							'desc' 		=> __( 'The minimum image height for uploaded designs from the customers.', 'radykal' ),
							'id' 		=> 'fpd_uploaded_designs_parameters_min_h',
							'css' 		=> 'width:70px;',
							'default'	=> '100',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Maximum Width', 'radykal' ),
							'desc' 		=> __( 'The maximum image width for uploaded designs from the customers.', 'radykal' ),
							'id' 		=> 'fpd_uploaded_designs_parameters_max_w',
							'css' 		=> 'width:70px;',
							'default'	=> '1000',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Maximum Height', 'radykal' ),
							'desc' 		=> __( 'The maximum image height for uploaded designs from the customers.', 'radykal' ),
							'id' 		=> 'fpd_uploaded_designs_parameters_max_h',
							'css' 		=> 'width:70px;',
							'default'	=> '1000',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Resize To Width', 'radykal' ),
							'desc' 		=> __( 'Resize the uploaded image to this width, when width is larger than height.', 'radykal' ),
							'id' 		=> 'fpd_uploaded_designs_parameters_resize_to_w',
							'css' 		=> 'width:70px;',
							'default'	=> '300',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Resize To Height', 'radykal' ),
							'desc' 		=> __( 'Resize the uploaded image to this height, when height is larger than width.', 'radykal' ),
							'id' 		=> 'fpd_uploaded_designs_parameters_resize_to_h',
							'css' 		=> 'width:70px;',
							'default'	=> '300',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array( 'type' => 'sectionend'),

						array(	'title' => __( 'Custom Text Parameters', 'radykal' ), 'type' => 'title','desc' => __('Set here the default parameters, that will be used when a customer adds a custom text.', 'radykal') ),

						array(
							'title' => __( 'X-Position', 'radykal' ),
							'desc' 		=> __( 'The x-position of the custom text element.', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_x',
							'css' 		=> 'width:70px;',
							'default'	=> '0',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Y-Position', 'radykal' ),
							'desc' 		=> __( 'The y-position of the custom text element.', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_y',
							'css' 		=> 'width:70px;',
							'default'	=> '0',
							'desc_tip'	=>  true,
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Z-Position', 'radykal' ),
							'desc' 		=> __( '-1 means that the element will be added at the top. An value higher than that, will add the element to that z-position.', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_z',
							'css' 		=> 'width:70px;',
							'default'	=> '-1',
							'desc_tip'	=>  true,
							'type' 		=> 'text',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Colors', 'radykal' ),
							'desc' 		=> __( 'Enter hex color(s) separated by comma.', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_colors',
							'css' 		=> 'width:300px;',
							'default'	=> '#000000',
							'desc_tip'	=>  true,
							'type' 		=> 'text'
						),

						array(
							'title' => __( 'Price', 'radykal' ),
							'desc' 		=> __( 'Enter the additional price for a text element.', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_price',
							'css' 		=> 'width:70px;',
							'default'	=> '0',
							'desc_tip'	=>  true,
							'type' 		=> 'text',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' => __( 'Auto-Center', 'radykal' ),
							'desc' 		=> __( 'Will center a new added text automtically in stage or in bounding box.', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_auto_center',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Draggable', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_draggable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Rotatable', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_rotatable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Resizable', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_resizable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Z-Changeable', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_zchangeable',
							'default'	=> 'yes',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Patternable', 'radykal' ),
							'desc' 		=> __( 'Can the customer choose a pattern?', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_patternable',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' => __( 'Use another element as bounding box?', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_bounding_box_control',
							'class'		=> 'fpd-bounding-box-control',
							'default'	=> 'no',
							'type' 		=> 'checkbox'
						),

						array(
							'title' 	=> __( 'Bounding Box Target', 'radykal' ),
							'desc' 		=> __( 'Enter the title of another element that should be used as bounding box for design elements.', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_bounding_box_target',
							'css' 		=> 'width:150px;',
							'class'		=> 'fpd-bounding-box-target-input',
							'default'	=> '',
							'desc_tip'	=>  true,
							'type' 		=> 'text'
						),

						array(
							'title'		=> __( 'Bounding Box X-Position', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_bounding_box_x',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Bounding Box Y-Position', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_bounding_box_y',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Bounding Box Width', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_bounding_box_width',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),

						array(
							'title' 	=> __( 'Bounding Box Height', 'radykal' ),
							'id' 		=> 'fpd_custom_texts_parameter_bounding_box_height',
							'css' 		=> 'width:70px;',
							'class'		=> 'fpd-bounding-box-custom-input',
							'default'	=> '',
							'type' 		=> 'number',
							'custom_attributes' => array(
								'min' 	=> 0,
								'step' 	=> 1
							)
						),


						array( 'type' => 'sectionend'),

					)),
					//labels
					'labels' => apply_filters('fancy_product_designer_labels_options', array(

						array(
						'title' => __( 'Sidebar Labels', 'radykal' ),
						'type' => 'title','desc' => __( 'Edit the text and tooltips for the elements in the sidebar.', 'radykal' )
						),


						array(
							'title' => __( 'Navigation Products Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Products" navigation tab.', 'radykal' ),
							'id' 		=> 'fpd_navigation_tab_products',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Products'
						),

						array(
							'title' => __( 'Navigation Designs Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Designs" navigation tab.', 'radykal' ),
							'id' 		=> 'fpd_navigation_tab_designs',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Designs'
						),

						array(
							'title' => __( 'Navigation Add-Text Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Add-Text" navigation tab.', 'radykal' ),
							'id' 		=> 'fpd_navigation_tab_add_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Add own text'
						),

						array(
							'title' => __( 'Navigation Edit-Elements Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Edit-Elements" navigation tab.', 'radykal' ),
							'id' 		=> 'fpd_navigation_tab_edit_elements',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Edit Elements'
						),

						array(
							'title' => __( 'Navigation Upload Designs Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Upload Designs" navigation tab.', 'radykal' ),
							'id' 		=> 'fpd_navigation_tab_upload_designs',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Add Own Designs'
						),

						array(
							'title' => __( 'Navigation Facebook Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Facebook" navigation tab.', 'radykal' ),
							'id' 		=> 'fpd_navigation_tab_facebook',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Add Photos From Facebook'
						),

						array(
							'title' => __( 'Navigation Saved-Products Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Saved-Products" navigation tab.', 'radykal' ),
							'id' 		=> 'fpd_navigation_tab_saved_products',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Your saved products'
						),

						array(
							'title' => __( 'Custom Text Headline', 'radykal' ),
							'desc' 		=> __( 'Headline to add custom text elements.', 'radykal' ),
							'id' 		=> 'fpd_custom_text_headline',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Add Text'
						),

						array(
							'title' => __( 'Custom Text Placeholder', 'radykal' ),
							'desc' 		=> __( 'Placeholder for the textarea.', 'radykal' ),
							'id' 		=> 'fpd_custom_text_placeholder',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Enter your text'
						),

						array(
							'title' => __( 'Custom Text Button', 'radykal' ),
							'desc' 		=> __( 'The text for the button to add custom text elements.', 'radykal' ),
							'id' 		=> 'fpd_custom_text_button',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Add Text'
						),

						array(
							'title' => __( 'Edit Elements Headline', 'radykal' ),
							'desc' 		=> __( 'Headline to customize elements.', 'radykal' ),
							'id' 		=> 'fpd_customize_headline',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Edit Elements'
						),

						array(
							'title' => __( 'None Option Elements Dropdown', 'radykal' ),
							'desc' 		=> __( 'Text for the none option of the elements dropdown.', 'radykal' ),
							'id' 		=> 'fpd_customize_dropdown_none',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'None'
						),

						array(
							'title' => __( 'Align Left Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Align-Left" button when customizing a text element.', 'radykal' ),
							'id' 		=> 'fpd_customize_text_tooltip_align_left',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Align Left'
						),

						array(
							'title' => __( 'Align Center Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Center-Left" button when customizing a text element.', 'radykal' ),
							'id' 		=> 'fpd_customize_text_tooltip_align_center',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Align Center'
						),

						array(
							'title' => __( 'Align Right Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Right-Left" button when customizing a text element.', 'radykal' ),
							'id' 		=> 'fpd_customize_text_tooltip_align_right',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Align Right'
						),

						array(
							'title' => __( 'Bold Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Bold" button when customizing a text element.', 'radykal' ),
							'id' 		=> 'fpd_customize_text_tooltip_bold',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Bold'
						),

						array(
							'title' => __( 'Italic Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Italic" button when customizing a text element.', 'radykal' ),
							'id' 		=> 'fpd_customize_text_tooltip_italic',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Italic'
						),

						array(
							'title' => __( 'Center Horizontal Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Center-Horizontal" button.', 'radykal' ),
							'id' 		=> 'fpd_customize_tooltip_center_horizontal',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Center Horizontal'
						),

						array(
							'title' => __( 'Center Vertical Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Center-Vertical" button.', 'radykal' ),
							'id' 		=> 'fpd_customize_tooltip_center_vertical',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Center Vertical'
						),

						array(
							'title' => __( 'Move-It-Down Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Move it down" button.', 'radykal' ),
							'id' 		=> 'fpd_customize_tooltip_move_it_down',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Move it down'
						),

						array(
							'title' => __( 'Move-It-Up Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Move it up" button.', 'radykal' ),
							'id' 		=> 'fpd_customize_tooltip_move_it_up',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Move it up'
						),

						array(
							'title' => __( 'Reset Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Reset to his origin" button.', 'radykal' ),
							'id' 		=> 'fpd_customize_tooltip_reset',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Reset to his origin'
						),

						array(
							'title' => __( 'Trash Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Trash" button.', 'radykal' ),
							'id' 		=> 'fpd_customize_tooltip_trash',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Trash'
						),

						array(
							'title' => __( 'Upload Designs Tooltip', 'radykal' ),
							'desc' 		=> __( 'Headline for the Upload Designs box.', 'radykal' ),
							'id' 		=> 'fpd_upload_designs_headline',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Add Own Designs'
						),

						array(
							'title' => __( 'Upload Designs Info', 'radykal' ),
							'desc' 		=> __( 'Some information for the Upload Designs box.', 'radykal' ),
							'desc_tip'	=>  true,
							'id' 		=> 'fpd_upload_designs_browser_info',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'To upload own designs, you have to use Firefox, Safari, Chrome or at least IE10!'
						),

						array(
							'title' => __( 'Upload Designs Button', 'radykal' ),
							'desc' 		=> __( 'Upload button in the Upload Designs box.', 'radykal' ),
							'id' 		=> 'fpd_upload_designs_button',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Upload'
						),

						array(
							'title' => __( 'Add Facebook Photos Headline', 'radykal' ),
							'desc' 		=> __( 'Headline for the "Facebook" tab.', 'radykal' ),
							'id' 		=> 'fpd_fb_headline',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Add Facebook Photos'
						),

						array(
							'title' => __( 'Facebook Photos: Select Friend', 'radykal' ),
							'desc' 		=> __( 'Default text for the select-dropdown to select a facebook friend.', 'radykal' ),
							'id' 		=> 'fpd_fb_select_friend',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Select a friend'
						),

						array(
							'title' => __( 'Facebook Photos: Select album', 'radykal' ),
							'desc' 		=> __( 'Default text for the select-dropdown to select an album.', 'radykal' ),
							'id' 		=> 'fpd_select_album',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Select an album'
						),

						array(
							'title' => __( 'Saved Products Headline', 'radykal' ),
							'desc' 		=> __( 'Headline for the "Saved Products" tab.', 'radykal' ),
							'id' 		=> 'fpd_saved_products_headline',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Saved Products'
						),

						array( 'type' => 'sectionend'),

						array(	'title' => __( 'Product Stage Labels', 'radykal' ), 'type' => 'title','desc' => __( 'Edit the texts and tooltips for the elements in the product stage.', 'radykal' ) ),

						array(
							'title' => __( 'Product Stage Headline', 'radykal' ),
							'desc' 		=> __( 'The headline for the product stage.', 'radykal' ),
							'id' 		=> 'fpd_stage_headline',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Fancy Product Designer'
						),

						array(
							'title' => __( 'Save Product Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Save Product" button in the menu bar.', 'radykal' ),
							'id' 		=> 'fpd_stage_menu_bar_save_product',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Save Product'
						),

						array(
							'title' => __( 'Download Product Image Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Download Product Image" button in the menu bar.', 'radykal' ),
							'id' 		=> 'fpd_stage_menu_bar_download_product_image',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Download Product Image'
						),

						array(
							'title' => __( 'Print Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Print" button in the menu bar.', 'radykal' ),
							'id' 		=> 'fpd_stage_menu_bar_print',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Print'
						),

						array(
							'title' => __( 'Save As PDF Tooltip', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Save As PDF" button in the menu bar.', 'radykal' ),
							'id' 		=> 'fpd_stage_menu_bar_pdf',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Save As PDF'
						),

						array(
							'title' => __( 'Reset Product', 'radykal' ),
							'desc' 		=> __( 'Tooltip for the "Save As PDF" button in the menu bar.', 'radykal' ),
							'id' 		=> 'fpd_stage_menu_bar_reset',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Reset Product'
						),

						array( 'type' => 'sectionend'),

						array(	'title' => __( 'Modification Tooltips', 'radykal' ), 'type' => 'title','desc' => __( 'Edit the texts for the modification tooltips.', 'radykal' ) ),

						array(
							'title' => __( 'X', 'radykal' ),
							'id' 		=> 'fpd_modification_tooltip_x',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'x: '
						),

						array(
							'title' => __( 'Y', 'radykal' ),
							'id' 		=> 'fpd_modification_tooltip_y',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> ' y: '
						),

						array(
							'title' => __( 'Width', 'radykal' ),
							'id' 		=> 'fpd_modification_tooltip_width',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'width: '
						),

						array(
							'title' => __( 'Height', 'radykal' ),
							'id' 		=> 'fpd_modification_tooltip_height',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'height: '
						),

						array(
							'title' => __( 'Angle', 'radykal' ),
							'id' 		=> 'fpd_modification_tooltip_angle',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'angle: '
						),

						array( 'type' => 'sectionend'),

						array(	'title' => __( 'Additional Labels', 'radykal' ), 'type' => 'title','desc' => __( 'Edit the texts for the other labels like alerts, confirm boxes etc..', 'radykal' ) ),

						array(
							'title' => __( 'Out Of Containment Alert', 'radykal' ),
							'desc' 		=> __( 'The text that appears in the tooltip when an element is out of his containment. The element title will be added before the text.', 'radykal' ),
							'id' 		=> 'fpd_out_of_containment_alert',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'is out of his containment!'
						),

						array(
							'title' => __( 'Uploaded Design Size Alert', 'radykal' ),
							'desc' 		=> __( 'The text for the alert when the image resolution is not in the range that you set for the minimum/maximum sizes for an uploaded design.', 'radykal' ),
							'desc_tip'	=>  true,
							'id' 		=> 'fpd_uploaded_design_size_alert',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Sorry! But the uploaded image size does not conform our indication of size.'
						),

						array(
							'title' => __( 'Confirm Product Deletion', 'radykal' ),
							'desc' 		=> __( 'The text that appears in the confirm box when deleting a saved product.', 'radykal' ),
							'id' 		=> 'fpd_confirm_product_delete',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Delete saved product?'
						),

						array(
							'title' => __( 'Colorpicker Cancel', 'radykal' ),
							'desc' 		=> __( 'The text for the cancel link in the colorpicker.', 'radykal' ),
							'id' 		=> 'fpd_colorpicker_cancel',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Cancel'
						),

						array(
							'title' => __( 'Colorpicker Choose', 'radykal' ),
							'desc' 		=> __( 'The text for the choose button in the colorpicker.', 'radykal' ),
							'id' 		=> 'fpd_colorpicker_choose',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Choose'
						),

						array(
							'title' => __( 'Catalog: Add To Cart Button', 'radykal' ),
							'desc' 		=> __( 'The text for the add to cart button in the product listings.', 'radykal' ),
							'id' 		=> 'fpd_add_to_cart_text',
							'type' 		=> 'text',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Customize'
						),

						array(
							'title' => __( 'Not Supported Device Information', 'radykal' ),
							'desc' 		=> __( 'The text that will be displayed instead the product designer, if you disable the product designer for smartphones or tablets.', 'radykal' ),
							'desc_tip'	=>  true,
							'id' 		=> 'fpd_not_supported_device_info',
							'type' 		=> 'textarea',
							'css' 		=> 'min-width:300px;',
							'default'	=> 'Sorry! But the product designer is not adapted for your device. Please use a device with a larger screen!'
						),

						array( 'type' => 'sectionend'),

					)),
					//fonts
					'fonts' => apply_filters('fancy_product_designer_fonts_options', array(

						array(
							'title' => __( 'Set the fonts for the fonts dropdown', 'radykal' ),
							'type' => 'title'
						),

						array(
							'title' => __( 'Common Fonts', 'radykal' ),
							'desc' 		=> 'Enter here common fonts separated by comma, which are installed on all system by default, e.g. Arial.',
							'id' 		=> 'fpd_common_fonts',
							'css' 		=> 'width:500px;',
							'type' 		=> 'text',
							'desc_tip'	=>  true,
							'default'	=> 'Arial,Helvetica,Times New Roman,Verdana,Geneva'
						),

						array(
							'title' 	=> __( 'Google Webfonts', 'radykal' ),
							'desc' 		=> __( "Choose fonts from Google Webfonts. Note that too many fonts will slow down the loading of your website.", 'radykal' ),
							'id' 		=> 'fpd_google_webfonts',
							'css' 		=> 'min-width:500px;',
							'default'	=> '',
							'type' 		=> 'multiselect',
							'class'		=> 'chosen_select',
							'desc_tip'	=>  true,
							'value'		=> '',
							'options' 	=> $optimised_google_webfonts
						),

						array(
							'title' 	=> __( 'Fonts Directory', 'radykal' ),
							'desc' 		=> __( "You can add your own fonts to the fonts directory of the plugin, these font files need to be .woff files.", 'radykal' ),
							'id' 		=> 'fpd_fonts_directory',
							'css' 		=> 'min-width:500px;',
							'default'	=> '',
							'type' 		=> 'multiselect',
							'class'		=> 'chosen_select',
							'desc_tip'	=>  true,
							'options' 	=> $woff_files
						),


						array( 'type' => 'sectionend'),

					))
			);
		}
	}
}

if(class_exists('Fancy_Product_Designer_Settings'))
	new Fancy_Product_Designer_Settings();

?>
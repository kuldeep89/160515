<?php
//include wp-load to use get_option
$parse_uri = explode( 'wp-content', $_SERVER['SCRIPT_FILENAME'] );
require_once( $parse_uri[0] . 'wp-load.php' );

//labels
$headline = get_option('fpd_product_title_menu_bar') == 'yes' ? '' : get_option('fpd_stage_headline');
$save_product_tooltip = get_option('fpd_stage_menu_bar_save_product');
$download_image_tooltip = get_option('fpd_stage_menu_bar_download_product_image');
$print_tooltip = get_option('fpd_stage_menu_bar_print');
$pdf_tooltip = get_option('fpd_stage_menu_bar_pdf');
$reset_tooltip = get_option('fpd_stage_menu_bar_reset');
?>

<section class="fpd-product-container fpd-border-color">
	<div class="fpd-menu-bar fpd-clearfix fpd-main-color">
		<h3><?php echo $headline; ?></h3>
		<!-- Menu -->
		<div class="fpd-menu">
			<ul class="fpd-clearfix">
				<li><span class="fpd-save-product fa fa-floppy-o fpd-main-color fpd-tooltip" title="<?php echo $save_product_tooltip; ?>"></span></li>
				<li><a href="" download="" target="_blank" class="fpd-download-image fa fa-cloud-download fpd-main-color fpd-tooltip" title="<?php echo $download_image_tooltip; ?>"></a></li>
				<li><span class="fpd-save-pdf fa fa-file-o fpd-tooltip" title="<?php echo $pdf_tooltip; ?>"></span></li>
				<li><span class="fpd-print fa fa-print fpd-tooltip" title="<?php echo $print_tooltip; ?>"></span></li>
				<li><span class="fpd-reset-product fa fa-undo fpd-tooltip" title="<?php echo $reset_tooltip; ?>"></span></li>
			</ul>
		</div>
	</div>
	<!-- Kinetic Stage -->
	<div class="fpd-product-stage fpd-content-color">
		<canvas></canvas>
	</div>
</section>
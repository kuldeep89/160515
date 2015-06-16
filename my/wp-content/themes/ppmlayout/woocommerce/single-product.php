<?php
/**
 * The Template for displaying all single products.
 *
 * Override this template by copying it to yourtheme/woocommerce/single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>
<div class="container-fluid">
	<a href="<?php echo site_url() ?>/shop/shopping-cart/" class="view-cart-button">View <?php echo ($woocommerce->cart->has_quoted_item == false) ? 'Cart' : 'Quote' ?></a>
	<!-- BEGIN PAGE HEADER-->
	    <div class="row-fluid wooCommerceProduct">
	       <div class="span12">
	                         
				<!-- BEGIN PAGE TITLE & BREADCRUMB-->			
				<h3 class="page-title">
					Shop
					<small>
						<?php
							$category_description = category_description();
							if ( ! empty( $category_description ) )
								echo apply_filters( 'category_archive_meta', '<div class="category-archive-meta">' . $category_description . '</div>' );
						?>
					</small>
				</h3>
				<ul class="breadcrumb">
					<li>
						<?php if ( function_exists('yoast_breadcrumb') ) { $bc = yoast_breadcrumb('<span id="breadcrumbs">','</span>', false); } ?>
						<?php echo $bc ?>
					</li>	
				</ul>
				<!-- END PAGE TITLE & BREADCRUMB-->

	       </div>
	    </div>
	    <!-- END PAGE HEADER-->
	<?php
		/**
		 * woocommerce_before_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
		 * @hooked woocommerce_breadcrumb - 20
		 */
		// do_action('woocommerce_before_main_content');
	?>

		<?php while ( have_posts() ) : the_post(); ?>

			<?php 

				woocommerce_get_template_part( 'content', 'single-product' ); 

				if(get_field('product_type') == "embroidery") {
				    echo '<div class="row-fluid"><div class="span12"><a href="#embroideryModal" class="productTypeLink" role="button" data-toggle="modal">Embroidery pricing information</a></div></div>';
				} elseif(get_field('product_type') == "digitization") {
					echo '<div class="row-fluid"><div class="span12"><a href="#digitizationModal" class="productTypeLink" role="button" data-toggle="modal">Screen printing pricing information</a></div></div>';
				}

			?>
			
		<?php endwhile; // end of the loop. ?>

	<?php
	
		/**
		 * woocommerce_after_main_content hook
		 *
		 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action('woocommerce_after_main_content');
	?>

	<?php
		/**
		 * woocommerce_sidebar hook
		 *
		 * @hooked woocommerce_get_sidebar - 10
		 */
		//do_action('woocommerce_sidebar');
	?>

</div>


<?php 

if(get_field('product_type') == "embroidery") {
    echo   '<div id="embroideryModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="embroideryModalLabel" aria-hidden="true">
			  <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
			    <h3 id="embroideryModalLabel">Embroidery Cost Estimates</h3>
			  </div>
			  <div class="modal-body">';
		   		$page = get_page_by_title('Embroidery Cost Estimates'); 
				$content = apply_filters('the_content', $page->post_content);
				echo $content;
	echo	  '</div>
			  <div class="modal-footer">
			    <button class="btn close-btn" data-dismiss="modal" aria-hidden="true">Close</button>
			  </div>
			</div>';
} elseif(get_field('product_type') == "digitization") {
	echo   '<div id="digitizationModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="digitizationModalLabel" aria-hidden="true">
			  <div class="modal-header">
			    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
			    <h3 id="digitizationModalLabel">Additional Screen Print Locations</h3>
			  </div>
			  <div class="modal-body">';
		   		$page = get_page_by_title('Additional Screen Print Locations'); 
				$content = apply_filters('the_content', $page->post_content);
				echo $content;
	echo	  '</div>
			  <div class="modal-footer">
			    <button class="btn close-btn" data-dismiss="modal" aria-hidden="true">Close</button>
			  </div>
			</div>';
}

get_footer(); 

?>
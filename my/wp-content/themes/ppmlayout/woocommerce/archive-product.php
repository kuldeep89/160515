<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

get_header(); ?>

<!-- BEGIN PAGE CONTAINER-->
<a href="<?php echo site_url() ?>/shop/shopping-cart/" class="view-cart-button" style="margin-right:20px;">View <?php echo ($woocommerce->cart->has_quoted_item == false) ? 'Cart' : 'Quote' ?></a>
<div class="container-fluid">
    <!-- BEGIN PAGE HEADER-->
    <div class="row-fluid">
       <div class="span12">
                         
			<!-- BEGIN PAGE TITLE & BREADCRUMB-->			
			<h3 class="page-title">
				<?php echo get_the_term_list( $post->ID, 'product_cat', '<h3 class="page-title category-title" style="color:#000;">', '</h3>' ); ?>
				<small>
					<?php if( function_exists('the_field') ) {the_field('page_subtitle');} ?>
				</small>
			</h3>
			<ul class="breadcrumb">
				<li>
					<?php if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<span id="breadcrumbs">','</span>');} ?>
				</li>	
			</ul>
			<!-- END PAGE TITLE & BREADCRUMB-->
			
       </div>
    </div>
    <!-- END PAGE HEADER-->
    <div id="dashboard">
       <div class="row-fluid">
          <div class="span12 responsive">
			<?php woocommerce_product_loop_start(); ?>

				<?php woocommerce_product_subcategories(); ?>

				<?php while ( have_posts() ) : the_post(); ?>

					<?php woocommerce_get_template_part( 'content', 'product' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php woocommerce_product_loop_end(); ?>
		</div>                  
      </div>
	  <div class="clearfix"></div>
   </div>
</div>
<!-- END PAGE CONTAINER-->  

<?php get_footer(); ?>
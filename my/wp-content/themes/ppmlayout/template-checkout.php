<?php 	/* Template Name: Checkout Template */
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site will use a
 * different template.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */

get_header('checkout'); ?>

<?php while ( have_posts() ) : the_post(); ?>
 <div class="container-fluid">
    <div id="dashboard">
       <div class="row-fluid">
          <div class="span12 responsive">
			<?php get_template_part( 'content', 'page' ); ?>
			<?php comments_template( '', true ); ?>						
          </div>                  
       </div>
       <div class="clearfix"></div>
    </div>
 </div>
<?php endwhile; ?>

<?php get_footer('checkout'); ?>
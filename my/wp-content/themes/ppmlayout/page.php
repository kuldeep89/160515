<?php
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

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>
<!-- BEGIN PAGE TITLE & BREADCRUMB-->
<div class="page-breadcrumb-heading">
	<h3 class="page-title">
		<?php the_title(); ?>
		<small>
			<?php if( function_exists('the_field') ) {the_field('page_subtitle');} ?>
		</small>
	</h3>
	<ul class="breadcrumb">
		<li>
			<?php if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<span id="breadcrumbs">','</span>');} ?>
		</li>
	</ul>
</div>
<!-- END PAGE TITLE & BREADCRUMB-->
 <!-- BEGIN PAGE CONTAINER-->
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
 <!-- END PAGE CONTAINER-->    
<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
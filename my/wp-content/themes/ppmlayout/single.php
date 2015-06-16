<?php
/**
 * The Template for displaying all single posts.
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
			Academy
			<small>
				Learn, grow, stay current.
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
					<?php get_template_part( 'content', 'single' ); ?>				
		          </div>                  
		       </div>
		       <div class="clearfix"></div>
		    </div>
		 </div>
		<!-- END PAGE CONTAINER-->   
	<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
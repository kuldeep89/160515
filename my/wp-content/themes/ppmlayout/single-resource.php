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
	<!-- BEGIN PAGE TITLE & BREADCRUMB -->
	<div class="page-breadcrumb-heading">
		<h3 class="page-title">
			Resources
			<small>
				Downloadable resources for your business.
			</small>
		</h3>
		<ul class="breadcrumb">
			<li>
				<?php if ( function_exists('yoast_breadcrumb') ) {yoast_breadcrumb('<span id="breadcrumbs">','</span>');} ?>
			</li>	
		</ul>
	</div>
	<!-- END PAGE TITLE & BREADCRUMB -->
		<!-- BEGIN PAGE CONTAINER -->
		 <div class="container-fluid">
		    <div id="dashboard">
		       <div class="row-fluid">
		          <div class="span12 responsive">
					<?php get_template_part( 'content', 'single-resource' ); ?>
					
		          </div>                  
		       </div>
		       <div class="clearfix"></div>
		    </div>
		 </div>
		<!-- END PAGE CONTAINER -->
		<!-- BEGIN COMMENTS -->
		<section class="resources_comment_row">
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span9">
						<?php comments_template('/comments-resource.php'); ?> 
					</div>
					<div class="span3">
					</div>
				</div>
			</div>
		</section>  
		<!-- END COMMENTS --> 
		
		<section class="container-fluid idemnity_policy">
			<div class="row-fluid">
				<div class="span9">
					<p>While every effort has been taken to provide users with accurate and thorough information, it is always advisable to consult legal counsel, human resources, and to use discretion when considering how Saltsha content can help business owners and entrepreneurs learn, grow, and stay current. View Saltsha's Indemnity policy <a href="<?php get_site_url() ?>/terms-and-conditions">here.</a></p>
				</div>
				<div class="span3">
				</div>
			</div>
		</section>
	<?php endwhile; // end of the loop. ?>

<?php get_footer(); ?>
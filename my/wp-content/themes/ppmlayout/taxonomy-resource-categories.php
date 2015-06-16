<?php
/**
 * The template for displaying Archive pages.
 *
 * Used to display archive-type pages if nothing more specific matches a query.
 * For example, puts together date-based pages if no date.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */

get_header(); ?>

<?php if ( have_posts() ) : ?>
	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
	<div class="page-breadcrumb-heading">
		<h3 class="page-title">
					<?php if ( is_day() ) : ?>
						<?php printf( __( 'Resources %s', 'ppmlayout' ), '<small>' . get_the_date() . '</small>' ); ?>
					<?php elseif ( is_month() ) : ?>
						<?php printf( __( 'Resources %s', 'ppmlayout' ), '<small>' . get_the_date( _x( 'F Y', 'monthly archives date format', 'ppmlayout' ) ) . '</small>' ); ?>
					<?php elseif ( is_year() ) : ?>
						<?php printf( __( 'Resources %s', 'ppmlayout' ), '<small>' . get_the_date( _x( 'Y', 'yearly archives date format', 'ppmlayout' ) ) . '</small>' ); ?>
					<?php else : ?>
						<?php _e( 'Resources', 'ppmlayout' ); ?>
					<?php endif; ?>
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
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'resource' ); ?>
				<?php endwhile; ?>
				<?php ppmlayout_content_nav( 'nav-below' ); ?>				
	          </div>                  
	       </div>
	       <div class="clearfix"></div>
	    </div>
	 </div>
	 <!-- END PAGE CONTAINER--> 

<?php else : ?>

	<!-- BEGIN PAGE TITLE & BREADCRUMB-->
	<div class="page-breadcrumb-heading">
		<h3 class="page-title">
			<?php _e( 'Nothing Found', 'ppmlayout' ); ?>
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
		          <div class="row-fluid page-404">
                     <div class="span5 number">
                        N/A
                     </div>
                     <div class="span7 details">
                        <h3>Apologies, No Results.</h3>
                        <p>
                           We did not find any results.<br />
                           Perhaps searching will help find a related post.
                        </p>
                      
                        
                        <form method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
                           <div class="input-append">                      
                              <input class="m-wrap noResults" class="field" name="s" id="s" size="16" type="text" placeholder="keyword..." />
                              <input type="submit" name="submit" id="searchsubmit" class="btn blue" value="Search" />
                           </div>
                        </form>
                     </div>
                  </div>                 
		       </div>
		       <div class="clearfix"></div>
		    </div>
		 </div>
		 <!-- END PAGE CONTAINER-->    

<?php endif; ?>

<?php get_footer(); ?>
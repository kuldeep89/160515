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

get_header();  
if ( have_posts() ) : ?>

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
	 <div class="container-fluid featured_resources">
	    <div id="dashboard">
	      <ul id="featured_nav">
	      	<li><a href="#" class="current" data-identifier="f-featured">Featured</a></li>
	      	<li><a href="#" data-identifier="f-newest">Newest</a></li>
	      	<li><a href="#" data-identifier="f-rating">Rating</a></li>
	      </ul>
	       <div class="row-fluid">
	          <div class="span12 responsive featured">
	          	<?php query_posts(
	          		array (	'posts_per_page' => 4,
	          		 		'post_type' => 'resource',
	          		 		'resource-categories' => 'Featured'
			  		 	  )
	          	); ?>
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', 'resource' ); ?>
				<?php endwhile; ?>			
	          </div>                  
	       </div>
	       <div class="clearfix"></div>
	    </div>
	 </div>
	 
	 <?php wp_reset_query(); ?>
	 
	 <div class="container-fluid general_resources">
	    <div id="dashboard_2">
	       <select id="general_nav">
	          <option disabled selected>Filter by:</option>
	          <option value="alphabetical">Alphabetical</option>
		   	  <option value="newest">Newest</option>
		   	  <option value="rating">Rating</option>
	       </select>
	       <div class="row-fluid">
	          <div class="span12 responsive general">
	          	<?php 
	          		$post_count = wp_count_posts('resource')->publish;
	          		$pagination = 0;
	          		$GLOBALS['ppm_post_inc'] = 0; 
	          	
		  			query_posts(array('posts_per_page' => 8, 'post_type' => 'resource')); 
		  		 	 
		  		 	 while (have_posts()): the_post();
		  		 	 	get_template_part( 'content', 'resource' );
		  		 	 endwhile; 
			  	?>	
	          </div>                  
	       </div>
	       <div id="pagination">
	       	<button id="prev" class="inactive" value="prev"><img width='11' height='13' title='' alt='' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAANCAYAAAB/9ZQ7AAAAJ0lEQVQoz2P4//9/439U8ACIFRhwgZGq4QCahoOkmKw4BBQqUBRMACGlIJOa/MO5AAAAAElFTkSuQmCC'></button>
	       	<span>Page <?php echo ($pagination + 2)/2; ?></span>
	       	<button id="next" class="<?php if($post_count < 8) echo 'inactive';  ?>" value="next"><img width='11' height='13' title='' alt='' src='data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAANCAYAAAB/9ZQ7AAAAJ0lEQVQoz2P4//9/439U8ACIFRhwgZGq4QCahoOkmKw4BBQqUBRMACGlIJOa/MO5AAAAAElFTkSuQmCC'></button></div>	
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
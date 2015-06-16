<?php
/**
 * The template used to display Tag Archive pages
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
					Academy
					<small>
						<?php single_tag_title(); ?>
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
	          
				<?php while ( have_posts() ) : the_post(); ?>
					<?php get_template_part( 'content', get_post_format() ); ?>
				<?php endwhile; ?>
                  
               <div class="pagination pagination-right">
				<?php  global $wp_query;
					$big = 999999999; // need an unlikely integer

					echo paginate_links( array(
					'base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'format' => '?paged=%#%',
					'prev_text' => __('Prev'),
					'next_text' => __('Next'),
					'current' => max( 1, get_query_var('paged') ),
					'type' => 'list',
					'total' => $wp_query->max_num_pages
					) ); 
				?>
               </div>
				
	          </div>                  
	       </div>
	       <div class="clearfix"></div>
	    </div>
	 </div>
	<!-- END PAGE CONTAINER--> 
	
<?php else : ?>
	
	<!-- BEGIN PAGE CONTAINER-->
	 <div class="container-fluid">
	    <!-- BEGIN PAGE HEADER-->
	    <div class="row-fluid">
	       <div class="span12">
	                         
				<!-- BEGIN PAGE TITLE & BREADCRUMB-->			
				<h3 class="page-title">
					<?php _e( 'Nothing Found', 'ppmlayout' ); ?>
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
                              <input class="m-wrap" class="field" name="s" id="s" size="16" type="text" placeholder="keyword..." />
                              <input type="submit" name="submit" id="searchsubmit" class="btn blue" value="Search" />
                           </div>
                        </form>
                     </div>
                  </div>					
	          </div>                  
	       </div>
	       <div class="clearfix"></div>
	    </div>
	 </div>
	<!-- END PAGE CONTAINER-->
	   
<?php endif; ?>
			
<?php get_footer(); ?>

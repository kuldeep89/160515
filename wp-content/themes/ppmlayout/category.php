<?php
/**
 * The template for displaying Category Archive pages.
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
			<?php printf( __( 'Academy %s', 'ppmlayout' ), '<small>Learn, grow, stay current.</small>' ); ?>
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
	          	<h1>
	          		<?php 
	          			// Echo category name
	          			echo get_category(get_query_var('cat'))->name;
	          		?>
	          	</h1>
				<?php /* Start the Loop */ ?>
				<?php while ( have_posts() ) : the_post(); ?>
					
					<?php
						/* Include the Post-Format-specific template for the content.
						 * If you want to overload this in a child theme then include a file
						 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
						 */

						get_template_part( 'content', get_post_format() );
					?>

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
	          <div class="span12 responsive">
				<div class="row-fluid page-404">
                     <div class="span5 number">
                        N/A
                     </div>
                     <div class="span7 details">
                        <h3>Apologies, no results.</h3>
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
	       </div>
	       <div class="clearfix"></div>
	    </div>
	 </div>
	<!-- END PAGE CONTAINER-->

<?php endif; ?>

<?php get_footer(); ?>

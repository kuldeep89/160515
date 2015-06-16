<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage PPM_Layout
 */

get_header(); ?>

<link href="<?php echo get_template_directory_uri() ?>/all-categories.scss" rel='stylesheet' type='text/css'>

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
	          <div class="span12 responsive news-page">
	          	<?php if(is_home()): ?>

						<?php
						$args = array(
						  'orderby' => 'count',
						  'order' => 'DESC',
						  'exclude' => 1,
						  'number' => 9
						  );

						$categories = get_categories($args);
						  $catCount = 0;
						  foreach($categories as $category) {
						  	$catCount++;
						  	if ($catCount == 1 || $catCount == 3 || $catCount == 5 || $catCount == 7 ) { echo '<div class="span3">'; }
						    echo '<div class="acad top-news"><a  class="btn cat' . $catCount . '" href="' . get_category_link( $category->term_id ) . '" title="' . sprintf( __( "View all posts in %s" ), $category->name ) . '" ' . '>';
						    echo '<span>' . $category->name.'</span><i class="saltsha-cat-icon saltsha-cat-' . $category->slug.'"></i></a></div>';
						    query_posts( 'cat=' . $category->cat_ID . '&posts_per_page=3&orderby=date&order=DESC' );
						    while ( have_posts() ) : the_post();
						    	echo '<div class="news-blocks">';

						    	//Img goes here, if one.
						    	echo '<a href="' . get_permalink() . '">';
						    	echo (has_post_thumbnail()) ? the_post_thumbnail('post-blurb-size', array('class'=>'img-responsive')) : '';
								echo '</a>';
						    	echo '<div class="news-block-padding">';
						    	echo '<h3><a href="' . get_permalink() . '">' . get_the_title() . '</a></h3>';
								echo '<div class="news-block-tags"><em>';
						    	ppmlayout_posted_on();
						    	echo '</em></div>';
						    	echo '<p>';
						    	echo get_the_excerpt() . '</p>';
						    	echo '</div></div><div class="clear"></div>';
						    endwhile;
						    if($catCount == 2 || $catCount == 4 || $catCount == 6 || $catCount==9) { echo '</div>'; }
						    wp_reset_query();
						  }
						?>

				<?php else: ?>
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
			   <?php endif; ?>

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
	       </div>
	       <div class="clearfix"></div>
	    </div>
	 </div>
	<!-- END PAGE CONTAINER-->

<?php endif; ?>

<?php // get_sidebar(); ?>
<?php get_footer(); ?>
<?php
/**
 * The template for displaying Category Archive pages.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */

get_header(); ?>
<main>
	<?php if ( have_posts() ) : ?>
		<section class="postList">
			<div class="container">
				<div class="row">
					<header>
						<h1>
							<?php printf( __( 'Tag: %s', 'ppmlayout' ), '<span>' . single_cat_title( '', false ) . '</span>' );?>
						</h1>
					</header>
						
					<?php 
					$rowCount = 0;
					$columnStart = '<div class="col-md-6 col-xs-12">';
					while ( have_posts() ): the_post(); 
						if ($rowCount == 0) { 
							echo $columnStart;
						} elseif (($rowCount % 3) == 0 ) { 
							echo "</div>".$columnStart;
						}
						$rowCount++;
					?>
					
						<?php get_template_part( 'content', get_post_format() ); ?>
						
					<?php endwhile; ?>
					</div>
					<?php ppmlayout_content_nav( 'nav-below' ); ?>
				</div>
			</div>
		</section>
				
	<?php else : ?>
	
		<?php get_sidebar('404'); ?>
		
	<?php endif; ?>
	
</main>

<?php get_footer(); ?>

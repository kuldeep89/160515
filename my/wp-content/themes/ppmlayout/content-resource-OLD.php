<?php
/**
 * The default template for displaying content
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */
?>
	
	<?php $GLOBALS['ppm_post_inc']++; ?>
	<?php echo ($GLOBALS['ppm_post_inc'] == 1) ? '<div class="row-fluid">' : '' ?>
		<div class="span3">
			<!-- Entry -->
			<div class="news-blocks">
				
				<?php if( has_post_thumbnail(get_the_id()) ) : ?>
					<?php the_post_thumbnail( 'large' ); ?>
				<?php endif; ?>
				<div class="news-block-padding">
					<h3>
						<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'ppmlayout' ), the_title_attribute( 'echo=0' ) ); ?>">
							<?php the_title(); ?>
						</a>
					</h3>
					<div class="news-block-tags">
						<em><?php echo ('post' == get_post_type()) ? ppmlayout_posted_on() : '' ?></em>
					</div>
					<p>
						<?php echo get_the_excerpt(); ?>
					</p>
					<div class="clear"></div>
				</div>
			</div>
			<!-- End of Entry -->
		</div>
	<?php echo ($GLOBALS['ppm_post_inc'] % 4 == 0 && $GLOBALS['ppm_post_inc'] != get_option('posts_per_page')) ? '</div><div class="row-fluid">' : '' ?>
	<?php echo ($GLOBALS['ppm_post_inc'] == get_option('posts_per_page')) ? '</div>' : '' ?>
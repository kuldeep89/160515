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
			<div class="news-blocks resources_listing" style="background-image: url(<?php $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'large'); echo $image[0]; ?> );">
				
				<div class="news-block-padding" data-single="<?php echo the_permalink(); ?>">
					<h3>
						<a href="<?php the_permalink(); ?>" title="<?php printf( esc_attr__( 'Permalink to %s', 'ppmlayout' ), the_title_attribute( 'echo=0' ) ); ?>">
							<?php the_title(); ?>
						</a>
						<?php 
							$instance = array(
							'enabled'				=> '2',
							'displaystyle'			=> 'grey',
							'displayaverage'		=> '1',
							'averageratingtext'		=> '',
							'displaytotalratings'	=> '1',
							'displaybreakdown'		=> '0' );
							if (function_exists('display_average_rating')) display_average_rating($instance); 
						?>
					</h3>
					<div class="clear"></div>
				</div>
				<a href="<?php the_permalink(); ?>"><div class="shadow_layer"></div></a>
			</div>
		</div>
	<?php echo ($GLOBALS['ppm_post_inc'] % 4 == 0 && $GLOBALS['ppm_post_inc'] != get_option('posts_per_page') && $GLOBALS['ppm_post_inc'] != $wp_query->post_count) ? '</div><div class="row-fluid">' : '' ?>
	<?php echo ($GLOBALS['ppm_post_inc'] == $wp_query->post_count  || $GLOBALS['ppm_post_inc'] == get_option('posts_per_page')) ? '</div>' : '' ?>
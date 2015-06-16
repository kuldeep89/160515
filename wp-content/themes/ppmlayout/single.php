<?php
/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage PPM_Layout
 * @since PPM Layout 1.0
 */

get_header(); ?>

	<section class="post">
		<div class="container">
			<?php while(have_posts()): the_post(); ?>
				
				<div class="row">
					<div class="col-xs-9">
						<?php get_template_part('content', 'single'); ?>
						<div class="row">
							<div class="col-xs-12">
								<?php echo comments_template(); ?>
							</div>
						</div>
					</div>
					<div class="col-xs-3">
						<?php get_sidebar(); ?>
					</div>
				</div>
				
				<div class="row postNav">
					<div class="col-sm-6 col-xs-12">
						<?php $prev_post = get_adjacent_post(false, '', true);
						if(!empty($prev_post)) {
							echo '<a href="' . get_permalink($prev_post->ID) . '" class="previousPost" title="&laquo;' . $prev_post->post_title . '"><span>Prev :</span> ' . $prev_post->post_title . '</a>'; 
						} ?>
					</div>
					<div class="col-sm-6 col-xs-12">
						<?php $next_post = get_adjacent_post(false, '', false);
						if(!empty($next_post)) {
							echo '<a href="' . get_permalink($next_post->ID) . '" class="nextPost" title="' . $next_post->post_title . '&raquo;">' . $next_post->post_title . ' <span>: Next</span></a>'; 
						} ?>
					</div>
				</div>
				
			<?php endwhile; ?>
		</div>
	</section>

<?php get_footer(); ?>
<?php get_header(); ?>
<div class="row full-width">
	<div class="large-8 columns">
	<?php 
		if ( have_posts() ):  ?>
		<?php while ( have_posts() ): the_post(); ?>
			<article class="post_exerpt">
				<a href="<?php the_permalink(); ?>" title="Read more"><h3><?php the_title(); ?></h3></a>
				<aside><em><?php the_author(); ?></em> | <em><?php the_time('F jS, Y'); ?></em></aside>
				<?php if (has_post_thumbnail()){  ?><a class="featured_image" href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a><?php } ?>
				<div class="excerpt">
					<p><?php echo get_excerpt(); ?></p>
				</div>	
			</article>
		<?php endwhile; ?>
	<?php else: ?>
		<h4>No Posts</h4>
	<?php endif; ?>
	
	<?php if ( function_exists('FoundationPress_pagination') ) { FoundationPress_pagination(); } else if ( is_paged() ) { ?>
		<nav id="post-nav">
			<div class="post-previous"><?php next_posts_link( __( '&larr; Older posts', 'FoundationPress' ) ); ?></div>
			<div class="post-next"><?php previous_posts_link( __( 'Newer posts &rarr;', 'FoundationPress' ) ); ?></div>
		</nav>
	<?php } ?>
	</div>
	<?php get_sidebar(); ?>
</div>

<?php get_footer(); ?>
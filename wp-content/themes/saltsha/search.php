

<?php get_header(); ?>
<div class="row full-width">
	<div class="large-12 columns">

		<h4 class="search_title"><?php _e('Search Results for', 'FoundationPress'); ?> "<?php echo get_search_query(); ?>"</h4>
	</div>
</div>
<div class="row full-width">
	<div class="large-8 columns">
	<?php while ( have_posts() ) : the_post(); ?>
		<article class="post">
			<a href="<?php the_permalink(); ?>" title="Read more"><h3><?php the_title(); ?></h3></a>
			<aside><em><?php the_author(); ?></em> | <em><?php the_time('F jS, Y'); ?></em></aside>
			<?php if (has_post_thumbnail()){  ?><div class="featured_image"><?php the_post_thumbnail(); ?></div><?php } ?>
			<div class="excerpt">
				<p><?php echo get_excerpt(); ?></p>
			</div>	
		</article>
	<?php endwhile; ?>
	
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
<aside id="sidebar" class="small-12 large-4 columns">
	<?php do_action('foundationPress_before_sidebar'); ?>
	<?php dynamic_sidebar("sidebar-blog"); ?>
	<?php do_action('foundationPress_after_sidebar'); ?>
	<div class="row">
		<div class="large-12 columns">
			<?php if( have_rows('blog_cta','options') ): ?>
				<?php while( have_rows('blog_cta','options') ): the_row();
		
						$image = get_sub_field('cta_image','options');
						$link = get_sub_field('cta_link','options');
					?>
					<a href="<?php echo $link; ?>" target="_blank" class="blog_cta">
						<img src="<?php echo $image['url']; ?>" alt="<?php echo $image['alt']; ?>" />
					</a>
				<?php endwhile; ?>
			<?php endif; ?>
		</div>
	</div>
</aside>
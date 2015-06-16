<?php
/*
Template Name: Full Width
*/

get_header(); ?>

<div class="row full-width">
	<div class="small-12 large-12 columns" role="main">

		<?php while (have_posts()) : the_post(); ?>
					<?php the_content(); ?>
		<?php endwhile; ?>

	</div>
</div>
		
<?php get_footer(); ?>